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

    public function testAll(): void
    {
        $this->assertSame(
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

    public function testCount(): void
    {
        $this->assertSame(
            3,
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->count()
        );
    }

    public function testFetch(): void
    {
        $this->assertSame(
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

    public function testFirst(): void
    {
        $this->assertSame(
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

    public function testLast(): void
    {
        $this->assertSame(
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

    public function testColumnCount(): void
    {
        $this->assertSame(
            2,
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->columnCount()
        );
    }

    public function testColumns(): void
    {
        $this->assertSame(
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

    public function testIteration(): void
    {
        $query =  $this->db->builder()
            ->table('test')
            ->select()
            ->execute();

        $results = [];

        foreach ($query AS $row) {
            $results[] = $row;
        }

        $this->assertSame(
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
