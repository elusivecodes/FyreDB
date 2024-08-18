<?php
declare(strict_types=1);

namespace Tests;

use Fyre\DB\ConnectionManager;
use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Handlers\Mysql\MysqlConnection;
use PHPUnit\Framework\TestCase;
use Tests\Mysql\MysqlConnectionTrait;

use function getenv;

final class ConnectionManagerTest extends TestCase
{
    use MysqlConnectionTrait;

    public function testGetConfig(): void
    {
        $this->assertSame(
            [
                'default' => [
                    'className' => MysqlConnection::class,
                    'host' => getenv('MYSQL_HOST'),
                    'username' => getenv('MYSQL_USERNAME'),
                    'password' => getenv('MYSQL_PASSWORD'),
                    'database' => getenv('MYSQL_DATABASE'),
                    'port' => getenv('MYSQL_PORT'),
                    'collation' => 'utf8mb4_unicode_ci',
                    'charset' => 'utf8mb4',
                    'compress' => true,
                    'persist' => true,
                ],
                'other' => [
                    'className' => MysqlConnection::class,
                    'host' => getenv('MYSQL_HOST'),
                    'username' => getenv('MYSQL_USERNAME'),
                    'password' => getenv('MYSQL_PASSWORD'),
                    'database' => getenv('MYSQL_DATABASE'),
                    'port' => getenv('MYSQL_PORT'),
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
                'className' => MysqlConnection::class,
                'host' => getenv('MYSQL_HOST'),
                'username' => getenv('MYSQL_USERNAME'),
                'password' => getenv('MYSQL_PASSWORD'),
                'database' => getenv('MYSQL_DATABASE'),
                'port' => getenv('MYSQL_PORT'),
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
            'className' => MysqlConnection::class,
            'host' => getenv('MYSQL_HOST'),
            'username' => getenv('MYSQL_USERNAME'),
            'password' => getenv('MYSQL_PASSWORD'),
            'database' => getenv('MYSQL_DATABASE'),
            'port' => getenv('MYSQL_PORT'),
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
                'className' => MysqlConnection::class,
            ],
        ]);

        $this->assertSame(
            [
                'className' => MysqlConnection::class,
            ],
            ConnectionManager::getConfig('test')
        );

        ConnectionManager::unload('test');
    }

    public function testSetConfigExists(): void
    {
        $this->expectException(DbException::class);

        ConnectionManager::setConfig('default', [
            'className' => MysqlConnection::class,
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
            MysqlConnection::class,
            $handler1
        );
    }

    protected function setUp(): void
    {
        ConnectionManager::clear();

        ConnectionManager::setConfig([
            'default' => [
                'className' => MysqlConnection::class,
                'host' => getenv('MYSQL_HOST'),
                'username' => getenv('MYSQL_USERNAME'),
                'password' => getenv('MYSQL_PASSWORD'),
                'database' => getenv('MYSQL_DATABASE'),
                'port' => getenv('MYSQL_PORT'),
                'collation' => 'utf8mb4_unicode_ci',
                'charset' => 'utf8mb4',
                'compress' => true,
                'persist' => true,
            ],
            'other' => [
                'className' => MysqlConnection::class,
                'host' => getenv('MYSQL_HOST'),
                'username' => getenv('MYSQL_USERNAME'),
                'password' => getenv('MYSQL_PASSWORD'),
                'database' => getenv('MYSQL_DATABASE'),
                'port' => getenv('MYSQL_PORT'),
                'collation' => 'utf8mb4_unicode_ci',
                'charset' => 'utf8mb4',
                'compress' => true,
                'persist' => true,
            ],
        ]);

        $connection = ConnectionManager::use();

        $connection->query('DROP TABLE IF EXISTS test');

        $connection->query(<<<'EOT'
            CREATE TABLE test (
                id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                PRIMARY KEY (id)
            ) COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB
        EOT);
    }

    protected function tearDown(): void
    {
        if (!ConnectionManager::hasConfig()) {
            return;
        }

        $connection = ConnectionManager::use();
        $connection->query('DROP TABLE IF EXISTS test');
    }
}
