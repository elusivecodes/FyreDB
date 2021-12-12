<?php
declare(strict_types=1);

namespace Tests\Query;

trait Replacetest
{

    public function testReplace()
    {
        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test'
            ])
            ->execute();

        $this->assertEquals(
            true,
            $this->db->builder()
                ->table('test')
                ->replace([
                    'id' => 1,
                    'name' => 'Test 2'
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

    public function testReplaceBatch()
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
                ->replaceBatch([
                    [
                        'id' => 1,
                        'name' => 'Test 3'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 4'
                    ]
                ])
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

}
