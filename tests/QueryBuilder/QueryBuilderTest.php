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
        GetTest,
        HavingTest,
        InsertTest,
        JoinTest,
        ReplaceTest,
        SelectTest,
        UnionTest,
        UpdateTest,
        WhereTest,
        WithTest;

    public function testToString()
    {
        $this->assertSame(
            'SELECT * FROM test',
            (string) $this->db->builder()
                ->table('test')
                ->select()
        );
    }

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
    }

}
