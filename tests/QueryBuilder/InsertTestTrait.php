<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait InsertTestTrait
{

    public function testInsert(): void
    {
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test\', 1)',
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test',
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testInsertQueryBuilder(): void
    {
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test\', (SELECT id FROM test LIMIT 1))',
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test',
                    'value' => $this->db->builder()
                        ->table('test')
                        ->select(['id'])
                        ->limit(1)
                ])
                ->sql()
        );
    }

    public function testInsertClosure(): void
    {
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test\', (SELECT id FROM test LIMIT 1))',
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test',
                    'value' => function(QueryBuilder $builder) {
                        return $builder
                            ->table('test')
                            ->select(['id'])
                            ->limit(1);
                    }
                ])
                ->sql()
        );
    }

    public function testInsertLiteral(): void
    {
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test\', 2 * 10)',
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test',
                    'value' => function(QueryBuilder $builder) {
                        return $builder->literal('2 * 10');
                    }
                ])
                ->sql()
        );
    }

    public function testInsertMerge(): void
    {
        $this->assertSame(
            'INSERT INTO test (name, value) VALUES (\'Test\', 1)',
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test'
                ])
                ->insert([
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testInsertOverwrite(): void
    {
        $this->assertSame(
            'INSERT INTO test (value) VALUES (1)',
            $this->db->builder()
                ->table('test')
                ->insert([
                    'name' => 'Test'
                ])
                ->insert([
                    'value' => 1
                ], true)
                ->sql()
        );
    }

}
