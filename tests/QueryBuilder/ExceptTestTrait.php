<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait ExceptTestTrait
{

    public function testExcept(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) EXCEPT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except('(SELECT * FROM test2)')
                ->sql()
        );
    }

    public function testExceptQueryBuilder(): void
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            '(SELECT * FROM test) EXCEPT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except($query)
                ->sql()
        );
    }

    public function testExceptClosure(): void
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            '(SELECT * FROM test) EXCEPT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except(function(QueryBuilder $builder) {
                    return $builder->table('test2')
                        ->select();
                })
                ->sql()
        );
    }

    public function testExceptLiteral(): void
    {
        $query = $this->db->builder()
            ->table('test2')
            ->select();

        $this->assertSame(
            '(SELECT * FROM test) EXCEPT (SELECT * FROM test2)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except(function(QueryBuilder $builder) {
                    return $builder->literal('(SELECT * FROM test2)');
                })
                ->sql()
        );
    }

    public function testExceptMerge(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) EXCEPT (SELECT * FROM test2) EXCEPT (SELECT * FROM test3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except('(SELECT * FROM test2)')
                ->except('(SELECT * FROM test3)')
                ->sql()
        );
    }

    public function testExceptOverwrite(): void
    {
        $this->assertSame(
            '(SELECT * FROM test) EXCEPT (SELECT * FROM test3)',
            $this->db->builder()
                ->table('test')
                ->select()
                ->except('(SELECT * FROM test2)')
                ->except('(SELECT * FROM test3)', true)
                ->sql()
        );
    }

}
