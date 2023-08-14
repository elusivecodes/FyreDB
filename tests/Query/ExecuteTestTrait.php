<?php
declare(strict_types=1);

namespace Tests\Query;

trait ExecuteTestTrait
{

    public function testExecute(): void
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
            [
                'id' => 2,
                'name' => 'Test 2'
            ],
            $this->db->execute('SELECT * FROM test WHERE name = ?', ['Test 2'])
                ->first()
        );
    }

    public function testExecuteNamed(): void
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
            [
                'id' => 2,
                'name' => 'Test 2'
            ],
            $this->db->execute('SELECT * FROM test WHERE name = :name', ['name' => 'Test 2'])
                ->first()
        );
    }

    public function testExecuteUpdate(): void
    {
        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test'
            ])
            ->execute();

        $this->assertTrue(
            $this->db->execute('UPDATE test SET name = "Test 2" WHERE name = ?', ['Test'])
        );

        $this->assertSame(
            1,
            $this->db->affectedRows()
        );
    }

}
