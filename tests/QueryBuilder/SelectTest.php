<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait SelectTest
{

    public function testSelect()
    {
        $this->assertSame(
            'SELECT * FROM test',
            $this->db->builder()
                ->table('test')
                ->select()
                ->sql()
        );
    }

    public function testSelectAlias()
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

    public function testSelectMultipleTables()
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

    public function testSelectMultipleTableCalls()
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

    public function testSelectQueryBuilder()
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

    public function testSelectClosure()
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

    public function testSelectLiteral()
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

    public function testSelectWithoutTable()
    {
        $this->assertSame(
            'SELECT *',
            $this->db->builder()
                ->select()
                ->sql()
        );
    }

    public function testSelectFields()
    {
        $this->assertSame(
            'SELECT id, name FROM test',
            $this->db->builder()
                ->table('test')
                ->select('id, name')
                ->sql()
        );
    }

    public function testSelectFieldsArray()
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

    public function testSelectFieldsAs()
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

    public function testSelectFieldsQueryBuilder()
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

    public function testSelectFieldsClosure()
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

    public function testSelectFieldsLiteral()
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

    public function testSelectFieldsMultipleCalls()
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

    public function testSelectDistinct()
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

    public function testSelectEpilog()
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

    public function testSelectGroupBy()
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

    public function testSelectGroupByArray()
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

    public function testSelectGroupByMultipleCalls()
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

    public function testSelectOrderBy()
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

    public function testSelectOrderByArray()
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

    public function testSelectOrderByMultipleCalls()
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

    public function testSelectLimit()
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

    public function testSelectOffset()
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

    public function testSelectFull()
    {
        $this->assertSame(
            'SELECT DISTINCT test.id, test.name FROM test INNER JOIN test2 ON test2.id = test.id WHERE test.name = "test" ORDER BY test.id ASC GROUP BY test.id HAVING value = 1 LIMIT 10, 20 FOR UPDATE',
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

}
