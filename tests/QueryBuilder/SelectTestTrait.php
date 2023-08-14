<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait SelectTestTrait
{

    public function testSelect(): void
    {
        $this->assertSame(
            'SELECT * FROM test',
            $this->db->builder()
                ->table('test')
                ->select()
                ->sql()
        );
    }

    public function testSelectAlias(): void
    {
        $this->assertSame(
            'SELECT * FROM test AS alt',
            $this->db->builder()
                ->table([
                    'alt' => 'test'
                ])
                ->select()
                ->sql()
        );
    }

    public function testSelectMultipleTables(): void
    {
        $this->assertSame(
            'SELECT * FROM test AS alt, test2 AS alt2',
            $this->db->builder()
                ->table([
                    'alt' => 'test',
                    'alt2' => 'test2'
                ])
                ->select()
                ->sql()
        );
    }

    public function testSelectQueryBuilder(): void
    {
        $this->assertSame(
            'SELECT * FROM (SELECT * FROM test) AS alt',
            $this->db->builder()
                ->table([
                    'alt' => $this->db->builder()
                        ->table('test')
                        ->select()
                ])
                ->select()
                ->sql()
        );
    }

    public function testSelectClosure(): void
    {
        $this->assertSame(
            'SELECT * FROM (SELECT * FROM test) AS alt',
            $this->db->builder()
                ->table([
                    'alt' => function(QueryBuilder $builder) {
                        return $builder->table('test')
                            ->select();
                    }
                ])
                ->select()
                ->sql()
        );
    }

    public function testSelectLiteral(): void
    {
        $this->assertSame(
            'SELECT * FROM (SELECT * FROM test) AS alt',
            $this->db->builder()
                ->table([
                    'alt' => function(QueryBuilder $builder) {
                        return $builder->literal('(SELECT * FROM test)');
                    }
                ])
                ->select()
                ->sql()
        );
    }

    public function testSelectWithoutTable(): void
    {
        $this->assertSame(
            'SELECT *',
            $this->db->builder()
                ->select()
                ->sql()
        );
    }

    public function testSelectFields(): void
    {
        $this->assertSame(
            'SELECT id, name FROM test',
            $this->db->builder()
                ->table('test')
                ->select('id, name')
                ->sql()
        );
    }

    public function testSelectFieldsArray(): void
    {
        $this->assertSame(
            'SELECT id, name FROM test',
            $this->db->builder()
                ->table('test')
                ->select([
                    'id',
                    'name'
                ])
                ->sql()
        );
    }

    public function testSelectFieldsAs(): void
    {
        $this->assertSame(
            'SELECT name AS alt FROM test',
            $this->db->builder()
                ->table('test')
                ->select([
                    'alt' => 'name'
                ])
                ->sql()
        );
    }

    public function testSelectFieldsQueryBuilder(): void
    {
        $this->assertSame(
            'SELECT (SELECT name FROM test) AS alt FROM test',
            $this->db->builder()
                ->table('test')
                ->select([
                    'alt' => $this->db->builder()
                        ->table('test')
                        ->select(['name'])
                ])
                ->sql()
        );
    }

    public function testSelectFieldsClosure(): void
    {
        $this->assertSame(
            'SELECT (SELECT name FROM test LIMIT 1) AS alt FROM test',
            $this->db->builder()
                ->table('test')
                ->select([
                    'alt' => function(QueryBuilder $builder) {
                        return $builder
                            ->table('test')
                            ->select(['name'])
                            ->limit(1);
                    }
                ])
                ->sql()
        );
    }

    public function testSelectFieldsLiteral(): void
    {
        $this->assertSame(
            'SELECT UPPER(test) AS alt FROM test',
            $this->db->builder()
                ->table('test')
                ->select([
                    'alt' => function(QueryBuilder $builder) {
                        return $builder->literal('UPPER(test)');
                    }
                ])
                ->sql()
        );
    }

    public function testSelectDistinct(): void
    {
        $this->assertSame(
            'SELECT DISTINCT * FROM test',
            $this->db->builder()
                ->table('test')
                ->select()
                ->distinct()
                ->sql()
        );
    }

    public function testSelectEpilog(): void
    {
        $this->assertSame(
            'SELECT * FROM test FOR UPDATE',
            $this->db->builder()
                ->table('test')
                ->select()
                ->epilog('FOR UPDATE')
                ->sql()
        );
    }

    public function testSelectGroupBy(): void
    {
        $this->assertSame(
            'SELECT * FROM test GROUP BY id',
            $this->db->builder()
                ->table('test')
                ->select()
                ->groupBy('id')
                ->sql()
        );
    }

    public function testSelectGroupByArray(): void
    {
        $this->assertSame(
            'SELECT * FROM test GROUP BY id, name',
            $this->db->builder()
                ->table('test')
                ->select()
                ->groupBy([
                    'id',
                    'name'
                ])
                ->sql()
        );
    }

    public function testSelectOrderBy(): void
    {
        $this->assertSame(
            'SELECT * FROM test ORDER BY id ASC',
            $this->db->builder()
                ->table('test')
                ->select()
                ->orderBy('id ASC')
                ->sql()
        );
    }

    public function testSelectOrderByArray(): void
    {
        $this->assertSame(
            'SELECT * FROM test ORDER BY id ASC, value DESC',
            $this->db->builder()
                ->table('test')
                ->select()
                ->orderBy([
                    'id' => 'ASC',
                    'value' => 'DESC'
                ])
                ->sql()
        );
    }

    public function testSelectLimit(): void
    {
        $this->assertSame(
            'SELECT * FROM test LIMIT 1',
            $this->db->builder()
                ->table('test')
                ->select()
                ->limit(1)
                ->sql()
        );
    }

    public function testSelectLimitWithOffset(): void
    {
        $this->assertSame(
            'SELECT * FROM test LIMIT 10, 20',
            $this->db->builder()
                ->table('test')
                ->select()
                ->limit(20, 10)
                ->sql()
        );
    }

    public function testSelectOffset(): void
    {
        $this->assertSame(
            'SELECT * FROM test LIMIT 10, 20',
            $this->db->builder()
                ->table('test')
                ->select()
                ->limit(20)
                ->offset(10)
                ->sql()
        );
    }

    public function testSelectFull(): void
    {
        $this->assertSame(
            'SELECT DISTINCT test.id, test.name FROM test INNER JOIN test2 ON test2.id = test.id WHERE test.name = \'test\' GROUP BY test.id ORDER BY test.id ASC HAVING value = 1 LIMIT 10, 20 FOR UPDATE',
            $this->db->builder()
                ->table('test')
                ->select([
                    'test.id',
                    'test.name'
                ])
                ->distinct()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id = test.id'
                        ]
                    ]
                ])
                ->where([
                    'test.name' => 'test'
                ])
                ->orderBy([
                    'test.id' => 'ASC'
                ])
                ->groupBy([
                    'test.id'
                ])
                ->having([
                    'value' => 1
                ])
                ->limit(20, 10)
                ->epilog('FOR UPDATE')
                ->sql()
        );
    }

    public function testSelectMerge(): void
    {
        $this->assertSame(
            'SELECT id, name FROM test',
            $this->db->builder()
                ->table('test')
                ->select('id')
                ->select('name')
                ->sql()
        );
    }

    public function testSelectOverwrite(): void
    {
        $this->assertSame(
            'SELECT name FROM test',
            $this->db->builder()
                ->table('test')
                ->select('id')
                ->select('name', true)
                ->sql()
        );
    }

    public function testSelectTableMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM test AS alt, test2 AS alt2',
            $this->db->builder()
                ->table([
                    'alt' => 'test'
                ])
                ->table([
                    'alt2' => 'test2'
                ])
                ->select()
                ->sql()
        );
    }

    public function testSelectTableOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM test2 AS alt2',
            $this->db->builder()
                ->table([
                    'alt' => 'test'
                ])
                ->table([
                    'alt2' => 'test2'
                ], true)
                ->select()
                ->sql()
        );
    }

    public function testSelectOrderByMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM test ORDER BY id ASC, value DESC',
            $this->db->builder()
                ->table('test')
                ->select()
                ->orderBy([
                    'id' => 'ASC'
                ])
                ->orderBy([
                    'value' => 'DESC'
                ])
                ->sql()
        );
    }

    public function testSelectOrderByOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM test ORDER BY value DESC',
            $this->db->builder()
                ->table('test')
                ->select()
                ->orderBy([
                    'id' => 'ASC'
                ])
                ->orderBy([
                    'value' => 'DESC'
                ], true)
                ->sql()
        );
    }

    public function testSelectGroupByMerge(): void
    {
        $this->assertSame(
            'SELECT * FROM test GROUP BY id, name',
            $this->db->builder()
                ->table('test')
                ->select()
                ->groupBy('id')
                ->groupBy('name')
                ->sql()
        );
    }

    public function testSelectGroupByOverwrite(): void
    {
        $this->assertSame(
            'SELECT * FROM test GROUP BY name',
            $this->db->builder()
                ->table('test')
                ->select()
                ->groupBy('id')
                ->groupBy('name', true)
                ->sql()
        );
    }

}
