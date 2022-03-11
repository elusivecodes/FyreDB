<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\DB\ConnectionManager,
    Fyre\DB\Exceptions\DBException,
    Fyre\DB\Handlers\MySQL\MySQLConnection,
    PHPUnit\Framework\TestCase;

final class ConnectionManagerTest extends TestCase
{

    use
        ConnectionTrait;

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
