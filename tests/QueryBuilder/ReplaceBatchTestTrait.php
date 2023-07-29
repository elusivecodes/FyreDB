<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait ReplaceBatchTestTrait
{

    public function testReplaceBatch()
    {
        $this->assertSame(
            'REPLACE INTO test (id, name, value) VALUES (1, \'Test 1\', 1), (2, \'Test 2\', 2)',
            $this->db->builder()
                ->table('test')
                ->replaceBatch([
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
                ])
                ->sql()
        );
    }

    public function testReplaceBatchQueryBuilder()
    {
        $this->assertSame(
            'REPLACE INTO test (name, value) VALUES (\'Test 1\', (SELECT id FROM test LIMIT 1)), (\'Test 2\', (SELECT id FROM test LIMIT 1))',
            $this->db->builder()
                ->table('test')
                ->replaceBatch([
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

    public function testReplaceBatchClosure()
    {
        $this->assertSame(
            'REPLACE INTO test (name, value) VALUES (\'Test 1\', (SELECT id FROM test LIMIT 1)), (\'Test 2\', (SELECT id FROM test LIMIT 1))',
            $this->db->builder()
                ->table('test')
                ->replaceBatch([
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

    public function testReplaceBatchLiteral()
    {
        $this->assertSame(
            'REPLACE INTO test (name, value) VALUES (\'Test 1\', 2 * 10), (\'Test 2\', 2 * 20)',
            $this->db->builder()
                ->table('test')
                ->replaceBatch([
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

    public function testReplaceBatchMerge()
    {
        $this->assertSame(
            'REPLACE INTO test (id, name, value) VALUES (1, \'Test 1\', 1), (2, \'Test 2\', 2)',
            $this->db->builder()
                ->table('test')
                ->replaceBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1
                    ]
                ])
                ->replaceBatch([
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => 2
                    ]
                ])
                ->sql()
        );
    }

    public function testReplaceBatchOverwrite()
    {
        $this->assertSame(
            'REPLACE INTO test (id, name, value) VALUES (2, \'Test 2\', 2)',
            $this->db->builder()
                ->table('test')
                ->replaceBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1
                    ]
                ])
                ->replaceBatch([
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => 2
                    ]
                ], true)
                ->sql()
        );
    }

}
