<?php
declare(strict_types=1);

namespace Fyre\DB;

use Closure;
use Fyre\Container\Container;
use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Queries\DeleteQuery;
use Fyre\DB\Queries\InsertFromQuery;
use Fyre\DB\Queries\InsertQuery;
use Fyre\DB\Queries\ReplaceQuery;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\Queries\UpdateBatchQuery;
use Fyre\DB\Queries\UpdateQuery;
use Fyre\Event\EventDispatcherTrait;
use Fyre\Event\EventManager;
use Fyre\Log\LogManager;
use PDO;
use PDOException;
use PDOStatement;
use Throwable;

use function array_filter;
use function array_is_list;
use function array_keys;
use function array_map;
use function array_replace_recursive;
use function filter_var;
use function implode;
use function is_bool;
use function is_int;
use function is_resource;
use function is_string;
use function min;
use function preg_quote;
use function preg_replace;
use function usort;

use const FILTER_VALIDATE_FLOAT;

/**
 * Connection
 */
abstract class Connection
{
    use EventDispatcherTrait;

    protected static array $defaults = [
        'log' => false,
    ];

    protected int|null $affectedRows = null;

    protected array $afterCommitCallbacks = [];

    protected array $config;

    protected Container $container;

    protected QueryGenerator $generator;

    protected bool $inTransaction = false;

    protected LogManager $logManager;

    protected bool $logQueries = false;

    protected PDO|null $pdo = null;

    protected ConnectionRetry $retry;

    protected int $savePointLevel = 0;

    protected bool $useSavePoints = true;

    protected string|null $version = null;

    /**
     * New Connection constructor.
     *
     * @param Container $container The Container.
     * @param EventManager $eventManager The EventManager.
     * @param LogManager $logManager The LogManager.
     * @param array $options Options for the handler.
     */
    public function __construct(Container $container, EventManager $eventManager, LogManager $logManager, array $options = [])
    {
        $this->container = $container;
        $this->eventManager = $eventManager;
        $this->logManager = $logManager;

        $this->config = array_replace_recursive(self::$defaults, static::$defaults, $options);
        $this->logQueries = $this->config['log'];

        $this->connect();
    }

    /**
     * Connection destructor.
     */
    public function __destruct()
    {
        if ($this->inTransaction) {
            $this->logManager->handle('warning', 'Connection closing while a transaction is in process.');
        }

        $this->disconnect();
    }

    /**
     * Get the number of affected rows.
     *
     * @param int|null The number of affected rows.
     */
    public function affectedRows(): int|null
    {
        return $this->affectedRows;
    }

    /**
     * Queue a callback to execute after the transaction is committed.
     *
     * @param Closure $callback The callback.
     * @param int $priority The callback priority.
     * @param string|null $key The callback key.
     * @return Connection The Connection.
     */
    public function afterCommit(Closure $callback, int $priority = 1, string|null $key = null): static
    {
        if (!$this->savePointLevel) {
            $callback();
        } else {
            $data = [
                'callback' => $callback,
                'priority' => $priority,
                'savePointLevel' => $this->savePointLevel,
            ];

            if ($key === null) {
                $this->afterCommitCallbacks[] = $data;
            } else {
                $this->afterCommitCallbacks[$key] = $data;
            }
        }

        return $this;
    }

    /**
     * Begin a transaction.
     *
     * @return Connection The Connection.
     */
    public function begin(): static
    {
        if ($this->savePointLevel === 0) {
            $this->transBegin();
        } else if ($this->useSavePoints) {
            $this->transSavepoint((string) $this->savePointLevel);
        }

        $this->savePointLevel++;

        return $this;
    }

    /**
     * Commit a transaction.
     *
     * @return Connection The Connection.
     */
    public function commit(): static
    {
        if (!$this->savePointLevel) {
            return $this;
        }

        if ($this->savePointLevel === 1) {
            $this->transCommit();
        } else if ($this->useSavePoints) {
            $this->transRelease((string) ($this->savePointLevel - 1));
        }

        $this->savePointLevel--;

        if ($this->savePointLevel === 0) {
            $callbacks = $this->afterCommitCallbacks;

            $this->afterCommitCallbacks = [];

            usort($callbacks, fn(array $a, $b): int => $a['priority'] <=> $b['priority']);

            foreach ($callbacks as $callback) {
                try {
                    $callback['callback']();
                } catch (Throwable $e) {
                }
            }
        } else {
            $this->afterCommitCallbacks = array_map(
                function(array $afterCommitCallback): array {
                    $afterCommitCallback['savePointLevel'] = min($afterCommitCallback['savePointLevel'], $this->savePointLevel);

                    return $afterCommitCallback;
                },
                $this->afterCommitCallbacks
            );
        }

        return $this;
    }

    /**
     * Connect to the database.
     */
    abstract public function connect(): void;

    /**
     * Create a DeleteQuery.
     *
     * @param array|string|null $alias The alias to delete.
     * @return DeleteQuery A new DeleteQuery.
     */
    public function delete(array|string $alias = []): DeleteQuery
    {
        return new DeleteQuery($this, $alias);
    }

    /**
     * Disable query logging.
     *
     * @return Connection The Connection.
     */
    public function disableQueryLogging(): static
    {
        $this->logQueries = false;

        return $this;
    }

    /**
     * Disconnect from the database.
     */
    public function disconnect(): bool
    {
        $this->pdo = null;

        return true;
    }

    /**
     * Enable query logging.
     *
     * @return Connection The Connection.
     */
    public function enableQueryLogging(): static
    {
        $this->logQueries = true;

        return $this;
    }

    /**
     * Execute a SQL query with bound parameters.
     *
     * @param string $sql The SQL query.
     * @param array $params The parameters to bind.
     * @return ResultSet The ResultSet.
     *
     * @throws DbException if the query threw an error.
     */
    public function execute(string $sql, array $params): ResultSet
    {
        try {
            return $this->retry()->run(function() use ($sql, $params) {
                $this->dispatchEvent('Db.query', ['sql' => $sql, 'params' => $params]);

                if ($this->logQueries) {
                    if ($params === []) {
                        $logMessage = $sql;
                    } else {
                        $logParams = array_map(function(mixed $value): string {
                            if ($value === null) {
                                return 'NULL';
                            }

                            if ($value === false) {
                                return 'FALSE';
                            }

                            if ($value === true) {
                                return 'TRUE';
                            }

                            $value = (string) $value;

                            if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
                                return $value;
                            }

                            return $this->quote($value);
                        }, $params);

                        $logKeys = array_map(
                            fn(string $key): int|string => is_string($key) ?
                                '/:'.preg_quote($key, '/').'\b/' :
                                '/[?]/',
                            array_keys($params)
                        );

                        $logMessage = preg_replace($logKeys, $logParams, $sql);
                    }

                    $this->logManager->handle('debug', $logMessage, scope: 'queries');
                }

                $query = $this->pdo->prepare($sql);

                if (array_is_list($params)) {
                    $query->execute($params);
                } else {
                    foreach ($params as $param => $value) {
                        if (is_resource($value)) {
                            $type = PDO::PARAM_LOB;
                        } else if (is_int($value)) {
                            $type = PDO::PARAM_INT;
                        } else if (is_bool($value)) {
                            $type = PDO::PARAM_BOOL;
                        } else if ($value === null) {
                            $type = PDO::PARAM_NULL;
                        } else {
                            $type = PDO::PARAM_STR;
                        }

                        $query->bindValue($param, $value, $type);
                    }

                    $query->execute();
                }

                return $this->result($query);
            });
        } catch (PDOException $e) {
            throw DbException::forQueryError($e->getMessage());
        }
    }

    /**
     * Get the query generator.
     *
     * @return QueryGenerator The query generator.
     */
    public function generator(): QueryGenerator
    {
        return $this->generator ??= $this->container->build(QueryGenerator::class, ['connection' => $this]);
    }

    /**
     * Get the connection charset.
     *
     * @return string The connection charset.
     */
    abstract public function getCharset(): string;

    /**
     * Get the config.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get the last connection error.
     *
     * @return string The last  connection error.
     */
    public function getError(): string
    {
        $info = $this->pdo->errorInfo();

        return implode(' ', $info);
    }

    /**
     * Get the transaction save point level.
     *
     * @return int The transaction save point level.
     */
    public function getSavePointLevel(): int
    {
        return $this->savePointLevel;
    }

    /**
     * Create an InsertQuery.
     *
     * @return InsertQuery A new InsertQuery.
     */
    public function insert(): InsertQuery
    {
        return new InsertQuery($this);
    }

    /**
     * Create an InsertFromQuery.
     *
     * @param Closure|QueryLiteral|SelectQuery|string $from The query.
     * @param array $columns The columns.
     * @return InsertFromQuery A new InsertFromQuery.
     */
    public function insertFrom(Closure|QueryLiteral|SelectQuery|string $from, array $columns = []): InsertFromQuery
    {
        return new InsertFromQuery($this, $from, $columns);
    }

    /**
     * Get the last inserted ID.
     *
     * @return int|null The last inserted ID.
     */
    public function insertId(): int|null
    {
        $lastId = $this->pdo->lastInsertId();

        if ($lastId === false) {
            return null;
        }

        return (int) $lastId;
    }

    /**
     * Determine whether a transaction is in progress.
     *
     * @return bool TRUE if a transaction is in progress, otherwise FALSE.
     */
    public function inTransaction(): bool
    {
        return $this->inTransaction;
    }

    /**
     * Create a QueryLiteral.
     *
     * @param string $string The literal string.
     * @return QueryLiteral A new QueryLiteral.
     */
    public function literal(string $string): QueryLiteral
    {
        return new QueryLiteral($string);
    }

    /**
     * Execute a SQL query.
     *
     * @param string $sql The SQL query.
     * @return ResultSet The ResultSet.
     */
    public function query(string $sql): ResultSet
    {
        $result = $this->rawQuery($sql);

        return $this->result($result);
    }

    /**
     * Quote a string for use in SQL queries.
     *
     * @param string $value The value to quote.
     * @return string The quoted value.
     */
    public function quote(string $value): string
    {
        return $this->pdo->quote($value);
    }

    /**
     * Execute a raw SQL query.
     *
     * @param string $sql The SQL query.
     * @return PDOStatement The raw result.
     *
     * @throws DbException if the query threw an error.
     */
    public function rawQuery(string $sql): PDOStatement
    {
        try {
            return $this->retry()->run(function() use ($sql) {
                $this->dispatchEvent('Db.query', ['sql' => $sql]);

                if ($this->logQueries) {
                    $this->logManager->handle('debug', $sql, scope: 'queries');
                }

                return $this->pdo->query($sql);
            });
        } catch (PDOException $e) {
            throw DbException::forQueryError($e->getMessage());
        }
    }

    /**
     * Create a ReplaceQuery.
     *
     * @return ReplaceQuery A new ReplaceQuery.
     */
    public function replace(): ReplaceQuery
    {
        if (!$this->supports(DbFeature::Replace)) {
            throw DbException::forUnsupportedFeature('REPLACE');
        }

        return new ReplaceQuery($this);
    }

    /**
     * Rollback a transaction.
     *
     * @return Connection The Connection.
     */
    public function rollback(): static
    {
        if (!$this->savePointLevel) {
            return $this;
        }

        if ($this->savePointLevel === 1) {
            $this->transRollback();
        } else if ($this->useSavePoints) {
            $this->transRollbackTo((string) ($this->savePointLevel - 1));
        }

        $this->savePointLevel--;

        if ($this->savePointLevel === 0) {
            $this->afterCommitCallbacks = [];
        } else {
            $this->afterCommitCallbacks = array_filter(
                $this->afterCommitCallbacks,
                fn(array $callback): bool => $callback['savePointLevel'] <= $this->savePointLevel
            );
        }

        return $this;
    }

    /**
     * Create a SelectQuery.
     *
     * @param array|string $fields The fields.
     * @return SelectQuery A new SelectQuery.
     */
    public function select(array|string $fields = '*'): SelectQuery
    {
        return new SelectQuery($this, $fields);
    }

    /**
     * Set the connection charset.
     *
     * @param string $charset The charset.
     * @return Connection The Connection.
     */
    public function setCharset(string $charset): static
    {
        $this->rawQuery('SET NAMES '.$this->quote($charset));

        return $this;
    }

    /**
     * Determine whether the connection supports a feature.
     *
     * @param DbFeature $feature The DB feature.
     * @return bool TRUE if the connection supports the feature, otherwise FALSE.
     */
    abstract public function supports(DbFeature $feature): bool;

    /**
     * Execute a callback inside a database transaction.
     *
     * @param Closure $callback The callback.
     * @return bool TRUE if the transaction was successful, otherwise FALSE.
     *
     * @throws Throwable if the callback throws an exception.
     */
    public function transactional(Closure $callback): bool
    {
        try {
            $this->begin();

            $result = $callback($this);
        } catch (Throwable $e) {
            $this->rollback();

            throw $e;
        }

        if ($result === false) {
            $this->rollback();

            return false;
        }

        $this->commit();

        return true;
    }

    /**
     * Create an UpdateQuery.
     *
     * @param array|string|null $table The table.
     * @return UpdateQuery A new UpdateQuery.
     */
    public function update(array|string|null $table = null): UpdateQuery
    {
        return new UpdateQuery($this, $table);
    }

    /**
     * Create an UpdateBatchQuery.
     *
     * @param string|null $table The table.
     * @return UpdateBatchQuery A new UpdateBatchQuery.
     */
    public function updateBatch(string|null $table = null): UpdateBatchQuery
    {
        return new UpdateBatchQuery($this, $table);
    }

    /**
     * Get the server version.
     *
     * @return string The server version.
     */
    public function version(): string
    {
        return $this->version ??= $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * Generate a result set from a raw result.
     *
     * @param PDOStatement $result The raw result.
     * @return ResultSet The result set.
     */
    protected function result(PDOStatement $result): ResultSet
    {
        $this->affectedRows = $result->rowCount();

        $class = static::resultSetClass();

        return $this->container->build($class, ['result' => $result]);
    }

    /**
     * Get the ResultSet class.
     *
     * @return string The ResultSet class.
     */
    abstract protected static function resultSetClass(): string;

    /**
     * Get the ConnectionRetry.
     *
     * @return ConnectionRetry The ConnectionRetry.
     */
    protected function retry(): mixed
    {
        return $this->retry ??= new ConnectionRetry($this);
    }

    /**
     * Begin a transaction.
     */
    protected function transBegin(): void
    {
        $this->rawQuery('BEGIN');
        $this->inTransaction = true;
    }

    /**
     * Commit a transaction.
     */
    protected function transCommit(): void
    {
        $this->rawQuery('COMMIT');
        $this->inTransaction = false;
    }

    /**
     * Release a transaction savepoint.
     *
     * @param string $savePoint The save point name.
     */
    protected function transRelease(string $savePoint): void
    {
        $this->rawQuery('RELEASE SAVEPOINT sp_'.$savePoint);
    }

    /**
     * Rollback a transaction.
     */
    protected function transRollback(): void
    {
        $this->rawQuery('ROLLBACK');
        $this->inTransaction = false;
    }

    /**
     * Rollback to a transaction savepoint.
     *
     * @param string $savePoint The save point name.
     */
    protected function transRollbackTo(string $savePoint): void
    {
        $this->rawQuery('ROLLBACK TO SAVEPOINT sp_'.$savePoint);
    }

    /**
     * Save a transaction save point.
     *
     * @param string $savePoint The save point name.
     */
    protected function transSavepoint(string $savePoint): void
    {
        $this->rawQuery('SAVEPOINT sp_'.$savePoint);
    }
}
