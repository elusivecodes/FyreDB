<?php
declare(strict_types=1);

namespace Tests\Query;

trait InsertTest
{

    public function testQueryInsert()
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

    public function testQueryInsertBatch()
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

    public function testQueryInsertId()
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

    public function testQueryInsertBatchId()
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

    public function testQueryInsertAffectedRows()
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

    public function testQueryInsertBatchAffectedRows()
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
