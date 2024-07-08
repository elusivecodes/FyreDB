<?php
declare(strict_types=1);

namespace Tests\Mysql\Sql;

use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait UnionTestTrait
{
    public function testUnion(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test2)',
            $this->db->select()
                ->from('test')
                ->union('(SELECT * FROM test2)')
                ->sql()
        );
    }

    public function testUnionClosure(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test2)',
            $this->db->select()
                ->from('test')
                ->union(function(Connection $db): SelectQuery {
                    return $db->select()
                        ->from('test2');
                })
                ->sql()
        );
    }

    public function testUnionLiteral(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test2)',
            $this->db->select()
                ->from('test')
                ->union(function(Connection $db): QueryLiteral {
                    return $db->literal('(SELECT * FROM test2)');
                })
                ->sql()
        );
    }

    public function testUnionMerge(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test2) UNION DISTINCT (SELECT * FROM test3)',
            $this->db->select()
                ->from('test')
                ->union('(SELECT * FROM test2)')
                ->union('(SELECT * FROM test3)')
                ->sql()
        );
    }

    public function testUnionOverwrite(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test3)',
            $this->db->select()
                ->from('test')
                ->union('(SELECT * FROM test2)')
                ->union('(SELECT * FROM test3)', true)
                ->sql()
        );
    }

    public function testUnionSelectQuery(): void
    {
        $query = $this->db->select()
            ->from('test2');

        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test2)',
            $this->db->select()
                ->from('test')
                ->union($query)
                ->sql()
        );
    }
}
