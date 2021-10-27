<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait UnionTest
{

    public function testQueryBuilderExcept()
    {
        $this->assertEquals(
            'SELECT * FROM test EXCEPT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except('SELECT * FROM test2')
                ->sql()
        );
    }

    public function testQueryBuilderExceptQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
            'SELECT * FROM test EXCEPT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except($query)
                ->sql()
        );
    }

    public function testQueryBuilderExceptClosure()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
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

    public function testQueryBuilderExceptLiteral()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
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

    public function testQueryBuilderIntersect()
    {
        $this->assertEquals(
            'SELECT * FROM test INTERSECT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect('SELECT * FROM test2')
                ->sql()
        );
    }

    public function testQueryBuilderIntersectQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
            'SELECT * FROM test INTERSECT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->intersect($query)
                ->sql()
        );
    }

    public function testQueryBuilderIntersectClosure()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
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

    public function testQueryBuilderIntersectLiteral()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
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

    public function testQueryBuilderUnion()
    {
        $this->assertEquals(
            'SELECT * FROM test UNION DISTINCT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->union('SELECT * FROM test2')
                ->sql()
        );
    }

    public function testQueryBuilderUnionQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
            'SELECT * FROM test UNION DISTINCT SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->union($query)
                ->sql()
        );
    }

    public function testQueryBuilderUnionClosure()
    {
        $this->assertEquals(
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

    public function testQueryBuilderUnionLiteral()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
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

    public function testQueryBuilderUnionAll()
    {
        $this->assertEquals(
            'SELECT * FROM test UNION ALL SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll('SELECT * FROM test2')
                ->sql()
        );
    }

    public function testQueryBuilderUnionAllQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
            'SELECT * FROM test UNION ALL SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll($query)
                ->sql()
        );
    }

    public function testQueryBuilderUnionAllClosure()
    {
        $this->assertEquals(
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

    public function testQueryBuilderUnionAllLiteral()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
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
