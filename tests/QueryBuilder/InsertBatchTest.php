<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait InsertBatchTest
{

    public function testInsertBatch()
    {
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test 1\', 1), (\'Test 2\', 2)',
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
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test 1\', (SELECT id FROM test LIMIT 1)), (\'Test 2\', (SELECT id FROM test LIMIT 1))',
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
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test 1\', (SELECT id FROM test LIMIT 1)), (\'Test 2\', (SELECT id FROM test LIMIT 1))',
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
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test 1\', 2 * 10), (\'Test 2\', 2 * 20)',
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

    public function testInsertBatchMerge()
    {
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test 1\', 1), (\'Test 2\', 2)',
            $this->db->builder()
                ->table('test')
                ->insertBatch([
                    [
                        'name' => 'Test 1',
                        'value' => 1
                    ]
                ])
                ->insertBatch([
                    [
                        'name' => 'Test 2',
                        'value' => 2
                    ]
                ])
                ->sql()
        );
    }

    public function testInsertBatchOverwrite()
    {
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test 2\', 2)',
            $this->db->builder()
                ->table('test')
                ->insertBatch([
                    [
                        'name' => 'Test 1',
                        'value' => 1
                    ]
                ])
                ->insertBatch([
                    [
                        'name' => 'Test 2',
                        'value' => 2
                    ]
                ], true)
                ->sql()
        );
    }

}
