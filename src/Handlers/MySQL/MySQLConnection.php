<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\MySQL;

use Fyre\DB\Connection;
use Fyre\DB\Exceptions\DbException;
use PDO;
use PDOException;

use function array_replace;
use function class_exists;

/**
 * MySQLConnection
 */
class MySQLConnection extends Connection
{

    /**
     * Connect to the database.
     * @throws RuntimeException if PDO extension is not installed.
     * @throws DbException if the connection failed.
     */
    public function connect(): void
    {
        if ($this->pdo) {
            return;
        }

        if (!class_exists('PDO')) {
            throw new RuntimeException('MySQL handler requires PDO extension');
        }

        $dsn = 'mysql:host='.$this->config['host'].';dbname='.$this->config['database'];

        if ($this->config['port']) {
            $dsn .= ';port='.$this->config['port'];
        }

        if ($this->config['charset']) {
            $dsn .= ';charset='.$this->config['charset'];
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        if ($this->config['timeout']) {
            $options[PDO::ATTR_TIMEOUT] = $this->config['timeout'];
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

        $options = array_replace($options, $this->config['flags']);

        try {
            $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
        } catch (PDOException $e) {
            throw DbException::forConnectionFailed($e->getMessage());
        }
    }

    /**
     * Get the ResultSet class.
     * @return string The ResultSet class.
     */
    protected static function resultSetClass(): string
    {
        return MySQLResultSet::class;
    }

}
