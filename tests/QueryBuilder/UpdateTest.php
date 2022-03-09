<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait UpdateTest
{

    public function testUpdate()
    {
        $this->assertSame(
            'UPDATE test SET value = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateAlias()
    {
        $this->assertSame(
            'UPDATE test AS alt SET value = 1',
            $this->db->builder()
                ->table([
                    'alt' => 'test'
                ])
                ->update([
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateMultipleTables()
    {
        $this->assertSame(
            'UPDATE test AS alt, test2 AS alt2 SET alt.value = 1, alt2.value = 2',
            $this->db->builder()
                ->table([
                    'alt' => 'test',
                    'alt2' => 'test2'
                ])
                ->update([
                    'alt.value' => 1,
                    'alt2.value' => 2
                ])
                ->sql()
        );
    }

    public function testUpdateJoin()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1 INNER JOIN test2 ON test2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => 1
                ])
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testUpdateWhere()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1 WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => 1
                ])
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateQueryBuilder()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = (SELECT id FROM test LIMIT 1) WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => $this->db->builder()
                        ->table('test')
                        ->select(['id'])
                        ->limit(1)
                ])
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateClosure()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = (SELECT id FROM test LIMIT 1) WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => function(QueryBuilder $builder) {
                        return $builder
                            ->table('test')
                            ->select(['id'])
                            ->limit(1);
                    }
                ])
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateLiteral()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 2 * 10 WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => function(QueryBuilder $builder) {
                        return $builder->literal('2 * 10');
                    }
                ])
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

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

    public function testUpdateFull()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1 INNER JOIN test2 ON test2.id = test.id WHERE test.name = \'test\'',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => 1
                ])
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id = test.id'
                        ]
                    ]
                ])
                ->where([
                    'test.name' => 'test'
                ])
                ->sql()
        );
    }

}
