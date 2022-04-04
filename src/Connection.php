<?php
declare(strict_types=1);

namespace Fyre\DB;

use
    Closure,
    Exception,
    Fyre\DB\Exceptions\DbException,
    Throwable;

use function
    array_replace_recursive;

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
            'cipher' => null
        ]
    ];

    protected array $config;

    protected QueryGenerator $generator;

    protected int $savePointLevel = 0;

    /**
     * New Connection constructor.
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
     * @param int The number of affected rows.
     */
    abstract public function affectedRows(): int;

    /**
     * Begin a transaction.
     * @return Connection The Connection.
     */
    public function begin(): static
    {
        if ($this->savePointLevel === 0) {
            $this->transBegin();
        } else {
            $this->transSavepoint((string) $this->savePointLevel);
        }

        $this->savePointLevel++;

        return $this;
    }

    /**
     * Create a QueryBuilder.
     * @return QueryBuilder A new QueryBuilder.
     */
    public function builder(): QueryBuilder
    {
        return new QueryBuilder($this);
    }

    /**
     * Commit a transaction.
     * @return Connection The Connection.
     */
    public function commit(): static
    {
        $this->savePointLevel--;

        if ($this->savePointLevel === 0) {
            $this->transCommit();
        } else {
            $this->transRelease((string) $this->savePointLevel);
        }

        return $this;
    }

    /**
     * Connect to the database.
     */
    abstract public function connect(): void;

    /**
     * Disconnect from the database.
     */
    abstract public function disconnect(): bool;

    /**
     * Execute a SQL query with bound parameters.
     * @param string $query The SQL query.
     * @param array $params The parameters to bind.
     * @return ResultSet|bool The result for SELECT queries, otherwise TRUE for successful queries.
     */
    abstract public function execute(string $query, array $params): ResultSet|bool;

    /**
     * Get the query generator.
     * @return QueryGenerator The query generator.
     */
    public function generator(): QueryGenerator
    {
        return $this->generator ??= new QueryGenerator($this);
    }

    /**
     * Get the connection charset.
     * @return string The connection charset.
     */
    abstract public function getCharset(): string;

    /**
     * Get the connection collation.
     * @return string The connection collation.
     */
    abstract public function getCollation(): string;

    /**
     * Get the config.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get the last connection error.
     * @return string The last  connection error.
     */
    abstract public function getError(): string;

    /**
     * Get the last inserted ID.
     * @return int The last inserted ID.
     */
    abstract public function insertId(): int;

    /**
     * Execute a SQL query.
     * @param string $sql The SQL query.
     * @return ResultSet|bool The result for SELECT queries, otherwise TRUE for successful queries.
     * @throws DbException if the query failed.
     */
    public function query(string $sql): ResultSet|bool
    {
        $result = $this->rawQuery($sql);

        return $this->result($result);
    }

    /**
     * Quote a string for use in SQL queries.
     * @param string $value The value to quote.
     * @return string The quoted value.
     */
    abstract public function quote(string $value): string;

    /**
     * Execute a raw SQL query.
     * @param string $sql The SQL query.
     * @return mixed The raw result.
     */
    abstract public function rawQuery(string $sql): mixed;

    /**
     * Generate a result set from a raw result.
     * @param mixed $result The raw result.
     * @return ResultSet|bool The result set or TRUE if the query was successful.
     */
    abstract public function result(mixed $result): ResultSet|bool;

    /**
     * Rollback a transaction.
     * @return Connection The Connection.
     */
    public function rollback(): static
    {
        $this->savePointLevel--;

        if ($this->savePointLevel === 0) {
            $this->transRollback();
        } else {
            $this->transRollbackTo((string) $this->savePointLevel);
        }

        return $this;
    }

    /**
     * Execute a callback inside a database transaction.
     * @param Closure $callback The callback.
     * @return bool TRUE if the transaction was successful, otherwise FALSE.
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
     * Begin a transaction.
     */
    abstract protected function transBegin(): void;

    /**
     * Commit a transaction.
     */
    abstract protected function transCommit(): void;

    /**
     * Release a transaction savepoint.
     * @param string $savePoint The save point name.
     */
    abstract protected function transRelease(string $savePoint): void;

    /**
     * Rollback a transaction.
     */
    abstract protected function transRollback(): void;

    /**
     * Rollback to a transaction savepoint.
     * @param string $savePoint The save point name.
     */
    abstract protected function transRollbackTo(string $savePoint): void;

    /**
     * Save a transaction save point.
     * @param string $savePoint The save point name.
     */
    abstract protected function transSavepoint(string $savePoint): void;

}
