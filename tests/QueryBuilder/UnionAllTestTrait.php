<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait UnionAllTestTrait
{

    public function testUnionAll(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION ALL (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll('(SELECT * FROM test2)')
                ->sql()
        );
    }

    public function testUnionAllQueryBuilder(): void
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            '(SELECT * FROM test) UNION ALL (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll($query)
                ->sql()
        );
    }

    public function testUnionAllClosure(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION ALL (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll(function(QueryBuilder $builder) {
                    return $builder->table('test2')
                        ->select();
                })
                ->sql()
        );
    }

    public function testUnionAllLiteral(): void
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            '(SELECT * FROM test) UNION ALL (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll(function(QueryBuilder $builder) {
                    return $builder->literal('(SELECT * FROM test2)');
                })
                ->sql()
        );
    }

    public function testUnionAllMerge(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION ALL (SELECT * FROM test2) UNION ALL (SELECT * FROM test3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll('(SELECT * FROM test2)')
                ->unionAll('(SELECT * FROM test3)')
                ->sql()
        );
    }

    public function testUnionAllOverwrite(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) UNION ALL (SELECT * FROM test3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->unionAll('(SELECT * FROM test2)')
                ->unionAll('(SELECT * FROM test3)', true)
                ->sql()
        );
    }

}
