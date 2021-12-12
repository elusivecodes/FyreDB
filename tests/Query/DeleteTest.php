<?php
declare(strict_types=1);

namespace Tests\Query;

trait DeleteTest
{

    public function testDelete()
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
                ->delete()
                ->where([
                    'id' => 1
                ])
                ->execute()
        );

        $this->assertEquals(
            [],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testDeleteAffectedRows()
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
            ->delete()
            ->execute();

        $this->assertEquals(
            2,
            $this->db->affectedRows()
        );
    }

}
