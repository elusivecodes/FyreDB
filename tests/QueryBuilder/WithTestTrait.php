<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait WithTestTrait
{

    public function testWithQueryBuilder(): void
    {
        $query = $this->db->builder()
            ->table('test')
            ->select();

        $this->assertSame(
            'WITH alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->builder()
                ->with([
                    'alt' => $query
                ])
                ->table('alt')
                ->select()
                ->sql()
        );
    }

    public function testWithClosure(): void
    {
        $this->assertSame(
            'WITH alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->builder()
                ->with([
                    'alt' => function(QueryBuilder $builder) {
                        return $builder->table('test')
                            ->select();
                    }
                ])
                ->table('alt')
                ->select()
                ->sql()
        );
    }

    public function testWithLiteral(): void
    {
        $this->assertSame(
            'WITH alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->builder()
                ->with([
                    'alt' => function(QueryBuilder $builder) {
                        return $builder->literal('(SELECT * FROM test)');
                    }
                ])
                ->table('alt')
                ->select()
                ->sql()
        );
    }

    public function testWithRecursiveQueryBuilder(): void
    {
        $query = $this->db->builder()
            ->table('test')
            ->select();

        $this->assertSame(
            'WITH RECURSIVE alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->builder()
                ->withRecursive([
                    'alt' => $query
                ])
                ->table('alt')
                ->select()
                ->sql()
        );
    }

    public function testWithRecursiveClosure(): void
    {
        $this->assertSame(
            'WITH RECURSIVE alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->builder()
                ->withRecursive([
                    'alt' => function(QueryBuilder $builder) {
                        return $builder->table('test')
                            ->select();
                    }
                ])
                ->table('alt')
                ->select()
                ->sql()
        );
    }

    public function testWithRecursiveLiteral(): void
    {
        $this->assertSame(
            'WITH RECURSIVE alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->builder()
                ->withRecursive([
                    'alt' => function(QueryBuilder $builder) {
                        return $builder->literal('(SELECT * FROM test)');
                    }
                ])
                ->table('alt')
                ->select()
                ->sql()
        );
    }

    public function testWithMerge(): void
    {
        $query1 = $this->db->builder()
            ->table('test1')
            ->select();

        $query2 = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'WITH alt1 AS (SELECT * FROM test1), alt2 AS (SELECT * FROM test2) SELECT * FROM alt1',
            $this->db->builder()
                ->with([
                    'alt1' => $query1
                ])
                ->with([
                    'alt2' => $query2
                ])
                ->table('alt1')
                ->select()
                ->sql()
        );
    }

    public function testWithOverwrite(): void
    {
        $query1 = $this->db->builder()
            ->table('test1')
            ->select();

        $query2 = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            'WITH alt2 AS (SELECT * FROM test2) SELECT * FROM alt2',
            $this->db->builder()
                ->with([
                    'alt1' => $query1
                ])
                ->with([
                    'alt2' => $query2
                ], true)
                ->table('alt2')
                ->select()
                ->sql()
        );
    }

}
