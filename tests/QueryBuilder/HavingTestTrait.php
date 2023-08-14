<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;
use Fyre\DB\QueryLiteral;

trait HavingTestTrait
{

    public function testHaving(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING name IS NULL',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having('name IS NULL')
                ->sql()
        );
    }

    public function testHavingArray(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING name = \'test\'',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'name' => 'test'
                ])
                ->sql()
        );
    }

    public function testHavingInteger(): void
    {
        $this->assertSame(
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

    public function testHavingFloat(): void
    {
        $this->assertSame(
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

    public function testHavingBooleanTrue(): void
    {
        $this->assertSame(
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

    public function testHavingBooleanFalse(): void
    {
        $this->assertSame(
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

    public function testHavingEqual(): void
    {
        $this->assertSame(
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

    public function testHavingNotEqual(): void
    {
        $this->assertSame(
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

    public function testHavingGreaterThan(): void
    {
        $this->assertSame(
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

    public function testHavingLessThan(): void
    {
        $this->assertSame(
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

    public function testHavingGreaterThanOrEqual(): void
    {
        $this->assertSame(
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

    public function testHavingLessThanOrEqual(): void
    {
        $this->assertSame(
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

    public function testHavingIsNull(): void
    {
        $this->assertSame(
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


    public function testHavingIsNotNull(): void
    {
        $this->assertSame(
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

    public function testHavingLike(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING name LIKE \'%test%\'',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'name LIKE' => '%test%'
                ])
                ->sql()
        );
    }

    public function testHavingNotLike(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING name NOT LIKE \'%test%\'',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'name NOT LIKE' => '%test%'
                ])
                ->sql()
        );
    }

    public function testHavingIn(): void
    {
        $this->assertSame(
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

    public function testHavingNotIn(): void
    {
        $this->assertSame(
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

    public function testHavingMultiple(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = 1 AND name = \'test\'',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value' => 1,
                    'name' => 'test'
                ])
                ->sql()
        );
    }

    public function testHavingAnd(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING (value = 1 AND name = \'test\')',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'and' => [
                        'value' => 1,
                        'name' => 'test'
                    ]
                ])
                ->sql()
        );
    }

    public function testHavingOr(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING (value = 1 OR name = \'test\')',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'or' => [
                        'value' => 1,
                        'name' => 'test'
                    ]
                ])
                ->sql()
        );
    }

    public function testHavingNot(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING NOT (value = 1 AND name = \'test\')',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'not' => [
                        'value' => 1,
                        'name' => 'test'
                    ]
                ])
                ->sql()
        );
    }

    public function testHavingGroups(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING (value = 1 AND (name = \'test\' OR name IS NULL))',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
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

    public function testHavingQueryBuilder(): void
    {
        $this->assertSame(
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

    public function testHavingClosure(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value IN (SELECT id FROM test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value IN' => fn(QueryBuilder $builder): QueryBuilder => $builder
                        ->table('test')
                        ->select(['id'])
                ])
                ->sql()
        );
    }

    public function testHavingLiteral(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = UPPER(test)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'value' => fn(QueryBuilder $builder): QueryLiteral => $builder
                        ->literal('UPPER(test)')
                ])
                ->sql()
        );
    }

    public function testHavingMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING name = \'test\' AND value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'name' => 'test'
                ])
                ->having([
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testHavingOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->having([
                    'name' => 'test'
                ])
                ->having([
                    'value' => 1
                ], true)
                ->sql()
        );
    }

}
