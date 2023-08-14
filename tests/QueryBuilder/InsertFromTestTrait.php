<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait InsertFromTestTrait
{

    public function testInsertFromQueryBuilder(): void
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

    public function testInsertFromClosure(): void
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

    public function testInsertFromLiteral(): void
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

    public function testInsertFromString(): void
    {
        $this->assertSame(
            'INSERT INTO test (id, name) VALUES (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->insertFrom('(SELECT * FROM test2)', ['id', 'name'])
                ->sql()
        );
    }

    public function testInsertFromColumnsQueryBuilder(): void
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

    public function testInsertFromColumnsClosure(): void
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

    public function testInsertFromColumnsLiteral(): void
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

    public function testInsertFromColumnsString(): void
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
