<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\MySQL;

use
    Exception,
    Fyre\DB\Connection,
    Fyre\DB\Exceptions\DBException,
    Fyre\DB\ResultSet,
    mysqli;

use const
    MYSQLI_CLIENT_COMPRESS,
    MYSQLI_INIT_COMMAND,
    MYSQLI_OPT_CONNECT_TIMEOUT,
    MYSQLI_SET_CHARSET_NAME;

use function
    mysql_init;

/**
 * MySQLConnection
 */
class MySQLConnection extends Connection
{

    protected static array $defaults = [
        'port' => '3306'
    ];

    protected array $config;

    protected mysqli|null $connection = null;

    /**
     * Get the number of affected rows.
     * @param int The number of affected rows.
     */
    public function affectedRows(): int
    {
        return (int) $this->connection->affected_rows;
    }

    /**
     * Connect to the database.
     */
    public function connect(): void
    {
        if ($this->connection) {
            return;
        }

        $this->connection = mysqli_init();

        if ($this->config['ssl'] && $this->config['ssl']['key']) {
            $this->connection->ssl_set(
                $this->config['ssl']['key'] ?? null,
                $this->config['ssl']['cert'] ?? null,
                $this->config['ssl']['ca'] ?? null,
                $this->config['ssl']['capath'] ?? null,
                $this->config['ssl']['cipher'] ?? null
            );
        }

        if ($this->config['timeout']) {
            $this->connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, $this->config['timeout']);
        }

        if ($this->config['charset']) {
            $this->connection->options(MYSQLI_SET_CHARSET_NAME, $this->config['charset']);
        }

        if ($this->config['collation']) {
            $this->connection->options(MYSQLI_INIT_COMMAND, 'SET collation_connection = '.$this->config['collation']);
        }

        $flags = 0;

        if ($this->config['compress']) {
            $flags |= MYSQLI_CLIENT_COMPRESS;
        }

        try {
            $this->connection->real_connect(
                $this->config['host'],
                $this->config['username'],
                $this->config['password'],
                $this->config['database'],
                (int) $this->config['port'],
                '',
                $flags
            );
        } catch (Exception $e) {
            throw DBException::forConnectionFailed($e->getMessage());
        }
    }

    /**
     * Disconnect from the database.
     */
    public function disconnect(): bool
    {
        return $this->connection->close();
    }

    /**
     * Get the connection charset.
     * @return string The connection charset.
     */
    public function getCharset(): string
    {
        return $this->connection->get_charset()->charset;
    }

    /**
     * Get the connection collation.
     * @return string The connection collation.
     */
    public function getCollation(): string
    {
        return $this->connection->get_charset()->collation;
    }

    /**
     * Get the last connection error.
     * @return string The last  connection error.
     */
    public function getError(): string
    {
        return $this->connection->error;
    }

    /**
     * Get the last inserted ID.
     * @return int The last inserted ID.
     */
    public function insertId(): int
    {
        return (int) $this->connection->insert_id;
    }

    /**
     * Quote a string for use in SQL queries.
     * @param string $value The value to quote.
     * @return string The quoted value.
     */
    public function quote(string $value): string
    {
        return '"'.$this->connection->real_escape_string($value).'"';
    }

    /**
     * Execute a raw SQL query.
     * @param string $query The SQL query.
     * @return mixed The raw result.
     */
    public function rawQuery(string $query)
    {
        return $this->connection->query($query);
    }

    /**
     * Build a result set from a raw result.
     * @param mixed $result The raw result.
     * @return ResultSet The result set.
     */
    public function results($result): ResultSet
    {
        return new MySQLResultSet($result);
    }

}
