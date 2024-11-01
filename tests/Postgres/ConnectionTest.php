<?php
declare(strict_types=1);

namespace Tests\Postgres;

use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Handlers\Postgres\PostgresConnection;
use PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase
{
    use PostgresConnectionTrait;

    public function testCharset(): void
    {
        $this->assertSame(
            'UTF8',
            $this->db->getCharset()
        );
    }

    public function testFailedConnection(): void
    {
        $this->expectException(DbException::class);

        $this->connection->setConfig('invalid', [
            'className' => PostgresConnection::class,
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
            '/^\d+\.\d+.*/',
            $this->db->version()
        );
    }
}
