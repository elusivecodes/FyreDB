<?php

namespace Fyre;

use
    mysqli;

use const
    FILTER_VALIDATE_FLOAT,
    MYSQLI_CLIENT_COMPRESS,
    MYSQLI_INIT_COMMAND,
    MYSQLI_OPT_CONNECT_TIMEOUT,
    MYSQLI_SET_CHARSET_NAME;

use function
    array_replace_recursive,
    filter_var,
    mysql_init;

class Connection
{

    protected static array $defaults = [
        'host' => '',
        'username' => '',
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

    protected mysqli $connection;

    public function __construct(array $config)
    {
        $this->config = array_replace_recursive(static::$defaults, $config);

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

        $result = $this->connection->real_connect(
            $this->config['host'],
            $this->config['username'],
            $this->config['password'],
            $this->config['database'],
            (int) $this->config['port'],
            '',
            $flags
        );

        if (!$result) {
            throw SQLException::forConnectionFailed();
        }
    }

    public function affectedRows(): int
    {
        return (int) $this->connection->affected_rows;
    }

    public function begin(): bool
    {
        return $this->query('START TRANSACTION');
    }

    public function builder(): QueryBuilder
    {
        return new QueryBuilder($this);
    }

    public function close(): bool
    {
        return $this->connection->close();
    }

    public function commit(): bool
    {
        return $this->query('COMMIT');
    }

    public function insertId(): int
    {
        return (int) $this->connection->insert_id;
    }

    public function query(string $query): Query|bool
    {
        $result = $this->connection->query($query);

        if ($result === false) {
            throw SQLException::forQueryFailed($this->connection->error);
        }

        if ($result === true) {
            return $result;
        }

        return new Query($result);
    }

    public function quote(string|int|float|bool|null $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if ($value === false) {
            return '0';
        }

        if ($value === true) {
            return '1';
        }

        if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
            return (string) (float) $value;
        }

        return '"'.$this->connection->real_escape_string($value).'"';
    }

    public function rollback(): bool
    {
        return $this->query('ROLLBACK');
    }

}
