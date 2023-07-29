<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use PHPUnit\Framework\TestCase;
use Tests\ConnectionTrait;

final class QueryBuilderTest extends TestCase
{

    protected Connection $db;

    use ConnectionTrait;
    use DeleteTestTrait;
    use ExceptTestTrait;
    use GetTestTrait;
    use HavingTestTrait;
    use InsertTestTrait;
    use InsertBatchTestTrait;
    use InsertFromTestTrait;
    use IntersectTestTrait;
    use JoinTestTrait;
    use ReplaceTestTrait;
    use ReplaceBatchTestTrait;
    use SelectTestTrait;
    use UnionTestTrait;
    use UnionAllTestTrait;
    use UpdateTestTrait;
    use UpdateBatchTestTrait;
    use WhereTestTrait;
    use WithTestTrait;

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
