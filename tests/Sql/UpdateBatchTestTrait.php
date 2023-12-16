<?php
declare(strict_types=1);

namespace Tests\Sql;

use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait UpdateBatchTestTrait
{

    public function testUpdateBatch(): void
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 THEN \'Test 1\' WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 1 THEN 1 WHEN id = 2 THEN 2 END WHERE id IN (1, 2)',
            $this->db->updateBatch('test')
                ->set([
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

    public function testUpdateBatchArray(): void
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 AND value = 1 THEN \'Test 1\' WHEN id = 2 AND value = 2 THEN \'Test 2\' END WHERE ((id = 1 AND value = 1) OR (id = 2 AND value = 2))',
            $this->db->updateBatch('test')
                ->set([
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

    public function testUpdateBatchArrayNull(): void
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 AND value = 1 THEN \'Test 1\' WHEN id = 2 AND value IS NULL THEN \'Test 2\' END WHERE ((id = 1 AND value = 1) OR (id = 2 AND value IS NULL))',
            $this->db->updateBatch('test')
                ->set([
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

    public function testUpdateBatchSelectQuery(): void
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 THEN \'Test 1\' WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 1 THEN (SELECT id FROM test LIMIT 1) WHEN id = 2 THEN (SELECT id FROM test LIMIT 1) END WHERE id IN (1, 2)',
            $this->db->updateBatch('test')
                ->set([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => $this->db->select(['id'])
                            ->from('test')
                            ->limit(1)
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => $this->db->select(['id'])
                            ->from('test')
                            ->limit(1)
                    ]
                ], 'id')
                ->sql()
        );
    }

    public function testUpdateBatchClosure(): void
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 THEN \'Test 1\' WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 1 THEN (SELECT id FROM test LIMIT 1) WHEN id = 2 THEN (SELECT id FROM test LIMIT 1) END WHERE id IN (1, 2)',
            $this->db->updateBatch('test')
                ->set([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => function(Connection $db): SelectQuery {
                            return $db->select(['id'])
                                ->from('test')
                                ->limit(1);
                        }
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => function(Connection $db): SelectQuery {
                            return $db->select(['id'])
                                ->from('test')
                                ->limit(1);
                        }
                    ]
                ], 'id')
                ->sql()
        );
    }

    public function testUpdateBatchLiteral(): void
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 THEN \'Test 1\' WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 1 THEN 2 * 10 WHEN id = 2 THEN 2 * 20 END WHERE id IN (1, 2)',
            $this->db->updateBatch('test')
                ->set([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => function(Connection $db): QueryLiteral {
                            return $db->literal('2 * 10');
                        }
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => function(Connection $db): QueryLiteral {
                            return $db->literal('2 * 20');
                        }
                    ]
                ], 'id')
                ->sql()
        );
    }

    public function testUpdateBatchMerge(): void
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 1 THEN \'Test 1\' WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 1 THEN 1 WHEN id = 2 THEN 2 END WHERE id IN (1, 2)',
            $this->db->updateBatch('test')
                ->set([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1
                    ]
                ], 'id')
                ->set([
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => 2
                    ]
                ], 'id')
                ->sql()
        );
    }

    public function testUpdateBatchOverwrite(): void
    {
        $this->assertSame(
            'UPDATE test SET name = CASE WHEN id = 2 THEN \'Test 2\' END, value = CASE WHEN id = 2 THEN 2 END WHERE id = 2',
            $this->db->updateBatch('test')
                ->set([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1
                    ]
                ], 'id')
                ->set([
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
