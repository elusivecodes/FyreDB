<?php
declare(strict_types=1);

namespace Tests\Sqlite\Query;

use Fyre\DB\Exceptions\DbException;

trait DeleteTestTrait
{
    public function testDelete(): void
    {
        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test',
                ],
            ])
            ->execute();

        $this->db->delete()
            ->from('test')
            ->where([
                'id' => 1,
            ])
            ->execute();

        $this->assertSame(
            [],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testDeleteAffectedRows(): void
    {
        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 1',
                ],
                [
                    'name' => 'Test 2',
                ],
            ])
            ->execute();

        $this->db->delete()
            ->from('test')
            ->execute();

        $this->assertSame(
            2,
            $this->db->affectedRows()
        );
    }

    public function testDeleteVirtualTables(): void
    {
        $this->expectException(DbException::class);

        $this->db->delete()
            ->from([
                'alt' => $this->db->select()
                    ->from('test'),
            ]);
    }
}
