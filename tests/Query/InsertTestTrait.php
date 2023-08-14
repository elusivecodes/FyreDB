<?php
declare(strict_types=1);

namespace Tests\Query;

trait InsertTestTrait
{

    public function testInsert(): void
    {
        $this->assertTrue(
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test'
                ])
                ->execute()
        );

        $this->assertSame(
            [
                'id' => 1,
                'name' => 'Test'
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->first()
        );
    }

    public function testInsertBatch(): void
    {
        $this->assertTrue(
            $this->db->builder()
                ->table('test')
                ->insertBatch([
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
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testInsertId(): void
    {
        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test'
            ])
            ->execute();

        $this->assertSame(
            1,
            $this->db->insertId()
        );

        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test 2'
            ])
            ->execute();

        $this->assertSame(
            2,
            $this->db->insertId()
        );
    }

    public function testInsertBatchId(): void
    {
        $this->db->builder()
            ->table('test')
            ->insertBatch([
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
        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test'
            ])
            ->execute();

        $this->assertSame(
            1,
            $this->db->affectedRows()
        );
    }

    public function testInsertBatchAffectedRows(): void
    {
        $this->db->builder()
            ->table('test')
            ->insertBatch([
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
