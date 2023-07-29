<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\MySQL;

use Fyre\DB\Connection;
use Fyre\DB\Exceptions\DbException;
use Fyre\DB\ResultSet;
use PDO;
use PDOException;
use PDOStatement;

use function class_exists;
use function implode;

/**
 * MySQLConnection
 */
class MySQLConnection extends Connection
{

    protected array $config;

    protected PDO|null $connection = null;

    protected int|null $affectedRows = null;

    /**
     * Get the number of affected rows.
     * @param int The number of affected rows.
     */
    public function affectedRows(): int
    {
        return (int) $this->affectedRows;
    }

    /**
     * Connect to the database.
     * @throws DbException if the connection failed.
     */
    public function connect(): void
    {
        if ($this->connection) {
            return;
        }

        if (!class_exists('PDO')) {
            throw new RuntimeException('MySQL handler requires PDO extension');
        }

        $dsn = 'mysql:host='.$this->config['host'].';dbname='.$this->config['database'];

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        if ($this->config['port']) {
            $dsn .= ';port='.$this->config['port'];
        }

        if ($this->config['timeout']) {
            $options[PDO::ATTR_TIMEOUT] = $this->config['timeout'];
        }

        if ($this->config['charset']) {
            $dsn .= ';charset='.$this->config['charset'];
        }

        if ($this->config['collation']) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET collation_connection = '.$this->config['collation'];
        }

        if ($this->config['compress']) {
            $options[PDO::MYSQL_ATTR_COMPRESS] = true;
        }

        if ($this->config['persist']) {
            $options[PDO::ATTR_PERSISTENT] = true;
        }

        if ($this->config['ssl']) {
            if ($this->config['ssl']['key']) {
                $options[PDO::MYSQL_ATTR_SSL_KEY] = $this->config['ssl']['key'];
            }
            if ($this->config['ssl']['cert']) {
                $options[PDO::MYSQL_ATTR_SSL_CERT] = $this->config['ssl']['cert'];
            }
            if ($this->config['ssl']['ca']) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $this->config['ssl']['ca'];
            }
            if ($this->config['ssl']['capath']) {
                $options[PDO::MYSQL_ATTR_SSL_CAPATH] = $this->config['ssl']['capath'];
            }
            if ($this->config['ssl']['cipher']) {
                $options[PDO::MYSQL_ATTR_SSL_CIPHER] = $this->config['ssl']['cipher'];
            }
        }

        try {
            $this->connection = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
        } catch (PDOException $e) {
            throw DbException::forConnectionFailed($e->getMessage());
        }
    }

    /**
     * Disconnect from the database.
     */
    public function disconnect(): bool
    {
        $this->connection = null;

        return true;
    }

    /**
     * Execute a SQL query with bound parameters.
     * @param string $sql The SQL query.
     * @param array $params The parameters to bind.
     * @return ResultSet|bool The result for SELECT queries, otherwise TRUE for successful queries.
     */
    public function execute(string $sql, array $params): ResultSet|bool
    {
        try {
            $query = $this->connection->prepare($sql);

            $query->execute($params);

            return $this->result($query);
        } catch (PDOException $e) {
            throw DbException::forQueryError($e->getMessage());
        }
    }

    /**
     * Get the connection charset.
     * @return string The connection charset.
     */
    public function getCharset(): string
    {
        return $this->rawQuery('SELECT CHARSET("")')->fetchColumn();
    }

    /**
     * Get the connection collation.
     * @return string The connection collation.
     */
    public function getCollation(): string
    {
        return $this->rawQuery('SELECT COLLATION("")')->fetchColumn();
    }

    /**
     * Get the last connection error.
     * @return string The last  connection error.
     */
    public function getError(): string
    {
        $info = $this->connection->errorInfo();

        return implode(' ', $info);
    }

    /**
     * Get the last inserted ID.
     * @return int The last inserted ID.
     */
    public function insertId(): int
    {
        return (int) $this->connection->lastInsertId();
    }

    /**
     * Quote a string for use in SQL queries.
     * @param string $value The value to quote.
     * @return string The quoted value.
     */
    public function quote(string $value): string
    {
        return $this->connection->quote($value);
    }

    /**
     * Execute a raw SQL query.
     * @param string $sql The SQL query.
     * @return PDOStatement The raw result.
     * @throws DbException if the query threw an error.
     */
    public function rawQuery(string $sql): PDOStatement
    {
        try {
            return $this->connection->query($sql);
        } catch (PDOException $e) {
            throw DbException::forQueryError($e->getMessage());
        }
    }

    /**
     * Generate a result set from a raw result.
     * @param $result The raw result.
     * @return MySQLResultSet|bool The result set or TRUE if the query was successful.
     */
    public function result($result): MySQLResultSet|bool
    {
        if (!$result || $result->columnCount() === 0) {
            $this->affectedRows = $result->rowCount();

            return true;
        }

        return new MySQLResultSet($result);
    }

    /**
     * Begin a transaction.
     */
    protected function transBegin(): void
    {
        $this->query('BEGIN');
    }

    /**
     * Commit a transaction.
     */
    protected function transCommit(): void
    {
        $this->query('COMMIT');
    }

    /**
     * Release a transaction savepoint.
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
        $this->query('ROLLBACK');
    }

    /**
     * Rollback to a transaction savepoint.
     * @param string $savePoint The save point name.
     */
    protected function transRollbackTo(string $savePoint): void
    {
        $this->query('ROLLBACK TO SAVEPOINT sp_'.$savePoint);
    }

    /**
     * Save a transaction save point.
     * @param string $savePoint The save point name.
     */
    protected function transSavepoint(string $savePoint): void
    {
        $this->query('SAVEPOINT sp_'.$savePoint);
    }

}
