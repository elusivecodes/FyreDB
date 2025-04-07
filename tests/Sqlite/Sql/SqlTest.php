<?php
declare(strict_types=1);

namespace Tests\Sqlite\Sql;

use PHPUnit\Framework\TestCase;
use Tests\Sqlite\SqliteConnectionTrait;

use function file_get_contents;

final class SqlTest extends TestCase
{
    use DeleteTestTrait;
    use ExceptTestTrait;
    use HavingTestTrait;
    use InsertFromTestTrait;
    use InsertTestTrait;
    use IntersectTestTrait;
    use JoinTestTrait;
    use ReplaceTestTrait;
    use SelectTestTrait;
    use SqliteConnectionTrait;
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

    public function testQueryLogging(): void
    {
        $this->assertSame(
            $this->db,
            $this->db->enableQueryLogging()
        );

        $this->db->select([
            'test.id',
            'test.name',
        ])
            ->from('test')
            ->where([
                'test.name' => 'test',
            ])
            ->execute();

        $this->assertSame(
            $this->db,
            $this->db->disableQueryLogging()
        );

        $this->assertStringContainsString(
            'SELECT test.id, test.name FROM test WHERE test.name = \'test\'',
            file_get_contents('log/queries-cli.log')
        );
    }

    public function testRawQueryLogging(): void
    {
        $this->db->enableQueryLogging();

        $this->db->rawQuery('SELECT 1');

        $this->db->disableQueryLogging();

        $this->assertStringContainsString(
            'SELECT 1',
            file_get_contents('log/queries-cli.log')
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
