<?php
declare(strict_types=1);

namespace Tests\Mysql\Sql;

use Fyre\DateTime\DateTime;
use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait HavingTestTrait
{
    public function testHaving(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING name IS NULL',
            $this->db->select()
                ->from('test')
                ->having('name IS NULL')
                ->sql()
        );
    }

    public function testHavingAnd(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING (value = 1 AND name = \'test\')',
            $this->db->select()
                ->from('test')
                ->having([
                    'and' => [
                        'value' => 1,
                        'name' => 'test',
                    ],
                ])
                ->sql()
        );
    }

    public function testHavingArray(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING name = \'test\'',
            $this->db->select()
                ->from('test')
                ->having([
                    'name' => 'test',
                ])
                ->sql()
        );
    }

    public function testHavingBooleanFalse(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = 0',
            $this->db->select()
                ->from('test')
                ->having([
                    'value' => false,
                ])
                ->sql()
        );
    }

    public function testHavingBooleanTrue(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = 1',
            $this->db->select()
                ->from('test')
                ->having([
                    'value' => true,
                ])
                ->sql()
        );
    }

    public function testHavingClosure(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value IN (SELECT id FROM test)',
            $this->db->select()
                ->from('test')
                ->having([
                    'value IN' => function(Connection $db): SelectQuery {
                        return $db->select(['id'])
                            ->from('test');
                    },
                ])
                ->sql()
        );
    }

    public function testHavingDateTime(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = \'2020-01-01 00:00:00\'',
            $this->db->select()
                ->from('test')
                ->having([
                    'value' => DateTime::fromArray([2020, 1, 1]),
                ])
                ->sql()
        );
    }

    public function testHavingEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = 1',
            $this->db->select()
                ->from('test')
                ->having([
                    'value =' => 1,
                ])
                ->sql()
        );
    }

    public function testHavingFloat(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = 1.25',
            $this->db->select()
                ->from('test')
                ->having([
                    'value' => 1.25,
                ])
                ->sql()
        );
    }

    public function testHavingGreaterThan(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value > 1',
            $this->db->select()
                ->from('test')
                ->having([
                    'value >' => 1,
                ])
                ->sql()
        );
    }

    public function testHavingGreaterThanOrEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value >= 1',
            $this->db->select()
                ->from('test')
                ->having([
                    'value >=' => 1,
                ])
                ->sql()
        );
    }

    public function testHavingGroups(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING (value = 1 AND (name = \'test\' OR name IS NULL))',
            $this->db->select()
                ->from('test')
                ->having([
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

    public function testHavingIn(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value IN (1, 2, 3)',
            $this->db->select()
                ->from('test')
                ->having([
                    'value IN' => [1, 2, 3],
                ])
                ->sql()
        );
    }

    public function testHavingInteger(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING id = 1',
            $this->db->select()
                ->from('test')
                ->having([
                    'id' => 1,
                ])
                ->sql()
        );
    }

    public function testHavingIsNotNull(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value IS NOT NULL',
            $this->db->select()
                ->from('test')
                ->having([
                    'value IS NOT' => null,
                ])
                ->sql()
        );
    }

    public function testHavingIsNull(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value IS NULL',
            $this->db->select()
                ->from('test')
                ->having([
                    'value IS' => null,
                ])
                ->sql()
        );
    }

    public function testHavingLessThan(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value < 1',
            $this->db->select()
                ->from('test')
                ->having([
                    'value <' => 1,
                ])
                ->sql()
        );
    }

    public function testHavingLessThanOrEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value <= 1',
            $this->db->select()
                ->from('test')
                ->having([
                    'value <=' => 1,
                ])
                ->sql()
        );
    }

    public function testHavingLike(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING name LIKE \'%test%\'',
            $this->db->select()
                ->from('test')
                ->having([
                    'name LIKE' => '%test%',
                ])
                ->sql()
        );
    }

    public function testHavingLiteral(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = UPPER(test)',
            $this->db->select()
                ->from('test')
                ->having([
                    'value' => function(Connection $db): QueryLiteral {
                        return $db->literal('UPPER(test)');
                    },
                ])
                ->sql()
        );
    }

    public function testHavingMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING name = \'test\' AND value = 1',
            $this->db->select()
                ->from('test')
                ->having([
                    'name' => 'test',
                ])
                ->having([
                    'value' => 1,
                ])
                ->sql()
        );
    }

    public function testHavingMultiple(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = 1 AND name = \'test\'',
            $this->db->select()
                ->from('test')
                ->having([
                    'value' => 1,
                    'name' => 'test',
                ])
                ->sql()
        );
    }

    public function testHavingNot(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING NOT (value = 1 AND name = \'test\')',
            $this->db->select()
                ->from('test')
                ->having([
                    'not' => [
                        'value' => 1,
                        'name' => 'test',
                    ],
                ])
                ->sql()
        );
    }

    public function testHavingNotEqual(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value != 1',
            $this->db->select()
                ->from('test')
                ->having([
                    'value !=' => 1,
                ])
                ->sql()
        );
    }

    public function testHavingNotIn(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value NOT IN (1, 2, 3)',
            $this->db->select()
                ->from('test')
                ->having([
                    'value NOT IN' => [1, 2, 3],
                ])
                ->sql()
        );
    }

    public function testHavingNotLike(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING name NOT LIKE \'%test%\'',
            $this->db->select()
                ->from('test')
                ->having([
                    'name NOT LIKE' => '%test%',
                ])
                ->sql()
        );
    }

    public function testHavingOr(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING (value = 1 OR name = \'test\')',
            $this->db->select()
                ->from('test')
                ->having([
                    'or' => [
                        'value' => 1,
                        'name' => 'test',
                    ],
                ])
                ->sql()
        );
    }

    public function testHavingOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value = 1',
            $this->db->select()
                ->from('test')
                ->having([
                    'name' => 'test',
                ])
                ->having([
                    'value' => 1,
                ], true)
                ->sql()
        );
    }

    public function testHavingSelectQuery(): void
    {
        $this->assertSame(
            'SELECT * FROM test HAVING value IN (SELECT id FROM test)',
            $this->db->select()
                ->from('test')
                ->having([
                    'value IN' => $this->db->select(['id'])
                        ->from('test'),
                ])
                ->sql()
        );
    }
}
