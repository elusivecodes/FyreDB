<?php
declare(strict_types=1);

namespace Tests\Postgres\Sql;

use Fyre\DB\Exceptions\DbException;

trait DeleteTestTrait
{
    public function testDelete(): void
    {
        $this->assertSame(
            'DELETE FROM test',
            $this->db->delete()
                ->from('test')
                ->sql()
        );
    }

    public function testDeleteAlias(): void
    {
        $this->expectException(DbException::class);

        $this->db->delete('alt');
    }

    public function testDeleteFull(): void
    {
        $this->assertSame(
            'DELETE FROM test WHERE test.name = \'test\' ORDER BY test.id ASC LIMIT 20',
            $this->db->delete()
                ->from('test')
                ->where([
                    'test.name' => 'test',
                ])
                ->orderBy([
                    'test.id' => 'ASC',
                ])
                ->limit(20)
                ->sql()
        );
    }

    public function testDeleteJoin(): void
    {
        $this->expectException(DbException::class);

        $this->db->delete()
            ->from('test')
            ->join([
                [
                    'table' => 'test2',
                    'conditions' => [
                        'test2.id = test.id',
                    ],
                ],
            ]);
    }

    public function testDeleteLimit(): void
    {
        $this->assertSame(
            'DELETE FROM test LIMIT 1',
            $this->db->delete()
                ->from('test')
                ->limit(1)
                ->sql()
        );
    }

    public function testDeleteMultipleTables(): void
    {
        $this->expectException(DbException::class);

        $this->db->delete()
            ->from([
                'alt' => 'test',
                'alt2' => 'test2',
            ]);
    }

    public function testDeleteOrderBy(): void
    {
        $this->assertSame(
            'DELETE FROM test ORDER BY id ASC',
            $this->db->delete()
                ->from('test')
                ->orderBy('id ASC')
                ->sql()
        );
    }

    public function testDeleteOrderByArray(): void
    {
        $this->assertSame(
            'DELETE FROM test ORDER BY id ASC, value DESC',
            $this->db->delete()
                ->from('test')
                ->orderBy([
                    'id' => 'ASC',
                    'value' => 'DESC',
                ])
                ->sql()
        );
    }

    public function testDeleteTables(): void
    {
        $this->assertSame(
            'DELETE FROM test AS alt',
            $this->db->delete()
                ->from([
                    'alt' => 'test',
                ])
                ->sql()
        );
    }

    public function testDeleteUsing(): void
    {
        $this->assertSame(
            'DELETE FROM test USING test2 WHERE test.id = test2.id',
            $this->db->delete()
                ->from('test')
                ->using('test2')
                ->where([
                    'test.id = test2.id',
                ])
                ->sql()
        );
    }

    public function testDeleteWhere(): void
    {
        $this->assertSame(
            'DELETE FROM test WHERE id = 1',
            $this->db->delete()
                ->from('test')
                ->where([
                    'id' => 1,
                ])
                ->sql()
        );
    }
}
