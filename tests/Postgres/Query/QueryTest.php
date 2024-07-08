<?php
declare(strict_types=1);

namespace Tests\Postgres\Query;

use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use PHPUnit\Framework\TestCase;
use Tests\Postgres\PostgresConnectionTrait;

final class QueryTest extends TestCase
{
    use DeleteTestTrait;
    use ExecuteTestTrait;
    use GetTestTrait;
    use InsertTestTrait;
    use PostgresConnectionTrait;
    use ReplaceTestTrait;
    use TransactionTestTrait;
    use UpdateTestTrait;

    protected Connection $db;

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
    }

    protected function tearDown(): void
    {
        $this->db->query('TRUNCATE test RESTART IDENTITY');
    }
}
