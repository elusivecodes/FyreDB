<?php
declare(strict_types=1);

namespace Tests\Query;

use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use PHPUnit\Framework\TestCase;
use Tests\ConnectionTrait;

final class QueryTest extends TestCase
{

    protected Connection $db;

    use ConnectionTrait;
    use DeleteTestTrait;
    use ExecuteTestTrait;
    use InsertTestTrait;
    use ReplaceTestTrait;
    use TransactionTestTrait;
    use UpdateTestTrait;

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
    }

    protected function tearDown(): void
    {
        $this->db->query('TRUNCATE test');
    }

}
