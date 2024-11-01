<?php
declare(strict_types=1);

namespace Tests\Sqlite;

use Fyre\DB\Exceptions\DbException;
use PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase
{
    use SqliteConnectionTrait;

    public function testCharset(): void
    {
        $this->assertSame(
            'UTF-8',
            $this->db->getCharset()
        );
    }

    public function testFailedQuery(): void
    {
        $this->expectException(DbException::class);

        $this->db->query('INVALID');
    }

    public function testVersion(): void
    {
        $this->assertMatchesRegularExpression(
            '/^\d+\.\d+.*/',
            $this->db->version()
        );
    }
}
