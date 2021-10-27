<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait HavingTest
{

    public function testQueryBuilderHaving()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING name IS NULL',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having('name IS NULL')
                ->sql()
        );
    }

    public function testQueryBuilderHavingArray()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING name = "test"',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'name' => 'test'
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingInteger()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING id = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingFloat()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value = 1.25',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value' => 1.25
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingBooleanTrue()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value' => true
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingBooleanFalse()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value = 0',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value' => false
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingEqual()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value =' => 1
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingNotEqual()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value != 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value !=' => 1
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingGreaterThan()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value > 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value >' => 1
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingLessThan()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value < 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value <' => 1
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingGreaterThanOrEqual()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value >= 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value >=' => 1
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingLessThanOrEqual()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value <= 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value <=' => 1
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingIsNull()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value IS NULL',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value IS' => null
                ])
                ->sql()
        );
    }


    public function testQueryBuilderHavingIsNotNull()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value IS NOT NULL',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value IS NOT' => null
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingLike()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING name LIKE "%test%"',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'name LIKE' => '%test%'
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingNotLike()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING name NOT LIKE "%test%"',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'name NOT LIKE' => '%test%'
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingIn()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value IN (1, 2, 3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value IN' => [1, 2, 3]
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingNotIn()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value NOT IN (1, 2, 3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value NOT IN' => [1, 2, 3]
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingMultiple()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value = 1 AND name = "test"',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value' => 1,
                    'name' => "test"
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingAnd()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING (value = 1 AND name = "test")',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'and' => [
                        'value' => 1,
                        'name' => "test"
                    ]
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingOr()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING (value = 1 OR name = "test")',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'or' => [
                        'value' => 1,
                        'name' => "test"
                    ]
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingGroups()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING (value = 1 AND (name = "test" OR name IS NULL))',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    [
                        'value' => 1,
                        'or' => [
                            'name' => "test",
                            'name IS NULL'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingQueryBuilder()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value IN (SELECT id FROM test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value IN' => $this->db->builder()
                        ->table('test')
                        ->select(['id'])
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingClosure()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value IN (SELECT id FROM test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value IN' => function(QueryBuilder $builder) {
                        return $builder
                            ->table('test')
                            ->select(['id']);
                    }
                ])
                ->sql()
        );
    }

    public function testQueryBuilderHavingLiteral()
    {
        $this->assertEquals(
            'SELECT * FROM test HAVING value = UPPER(test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value' => function(QueryBuilder $builder) {
                        return $builder->literal('UPPER(test)');
                    }
                ])
                ->sql()
        );
    }

}
