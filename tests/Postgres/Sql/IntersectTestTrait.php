<?php
declare(strict_types=1);

namespace Tests\Postgres\Sql;

use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait IntersectTestTrait
{
    public function testIntersect(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") INTERSECT (SELECT * FROM test2)',
            $this->db->select()
                ->from('test')
                ->intersect('(SELECT * FROM test2)')
                ->sql()
        );
    }

    public function testIntersectClosure(): void
    {
        $query = $this->db->select()
            ->from('test2');

        $this->assertSame(
            '(SELECT * FROM "test") INTERSECT (SELECT * FROM "test2")',
            $this->db->select()
                ->from('test')
                ->intersect(function(Connection $db): SelectQuery {
                    return $db->select()
                        ->from('test2');
                })
                ->sql()
        );
    }

    public function testIntersectLiteral(): void
    {
        $query = $this->db->select()
            ->from('test2');

        $this->assertSame(
            '(SELECT * FROM "test") INTERSECT (SELECT * FROM test2)',
            $this->db->select()
                ->from('test')
                ->intersect(function(Connection $db): QueryLiteral {
                    return $db->literal('(SELECT * FROM test2)');
                })
                ->sql()
        );
    }

    public function testIntersectMerge(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") INTERSECT (SELECT * FROM test2) INTERSECT (SELECT * FROM test3)',
            $this->db->select()
                ->from('test')
                ->intersect('(SELECT * FROM test2)')
                ->intersect('(SELECT * FROM test3)')
                ->sql()
        );
    }

    public function testIntersectOverwrite(): void
    {
        $this->assertSame(
            '(SELECT * FROM "test") INTERSECT (SELECT * FROM test3)',
            $this->db->select()
                ->from('test')
                ->intersect('(SELECT * FROM test2)')
                ->intersect('(SELECT * FROM test3)', true)
                ->sql()
        );
    }

    public function testIntersectSelectQuery(): void
    {
        $query = $this->db->select()
            ->from('test2');

        $this->assertSame(
            '(SELECT * FROM "test") INTERSECT (SELECT * FROM "test2")',
            $this->db->select()
                ->from('test')
                ->intersect($query)
                ->sql()
        );
    }
}
