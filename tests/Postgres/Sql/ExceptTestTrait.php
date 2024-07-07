<?php
declare(strict_types=1);

namespace Tests\Postgres\Sql;

use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait ExceptTestTrait
{
    public function testExcept(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") EXCEPT (SELECT * FROM test2)',
            $this->db->select()
                ->from('test')
                ->except('(SELECT * FROM test2)')
                ->sql()
        );
    }

    public function testExceptClosure(): void
    {
        $query = $this->db->select()
            ->from('test2');

        $this->assertSame(
            '(SELECT * FROM "test") EXCEPT (SELECT * FROM "test2")',
            $this->db->select()
                ->from('test')
                ->except(function(Connection $db): SelectQuery {
                    return $db->select()
                        ->from('test2');
                })
                ->sql()
        );
    }

    public function testExceptLiteral(): void
    {
        $query = $this->db->select()
            ->from('test2');

        $this->assertSame(
            '(SELECT * FROM "test") EXCEPT (SELECT * FROM test2)',
            $this->db->select()
                ->from('test')
                ->except(function(Connection $db): QueryLiteral {
                    return $db->literal('(SELECT * FROM test2)');
                })
                ->sql()
        );
    }

    public function testExceptMerge(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") EXCEPT (SELECT * FROM test2) EXCEPT (SELECT * FROM test3)',
            $this->db->select()
                ->from('test')
                ->except('(SELECT * FROM test2)')
                ->except('(SELECT * FROM test3)')
                ->sql()
        );
    }

    public function testExceptOverwrite(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") EXCEPT (SELECT * FROM test3)',
            $this->db->select()
                ->from('test')
                ->except('(SELECT * FROM test2)')
                ->except('(SELECT * FROM test3)', true)
                ->sql()
        );
    }

    public function testExceptSelectQuery(): void
    {
        $query = $this->db->select()
            ->from('test2');

        $this->assertSame(
            '(SELECT * FROM "test") EXCEPT (SELECT * FROM "test2")',
            $this->db->select()
                ->from('test')
                ->except($query)
                ->sql()
        );
    }
}
