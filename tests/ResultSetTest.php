<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\DB\Connection,
    Fyre\DB\ConnectionManager,
    PHPUnit\Framework\TestCase;

final class ResultSetTest extends TestCase
{

    protected Connection $db;

    use
        ConnectionTrait;

    public function testResultSetToArray(): void
    {
        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'name' => 'Test 1'
                ],
                [
                    'id' => 2,
                    'name' => 'Test 2'
                ],
                [
                    'id' => 3,
                    'name' => 'Test 3'
                ]
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testResultSetCount(): void
    {
        $this->assertEquals(
            3,
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->count()
        );
    }

    public function testResultSetFetch(): void
    {
        $this->assertEquals(
            [
                'id' => 2,
                'name' => 'Test 2'
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->fetch(1)
        );
    }

    public function testResultSetFirst(): void
    {
        $this->assertEquals(
            [
                'id' => 1,
                'name' => 'Test 1'
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->first()
        );
    }

    public function testResultSetLast(): void
    {
        $this->assertEquals(
            [
                'id' => 3,
                'name' => 'Test 3'
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->last()
        );
    }

    public function testResultSetColumnCount(): void
    {
        $this->assertEquals(
            2,
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->columnCount()
        );
    }

    public function testResultSetColumns(): void
    {
        $this->assertEquals(
            [
                'id',
                'name'
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->columns()
        );
    }

    public function testResultSetIteration(): void
    {
        $query =  $this->db->builder()
            ->table('test')
            ->select()
            ->execute();

        $results = [];

        foreach ($query AS $row) {
            $results[] = $row;
        }

        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'name' => 'Test 1'
                ],
                [
                    'id' => 2,
                    'name' => 'Test 2'
                ],
                [
                    'id' => 3,
                    'name' => 'Test 3'
                ]
            ],
            $results
        );
    }

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
        $this->db->builder()
            ->table('test')
            ->insertBatch([
                [
                    'name' => 'Test 1'
                ],
                [
                    'name' => 'Test 2'
                ],
                [
                    'name' => 'Test 3'
                ]
            ])
            ->execute();
    }

    protected function tearDown(): void
    {
        $this->db->query('TRUNCATE test');
    }

}
