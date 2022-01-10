<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait JoinTest
{

    public function testJoinUsing()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 USING id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'using' => 'id'
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditions()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->select()
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

    public function testJoinType()
    {
        $this->assertSame(
            'SELECT * FROM test LEFT OUTER JOIN test2 ON test2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'type' => 'LEFT OUTER',
                        'conditions' => [
                            'test2.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinTableKey()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    'test2' => [
                        'conditions' => [
                            'test2.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinAlias()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 AS t2 ON t2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'alias' => 't2',
                        'conditions' => [
                            't2.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinAliasKey()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 AS t2 ON t2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    't2' => [
                        'table' => 'test2',
                        'conditions' => [
                            't2.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinQueryBuilder()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN (SELECT * FROM test) AS t2 ON t2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    't2' => [
                        'table' => $this->db->builder()
                            ->table('test')
                            ->select(),
                        'conditions' => [
                            't2.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinClosure()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN (SELECT * FROM test) AS t2 ON t2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    't2' => [
                        'table' => function(QueryBuilder $builder) {
                            return $builder->table('test')
                                ->select();
                        },
                        'conditions' => [
                            't2.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinLiteral()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN (SELECT * FROM test) AS t2 ON t2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    't2' => [
                        'table' => function(QueryBuilder $builder) {
                            return $builder->literal('(SELECT * FROM test)');
                        },
                        'conditions' => [
                            't2.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinMultipleJoins()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.id = test.id INNER JOIN test3 ON test3.id = test.id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    'test2' => [
                        'conditions' => [
                            'test2.id = test.id'
                        ]
                    ],
                    'test3' => [
                        'conditions' => [
                            'test3.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinMultipleJoinCalls()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.id = test.id INNER JOIN test3 ON test3.id = test.id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    'test2' => [
                        'conditions' => [
                            'test2.id = test.id'
                        ]
                    ]
                ])
                ->join([
                    'test3' => [
                        'conditions' => [
                            'test3.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsInteger()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.id = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id' => 1
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsFloat()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value = 1.25',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value' => 1.25
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsBooleanTrue()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value' => true
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsBooleanFalse()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value = 0',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value' => false
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsEqual()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value =' => 1
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsNotEqual()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value != 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value !=' => 1
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsGreaterThan()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value > 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value >' => 1
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsLessThan()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value < 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value <' => 1
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsGreaterThanOrEqual()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value >= 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value >=' => 1
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsLessThanOrEqual()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value <= 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value <=' => 1
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsIsNull()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value IS NULL',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value IS' => null
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsIsNotNull()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value IS NOT NULL',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value IS NOT' => null
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsLike()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.name LIKE \'%test%\'',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.name LIKE' => '%test%'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsNotLike()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.name NOT LIKE \'%test%\'',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.name NOT LIKE' => '%test%'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsIn()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value IN (1, 2, 3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value IN' => [1, 2, 3]
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsNotIn()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value NOT IN (1, 2, 3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value NOT IN' => [1, 2, 3]
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsMultiple()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.id = test.id AND test2.value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id = test.id',
                            'test2.value' => 1
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsAnd()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON (test2.id = test.id AND test2.value = 1)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'and' => [
                                'test2.id = test.id',
                                'test2.value' => 1
                            ]
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsOr()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON (test2.id = test.id OR test2.value = 1)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'or' => [
                                'test2.id = test.id',
                                'test2.value' => 1
                            ]
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsGroups()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON (test2.id = test.id AND (test2.value = 1 OR test2.value IS NULL))',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            [
                                'test2.id = test.id',
                                'or' => [
                                    'test2.value' => 1,
                                    'test2.value IS NULL'
                                ]
                            ]
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsQueryBuilder()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value IN (SELECT id FROM test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value IN' => $this->db->builder()
                                ->table('test')
                                ->select(['id'])
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsClosure()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value IN (SELECT id FROM test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value IN' => function(QueryBuilder $builder) {
                                return $builder
                                    ->table('test')
                                    ->select(['id']);
                            }
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testJoinConditionsLiteral()
    {
        $this->assertSame(
            'SELECT * FROM test INNER JOIN test2 ON test2.value = UPPER(test.test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.value' => function(QueryBuilder $builder) {
                                return $builder->literal('UPPER(test.test)');
                            }
                        ]
                    ]
                ])
                ->sql()
        );
    }

}
