<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\Connection,
    Fyre\DB\ConnectionManager,
    PHPUnit\Framework\TestCase,
    Tests\ConnectionTrait;

final class QueryBuilderTest extends TestCase
{

    protected Connection $db;

    use
        ConnectionTrait,
        DeleteTest,
        HavingTest,
        InsertTest,
        JoinTest,
        ReplaceTest,
        SelectTest,
        UnionTest,
        UpdateTest,
        WhereTest,
        WithTest;

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
    }

}
