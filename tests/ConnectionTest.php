<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\DB\Connection,
    Fyre\DB\ConnectionManager,
    Fyre\DB\Exceptions\DbException,
    PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase
{

    protected Connection $db;

    use
        ConnectionTrait;

    public function testCharset(): void
    {
        $this->assertSame(
            'utf8mb4',
            $this->db->getCharset()
        );
    }

    public function testCollation(): void
    {
        $this->assertSame(
            'utf8mb4_unicode_ci',
            $this->db->getCollation()
        );
    }

    public function testFailedConnection(): void
    {
        $this->expectException(DbException::class);

        ConnectionManager::use('invalid');
    }

    public function testFailedQuery(): void
    {
        $this->expectException(DbException::class);

        $this->db->query('INVALID');
    }

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
    }

}
