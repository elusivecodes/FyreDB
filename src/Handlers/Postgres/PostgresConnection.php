<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\Postgres;

use Fyre\DB\Connection;
use Fyre\DB\DbFeature;
use Fyre\DB\Exceptions\DbException;
use Fyre\DB\QueryGenerator;
use Pdo\Pgsql;
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

    protected string $schema;

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
            Pgsql::ATTR_ERRMODE => Pgsql::ERRMODE_EXCEPTION,
        ];

        if ($this->config['timeout']) {
            $options[Pgsql::ATTR_TIMEOUT] = $this->config['timeout'];
        }

        if ($this->config['persist']) {
            $options[Pgsql::ATTR_PERSISTENT] = true;
        }

        $options = array_replace($options, $this->config['flags']);

        try {
            $this->pdo = new Pgsql($dsn, $this->config['username'], $this->config['password'], $options);
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
     * Disable foreign key checks.
     *
     * @return Connection The Connection.
     */
    public function disableForeignKeys(): static
    {
        $this->rawQuery('SET CONSTRAINTS ALL DEFERRED');

        return $this;
    }

    /**
     * Enable foreign key checks.
     *
     * @return Connection The Connection.
     */
    public function enableForeignKeys(): static
    {
        $this->rawQuery('SET CONSTRAINTS ALL IMMEDIATE');

        return $this;
    }

    /**
     * Get the query generator.
     *
     * @return QueryGenerator The query generator.
     */
    public function generator(): QueryGenerator
    {
        return $this->generator ??= $this->container->build(PostgresQueryGenerator::class, ['connection' => $this]);
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
     * Get the connection schema.
     *
     * @return string The schema name.
     */
    public function getSchema(): string
    {
        return $this->schema ?? $this->config['schema'];
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

        $this->schema = $schema;

        return $this;
    }

    /**
     * Determine whether the connection supports a feature.
     *
     * @param DbFeature $feature The DB feature.
     * @return bool TRUE if the connection supports the feature, otherwise FALSE.
     */
    public function supports(DbFeature $feature): bool
    {
        return match ($feature) {
            DbFeature::DeleteUsing,
            DbFeature::InsertReturning,
            DbFeature::UpdateFrom => true,
            default => false,
        };
    }

    /**
     * Truncate a table.
     *
     * @param string $tableName The table name.
     * @return Connection The Connection.
     */
    public function truncate(string $tableName): static
    {
        $this->rawQuery('TRUNCATE '.$tableName.' RESTART IDENTITY CASCADE');

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
