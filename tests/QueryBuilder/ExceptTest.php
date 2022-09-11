<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait ExceptTest
{

    public function testExcept()
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

    public function testExceptQueryBuilder()
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

    public function testExceptClosure()
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

    public function testExceptLiteral()
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

    public function testExceptMerge()
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

    public function testExceptOverwrite()
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
