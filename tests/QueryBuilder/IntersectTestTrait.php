<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait IntersectTestTrait
{

    public function testIntersect(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) INTERSECT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect('(SELECT * FROM test2)')
                ->sql()
        );
    }

    public function testIntersectQueryBuilder(): void
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            '(SELECT * FROM test) INTERSECT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect($query)
                ->sql()
        );
    }

    public function testIntersectClosure(): void
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            '(SELECT * FROM test) INTERSECT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect(function(QueryBuilder $builder) {
                    return $builder->table('test2')
                        ->select();
                })
                ->sql()
        );
    }

    public function testIntersectLiteral(): void
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            '(SELECT * FROM test) INTERSECT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect(function(QueryBuilder $builder) {
                    return $builder->literal('(SELECT * FROM test2)');
                })
                ->sql()
        );
    }

    public function testIntersectMerge(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) INTERSECT (SELECT * FROM test2) INTERSECT (SELECT * FROM test3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect('(SELECT * FROM test2)')
                ->intersect('(SELECT * FROM test3)')
                ->sql()
        );
    }

    public function testIntersectOverwrite(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) INTERSECT (SELECT * FROM test3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect('(SELECT * FROM test2)')
                ->intersect('(SELECT * FROM test3)', true)
                ->sql()
        );
    }

}
