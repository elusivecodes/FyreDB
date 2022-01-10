<?php
declare(strict_types=1);

namespace Tests\Query;

use
    Fyre\DB\Connection,
    Fyre\DB\ConnectionManager,
    PHPUnit\Framework\TestCase,
    Tests\ConnectionTrait;

final class QueryTest extends TestCase
{

    protected Connection $db;

    use
        ConnectionTrait,
        DeleteTest,
        ExecuteTest,
        InsertTest,
        ReplaceTest,
        TransactionTest,
        UpdateTest;

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
    }

    protected function tearDown(): void
    {
        $this->db->query('TRUNCATE test');
    }

}
