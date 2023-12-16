<?php
declare(strict_types=1);

namespace Tests\Sql;

use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use PHPUnit\Framework\TestCase;
use Tests\ConnectionTrait;

final class SqlTest extends TestCase
{

    protected Connection $db;

    use ConnectionTrait;
    use DeleteTestTrait;
    use ExceptTestTrait;
    use HavingTestTrait;
    use InsertTestTrait;
    use InsertFromTestTrait;
    use IntersectTestTrait;
    use JoinTestTrait;
    use ReplaceTestTrait;
    use SelectTestTrait;
    use UnionTestTrait;
    use UnionAllTestTrait;
    use UpdateTestTrait;
    use UpdateBatchTestTrait;
    use WhereTestTrait;
    use WithTestTrait;

    public function testGetConnection(): void
    {
        $this->assertSame(
            $this->db,
            $this->db->select()
                ->getConnection()
        );
    }
    
    public function testToString(): void
    {
        $this->assertSame(
            'SELECT * FROM test',
            (string) $this->db->select()
                ->from('test')
        );
    }

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
    }

}
