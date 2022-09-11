<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait UpdateBatchTest
{

    public function testUpdateBatch()
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 THEN \'Test 1\' WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 1 THEN 1 WHEN id = 2 THEN 2 END WHERE id IN (1, 2)',
            $this->db->builder()
                ->table('test')
                ->updateBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => 2
                    ]
                ], 'id')
                ->sql()
        );
    }

    public function testUpdateBatchArray()
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 AND value = 1 THEN \'Test 1\' WHEN id = 2 AND value = 2 THEN \'Test 2\' END WHERE ((id = 1 AND value = 1) OR (id = 2 AND value = 2))',
            $this->db->builder()
                ->table('test')
                ->updateBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => 2
                    ]
                ], ['id', 'value'])
                ->sql()
        );
    }

    public function testUpdateBatchArrayNull()
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 AND value = 1 THEN \'Test 1\' WHEN id = 2 AND value IS NULL THEN \'Test 2\' END WHERE ((id = 1 AND value = 1) OR (id = 2 AND value IS NULL))',
            $this->db->builder()
                ->table('test')
                ->updateBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => null
                    ]
                ], ['id', 'value'])
                ->sql()
        );
    }

    public function testUpdateBatchQueryBuilder()
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 THEN \'Test 1\' WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 1 THEN (SELECT id FROM test LIMIT 1) WHEN id = 2 THEN (SELECT id FROM test LIMIT 1) END WHERE id IN (1, 2)',
            $this->db->builder()
                ->table('test')
                ->updateBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => $this->db->builder()
                            ->table('test')
                            ->select(['id'])
                            ->limit(1)
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => $this->db->builder()
                            ->table('test')
                            ->select(['id'])
                            ->limit(1)
                    ]
                ], 'id')
                ->sql()
        );
    }

    public function testUpdateBatchClosure()
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 THEN \'Test 1\' WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 1 THEN (SELECT id FROM test LIMIT 1) WHEN id = 2 THEN (SELECT id FROM test LIMIT 1) END WHERE id IN (1, 2)',
            $this->db->builder()
                ->table('test')
                ->updateBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => function(QueryBuilder $builder) {
                            return $builder
                                ->table('test')
                                ->select(['id'])
                                ->limit(1);
                        }
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => function(QueryBuilder $builder) {
                            return $builder
                                ->table('test')
                                ->select(['id'])
                                ->limit(1);
                        }
                    ]
                ], 'id')
                ->sql()
        );
    }

    public function testUpdateBatchLiteral()
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 THEN \'Test 1\' WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 1 THEN 2 * 10 WHEN id = 2 THEN 2 * 20 END WHERE id IN (1, 2)',
            $this->db->builder()
                ->table('test')
                ->updateBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => function(QueryBuilder $builder) {
                            return $builder->literal('2 * 10');
                        }
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => function(QueryBuilder $builder) {
                            return $builder->literal('2 * 20');
                        }
                    ]
                ], 'id')
                ->sql()
        );
    }

    public function testUpdateBatchMerge()
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 THEN \'Test 1\' WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 1 THEN 1 WHEN id = 2 THEN 2 END WHERE id IN (1, 2)',
            $this->db->builder()
                ->table('test')
                ->updateBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1
                    ]
                ], 'id')
                ->updateBatch([
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => 2
                    ]
                ], 'id')
                ->sql()
        );
    }

    public function testUpdateBatchOverwrite()
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 2 THEN 2 END WHERE id = 2',
            $this->db->builder()
                ->table('test')
                ->updateBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1
                    ]
                ], 'id')
                ->updateBatch([
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => 2
                    ]
                ], 'id', true)
                ->sql()
        );
    }

}
