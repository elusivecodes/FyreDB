<?php
declare(strict_types=1);

namespace Tests\Query;

trait InsertTest
{

    public function testInsert()
    {
        $this->assertEquals(
            true,
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test'
                ])
                ->execute()
        );

        $this->assertEquals(
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

    public function testInsertBatch()
    {
        $this->assertEquals(
            true,
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

        $this->assertEquals(
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

    public function testInsertId()
    {
        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test'
            ])
            ->execute();

        $this->assertEquals(
            1,
            $this->db->insertId()
        );

        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test 2'
            ])
            ->execute();

        $this->assertEquals(
            2,
            $this->db->insertId()
        );
    }

    public function testInsertBatchId()
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

        $this->assertEquals(
            1,
            $this->db->insertId()
        );
    }

    public function testInsertAffectedRows()
    {
        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test'
            ])
            ->execute();

        $this->assertEquals(
            1,
            $this->db->affectedRows()
        );
    }

    public function testInsertBatchAffectedRows()
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

        $this->assertEquals(
            2,
            $this->db->affectedRows()
        );
    }

}
