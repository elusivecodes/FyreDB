<?php
declare(strict_types=1);

namespace Tests\Sqlite\Sql;

use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait WithTestTrait
{
    public function testWithClosure(): void
    {
        $this->assertSame(
            'WITH alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->select()
                ->with([
                    'alt' => function(Connection $db): SelectQuery {
                        return $db->select()
                            ->from('test');
                    },
                ])
                ->from('alt')
                ->sql()
        );
    }

    public function testWithLiteral(): void
    {
        $this->assertSame(
            'WITH alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->select()
                ->with([
                    'alt' => function(Connection $db): QueryLiteral {
                        return $db->literal('(SELECT * FROM test)');
                    },
                ])
                ->from('alt')
                ->sql()
        );
    }

    public function testWithMerge(): void
    {
        $query1 = $this->db->select()
            ->from('test1');

        $query2 = $this->db->select()
            ->from('test2');

        $this->assertSame(
            'WITH alt1 AS (SELECT * FROM test1), alt2 AS (SELECT * FROM test2) SELECT * FROM alt1',
            $this->db->select()
                ->with([
                    'alt1' => $query1,
                ])
                ->with([
                    'alt2' => $query2,
                ])
                ->from('alt1')
                ->sql()
        );
    }

    public function testWithOverwrite(): void
    {
        $query1 = $this->db->select()
            ->from('test1');

        $query2 = $this->db->select()
            ->from('test2');

        $this->assertSame(
            'WITH alt2 AS (SELECT * FROM test2) SELECT * FROM alt2',
            $this->db->select()
                ->with([
                    'alt1' => $query1,
                ])
                ->with([
                    'alt2' => $query2,
                ], true)
                ->from('alt2')
                ->sql()
        );
    }

    public function testWithRecursiveClosure(): void
    {
        $this->assertSame(
            'WITH RECURSIVE alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->select()
                ->withRecursive([
                    'alt' => function(Connection $db): SelectQuery {
                        return $db->select()
                            ->from('test');
                    },
                ])
                ->from('alt')
                ->sql()
        );
    }

    public function testWithRecursiveLiteral(): void
    {
        $this->assertSame(
            'WITH RECURSIVE alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->select()
                ->withRecursive([
                    'alt' => function(Connection $db): QueryLiteral {
                        return $db->literal('(SELECT * FROM test)');
                    },
                ])
                ->from('alt')
                ->sql()
        );
    }

    public function testWithRecursiveSelectQuery(): void
    {
        $query = $this->db->select()
            ->from('test');

        $this->assertSame(
            'WITH RECURSIVE alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->select()
                ->withRecursive([
                    'alt' => $query,
                ])
                ->from('alt')
                ->sql()
        );
    }

    public function testWithSelectQuery(): void
    {
        $query = $this->db->select()
            ->from('test');

        $this->assertSame(
            'WITH alt AS (SELECT * FROM test) SELECT * FROM alt',
            $this->db->select()
                ->with([
                    'alt' => $query,
                ])
                ->from('alt')
                ->sql()
        );
    }
}
