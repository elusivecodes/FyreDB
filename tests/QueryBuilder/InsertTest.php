<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait InsertTest
{

    public function testInsert()
    {
        $this->assertEquals(
            'INSERT INTO test (name, value) VALUES ("Test", 1)',
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test',
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testInsertQueryBuilder()
    {
        $this->assertEquals(
            'INSERT INTO test (name, value) VALUES ("Test", (SELECT id FROM test LIMIT 1))',
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test',
                    'value' => $this->db->builder()
                        ->table('test')
                        ->select(['id'])
                        ->limit(1)
                ])
                ->sql()
        );
    }

    public function testInsertClosure()
    {
        $this->assertEquals(
            'INSERT INTO test (name, value) VALUES ("Test", (SELECT id FROM test LIMIT 1))',
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test',
                    'value' => function(QueryBuilder $builder) {
                        return $builder
                            ->table('test')
                            ->select(['id'])
                            ->limit(1);
                    }
                ])
                ->sql()
        );
    }

    public function testInsertLiteral()
    {
        $this->assertEquals(
            'INSERT INTO test (name, value) VALUES ("Test", 2 * 10)',
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test',
                    'value' => function(QueryBuilder $builder) {
                        return $builder->literal('2 * 10');
                    }
                ])
                ->sql()
        );
    }

    public function testInsertBatch()
    {
        $this->assertEquals(
            'INSERT INTO test (name, value) VALUES ("Test 1", 1), ("Test 2", 2)',
            $this->db->builder()
                ->table('test')
                ->insertBatch([
                    [
                        'name' => 'Test 1',
                        'value' => 1
                    ],
                    [
                        'name' => 'Test 2',
                        'value' => 2
                    ]
                ])
                ->sql()
        );
    }

    public function testInsertBatchQueryBuilder()
    {
        $this->assertEquals(
            'INSERT INTO test (name, value) VALUES ("Test 1", (SELECT id FROM test LIMIT 1)), ("Test 2", (SELECT id FROM test LIMIT 1))',
            $this->db->builder()
                ->table('test')
                ->insertBatch([
                    [
                        'name' => 'Test 1',
                        'value' => $this->db->builder()
                            ->table('test')
                            ->select(['id'])
                            ->limit(1)
                    ],
                    [
                        'name' => 'Test 2',
                        'value' => $this->db->builder()
                            ->table('test')
                            ->select(['id'])
                            ->limit(1)
                    ]
                ])
                ->sql()
        );
    }

    public function testInsertBatchClosure()
    {
        $this->assertEquals(
            'INSERT INTO test (name, value) VALUES ("Test 1", (SELECT id FROM test LIMIT 1)), ("Test 2", (SELECT id FROM test LIMIT 1))',
            $this->db->builder()
                ->table('test')
                ->insertBatch([
                    [
                        'name' => 'Test 1',
                        'value' => function(QueryBuilder $builder) {
                            return $builder
                                ->table('test')
                                ->select(['id'])
                                ->limit(1);
                        }
                    ],
                    [
                        'name' => 'Test 2',
                        'value' => function(QueryBuilder $builder) {
                            return $builder
                                ->table('test')
                                ->select(['id'])
                                ->limit(1);
                        }
                    ]
                ])
                ->sql()
        );
    }

    public function testInsertBatchLiteral()
    {
        $this->assertEquals(
            'INSERT INTO test (name, value) VALUES ("Test 1", 2 * 10), ("Test 2", 2 * 20)',
            $this->db->builder()
                ->table('test')
                ->insertBatch([
                    [
                        'name' => 'Test 1',
                        'value' => function(QueryBuilder $builder) {
                            return $builder->literal('2 * 10');
                        }
                    ],
                    [
                        'name' => 'Test 2',
                        'value' => function(QueryBuilder $builder) {
                            return $builder->literal('2 * 20');
                        }
                    ]
                ])
                ->sql()
        );
    }

    public function testInsertFromQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
            'INSERT INTO test VALUES SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->insertFrom($query)
                ->sql()
        );
    }

    public function testInsertFromClosure()
    {
        $this->assertEquals(
            'INSERT INTO test VALUES SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->insertFrom(function($builder) {
                    return $builder->table('test2')
                        ->select();
                })
                ->sql()
        );
    }

    public function testInsertFromLiteral()
    {
        $this->assertEquals(
            'INSERT INTO test VALUES SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->insertFrom(function($builder) {
                    return $builder->literal('SELECT * FROM test2');
                })
                ->sql()
        );
    }

    public function testInsertFromString()
    {
        $this->assertEquals(
            'INSERT INTO test (id, name) VALUES SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->insertFrom('SELECT * FROM test2', ['id', 'name'])
                ->sql()
        );
    }

    public function testInsertFromColumnsQueryBuilder()
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertEquals(
            'INSERT INTO test (id, name) VALUES SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->insertFrom($query, ['id', 'name'])
                ->sql()
        );
    }

    public function testInsertFromColumnsClosure()
    {
        $this->assertEquals(
            'INSERT INTO test (id, name) VALUES SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->insertFrom(function($builder) {
                    return $builder->table('test2')
                        ->select();
                }, ['id', 'name'])
                ->sql()
        );
    }

    public function testInsertFromColumnsLiteral()
    {
        $this->assertEquals(
            'INSERT INTO test (id, name) VALUES SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->insertFrom(function($builder) {
                    return $builder->literal('SELECT * FROM test2');
                }, ['id', 'name'])
                ->sql()
        );
    }

    public function testInsertFromColumnsString()
    {
        $this->assertEquals(
            'INSERT INTO test (id, name) VALUES SELECT * FROM test2',
            $this->db->builder()
                ->table('test')
                ->insertFrom('SELECT * FROM test2', ['id', 'name'])
                ->sql()
        );
    }

}
