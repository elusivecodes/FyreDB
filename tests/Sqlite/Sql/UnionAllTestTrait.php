<?php
declare(strict_types=1);

namespace Tests\Sqlite\Sql;

use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait UnionAllTestTrait
{
    public function testUnionAll(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") UNION ALL (SELECT * FROM test2)',
            $this->db->select()
                ->from('test')
                ->unionAll('(SELECT * FROM test2)')
                ->sql()
        );
    }

    public function testUnionAllClosure(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") UNION ALL (SELECT * FROM "test2")',
            $this->db->select()
                ->from('test')
                ->unionAll(function(Connection $db): SelectQuery {
                    return $db->select()
                        ->from('test2');
                })
                ->sql()
        );
    }

    public function testUnionAllLiteral(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") UNION ALL (SELECT * FROM test2)',
            $this->db->select()
                ->from('test')
                ->unionAll(function(Connection $db): QueryLiteral {
                    return $db->literal('(SELECT * FROM test2)');
                })
                ->sql()
        );
    }

    public function testUnionAllMerge(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") UNION ALL (SELECT * FROM test2) UNION ALL (SELECT * FROM test3)',
            $this->db->select()
                ->from('test')
                ->unionAll('(SELECT * FROM test2)')
                ->unionAll('(SELECT * FROM test3)')
                ->sql()
        );
    }

    public function testUnionAllOverwrite(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") UNION ALL (SELECT * FROM test3)',
            $this->db->select()
                ->from('test')
                ->unionAll('(SELECT * FROM test2)')
                ->unionAll('(SELECT * FROM test3)', true)
                ->sql()
        );
    }

    public function testUnionAllSelectQuery(): void
    {
        $query = $this->db->select()
            ->from('test2');

        $this->assertSame(
            '(SELECT * FROM "test") UNION ALL (SELECT * FROM "test2")',
            $this->db->select()
                ->from('test')
                ->unionAll($query)
                ->sql()
        );
    }
}
