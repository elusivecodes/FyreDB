<?php
declare(strict_types=1);

namespace Fyre\DB;

use Closure;
use Exception;
use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Queries\DeleteQuery;
use Fyre\DB\Queries\InsertFromQuery;
use Fyre\DB\Queries\InsertQuery;
use Fyre\DB\Queries\ReplaceQuery;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\Queries\UpdateBatchQuery;
use Fyre\DB\Queries\UpdateQuery;
use PDO;
use PDOException;
use PDOStatement;
use Throwable;

use function array_replace_recursive;
use function implode;

/**
 * Connection
 */
abstract class Connection
{
    protected static array $defaults = [
        'host' => '127.0.0.1',
        'username' => '',
        'password' => '',
        'database' => '',
        'port' => '3306',
        'collation' => 'utf8mb4_unicode_ci',
        'charset' => 'utf8mb4',
        'compress' => false,
        'persist' => false,
        'timeout' => null,
        'ssl' => [
            'key' => null,
            'cert' => null,
            'ca' => null,
            'capath' => null,
            'cipher' => null,
        ],
        'flags' => [],
    ];

    protected int|null $affectedRows = null;

    protected array $config;

    protected QueryGenerator $generator;

    protected PDO|null $pdo = null;

    protected ConnectionRetry $retry;

    protected int $savePointLevel = 0;

    protected bool $useSavePoints = true;

    protected string|null $version = null;

    /**
     * New Connection constructor.
     *
     * @param array $options Options for the handler.
     */
    public function __construct(array $options = [])
    {
        $this->config = array_replace_recursive(self::$defaults, static::$defaults, $options);

        $this->connect();
    }

    /**
     * Connection destructor.
     */
    public function __destruct()
    {
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

        $this->savePointLevel--;

        if ($this->savePointLevel === 0) {
            $this->transCommit();
        } else if ($this->useSavePoints) {
            $this->transRelease((string) $this->savePointLevel);
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
     * @param string|array|null $alias The alias to delete.
     * @return DeleteQuery A new DeleteQuery.
     */
    public function delete(array|string|null $alias = null): DeleteQuery
    {
        return new DeleteQuery($this, $alias);
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
     * Execute a SQL query with bound parameters.
     *
     * @param string $sql The SQL query.
     * @param array $params The parameters to bind.
     * @return ResultSet|bool The result for SELECT queries, otherwise TRUE for successful queries.
     *
     * @throws DbException if the query threw an error.
     */
    public function execute(string $sql, array $params): bool|ResultSet
    {
        try {
            return $this->retry()->run(function() use ($sql, $params) {
                $query = $this->pdo->prepare($sql);

                $query->execute($params);

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
        return $this->generator ??= new QueryGenerator($this);
    }

    /**
     * Get the connection charset.
     *
     * @return string The connection charset.
     */
    public function getCharset(): string
    {
        return $this->rawQuery('SELECT CHARSET("")')->fetchColumn();
    }

    /**
     * Get the connection collation.
     *
     * @return string The connection collation.
     */
    public function getCollation(): string
    {
        return $this->rawQuery('SELECT COLLATION("")')->fetchColumn();
    }

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
     * @param Closure|SelectQuery|QueryLiteral|string $from The query.
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
     * Determine if a transaction is in progress.
     *
     * @return bool TRUE if a transaction is in progress, otherwise FALSE.
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
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
     * @return ResultSet|bool The result for SELECT queries, otherwise TRUE for successful queries.
     */
    public function query(string $sql): bool|ResultSet
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

        $this->savePointLevel--;

        if ($this->savePointLevel === 0) {
            $this->transRollback();
        } else if ($this->useSavePoints) {
            $this->transRollbackTo((string) $this->savePointLevel);
        }

        return $this;
    }

    /**
     * Create a SelectQuery.
     *
     * @param string|array $fields The fields.
     * @return SelectQuery A new SelectQuery.
     */
    public function select(array|string $fields = '*'): SelectQuery
    {
        return new SelectQuery($this, $fields);
    }

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
     * @param string|array|null $table The table.
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
     * @return ResultSet|bool The result set or TRUE if the query was successful.
     */
    protected function result(PDOStatement $result): bool|ResultSet
    {
        if ($result->columnCount() === 0) {
            $this->affectedRows = $result->rowCount();

            return true;
        }

        $class = static::resultSetClass();

        return new $class($result);
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
        $this->retry()->run(function() {
            $this->pdo->beginTransaction();
        });
    }

    /**
     * Commit a transaction.
     */
    protected function transCommit(): void
    {
        $this->pdo->commit();
    }

    /**
     * Release a transaction savepoint.
     *
     * @param string $savePoint The save point name.
     */
    protected function transRelease(string $savePoint): void
    {
        $this->query('RELEASE SAVEPOINT sp_'.$savePoint);
    }

    /**
     * Rollback a transaction.
     */
    protected function transRollback(): void
    {
        $this->pdo->rollBack();
    }

    /**
     * Rollback to a transaction savepoint.
     *
     * @param string $savePoint The save point name.
     */
    protected function transRollbackTo(string $savePoint): void
    {
        $this->query('ROLLBACK TO SAVEPOINT sp_'.$savePoint);
    }

    /**
     * Save a transaction save point.
     *
     * @param string $savePoint The save point name.
     */
    protected function transSavepoint(string $savePoint): void
    {
        $this->query('SAVEPOINT sp_'.$savePoint);
    }
}
