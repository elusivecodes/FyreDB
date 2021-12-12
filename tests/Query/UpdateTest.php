<?php
declare(strict_types=1);

namespace Tests\Query;

trait UpdateTest
{

    public function testUpdate()
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
            true,
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test 2'
                ])
                ->where([
                    'id' => 1
                ])
                ->execute()
        );

        $this->assertEquals(
            [
                'id' => 1,
                'name' => 'Test 2'
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->first()
        );
    }

    public function testUpdateBatch()
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
            true,
            $this->db->builder()
                ->table('test')
                ->updateBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 3'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 4'
                    ]
                ], 'id')
                ->execute()
        );

        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'name' => 'Test 3'
                ],
                [
                    'id' => 2,
                    'name' => 'Test 4'
                ]
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testUpdateAffectedRows()
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

        $this->db->builder()
            ->table('test')
            ->update([
                'name' => 'Test 3'
            ])
            ->where([
                'id' => 1
            ])
            ->execute();

        $this->assertEquals(
            1,
            $this->db->affectedRows()
        );
    }

    public function testUpdateBatchAffectedRows()
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

        $this->db->builder()
            ->table('test')
            ->updateBatch([
                [
                    'id' => 1,
                    'name' => 'Test 3'
                ],
                [
                    'id' => 2,
                    'name' => 'Test 4'
                ]
            ], 'id')
            ->execute();

        $this->assertEquals(
            2,
            $this->db->affectedRows()
        );
    }

}
