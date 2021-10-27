<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\DB\Connection,
    Fyre\DB\ConnectionManager,
    Fyre\DB\Exceptions\DBException,
    PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase
{

    protected Connection $db;

    use
        ConnectionTrait;

    public function testConnectionCharset(): void
    {
        $this->assertEquals(
            'utf8mb4',
            $this->db->getCharset()
        );
    }

    public function testConnectionCollation(): void
    {
        $this->assertEquals(
            'utf8mb4_general_ci',
            $this->db->getCollation()
        );
    }

    public function testFailedConnection(): void
    {
        $this->expectException(DBException::class);

        ConnectionManager::use('invalid');
    }

    public function testFailedQuery(): void
    {
        $this->expectException(DBException::class);

        $this->db->query('INVALID');
    }

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
    }

}
