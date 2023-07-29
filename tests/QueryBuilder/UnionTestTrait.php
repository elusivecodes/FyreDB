<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait UnionTestTrait
{

    public function testUnion()
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->union('(SELECT * FROM test2)')
                ->sql()
        );
    }

    public function testUnionQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->union($query)
                ->sql()
        );
    }

    public function testUnionClosure()
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->union(function(QueryBuilder $builder) {
                    return $builder->table('test2')
                        ->select();
                })
                ->sql()
        );
    }

    public function testUnionLiteral()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->union(function(QueryBuilder $builder) {
                    return $builder->literal('(SELECT * FROM test2)');
                })
                ->sql()
        );
    }

    public function testUnionMerge()
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test2) UNION DISTINCT (SELECT * FROM test3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->union('(SELECT * FROM test2)')
                ->union('(SELECT * FROM test3)')
                ->sql()
        );
    }

    public function testUnionOverwrite()
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION DISTINCT (SELECT * FROM test3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->union('(SELECT * FROM test2)')
                ->union('(SELECT * FROM test3)', true)
                ->sql()
        );
    }

}
