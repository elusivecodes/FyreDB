<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\Mysql;

use Fyre\DB\Connection;
use Fyre\DB\DbFeature;
use Fyre\DB\Exceptions\DbException;
use Pdo\Mysql;
use PDOException;
use RuntimeException;

use function array_replace;
use function class_exists;

/**
 * MysqlConnection
 */
class MysqlConnection extends Connection
{
    protected static array $defaults = [
        'host' => '127.0.0.1',
        'username' => '',
        'password' => '',
        'database' => '',
        'port' => '3306',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
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
            throw new RuntimeException('Mysql handler requires PDO extension');
        }

        $dsn = 'mysql:host='.$this->config['host'].';dbname='.$this->config['database'];

        if ($this->config['port']) {
            $dsn .= ';port='.$this->config['port'];
        }

        if ($this->config['charset']) {
            $dsn .= ';charset='.$this->config['charset'];
        }

        $options = [
            Mysql::ATTR_ERRMODE => Mysql::ERRMODE_EXCEPTION,
        ];

        if ($this->config['timeout']) {
            $options[Mysql::ATTR_TIMEOUT] = $this->config['timeout'];
        }

        if ($this->config['collation']) {
            $options[Mysql::MYSQL_ATTR_INIT_COMMAND] = 'SET collation_connection = '.$this->config['collation'];
        }

        if ($this->config['compress']) {
            $options[Mysql::MYSQL_ATTR_COMPRESS] = true;
        }

        if ($this->config['persist']) {
            $options[Mysql::ATTR_PERSISTENT] = true;
        }

        if ($this->config['ssl']) {
            if ($this->config['ssl']['key']) {
                $options[Mysql::MYSQL_ATTR_SSL_KEY] = $this->config['ssl']['key'];
            }
            if ($this->config['ssl']['cert']) {
                $options[Mysql::MYSQL_ATTR_SSL_CERT] = $this->config['ssl']['cert'];
            }
            if ($this->config['ssl']['ca']) {
                $options[Mysql::MYSQL_ATTR_SSL_CA] = $this->config['ssl']['ca'];
            }
            if ($this->config['ssl']['capath']) {
                $options[Mysql::MYSQL_ATTR_SSL_CAPATH] = $this->config['ssl']['capath'];
            }
            if ($this->config['ssl']['cipher']) {
                $options[Mysql::MYSQL_ATTR_SSL_CIPHER] = $this->config['ssl']['cipher'];
            }
        }

        $options = array_replace($options, $this->config['flags']);

        try {
            $this->pdo = new Mysql($dsn, $this->config['username'], $this->config['password'], $options);
        } catch (PDOException $e) {
            throw DbException::forConnectionFailed($e->getMessage());
        }
    }

    /**
     * Disable foreign key checks.
     *
     * @return Connection The Connection.
     */
    public function disableForeignKeys(): static
    {
        $this->rawQuery('SET FOREIGN_KEY_CHECKS = 0');

        return $this;
    }

    /**
     * Enable foreign key checks.
     *
     * @return Connection The Connection.
     */
    public function enableForeignKeys(): static
    {
        $this->rawQuery('SET FOREIGN_KEY_CHECKS = 1');

        return $this;
    }

    /**
     * Get the connection charset.
     *
     * @return string The connection charset.
     */
    public function getCharset(): string
    {
        return $this->rawQuery('SELECT @@character_set_client')->fetchColumn();
    }

    /**
     * Get the connection collation.
     *
     * @return string The connection collation.
     */
    public function getCollation(): string
    {
        return $this->rawQuery('SELECT @@collation_connection')->fetchColumn();
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
            DbFeature::DeleteAlias,
            DbFeature::DeleteJoin,
            DbFeature::DeleteMultipleTables,
            DbFeature::Replace,
            DbFeature::UpdateJoin,
            DbFeature::UpdateMultipleTables => true,
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
        $this->rawQuery('TRUNCATE TABLE '.$tableName);

        return $this;
    }

    /**
     * Get the ResultSet class.
     *
     * @return string The ResultSet class.
     */
    protected static function resultSetClass(): string
    {
        return MysqlResultSet::class;
    }
}
