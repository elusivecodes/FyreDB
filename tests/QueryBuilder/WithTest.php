<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait WithTest
{

    public function testWithQueryBuilder()
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

    public function testWithClosure()
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

    public function testWithLiteral()
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

    public function testWithRecursiveQueryBuilder()
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

    public function testWithRecursiveClosure()
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

    public function testWithRecursiveLiteral()
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

}
