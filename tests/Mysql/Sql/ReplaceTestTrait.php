<?php
declare(strict_types=1);

namespace Tests\Mysql\Sql;

use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait ReplaceTestTrait
{
    public function testReplace(): void
    {
        $this->assertSame(
            'REPLACE INTO `test` (`id`, `name`, `value`) VALUES (1, \'Test 1\', 1), (2, \'Test 2\', 2)',
            $this->db->replace()
                ->into('test')
                ->values([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1,
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => 2,
                    ],
                ])
                ->sql()
        );
    }

    public function testReplaceClosure(): void
    {
        $this->assertSame(
            'REPLACE INTO `test` (`name`, `value`) VALUES (\'Test 1\', (SELECT `id` FROM `test` LIMIT 1)), (\'Test 2\', (SELECT `id` FROM `test` LIMIT 1))',
            $this->db->replace()
                ->into('test')
                ->values([
                    [
                        'name' => 'Test 1',
                        'value' => function(Connection $db): SelectQuery {
                            return $db->select(['id'])
                                ->from('test')
                                ->limit(1);
                        },
                    ],
                    [
                        'name' => 'Test 2',
                        'value' => function(Connection $db): SelectQuery {
                            return $db->select(['id'])
                                ->from('test')
                                ->limit(1);
                        },
                    ],
                ])
                ->sql()
        );
    }

    public function testReplaceLiteral(): void
    {
        $this->assertSame(
            'REPLACE INTO `test` (`name`, `value`) VALUES (\'Test 1\', 2 * 10), (\'Test 2\', 2 * 20)',
            $this->db->replace()
                ->into('test')
                ->values([
                    [
                        'name' => 'Test 1',
                        'value' => function(Connection $db): QueryLiteral {
                            return $db->literal('2 * 10');
                        },
                    ],
                    [
                        'name' => 'Test 2',
                        'value' => function(Connection $db): QueryLiteral {
                            return $db->literal('2 * 20');
                        },
                    ],
                ])
                ->sql()
        );
    }

    public function testReplaceMerge(): void
    {
        $this->assertSame(
            'REPLACE INTO `test` (`id`, `name`, `value`) VALUES (1, \'Test 1\', 1), (2, \'Test 2\', 2)',
            $this->db->replace()
                ->into('test')
                ->values([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1,
                    ],
                ])
                ->values([
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => 2,
                    ],
                ])
                ->sql()
        );
    }

    public function testReplaceOverwrite(): void
    {
        $this->assertSame(
            'REPLACE INTO `test` (`id`, `name`, `value`) VALUES (2, \'Test 2\', 2)',
            $this->db->replace()
                ->into('test')
                ->values([
                    [
                        'id' => 1,
                        'name' => 'Test 1',
                        'value' => 1,
                    ],
                ])
                ->values([
                    [
                        'id' => 2,
                        'name' => 'Test 2',
                        'value' => 2,
                    ],
                ], true)
                ->sql()
        );
    }

    public function testReplaceSelectQuery(): void
    {
        $this->assertSame(
            'REPLACE INTO `test` (`name`, `value`) VALUES (\'Test 1\', (SELECT `id` FROM `test` LIMIT 1)), (\'Test 2\', (SELECT `id` FROM `test` LIMIT 1))',
            $this->db->replace()
                ->into('test')
                ->values([
                    [
                        'name' => 'Test 1',
                        'value' => $this->db->select(['id'])
                            ->from('test')
                            ->limit(1),
                    ],
                    [
                        'name' => 'Test 2',
                        'value' => $this->db->select(['id'])
                            ->from('test')
                            ->limit(1),
                    ],
                ])
                ->sql()
        );
    }
}
