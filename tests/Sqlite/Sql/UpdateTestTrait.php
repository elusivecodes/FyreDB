<?php
declare(strict_types=1);

namespace Tests\Sqlite\Sql;

use Fyre\DateTime\DateTime;
use Fyre\DB\Connection;
use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait UpdateTestTrait
{
    public function testUpdate(): void
    {
        $this->assertSame(
            'UPDATE test SET value = 1',
            $this->db->update('test')
                ->set([
                    'value' => 1,
                ])
                ->sql()
        );
    }

    public function testUpdateAlias(): void
    {
        $this->assertSame(
            'UPDATE test AS alt SET value = 1',
            $this->db->update([
                'alt' => 'test',
            ])
                ->set([
                    'value' => 1,
                ])
                ->sql()
        );
    }

    public function testUpdateClosure(): void
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = (SELECT id FROM test LIMIT 1) WHERE id = 1',
            $this->db->update('test')
                ->set([
                    'name' => 'Test',
                    'value' => function(Connection $db): SelectQuery {
                        return $db->select(['id'])
                            ->from('test')
                            ->limit(1);
                    },
                ])
                ->where([
                    'id' => 1,
                ])
                ->sql()
        );
    }

    public function testUpdateDateTime(): void
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = \'2020-01-01 00:00:00\' WHERE id = 1',
            $this->db->update('test')
                ->set([
                    'name' => 'Test',
                    'value' => DateTime::fromArray([2020, 1, 1]),
                ])
                ->where([
                    'id' => 1,
                ])
                ->sql()
        );
    }

    public function testUpdateFrom(): void
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1 FROM test2 WHERE test.id = test2.id AND test.name = \'test\'',
            $this->db->update('test')
                ->set([
                    'name' => 'Test',
                    'value' => 1,
                ])
                ->from('test2')
                ->where([
                    'test.id = test2.id',
                    'test.name' => 'test',
                ])
                ->sql()
        );
    }

    public function testUpdateFull(): void
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1 WHERE test.name = \'test\'',
            $this->db->update('test')
                ->set([
                    'name' => 'Test',
                    'value' => 1,
                ])
                ->where([
                    'test.name' => 'test',
                ])
                ->sql()
        );
    }

    public function testUpdateJoin(): void
    {
        $this->expectException(DbException::class);

        $this->db->update('test')
            ->set([
                'name' => 'Test',
                'value' => 1,
            ])
            ->join([
                [
                    'table' => 'test2',
                    'conditions' => [
                        'test2.id = test.id',
                    ],
                ],
            ]);
    }

    public function testUpdateLiteral(): void
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 2 * 10 WHERE id = 1',
            $this->db->update('test')
                ->set([
                    'name' => 'Test',
                    'value' => function(Connection $db): QueryLiteral {
                        return $db->literal('2 * 10');
                    },
                ])
                ->where([
                    'id' => 1,
                ])
                ->sql()
        );
    }

    public function testUpdateMerge(): void
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1',
            $this->db->update('test')
                ->set([
                    'name' => 'Test',
                ])
                ->set([
                    'value' => 1,
                ])
                ->sql()
        );
    }

    public function testUpdateMultipleTables(): void
    {
        $this->expectException(DbException::class);

        $this->db->update([
            'alt' => 'test',
            'alt2' => 'test2',
        ]);
    }

    public function testUpdateOverwrite(): void
    {
        $this->assertSame(
            'UPDATE test SET value = 1',
            $this->db->update('test')
                ->set([
                    'name' => 'Test',
                ])
                ->set([
                    'value' => 1,
                ], true)
                ->sql()
        );
    }

    public function testUpdateSelectQuery(): void
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = (SELECT id FROM test LIMIT 1) WHERE id = 1',
            $this->db->update('test')
                ->set([
                    'name' => 'Test',
                    'value' => $this->db->select(['id'])
                        ->from('test')
                        ->limit(1),
                ])
                ->where([
                    'id' => 1,
                ])
                ->sql()
        );
    }

    public function testUpdateWhere(): void
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1 WHERE id = 1',
            $this->db->update('test')
                ->set([
                    'name' => 'Test',
                    'value' => 1,
                ])
                ->where([
                    'id' => 1,
                ])
                ->sql()
        );
    }
}
