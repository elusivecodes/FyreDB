<?php
declare(strict_types=1);

namespace Tests\Query;

trait DeleteTestTrait
{

    public function testDelete(): void
    {
        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test'
                ]
            ])
            ->execute();

        $this->assertTrue(
            $this->db->delete()
                ->from('test')
                ->where([
                    'id' => 1
                ])
                ->execute()
        );

        $this->assertSame(
            [],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testDeleteAffectedRows(): void
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

        $this->db->delete()
            ->from('test')
            ->execute();

        $this->assertSame(
            2,
            $this->db->affectedRows()
        );
    }

}
