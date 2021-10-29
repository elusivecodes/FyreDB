<?php
declare(strict_types=1);

namespace Fyre\DB;

use
    Closure,
    Exception,
    Fyre\DB\Exceptions\DBException,
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
        'compress' => true,
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

    /**
     * New Connection constructor.
     * @param array $config Options for the handler.
     */
    public function __construct(array $config = [])
    {
        $this->config = array_replace_recursive(static::$defaults, self::$defaults, $config);

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
     * @return bool TRUE if successful, otherwise FALSE.
     */
    public function begin(): bool
    {
        return $this->query('START TRANSACTION');
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
     * @return bool TRUE if successful, otherwise FALSE.
     */
    public function commit(): bool
    {
        return $this->query('COMMIT');
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
     * @param string $query The SQL query.
     * @return ResultSet|bool The result for SELECT queries, otherwise TRUE for successful queries.
     * @throws DBException if the query failed.
     */
    public function query(string $query): ResultSet|bool
    {
        $result = $this->rawQuery($query);

        if ($result === true) {
            return $result;
        }

        if ($result === false) {
            $error = $this->getError();

            throw DBException::forQueryError($error);
        }

        return $this->results($result);
    }

    /**
     * Quote a string for use in SQL queries.
     * @param string $value The value to quote.
     * @return string The quoted value.
     */
    abstract public function quote(string $value): string;

    /**
     * Execute a raw SQL query.
     * @param string $query The SQL query.
     * @return mixed The raw result.
     */
    abstract public function rawQuery(string $query);

    /**
     * Build a result set from a raw result.
     * @param mixed $result The raw result.
     * @return ResultSet The result set.
     */
    abstract public function results($result): ResultSet;

    /**
     * Rollback a transaction.
     * @return bool TRUE if successful, otherwise FALSE.
     */
    public function rollback(): bool
    {
        return $this->query('ROLLBACK');
    }

    /**
     * Execute a callback inside a database transaction.
     * @param Closure $callback The callback.
     * @throws Throwable if the callback throws an exception.
     */
    public function transactional(Closure $callback): void
    {
        try {
            $this->begin();
    
            $result = $callback($this);
        } catch (Throwable $e) {
            $this->rollback();

            throw $e;
        }

        if ($result !== false) {
            $this->commit();
        } else {
            $this->rollback();
        }
    }

}
