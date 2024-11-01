<?php
declare(strict_types=1);

namespace Tests\Mysql;

use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Handlers\Mysql\MysqlConnection;
use PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase
{
    use MysqlConnectionTrait;

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

        $this->connection->setConfig('invalid', [
            'className' => MysqlConnection::class,
            'username' => 'root',
            'database' => 'test',
        ]);

        $this->connection->use('invalid');
    }

    public function testFailedQuery(): void
    {
        $this->expectException(DbException::class);

        $this->db->query('INVALID');
    }

    public function testVersion(): void
    {
        $this->assertMatchesRegularExpression(
            '/^\d+\.\d+\.\d+.*/',
            $this->db->version()
        );
    }
}
