<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\DB\ConnectionManager,
    Fyre\DB\Exceptions\DBException,
    Fyre\DB\Handlers\MySQL\MySQLConnection,
    PHPUnit\Framework\TestCase;

use function
    getenv;

final class ConnectionManagerTest extends TestCase
{

    use
        ConnectionTrait;

    public function getConfig(): void
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
                    'persist' => true
                ],
                'invalid' => [
                    'className' => MySQLConnection::class,
                    'username' => 'root',
                    'database' => 'test'
                ]
            ],
            ConnectionManager::getConfig()
        );
    }

    public function getConfigKey(): void
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
                'persist' => true
            ],
            ConnectionManager::getConfig('invalid')
        );
    }
    
    public function getKey(): void
    {
        $handler = ConnectionManager::use();

        $this->assertSame(
            'default',
            ConnectionManager::getKey($handler)
        );
    }

    public function getKeyInvalid(): void
    {
        $handler = ConnectionManager::load([
            'className' => 'Invalid'
        ]);

        $this->assertSame(
            null,
            ConnectionManager::getKey($handler)
        );
    }

    public function testLoadInvalidHandler(): void
    {
        $this->expectException(DBException::class);

        ConnectionManager::load([
            'className' => 'Invalid'
        ]);
    }

    public function testSetConfig(): void
    {
        ConnectionManager::setConfig([
            'test' => [
                'className' => MySQLConnection::class
            ]
        ]);

        $this->assertSame(
            [
                'className' => MySQLConnection::class
            ],
            ConnectionManager::getConfig('test')
        );

        ConnectionManager::unload('test');
    }

    public function testSetConfigExists(): void
    {
        $this->expectException(DBException::class);

        ConnectionManager::setConfig('default', [
            'className' => MySQLConnection::class
        ]);
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

}
