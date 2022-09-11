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
        ExceptTest,
        GetTest,
        HavingTest,
        InsertTest,
        InsertBatchTest,
        InsertFromTest,
        IntersectTest,
        JoinTest,
        ReplaceTest,
        ReplaceBatchTest,
        SelectTest,
        UnionTest,
        UnionAllTest,
        UpdateTest,
        UpdateBatchTest,
        WhereTest,
        WithTest;

    public function testGetConnection()
    {
        $this->assertSame(
            $this->db,
            $this->db->builder()
                ->getConnection()
        );
    }
    
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
