<?php
declare(strict_types=1);

namespace Tests\Mysql\Sql;

use PHPUnit\Framework\TestCase;
use Tests\Mysql\MysqlConnectionTrait;

final class SqlTest extends TestCase
{
    use DeleteTestTrait;
    use ExceptTestTrait;
    use HavingTestTrait;
    use InsertFromTestTrait;
    use InsertTestTrait;
    use IntersectTestTrait;
    use JoinTestTrait;
    use MysqlConnectionTrait;
    use ReplaceTestTrait;
    use SelectTestTrait;
    use UnionAllTestTrait;
    use UnionTestTrait;
    use UpdateBatchTestTrait;
    use UpdateTestTrait;
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
}
