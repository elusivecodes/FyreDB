<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\DB\ConnectionManager,
    Fyre\DB\Handlers\MySQL\MySQLConnection;

use function
    getenv;

trait ConnectionTrait
{

    public static function setUpBeforeClass(): void
    {
        ConnectionManager::setConfig('default', [
            'className' => MySQLConnection::class,
            'host' => getenv('DB_HOST'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'database' => getenv('DB_NAME'),
            'port' => getenv('DB_PORT'),
            'collation' => 'utf8mb4_unicode_ci',
            'charset' => 'utf8mb4'
        ]);

        ConnectionManager::setConfig('invalid', [
            'className' => MySQLConnection::class,
            'username' => 'root',
            'database' => 'test'
        ]);
    }

}
