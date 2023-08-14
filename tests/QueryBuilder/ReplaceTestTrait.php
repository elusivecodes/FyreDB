<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait ReplaceTestTrait
{

    public function testReplace(): void
    {
        $this->assertSame(
            'REPLACE INTO test (id, name, value) VALUES (1, \'Test\', 1)',
            $this->db->builder()
                ->table('test')
                ->replace([
                    'id' => 1,
                    'name' => 'Test',
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testReplaceQueryBuilder(): void
    {
        $this->assertSame(
            'REPLACE INTO test (id, name, value) VALUES (1, \'Test\', (SELECT id FROM test LIMIT 1))',
            $this->db->builder()
                ->table('test')
                ->replace([
                    'id' => 1,
                    'name' => 'Test',
                    'value' => $this->db->builder()
                        ->table('test')
                        ->select(['id'])
                        ->limit(1)
                ])
                ->sql()
        );
    }

    public function testReplaceClosure(): void
    {
        $this->assertSame(
            'REPLACE INTO test (id, name, value) VALUES (1, \'Test\', (SELECT id FROM test LIMIT 1))',
            $this->db->builder()
                ->table('test')
                ->replace([
                    'id' => 1,
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

    public function testReplaceLiteral(): void
    {
        $this->assertSame(
            'REPLACE INTO test (id, name, value) VALUES (1, \'Test\', 2 * 10)',
            $this->db->builder()
                ->table('test')
                ->replace([
                    'id' => 1,
                    'name' => 'Test',
                    'value' => function(QueryBuilder $builder) {
                        return $builder->literal('2 * 10');
                    }
                ])
                ->sql()
        );
    }

    public function testReplaceMerge(): void
    {
        $this->assertSame(
            'REPLACE INTO test (id, name, value) VALUES (1, \'Test\', 1)',
            $this->db->builder()
                ->table('test')
                ->replace([
                    'id' => 1,
                    'name' => 'Test'
                ])
                ->replace([
                    'id' => 1,
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testReplaceOverwrite(): void
    {
        $this->assertSame(
            'REPLACE INTO test (id, value) VALUES (1, 1)',
            $this->db->builder()
                ->table('test')
                ->replace([
                    'id' => 1,
                    'name' => 'Test'
                ])
                ->replace([
                    'id' => 1,
                    'value' => 1
                ], true)
                ->sql()
        );
    }

}
