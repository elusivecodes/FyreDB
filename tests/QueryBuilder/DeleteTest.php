<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

trait DeleteTest
{

    public function testQueryBuilderDelete()
    {
        $this->assertEquals(
            'DELETE FROM test USING test',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->sql()
        );
    }

    public function testQueryBuilderDeleteAlias()
    {
        $this->assertEquals(
            'DELETE FROM test AS alt USING alt',
            $this->db->builder()
                ->table([
                    'alt' => 'test'
                ])
                ->delete()
                ->sql()
        );
    }

    public function testQueryBuilderDeleteMultipleTables()
    {
        $this->assertEquals(
            'DELETE FROM test AS alt, test2 AS alt2 USING alt, alt2',
            $this->db->builder()
                ->table([
                    'alt' => 'test',
                    'alt2' => 'test2'
                ])
                ->delete()
                ->sql()
        );
    }

    public function testQueryBuilderDeleteJoin()
    {
        $this->assertEquals(
            'DELETE FROM test USING test INNER JOIN test2 ON test2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id = test.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testQueryBuilderDeleteWhere()
    {
        $this->assertEquals(
            'DELETE FROM test USING test WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testQueryBuilderDeleteOrderBy()
    {
        $this->assertEquals(
            'DELETE FROM test USING test ORDER BY id ASC',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->orderBy('id ASC')
                ->sql()
        );
    }

    public function testQueryBuilderDeleteOrderByArray()
    {
        $this->assertEquals(
            'DELETE FROM test USING test ORDER BY id ASC, value DESC',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->orderBy([
                    'id' => 'ASC',
                    'value' => 'DESC'
                ])
                ->sql()
        );
    }

    public function testQueryBuilderDeleteLimit()
    {
        $this->assertEquals(
            'DELETE FROM test USING test LIMIT 1',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->limit(1)
                ->sql()
        );
    }

    public function testQueryBuilderDeleteOffset()
    {
        $this->assertEquals(
            'DELETE FROM test USING test LIMIT 10, 20',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->limit(20, 10)
                ->sql()
        );
    }

    public function testQueryBuilderDeleteFull()
    {
        $this->assertEquals(
            'DELETE FROM test USING test INNER JOIN test2 ON test2.id = test.id WHERE test.name = "test" ORDER BY test.id ASC LIMIT 10, 20',
            $this->db->builder()
                ->table('test')
                ->delete()
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
                ->limit(20, 10)
                ->sql()
        );
    }

}
