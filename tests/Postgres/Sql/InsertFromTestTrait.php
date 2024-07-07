<?php
declare(strict_types=1);

namespace Tests\Postgres\Sql;

use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait InsertFromTestTrait
{
    public function testInsertFromClosure(): void
    {
        $this->assertSame(
            'INSERT INTO "test" VALUES (SELECT * FROM "test2")',
            $this->db->insertFrom(function(Connection $db): SelectQuery {
                return $db->select()
                    ->from('test2');
            })
                ->into('test')
                ->sql()
        );
    }

    public function testInsertFromColumnsClosure(): void
    {
        $this->assertSame(
            'INSERT INTO "test" ("id", "name") VALUES (SELECT * FROM "test2")',
            $this->db->insertFrom(function(Connection $db): SelectQuery {
                return $db->select()
                    ->table('test2');
            }, ['id', 'name'])
                ->into('test')
                ->sql()
        );
    }

    public function testInsertFromColumnsLiteral(): void
    {
        $this->assertSame(
            'INSERT INTO "test" ("id", "name") VALUES (SELECT * FROM test2)',
            $this->db->insertFrom(function(Connection $db): QueryLiteral {
                return $db->literal('(SELECT * FROM test2)');
            }, ['id', 'name'])
                ->into('test')
                ->sql()
        );
    }

    public function testInsertFromColumnsSelectQuery(): void
    {
        $query = $this->db->select()
            ->from('test2');

        $this->assertSame(
            'INSERT INTO "test" ("id", "name") VALUES (SELECT * FROM "test2")',
            $this->db->insertFrom($query, ['id', 'name'])
                ->into('test')
                ->sql()
        );
    }

    public function testInsertFromColumnsString(): void
    {
        $this->assertSame(
            'INSERT INTO "test" ("id", "name") VALUES (SELECT * FROM test2)',
            $this->db->insertFrom('(SELECT * FROM test2)', ['id', 'name'])
                ->into('test')
                ->sql()
        );
    }

    public function testInsertFromLiteral(): void
    {
        $this->assertSame(
            'INSERT INTO "test" VALUES (SELECT * FROM test2)',
            $this->db->insertFrom(function(Connection $db): QueryLiteral {
                return $db->literal('(SELECT * FROM test2)');
            })
                ->into('test')
                ->sql()
        );
    }

    public function testInsertFromSelectQuery(): void
    {
        $query = $this->db->select()
            ->from('test2');

        $this->assertSame(
            'INSERT INTO "test" VALUES (SELECT * FROM "test2")',
            $this->db->insertFrom($query)
                ->into('test')
                ->sql()
        );
    }

    public function testInsertFromString(): void
    {
        $this->assertSame(
            'INSERT INTO "test" ("id", "name") VALUES (SELECT * FROM test2)',
            $this->db->insertFrom('(SELECT * FROM test2)', ['id', 'name'])
                ->into('test')
                ->sql()
        );
    }
}
