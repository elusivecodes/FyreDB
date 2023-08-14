<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait WhereTestTrait
{

    public function testWhere(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE name IS NULL',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where('name IS NULL')
                ->sql()
        );
    }

    public function testWhereArray(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE name = \'test\'',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'name' => 'test'
                ])
                ->sql()
        );
    }

    public function testWhereInteger(): void
    {
        $this->assertSame(
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

    public function testWhereFloat(): void
    {
        $this->assertSame(
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

    public function testWhereBooleanTrue(): void
    {
        $this->assertSame(
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

    public function testWhereBooleanFalse(): void
    {
        $this->assertSame(
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

    public function testWhereEqual(): void
    {
        $this->assertSame(
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

    public function testWhereNotEqual(): void
    {
        $this->assertSame(
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

    public function testWhereGreaterThan(): void
    {
        $this->assertSame(
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

    public function testWhereLessThan(): void
    {
        $this->assertSame(
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

    public function testWhereGreaterThanOrEqual(): void
    {
        $this->assertSame(
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

    public function testWhereLessThanOrEqual(): void
    {
        $this->assertSame(
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

    public function testWhereIsNull(): void
    {
        $this->assertSame(
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


    public function testWhereIsNotNull(): void
    {
        $this->assertSame(
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

    public function testWhereLike(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE name LIKE \'%test%\'',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'name LIKE' => '%test%'
                ])
                ->sql()
        );
    }

    public function testWhereNotLike(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE name NOT LIKE \'%test%\'',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'name NOT LIKE' => '%test%'
                ])
                ->sql()
        );
    }

    public function testWhereIn(): void
    {
        $this->assertSame(
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

    public function testWhereNotIn(): void
    {
        $this->assertSame(
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

    public function testWhereMultiple(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value = 1 AND name = \'test\'',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'value' => 1,
                    'name' => 'test'
                ])
                ->sql()
        );
    }

    public function testWhereAnd(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE (value = 1 AND name = \'test\')',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'and' => [
                        'value' => 1,
                        'name' => 'test'
                    ]
                ])
                ->sql()
        );
    }

    public function testWhereOr(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE (value = 1 OR name = \'test\')',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'or' => [
                        'value' => 1,
                        'name' => 'test'
                    ]
                ])
                ->sql()
        );
    }

    public function testWhereNot(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE NOT (value = 1 AND name = \'test\')',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'not' => [
                        'value' => 1,
                        'name' => 'test'
                    ]
                ])
                ->sql()
        );
    }

    public function testWhereGroups(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE (value = 1 AND (name = \'test\' OR name IS NULL))',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    [
                        'value' => 1,
                        'or' => [
                            'name' => 'test',
                            'name IS NULL'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testWhereQueryBuilder(): void
    {
        $this->assertSame(
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

    public function testWhereClosure(): void
    {
        $this->assertSame(
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

    public function testWhereLiteral(): void
    {
        $this->assertSame(
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

    public function testWhereMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE name = \'test\' AND value = 1',
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

    public function testWhereOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->where([
                    'name' => 'test'
                ])
                ->where([
                    'value' => 1
                ], true)
                ->sql()
        );
    }

}
