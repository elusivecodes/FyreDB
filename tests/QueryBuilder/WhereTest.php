<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait WhereTest
{

    public function testWhere()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE name IS NULL',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where('name IS NULL')
                ->sql()
        );
    }

    public function testWhereArray()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE name = "test"',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'name' => 'test'
                ])
                ->sql()
        );
    }

    public function testWhereMultipleCalls()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE name = "test" AND value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'name' => 'test'
                ])
                ->where([
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testWhereInteger()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testWhereFloat()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value = 1.25',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value' => 1.25
                ])
                ->sql()
        );
    }

    public function testWhereBooleanTrue()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value' => true
                ])
                ->sql()
        );
    }

    public function testWhereBooleanFalse()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value = 0',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value' => false
                ])
                ->sql()
        );
    }

    public function testWhereEqual()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value =' => 1
                ])
                ->sql()
        );
    }

    public function testWhereNotEqual()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value != 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value !=' => 1
                ])
                ->sql()
        );
    }

    public function testWhereGreaterThan()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value > 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value >' => 1
                ])
                ->sql()
        );
    }

    public function testWhereLessThan()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value < 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value <' => 1
                ])
                ->sql()
        );
    }

    public function testWhereGreaterThanOrEqual()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value >= 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value >=' => 1
                ])
                ->sql()
        );
    }

    public function testWhereLessThanOrEqual()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value <= 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value <=' => 1
                ])
                ->sql()
        );
    }

    public function testWhereIsNull()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value IS NULL',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value IS' => null
                ])
                ->sql()
        );
    }


    public function testWhereIsNotNull()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value IS NOT NULL',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value IS NOT' => null
                ])
                ->sql()
        );
    }

    public function testWhereLike()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE name LIKE "%test%"',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'name LIKE' => '%test%'
                ])
                ->sql()
        );
    }

    public function testWhereNotLike()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE name NOT LIKE "%test%"',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'name NOT LIKE' => '%test%'
                ])
                ->sql()
        );
    }

    public function testWhereIn()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value IN (1, 2, 3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value IN' => [1, 2, 3]
                ])
                ->sql()
        );
    }

    public function testWhereNotIn()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value NOT IN (1, 2, 3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value NOT IN' => [1, 2, 3]
                ])
                ->sql()
        );
    }

    public function testWhereMultiple()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value = 1 AND name = "test"',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value' => 1,
                    'name' => "test"
                ])
                ->sql()
        );
    }

    public function testWhereAnd()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE (value = 1 AND name = "test")',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'and' => [
                        'value' => 1,
                        'name' => "test"
                    ]
                ])
                ->sql()
        );
    }

    public function testWhereOr()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE (value = 1 OR name = "test")',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'or' => [
                        'value' => 1,
                        'name' => "test"
                    ]
                ])
                ->sql()
        );
    }

    public function testWhereGroups()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE (value = 1 AND (name = "test" OR name IS NULL))',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
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

    public function testWhereQueryBuilder()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value IN (SELECT id FROM test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value IN' => $this->db->builder()
                        ->table('test')
                        ->select(['id'])
                ])
                ->sql()
        );
    }

    public function testWhereClosure()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value IN (SELECT id FROM test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value IN' => function(QueryBuilder $builder) {
                        return $builder
                            ->table('test')
                            ->select(['id']);
                    }
                ])
                ->sql()
        );
    }

    public function testWhereLiteral()
    {
        $this->assertEquals(
            'SELECT * FROM test WHERE value = UPPER(test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value' => function(QueryBuilder $builder) {
                        return $builder->literal('UPPER(test)');
                    }
                ])
                ->sql()
        );
    }

}
