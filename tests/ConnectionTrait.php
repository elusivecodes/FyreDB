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
        ConnectionManager::clear();

        ConnectionManager::setConfig('default', [
            'className' => MySQLConnection::class,
            'host' => getenv('DB_HOST'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'database' => getenv('DB_NAME'),
            'port' => getenv('DB_PORT'),
            'collation' => 'utf8mb4_unicode_ci',
            'charset' => 'utf8mb4',
            'compress' => true,
            'persist' => true
        ]);

        ConnectionManager::setConfig('invalid', [
            'className' => MySQLConnection::class,
            'username' => 'root',
            'database' => 'test'
        ]);

        $connection = ConnectionManager::use();

        $connection->query('DROP TABLE IF EXISTS `test`');

        $connection->query(<<<EOT
            CREATE TABLE `test` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                PRIMARY KEY (`id`)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        EOT);
    }

    public static function tearDownAfterClass(): void
    {
        $connection = ConnectionManager::use();
        $connection->query('DROP TABLE IF EXISTS `test`');
    }

}
