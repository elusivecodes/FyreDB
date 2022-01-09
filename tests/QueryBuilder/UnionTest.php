<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait UnionTest
{

    public function testExcept()
    {
        $this->assertSame(
            'SELECT * FROM test EXCEPT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except('SELECT * FROM test2')
                ->sql()
        );
    }

    public function testExceptQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'SELECT * FROM test EXCEPT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except($query)
                ->sql()
        );
    }

    public function testExceptClosure()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'SELECT * FROM test EXCEPT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except(function(QueryBuilder $builder) {
                    return $builder->table('test2')
                        ->select();
                })
                ->sql()
        );
    }

    public function testExceptLiteral()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'SELECT * FROM test EXCEPT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except(function(QueryBuilder $builder) {
                    return $builder->literal('SELECT * FROM test2');
                })
                ->sql()
        );
    }

    public function testIntersect()
    {
        $this->assertSame(
            'SELECT * FROM test INTERSECT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect('SELECT * FROM test2')
                ->sql()
        );
    }

    public function testIntersectQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'SELECT * FROM test INTERSECT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect($query)
                ->sql()
        );
    }

    public function testIntersectClosure()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'SELECT * FROM test INTERSECT SELECT * FROM test2',
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

    public function testIntersectLiteral()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'SELECT * FROM test INTERSECT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect(function(QueryBuilder $builder) {
                    return $builder->literal('SELECT * FROM test2');
                })
                ->sql()
        );
    }

    public function testUnion()
    {
        $this->assertSame(
            'SELECT * FROM test UNION DISTINCT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->union('SELECT * FROM test2')
                ->sql()
        );
    }

    public function testUnionQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'SELECT * FROM test UNION DISTINCT SELECT * FROM test2',
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
            'SELECT * FROM test UNION DISTINCT SELECT * FROM test2',
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
            'SELECT * FROM test UNION DISTINCT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->union(function(QueryBuilder $builder) {
                    return $builder->literal('SELECT * FROM test2');
                })
                ->sql()
        );
    }

    public function testUnionAll()
    {
        $this->assertSame(
            'SELECT * FROM test UNION ALL SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll('SELECT * FROM test2')
                ->sql()
        );
    }

    public function testUnionAllQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'SELECT * FROM test UNION ALL SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll($query)
                ->sql()
        );
    }

    public function testUnionAllClosure()
    {
        $this->assertSame(
            'SELECT * FROM test UNION ALL SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll(function(QueryBuilder $builder) {
                    return $builder->table('test2')
                        ->select();
                })
                ->sql()
        );
    }

    public function testUnionAllLiteral()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'SELECT * FROM test UNION ALL SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll(function(QueryBuilder $builder) {
                    return $builder->literal('SELECT * FROM test2');
                })
                ->sql()
        );
    }

}
