<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\Sqlite;

use Fyre\DB\Connection;
use Fyre\DB\DbFeature;
use Fyre\DB\Exceptions\DbException;
use Pdo\Sqlite;
use PDOException;
use PDOStatement;
use RuntimeException;

use function array_intersect_key;
use function array_replace;
use function chmod;
use function class_exists;
use function file_exists;
use function http_build_query;
use function preg_match;

/**
 * SqliteConnection
 */
class SqliteConnection extends Connection
{
    protected static array $defaults = [
        'database' => ':memory:',
        'mask' => 0644,
        'cache' => null,
        'mode' => null,
        'persist' => false,
        'flags' => [],
    ];

    protected bool $hasSequences;

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

        $chmod = false;
        if ($this->config['database'] !== ':memory:' && $this->config['mode'] !== 'memory') {
            $chmod = !file_exists($this->config['database']);
        }

        $params = array_intersect_key($this->config, ['cache' => true, 'mode' => true]);

        if ($params !== []) {
            $dsn = 'sqlite:file:'.$this->config['database'].'?'.http_build_query($params);
        } else {
            $dsn = 'sqlite:'.$this->config['database'];
        }

        $options = [
            Sqlite::ATTR_ERRMODE => Sqlite::ERRMODE_EXCEPTION,
        ];

        if ($this->config['persist']) {
            $options[Sqlite::ATTR_PERSISTENT] = true;
        }

        $options = array_replace($options, $this->config['flags']);

        try {
            $this->pdo = new Sqlite($dsn, null, null, $options);
        } catch (PDOException $e) {
            throw DbException::forConnectionFailed($e->getMessage());
        }

        if ($chmod) {
            @chmod($this->config['database'], $this->config['mask']);
        }
    }

    /**
     * Disable foreign key checks.
     *
     * @return Connection The Connection.
     */
    public function disableForeignKeys(): static
    {
        $this->rawQuery('PRAGMA foreign_keys = OFF');

        return $this;
    }

    /**
     * Enable foreign key checks.
     *
     * @return Connection The Connection.
     */
    public function enableForeignKeys(): static
    {
        $this->rawQuery('PRAGMA foreign_keys = ON');

        return $this;
    }

    /**
     * Get the connection charset.
     *
     * @return string The connection charset.
     */
    public function getCharset(): string
    {
        return $this->rawQuery('PRAGMA ENCODING')->fetchColumn();
    }

    /**
     * Determine whether the database contains any sequences.
     *
     * @return bool TRUE if the database contains any sequences, otherwise FALSE.
     */
    public function hasSequences(): bool
    {
        return $this->hasSequences ??= $this->select('1')
            ->from('sqlite_master')
            ->where([
                'name' => 'sqlite_sequence',
            ])
            ->execute()
            ->count() > 0;
    }

    /**
     * Set the connection charset.
     *
     * @param string $charset The charset.
     * @return Connection The Connection.
     */
    public function setCharset(string $charset): static
    {
        $this->rawQuery('PRAGMA ENCODING = '.$this->quote($charset));

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
            DbFeature::Replace,
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
        if ($this->hasSequences()) {
            $this->delete()
                ->from('sqlite_sequence')
                ->where([
                    'name' => $tableName,
                ])
                ->execute();
        }

        $this->delete()
            ->from($tableName)
            ->execute();

        return $this;
    }

    /**
     * Generate a result set from a raw result.
     *
     * @param PDOStatement $result The raw result.
     * @return SqliteResultSet The result set.
     */
    protected function result(PDOStatement $result): SqliteResultSet
    {
        if (preg_match('/^(?:DELETE|UPDATE|INSERT)/i', $result->queryString)) {
            $this->affectedRows = $this->rawQuery('SELECT CHANGES()')->fetchColumn();
        } else {
            $this->affectedRows = $result->rowCount();
        }

        $class = static::resultSetClass();

        return $this->container->build($class, ['result' => $result]);
    }

    /**
     * Get the ResultSet class.
     *
     * @return string The ResultSet class.
     */
    protected static function resultSetClass(): string
    {
        return SqliteResultSet::class;
    }
}
