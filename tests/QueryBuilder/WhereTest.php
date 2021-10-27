<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait WhereTest
{

    public function testQueryBuilderWhere()
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

    public function testQueryBuilderWhereArray()
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

    public function testQueryBuilderWhereMultipleCalls()
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

    public function testQueryBuilderWhereInteger()
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

    public function testQueryBuilderWhereFloat()
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

    public function testQueryBuilderWhereBooleanTrue()
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

    public function testQueryBuilderWhereBooleanFalse()
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

    public function testQueryBuilderWhereEqual()
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

    public function testQueryBuilderWhereNotEqual()
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

    public function testQueryBuilderWhereGreaterThan()
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

    public function testQueryBuilderWhereLessThan()
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

    public function testQueryBuilderWhereGreaterThanOrEqual()
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

    public function testQueryBuilderWhereLessThanOrEqual()
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

    public function testQueryBuilderWhereIsNull()
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


    public function testQueryBuilderWhereIsNotNull()
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

    public function testQueryBuilderWhereLike()
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

    public function testQueryBuilderWhereNotLike()
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

    public function testQueryBuilderWhereIn()
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

    public function testQueryBuilderWhereNotIn()
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

    public function testQueryBuilderWhereMultiple()
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

    public function testQueryBuilderWhereAnd()
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

    public function testQueryBuilderWhereOr()
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

    public function testQueryBuilderWhereGroups()
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

    public function testQueryBuilderWhereQueryBuilder()
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

    public function testQueryBuilderWhereClosure()
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

    public function testQueryBuilderWhereLiteral()
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
