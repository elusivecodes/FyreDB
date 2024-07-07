<?php
declare(strict_types=1);

namespace Tests\Sqlite\Sql;

use Fyre\DateTime\DateTime;
use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait JoinTestTrait
{
    public function testJoinAlias(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" AS "t2" ON "t2"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'alias' => 't2',
                        'conditions' => [
                            't2.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinAliasKey(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" AS "t2" ON "t2"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    't2' => [
                        'table' => 'test2',
                        'conditions' => [
                            't2.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinClosure(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN (SELECT * FROM "test") AS "t2" ON "t2"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    't2' => [
                        'table' => function(Connection $db): SelectQuery {
                            return $db->select()
                                ->from('test');
                        },
                        'conditions' => [
                            't2.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditions(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsAnd(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON ("test2"."id" = "test"."id" AND "test2"."value" = 1)',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'and' => [
                                'test2.id = test.id',
                                'test2.value' => 1,
                            ],
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsBooleanFalse(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" = 0',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value' => false,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsBooleanTrue(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" = 1',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value' => true,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsClosure(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" IN (SELECT "id" FROM "test")',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value IN' => function(Connection $db): SelectQuery {
                                return $db->select(['id'])
                                    ->from('test');
                            },
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsDateTime(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" = \'2020-01-01 00:00:00\'',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value' => DateTime::fromArray([2020, 1, 1]),
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" = 1',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value =' => 1,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsFloat(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" = 1.25',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value' => 1.25,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsGreaterThan(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" > 1',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value >' => 1,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsGreaterThanOrEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" >= 1',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value >=' => 1,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsGroups(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON ("test2"."id" = "test"."id" AND ("test2"."value" = 1 OR "test2"."value" IS NULL))',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            [
                                'test2.id = test.id',
                                'or' => [
                                    'test2.value' => 1,
                                    'test2.value IS NULL',
                                ],
                            ],
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsIn(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" IN (1, 2, 3)',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value IN' => [1, 2, 3],
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsInteger(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."id" = 1',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id' => 1,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsIsNotNull(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" IS NOT NULL',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value IS NOT' => null,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsIsNull(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" IS NULL',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value IS' => null,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsLessThan(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" < 1',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value <' => 1,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsLessThanOrEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" <= 1',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value <=' => 1,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsLike(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."name" LIKE \'%test%\'',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.name LIKE' => '%test%',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsLiteral(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" = UPPER(test.test)',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value' => function(Connection $db): QueryLiteral {
                                return $db->literal('UPPER(test.test)');
                            },
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."id" = "test"."id" INNER JOIN "test3" ON "test3"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    'test2' => [
                        'conditions' => [
                            'test2.id = test.id',
                        ],
                    ],
                ])
                ->join([
                    'test3' => [
                        'conditions' => [
                            'test3.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsMultiple(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."id" = "test"."id" AND "test2"."value" = 1',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id = test.id',
                            'test2.value' => 1,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsNot(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON NOT ("test2"."id" = "test"."id" AND "test2"."value" = 1)',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'not' => [
                                'test2.id = test.id',
                                'test2.value' => 1,
                            ],
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsNotEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" != 1',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value !=' => 1,
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsNotIn(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" NOT IN (1, 2, 3)',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value NOT IN' => [1, 2, 3],
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsNotLike(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."name" NOT LIKE \'%test%\'',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.name NOT LIKE' => '%test%',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsOr(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON ("test2"."id" = "test"."id" OR "test2"."value" = 1)',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'or' => [
                                'test2.id = test.id',
                                'test2.value' => 1,
                            ],
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinConditionsOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test3" ON "test3"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    'test2' => [
                        'conditions' => [
                            'test2.id = test.id',
                        ],
                    ],
                ])
                ->join([
                    'test3' => [
                        'conditions' => [
                            'test3.id = test.id',
                        ],
                    ],
                ], true)
                ->sql()
        );
    }

    public function testJoinConditionsSelectQuery(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."value" IN (SELECT "id" FROM "test")',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value IN' => $this->db->select(['id'])
                                ->from('test'),
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinLiteral(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN (SELECT * FROM test) AS "t2" ON "t2"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    't2' => [
                        'table' => function(Connection $db): QueryLiteral {
                            return $db->literal('(SELECT * FROM test)');
                        },
                        'conditions' => [
                            't2.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinMultipleJoins(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."id" = "test"."id" INNER JOIN "test3" ON "test3"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    'test2' => [
                        'conditions' => [
                            'test2.id = test.id',
                        ],
                    ],
                    'test3' => [
                        'conditions' => [
                            'test3.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinSelectQuery(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN (SELECT * FROM "test") AS "t2" ON "t2"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    't2' => [
                        'table' => $this->db->select()
                            ->from('test'),
                        'conditions' => [
                            't2.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinTableKey(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" ON "test2"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    'test2' => [
                        'conditions' => [
                            'test2.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinType(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" LEFT OUTER JOIN "test2" ON "test2"."id" = "test"."id"',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'type' => 'LEFT OUTER',
                        'conditions' => [
                            'test2.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testJoinUsing(): void
    {
        $this->assertSame(
            'SELECT * FROM "test" INNER JOIN "test2" USING "id"',
            $this->db->select()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'using' => 'id',
                    ],
                ])
                ->sql()
        );
    }
}
