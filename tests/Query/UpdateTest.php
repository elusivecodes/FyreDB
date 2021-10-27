<?php
declare(strict_types=1);

namespace Tests\Query;

trait UpdateTest
{

    public function testQueryUpdate()
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

    public function testQueryUpdateBatch()
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
                ->toArray()
        );
    }

    public function testQueryUpdateAffectedRows()
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

    public function testQueryUpdateBatchAffectedRows()
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
