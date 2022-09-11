<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait InsertFromTest
{

    public function testInsertFromQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'INSERT INTO test VALUES (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->insertFrom($query)
                ->sql()
        );
    }

    public function testInsertFromClosure()
    {
        $this->assertSame(
            'INSERT INTO test VALUES (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->insertFrom(function(QueryBuilder $builder) {
                    return $builder->table('test2')
                        ->select();
                })
                ->sql()
        );
    }

    public function testInsertFromLiteral()
    {
        $this->assertSame(
            'INSERT INTO test VALUES (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->insertFrom(function(QueryBuilder $builder) {
                    return $builder->literal('(SELECT * FROM test2)');
                })
                ->sql()
        );
    }

    public function testInsertFromString()
    {
        $this->assertSame(
            'INSERT INTO test (id, name) VALUES (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->insertFrom('(SELECT * FROM test2)', ['id', 'name'])
                ->sql()
        );
    }

    public function testInsertFromColumnsQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'INSERT INTO test (id, name) VALUES (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->insertFrom($query, ['id', 'name'])
                ->sql()
        );
    }

    public function testInsertFromColumnsClosure()
    {
        $this->assertSame(
            'INSERT INTO test (id, name) VALUES (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->insertFrom(function(QueryBuilder $builder) {
                    return $builder->table('test2')
                        ->select();
                }, ['id', 'name'])
                ->sql()
        );
    }

    public function testInsertFromColumnsLiteral()
    {
        $this->assertSame(
            'INSERT INTO test (id, name) VALUES (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->insertFrom(function(QueryBuilder $builder) {
                    return $builder->literal('(SELECT * FROM test2)');
                }, ['id', 'name'])
                ->sql()
        );
    }

    public function testInsertFromColumnsString()
    {
        $this->assertSame(
            'INSERT INTO test (id, name) VALUES (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->insertFrom('(SELECT * FROM test2)', ['id', 'name'])
                ->sql()
        );
    }

}
