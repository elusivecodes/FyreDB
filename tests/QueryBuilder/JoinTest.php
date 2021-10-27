<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait JoinTest
{

    public function testQueryBuilderJoinUsing()
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

    public function testQueryBuilderJoinConditions()
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

    public function testQueryBuilderJoinType()
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

    public function testQueryBuilderJoinTableKey()
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

    public function testQueryBuilderJoinAlias()
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

    public function testQueryBuilderJoinAliasKey()
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

    public function testQueryBuilderJoinQueryBuilder()
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

    public function testQueryBuilderJoinClosure()
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

    public function testQueryBuilderJoinLiteral()
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

    public function testQueryBuilderJoinMultipleJoins()
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

    public function testQueryBuilderJoinMultipleJoinCalls()
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

    public function testQueryBuilderJoinConditionsInteger()
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

    public function testQueryBuilderJoinConditionsFloat()
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

    public function testQueryBuilderJoinConditionsBooleanTrue()
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

    public function testQueryBuilderJoinConditionsBooleanFalse()
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

    public function testQueryBuilderJoinConditionsEqual()
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

    public function testQueryBuilderJoinConditionsNotEqual()
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

    public function testQueryBuilderJoinConditionsGreaterThan()
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

    public function testQueryBuilderJoinConditionsLessThan()
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

    public function testQueryBuilderJoinConditionsGreaterThanOrEqual()
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

    public function testQueryBuilderJoinConditionsLessThanOrEqual()
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

    public function testQueryBuilderJoinConditionsIsNull()
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

    public function testQueryBuilderJoinConditionsIsNotNull()
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

    public function testQueryBuilderJoinConditionsLike()
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

    public function testQueryBuilderJoinConditionsNotLike()
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

    public function testQueryBuilderJoinConditionsIn()
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

    public function testQueryBuilderJoinConditionsNotIn()
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

    public function testQueryBuilderJoinConditionsMultiple()
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

    public function testQueryBuilderJoinConditionsAnd()
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

    public function testQueryBuilderJoinConditionsOr()
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

    public function testQueryBuilderJoinConditionsGroups()
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

    public function testQueryBuilderJoinConditionsQueryBuilder()
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

    public function testQueryBuilderJoinConditionsClosure()
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

    public function testQueryBuilderJoinConditionsLiteral()
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
