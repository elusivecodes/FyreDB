<?php
declare(strict_types=1);

namespace Tests\Postgres\Sql;

use Fyre\DB\Connection;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

trait SelectTestTrait
{
    public function testSelect(): void
    {
        $this->assertSame(
            'SELECT * FROM test',
            $this->db->select()
                ->from('test')
                ->sql()
        );
    }

    public function testSelectAlias(): void
    {
        $this->assertSame(
            'SELECT * FROM test AS alt',
            $this->db->select()
                ->from([
                    'alt' => 'test',
                ])
                ->sql()
        );
    }

    public function testSelectClosure(): void
    {
        $this->assertSame(
            'SELECT * FROM (SELECT * FROM test) AS alt',
            $this->db->select()
                ->from([
                    'alt' => function(Connection $db): SelectQuery {
                        return $db->select()
                            ->from('test');

                    },
                ])
                ->sql()
        );
    }

    public function testSelectDistinct(): void
    {
        $this->assertSame(
            'SELECT DISTINCT * FROM test',
            $this->db->select()
                ->from('test')
                ->distinct()
                ->sql()
        );
    }

    public function testSelectEpilog(): void
    {
        $this->assertSame(
            'SELECT * FROM test FOR UPDATE',
            $this->db->select()
                ->from('test')
                ->epilog('FOR UPDATE')
                ->sql()
        );
    }

    public function testSelectFields(): void
    {
        $this->assertSame(
            'SELECT id, name FROM test',
            $this->db->select('id, name')
                ->from('test')
                ->sql()
        );
    }

    public function testSelectFieldsArray(): void
    {
        $this->assertSame(
            'SELECT id, name FROM test',
            $this->db->select([
                'id',
                'name',
            ])
                ->from('test')
                ->sql()
        );
    }

    public function testSelectFieldsAs(): void
    {
        $this->assertSame(
            'SELECT name AS "alt" FROM test',
            $this->db->select([
                'alt' => 'name',
            ])
                ->from('test')
                ->sql()
        );
    }

    public function testSelectFieldsClosure(): void
    {
        $this->assertSame(
            'SELECT (SELECT name FROM test LIMIT 1) AS "alt" FROM test',
            $this->db->select([
                'alt' => function(Connection $db): SelectQuery {
                    return $db->select(['name'])
                        ->from('test')
                        ->limit(1);
                },
            ])
                ->from('test')
                ->sql()
        );
    }

    public function testSelectFieldsLiteral(): void
    {
        $this->assertSame(
            'SELECT UPPER(test) AS "alt" FROM test',
            $this->db->select([
                'alt' => function(Connection $db): QueryLiteral {
                    return $db->literal('UPPER(test)');
                },
            ])
                ->from('test')
                ->sql()
        );
    }

    public function testSelectFieldsSelectQuery(): void
    {
        $this->assertSame(
            'SELECT (SELECT name FROM test) AS "alt" FROM test',
            $this->db->select([
                'alt' => $this->db->select(['name'])
                    ->from('test'),
            ])
                ->from('test')
                ->sql()
        );
    }

    public function testSelectFieldsAsEscaped(): void
    {
        $this->assertSame(
            'SELECT name AS """alt""" FROM test',
            $this->db->select([
                '"alt"' => 'name',
            ])
                ->from('test')
                ->sql()
        );
    }

    public function testSelectFull(): void
    {
        $this->assertSame(
            'SELECT DISTINCT test.id, test.name FROM test INNER JOIN test2 ON test2.id = test.id WHERE test.name = \'test\' GROUP BY test.id ORDER BY test.id ASC HAVING value = 1 LIMIT 10, 20 FOR UPDATE',
            $this->db->select([
                'test.id',
                'test.name',
            ])
                ->from('test')
                ->distinct()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id = test.id',
                        ],
                    ],
                ])
                ->where([
                    'test.name' => 'test',
                ])
                ->orderBy([
                    'test.id' => 'ASC',
                ])
                ->groupBy([
                    'test.id',
                ])
                ->having([
                    'value' => 1,
                ])
                ->limit(20, 10)
                ->epilog('FOR UPDATE')
                ->sql()
        );
    }

    public function testSelectGroupBy(): void
    {
        $this->assertSame(
            'SELECT * FROM test GROUP BY id',
            $this->db->select()
                ->from('test')
                ->groupBy('id')
                ->sql()
        );
    }

    public function testSelectGroupByArray(): void
    {
        $this->assertSame(
            'SELECT * FROM test GROUP BY id, name',
            $this->db->select()
                ->from('test')
                ->groupBy([
                    'id',
                    'name',
                ])
                ->sql()
        );
    }

    public function testSelectGroupByMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM test GROUP BY id, name',
            $this->db->select()
                ->from('test')
                ->groupBy('id')
                ->groupBy('name')
                ->sql()
        );
    }

    public function testSelectGroupByOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM test GROUP BY name',
            $this->db->select()
                ->from('test')
                ->groupBy('id')
                ->groupBy('name', true)
                ->sql()
        );
    }

    public function testSelectLimit(): void
    {
        $this->assertSame(
            'SELECT * FROM test LIMIT 1',
            $this->db->select()
                ->from('test')
                ->limit(1)
                ->sql()
        );
    }

    public function testSelectLimitWithOffset(): void
    {
        $this->assertSame(
            'SELECT * FROM test LIMIT 10, 20',
            $this->db->select()
                ->from('test')
                ->limit(20, 10)
                ->sql()
        );
    }

    public function testSelectLiteral(): void
    {
        $this->assertSame(
            'SELECT * FROM (SELECT * FROM test) AS alt',
            $this->db->select()
                ->from([
                    'alt' => function(Connection $db): QueryLiteral {
                        return $db->literal('(SELECT * FROM test)');
                    },
                ])
                ->sql()
        );
    }

    public function testSelectMerge(): void
    {
        $this->assertSame(
            'SELECT id, name FROM test',
            $this->db->select('id')
                ->select('name')
                ->from('test')
                ->sql()
        );
    }

    public function testSelectMultipleTables(): void
    {
        $this->assertSame(
            'SELECT * FROM test AS alt, test2 AS alt2',
            $this->db->select()
                ->from([
                    'alt' => 'test',
                    'alt2' => 'test2',
                ])
                ->sql()
        );
    }

    public function testSelectOffset(): void
    {
        $this->assertSame(
            'SELECT * FROM test LIMIT 10, 20',
            $this->db->select()
                ->from('test')
                ->limit(20)
                ->offset(10)
                ->sql()
        );
    }

    public function testSelectOrderBy(): void
    {
        $this->assertSame(
            'SELECT * FROM test ORDER BY id ASC',
            $this->db->select()
                ->from('test')
                ->orderBy('id ASC')
                ->sql()
        );
    }

    public function testSelectOrderByArray(): void
    {
        $this->assertSame(
            'SELECT * FROM test ORDER BY id ASC, value DESC',
            $this->db->select()
                ->from('test')
                ->orderBy([
                    'id' => 'ASC',
                    'value' => 'DESC',
                ])
                ->sql()
        );
    }

    public function testSelectOrderByMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM test ORDER BY id ASC, value DESC',
            $this->db->select()
                ->from('test')
                ->orderBy([
                    'id' => 'ASC',
                ])
                ->orderBy([
                    'value' => 'DESC',
                ])
                ->sql()
        );
    }

    public function testSelectOrderByOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM test ORDER BY value DESC',
            $this->db->select()
                ->from('test')
                ->orderBy([
                    'id' => 'ASC',
                ])
                ->orderBy([
                    'value' => 'DESC',
                ], true)
                ->sql()
        );
    }

    public function testSelectOverwrite(): void
    {
        $this->assertSame(
            'SELECT name FROM test',
            $this->db->select('id')
                ->select('name', true)
                ->from('test')
                ->sql()
        );
    }

    public function testSelectSelectQuery(): void
    {
        $this->assertSame(
            'SELECT * FROM (SELECT * FROM test) AS alt',
            $this->db->select()
                ->from([
                    'alt' => $this->db->select()
                        ->from('test'),
                ])
                ->sql()
        );
    }

    public function testSelectTableMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM test AS alt, test2 AS alt2',
            $this->db->select()
                ->from([
                    'alt' => 'test',
                ])
                ->from([
                    'alt2' => 'test2',
                ])
                ->sql()
        );
    }

    public function testSelectTableOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM test2 AS alt2',
            $this->db->select()
                ->from([
                    'alt' => 'test',
                ])
                ->from([
                    'alt2' => 'test2',
                ], true)
                ->sql()
        );
    }

    public function testSelectWithoutTable(): void
    {
        $this->assertSame(
            'SELECT *',
            $this->db->select()
                ->sql()
        );
    }
}
