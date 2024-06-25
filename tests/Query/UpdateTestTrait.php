<?php
declare(strict_types=1);

namespace Tests\Query;

use Fyre\DB\Exceptions\DbException;

trait UpdateTestTrait
{
    public function testUpdate(): void
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

        $this->assertTrue(
            $this->db->update('test')
                ->set([
                    'name' => 'Test 2',
                ])
                ->where([
                    'id' => 1,
                ])
                ->execute()
        );

        $this->assertSame(
            [
                'id' => 1,
                'name' => 'Test 2',
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->first()
        );
    }

    public function testUpdateAffectedRows(): void
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

        $this->db->update()
            ->table('test')
            ->set([
                'name' => 'Test 3',
            ])
            ->where([
                'id' => 1,
            ])
            ->execute();

        $this->assertSame(
            1,
            $this->db->affectedRows()
        );
    }

    public function testUpdateBatch(): void
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

        $this->assertTrue(
            $this->db->updateBatch('test')
                ->set([
                    [
                        'id' => 1,
                        'name' => 'Test 3',
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 4',
                    ],
                ], 'id')
                ->execute()
        );

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => 'Test 3',
                ],
                [
                    'id' => 2,
                    'name' => 'Test 4',
                ],
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testUpdateBatchAffectedRows(): void
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

        $this->db->updateBatch('test')
            ->set([
                [
                    'id' => 1,
                    'name' => 'Test 3',
                ],
                [
                    'id' => 2,
                    'name' => 'Test 4',
                ],
            ], 'id')
            ->execute();

        $this->assertSame(
            2,
            $this->db->affectedRows()
        );
    }

    public function testUpdateBatchMultipleTables(): void
    {
        $this->expectException(DbException::class);

        $this->db->updateBatch()
            ->table([
                'alt' => 'test',
                'alt2' => 'test2',
            ]);
    }

    public function testUpdateBatchVirtualTables(): void
    {
        $this->expectException(DbException::class);

        $this->db->updateBatch()
            ->table([
                'alt' => $this->db->select()
                    ->from('test'),
            ]);
    }

    public function testUpdateVirtualTables(): void
    {
        $this->expectException(DbException::class);

        $this->db->update([
            'alt' => $this->db->select()
                ->from('test'),
        ]);
    }
}
