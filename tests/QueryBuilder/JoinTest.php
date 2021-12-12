<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait JoinTest
{

    public function testJoinUsing()
    {
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
            'SELECT * FROM test INNER JOIN test2 ON test2.name LIKE "%test%"',
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
        $this->assertEquals(
            'SELECT * FROM test INNER JOIN test2 ON test2.name NOT LIKE "%test%"',
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
