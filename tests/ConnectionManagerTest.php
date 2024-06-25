<?php
declare(strict_types=1);

namespace Tests;

use Fyre\DB\ConnectionManager;
use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Handlers\MySQL\MySQLConnection;
use PHPUnit\Framework\TestCase;

use function getenv;

final class ConnectionManagerTest extends TestCase
{
    use ConnectionTrait;

    public function testGetConfig(): void
    {
        $this->assertSame(
            [
                'default' => [
                    'className' => MySQLConnection::class,
                    'host' => getenv('DB_HOST'),
                    'username' => getenv('DB_USERNAME'),
                    'password' => getenv('DB_PASSWORD'),
                    'database' => getenv('DB_NAME'),
                    'port' => getenv('DB_PORT'),
                    'collation' => 'utf8mb4_unicode_ci',
                    'charset' => 'utf8mb4',
                    'compress' => true,
                    'persist' => true,
                ],
                'other' => [
                    'className' => MySQLConnection::class,
                    'host' => getenv('DB_HOST'),
                    'username' => getenv('DB_USERNAME'),
                    'password' => getenv('DB_PASSWORD'),
                    'database' => getenv('DB_NAME'),
                    'port' => getenv('DB_PORT'),
                    'collation' => 'utf8mb4_unicode_ci',
                    'charset' => 'utf8mb4',
                    'compress' => true,
                    'persist' => true,
                ],
            ],
            ConnectionManager::getConfig()
        );
    }

    public function testGetConfigKey(): void
    {
        $this->assertSame(
            [
                'className' => MySQLConnection::class,
                'host' => getenv('DB_HOST'),
                'username' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
                'database' => getenv('DB_NAME'),
                'port' => getenv('DB_PORT'),
                'collation' => 'utf8mb4_unicode_ci',
                'charset' => 'utf8mb4',
                'compress' => true,
                'persist' => true,
            ],
            ConnectionManager::getConfig('default')
        );
    }

    public function testGetKey(): void
    {
        $handler = ConnectionManager::use();

        $this->assertSame(
            'default',
            ConnectionManager::getKey($handler)
        );
    }

    public function testGetKeyInvalid(): void
    {
        $handler = ConnectionManager::load([
            'className' => MySQLConnection::class,
            'host' => getenv('DB_HOST'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'database' => getenv('DB_NAME'),
            'port' => getenv('DB_PORT'),
        ]);

        $this->assertSame(
            null,
            ConnectionManager::getKey($handler)
        );
    }

    public function testIsLoaded(): void
    {
        ConnectionManager::use();

        $this->assertTrue(
            ConnectionManager::isLoaded()
        );
    }

    public function testIsLoadedInvalid(): void
    {
        $this->assertFalse(
            ConnectionManager::isLoaded('test')
        );
    }

    public function testIsLoadedKey(): void
    {
        ConnectionManager::use('other');

        $this->assertTrue(
            ConnectionManager::isLoaded('other')
        );
    }

    public function testLoadInvalidHandler(): void
    {
        $this->expectException(DbException::class);

        ConnectionManager::load([
            'className' => 'Invalid',
        ]);
    }

    public function testSetConfig(): void
    {
        ConnectionManager::setConfig([
            'test' => [
                'className' => MySQLConnection::class,
            ],
        ]);

        $this->assertSame(
            [
                'className' => MySQLConnection::class,
            ],
            ConnectionManager::getConfig('test')
        );

        ConnectionManager::unload('test');
    }

    public function testSetConfigExists(): void
    {
        $this->expectException(DbException::class);

        ConnectionManager::setConfig('default', [
            'className' => MySQLConnection::class,
        ]);
    }

    public function testUnload(): void
    {
        ConnectionManager::use();

        $this->assertTrue(
            ConnectionManager::unload()
        );

        $this->assertFalse(
            ConnectionManager::isLoaded()
        );
        $this->assertFalse(
            ConnectionManager::hasConfig()
        );
    }

    public function testUnloadInvalid(): void
    {
        $this->assertFalse(
            ConnectionManager::unload('test')
        );
    }

    public function testUnloadKey(): void
    {
        ConnectionManager::use('other');

        $this->assertTrue(
            ConnectionManager::unload('other')
        );

        $this->assertFalse(
            ConnectionManager::isLoaded('other')
        );
        $this->assertFalse(
            ConnectionManager::hasConfig('other')
        );
    }

    public function testUse(): void
    {
        $handler1 = ConnectionManager::use();
        $handler2 = ConnectionManager::use();

        $this->assertSame($handler1, $handler2);

        $this->assertInstanceOf(
            MySQLConnection::class,
            $handler1
        );
    }

    public function setUp(): void
    {
        ConnectionManager::clear();

        ConnectionManager::setConfig([
            'default' => [
                'className' => MySQLConnection::class,
                'host' => getenv('DB_HOST'),
                'username' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
                'database' => getenv('DB_NAME'),
                'port' => getenv('DB_PORT'),
                'collation' => 'utf8mb4_unicode_ci',
                'charset' => 'utf8mb4',
                'compress' => true,
                'persist' => true,
            ],
            'other' => [
                'className' => MySQLConnection::class,
                'host' => getenv('DB_HOST'),
                'username' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
                'database' => getenv('DB_NAME'),
                'port' => getenv('DB_PORT'),
                'collation' => 'utf8mb4_unicode_ci',
                'charset' => 'utf8mb4',
                'compress' => true,
                'persist' => true,
            ],
        ]);

        $connection = ConnectionManager::use();

        $connection->query('DROP TABLE IF EXISTS `test`');

        $connection->query(<<<'EOT'
            CREATE TABLE `test` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                PRIMARY KEY (`id`)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        EOT);
    }

    public function tearDown(): void
    {
        if (!ConnectionManager::hasConfig()) {
            return;
        }

        $connection = ConnectionManager::use();
        $connection->query('DROP TABLE IF EXISTS `test`');
    }
}
