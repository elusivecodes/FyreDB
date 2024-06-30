<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\Postgres;

use BadMethodCallException;
use Fyre\DB\Connection;
use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Queries\ReplaceQuery;
use Fyre\DB\QueryGenerator;
use PDO;
use PDOException;
use RuntimeException;

use function array_replace;
use function class_exists;

/**
 * PostgresConnection
 */
class PostgresConnection extends Connection
{
    protected static array $defaults = [
        'host' => '127.0.0.1',
        'username' => '',
        'password' => '',
        'database' => '',
        'port' => '5432',
        'charset' => 'utf8',
        'schema' => 'public',
        'persist' => false,
        'timeout' => null,
        'flags' => [],
    ];

    /**
     * Connect to the database.
     *
     * @throws RuntimeException if PDO extension is not installed.
     * @throws DbException if the connection failed.
     */
    public function connect(): void
    {
        if ($this->pdo) {
            return;
        }

        if (!class_exists('PDO')) {
            throw new RuntimeException('Postgres handler requires PDO extension');
        }

        $dsn = 'pgsql:host='.$this->config['host'].';dbname='.$this->config['database'];

        if ($this->config['port']) {
            $dsn .= ';port='.$this->config['port'];
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        if ($this->config['timeout']) {
            $options[PDO::ATTR_TIMEOUT] = $this->config['timeout'];
        }

        if ($this->config['persist']) {
            $options[PDO::ATTR_PERSISTENT] = true;
        }

        $options = array_replace($options, $this->config['flags']);

        try {
            $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
        } catch (PDOException $e) {
            throw DbException::forConnectionFailed($e->getMessage());
        }

        if ($this->config['charset']) {
            $this->setCharset($this->config['charset']);
        }

        if ($this->config['schema']) {
            $this->setSchema($this->config['schema']);
        }
    }

    /**
     * Get the query generator.
     *
     * @return QueryGenerator The query generator.
     */
    public function generator(): QueryGenerator
    {
        return $this->generator ??= new PostgresQueryGenerator($this);
    }

    /**
     * Get the connection charset.
     *
     * @return string The connection charset.
     */
    public function getCharset(): string
    {
        return $this->rawQuery('SHOW CLIENT_ENCODING')->fetchColumn();
    }

    /**
     * Create a ReplaceQuery.
     *
     * @return ReplaceQuery A new ReplaceQuery.
     *
     * @throws BadMethodCallException
     */
    public function replace(): ReplaceQuery
    {
        throw new BadMethodCallException();
    }

    /**
     * Set the connection schema.
     *
     * @param string $schema The schema name.
     * @return Connection The Connection.
     */
    public function setSchema(string $schema): static
    {
        $this->rawQuery('SET search_path TO '.$this->quote($schema));

        return $this;
    }

    /**
     * Get the ResultSet class.
     *
     * @return string The ResultSet class.
     */
    protected static function resultSetClass(): string
    {
        return PostgresResultSet::class;
    }
}
