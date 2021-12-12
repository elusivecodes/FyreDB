<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait HavingTest
{

    public function testHaving()
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

    public function testHavingArray()
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

    public function testHavingInteger()
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

    public function testHavingFloat()
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

    public function testHavingBooleanTrue()
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

    public function testHavingBooleanFalse()
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

    public function testHavingEqual()
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

    public function testHavingNotEqual()
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

    public function testHavingGreaterThan()
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

    public function testHavingLessThan()
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

    public function testHavingGreaterThanOrEqual()
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

    public function testHavingLessThanOrEqual()
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

    public function testHavingIsNull()
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


    public function testHavingIsNotNull()
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

    public function testHavingLike()
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

    public function testHavingNotLike()
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

    public function testHavingIn()
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

    public function testHavingNotIn()
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

    public function testHavingMultiple()
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

    public function testHavingAnd()
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

    public function testHavingOr()
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

    public function testHavingGroups()
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

    public function testHavingQueryBuilder()
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

    public function testHavingClosure()
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

    public function testHavingLiteral()
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
