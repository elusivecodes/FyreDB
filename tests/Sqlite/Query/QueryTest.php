<?php
declare(strict_types=1);

namespace Tests\Sqlite\Query;

use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use PHPUnit\Framework\TestCase;
use Tests\Sqlite\SqliteConnectionTrait;

final class QueryTest extends TestCase
{
    use DeleteTestTrait;
    use ExecuteTestTrait;
    use GetTestTrait;
    use InsertTestTrait;
    use ReplaceTestTrait;
    use SqliteConnectionTrait;
    use TransactionTestTrait;
    use UpdateTestTrait;

    protected Connection $db;

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
    }

    protected function tearDown(): void
    {
        $this->db->query('DELETE FROM "test"');
    }
}
