<?php
declare(strict_types=1);

namespace Tests\Query;

trait DeleteTestTrait
{

    public function testDelete(): void
    {
        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test'
            ])
            ->execute();

        $this->assertTrue(
            $this->db->builder()
                ->table('test')
                ->delete()
                ->where([
                    'id' => 1
                ])
                ->execute()
        );

        $this->assertSame(
            [],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testDeleteAffectedRows(): void
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

        $this->assertSame(
            2,
            $this->db->affectedRows()
        );
    }

}
