<?php
declare(strict_types=1);

namespace Tests\Postgres\Sql;

use Fyre\DateTime\DateTime;
use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait WhereTestTrait
{
    public function testWhere(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE name IS NULL',
            $this->db->select()
                ->from('test')
                ->where('name IS NULL')
                ->sql()
        );
    }

    public function testWhereAnd(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE (value = 1 AND name = \'test\')',
            $this->db->select()
                ->from('test')
                ->where([
                    'and' => [
                        'value' => 1,
                        'name' => 'test',
                    ],
                ])
                ->sql()
        );
    }

    public function testWhereArray(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE name = \'test\'',
            $this->db->select()
                ->from('test')
                ->where([
                    'name' => 'test',
                ])
                ->sql()
        );
    }

    public function testWhereBooleanFalse(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value = 0',
            $this->db->select()
                ->from('test')
                ->where([
                    'value' => false,
                ])
                ->sql()
        );
    }

    public function testWhereBooleanTrue(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value = 1',
            $this->db->select()
                ->from('test')
                ->where([
                    'value' => true,
                ])
                ->sql()
        );
    }

    public function testWhereClosure(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value IN (SELECT id FROM test)',
            $this->db->select()
                ->from('test')
                ->where([
                    'value IN' => function(Connection $db): SelectQuery {
                        return $db->select(['id'])
                            ->from('test');
                    },
                ])
                ->sql()
        );
    }

    public function testWhereDateTime(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value = \'2020-01-01 00:00:00\'',
            $this->db->select()
                ->from('test')
                ->where([
                    'value' => DateTime::fromArray([2020, 1, 1]),
                ])
                ->sql()
        );
    }

    public function testWhereEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value = 1',
            $this->db->select()
                ->from('test')
                ->where([
                    'value =' => 1,
                ])
                ->sql()
        );
    }

    public function testWhereFloat(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value = 1.25',
            $this->db->select()
                ->from('test')
                ->where([
                    'value' => 1.25,
                ])
                ->sql()
        );
    }

    public function testWhereGreaterThan(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value > 1',
            $this->db->select()
                ->from('test')
                ->where([
                    'value >' => 1,
                ])
                ->sql()
        );
    }

    public function testWhereGreaterThanOrEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value >= 1',
            $this->db->select()
                ->from('test')
                ->where([
                    'value >=' => 1,
                ])
                ->sql()
        );
    }

    public function testWhereGroups(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE (value = 1 AND (name = \'test\' OR name IS NULL))',
            $this->db->select()
                ->from('test')
                ->where([
                    [
                        'value' => 1,
                        'or' => [
                            'name' => 'test',
                            'name IS NULL',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testWhereIn(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value IN (1, 2, 3)',
            $this->db->select()
                ->from('test')
                ->where([
                    'value IN' => [1, 2, 3],
                ])
                ->sql()
        );
    }

    public function testWhereInteger(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE id = 1',
            $this->db->select()
                ->from('test')
                ->where([
                    'id' => 1,
                ])
                ->sql()
        );
    }

    public function testWhereIsNotNull(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value IS NOT NULL',
            $this->db->select()
                ->from('test')
                ->where([
                    'value IS NOT' => null,
                ])
                ->sql()
        );
    }

    public function testWhereIsNull(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value IS NULL',
            $this->db->select()
                ->from('test')
                ->where([
                    'value IS' => null,
                ])
                ->sql()
        );
    }

    public function testWhereLessThan(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value < 1',
            $this->db->select()
                ->from('test')
                ->where([
                    'value <' => 1,
                ])
                ->sql()
        );
    }

    public function testWhereLessThanOrEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value <= 1',
            $this->db->select()
                ->from('test')
                ->where([
                    'value <=' => 1,
                ])
                ->sql()
        );
    }

    public function testWhereLike(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE name LIKE \'%test%\'',
            $this->db->select()
                ->from('test')
                ->where([
                    'name LIKE' => '%test%',
                ])
                ->sql()
        );
    }

    public function testWhereLiteral(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value = UPPER(test)',
            $this->db->select()
                ->from('test')
                ->where([
                    'value' => function(Connection $db): QueryLiteral {
                        return $db->literal('UPPER(test)');
                    },
                ])
                ->sql()
        );
    }

    public function testWhereMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE name = \'test\' AND value = 1',
            $this->db->select()
                ->from('test')
                ->where([
                    'name' => 'test',
                ])
                ->where([
                    'value' => 1,
                ])
                ->sql()
        );
    }

    public function testWhereMultiple(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value = 1 AND name = \'test\'',
            $this->db->select()
                ->from('test')
                ->where([
                    'value' => 1,
                    'name' => 'test',
                ])
                ->sql()
        );
    }

    public function testWhereNot(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE NOT (value = 1 AND name = \'test\')',
            $this->db->select()
                ->from('test')
                ->where([
                    'not' => [
                        'value' => 1,
                        'name' => 'test',
                    ],
                ])
                ->sql()
        );
    }

    public function testWhereNotEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value != 1',
            $this->db->select()
                ->from('test')
                ->where([
                    'value !=' => 1,
                ])
                ->sql()
        );
    }

    public function testWhereNotIn(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value NOT IN (1, 2, 3)',
            $this->db->select()
                ->from('test')
                ->where([
                    'value NOT IN' => [1, 2, 3],
                ])
                ->sql()
        );
    }

    public function testWhereNotLike(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE name NOT LIKE \'%test%\'',
            $this->db->select()
                ->from('test')
                ->where([
                    'name NOT LIKE' => '%test%',
                ])
                ->sql()
        );
    }

    public function testWhereOr(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE (value = 1 OR name = \'test\')',
            $this->db->select()
                ->from('test')
                ->where([
                    'or' => [
                        'value' => 1,
                        'name' => 'test',
                    ],
                ])
                ->sql()
        );
    }

    public function testWhereOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value = 1',
            $this->db->select()
                ->from('test')
                ->where([
                    'name' => 'test',
                ])
                ->where([
                    'value' => 1,
                ], true)
                ->sql()
        );
    }

    public function testWhereSelectQuery(): void
    {
        $this->assertSame(
            'SELECT * FROM test WHERE value IN (SELECT id FROM test)',
            $this->db->select()
                ->from('test')
                ->where([
                    'value IN' => $this->db->select(['id'])
                        ->from('test'),
                ])
                ->sql()
        );
    }
}
