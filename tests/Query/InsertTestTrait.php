<?php
declare(strict_types=1);

namespace Tests\Query;

trait InsertTestTrait
{

    public function testInsert(): void
    {
        $this->assertTrue(
            $this->db->insert()
                ->into('test')
                ->values([
                    [
                        'name' => 'Test'
                    ]
                ])
                ->execute()
        );

        $this->assertSame(
            [
                'id' => 1,
                'name' => 'Test'
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->first()
        );
    }

    public function testInsertBatch(): void
    {
        $this->assertTrue(
            $this->db->insert()
                ->into('test')
                ->values([
                    [
                        'name' => 'Test 1'
                    ],
                    [
                        'name' => 'Test 2'
                    ]
                ])
                ->execute()
        );

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => 'Test 1'
                ],
                [
                    'id' => 2,
                    'name' => 'Test 2'
                ]
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testInsertId(): void
    {
        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test'
                ]
            ])
            ->execute();

        $this->assertSame(
            1,
            $this->db->insertId()
        );

        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 2'
                ]
            ])
            ->execute();

        $this->assertSame(
            2,
            $this->db->insertId()
        );
    }

    public function testInsertBatchId(): void
    {
        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 1'
                ],
                [
                    'name' => 'Test 2'
                ]
            ])
            ->execute();

        $this->assertSame(
            1,
            $this->db->insertId()
        );
    }

    public function testInsertAffectedRows(): void
    {
        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test'
                ]
            ])
            ->execute();

        $this->assertSame(
            1,
            $this->db->affectedRows()
        );
    }

    public function testInsertBatchAffectedRows(): void
    {
        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 1'
                ],
                [
                    'name' => 'Test 2'
                ]
            ])
            ->execute();

        $this->assertSame(
            2,
            $this->db->affectedRows()
        );
    }

}
