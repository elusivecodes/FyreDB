<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

trait DeleteTest
{

    public function testDelete()
    {
        $this->assertSame(
            'DELETE FROM test',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->sql()
        );
    }

    public function testDeleteTables()
    {
        $this->assertSame(
            'DELETE FROM test AS alt',
            $this->db->builder()
                ->table([
                    'alt' => 'test'
                ])
                ->delete()
                ->sql()
        );
    }

    public function testDeleteAlias()
    {
        $this->assertSame(
            'DELETE alt FROM test AS alt',
            $this->db->builder()
                ->table([
                    'alt' => 'test'
                ])
                ->delete('alt')
                ->sql()
        );
    }


    public function testDeleteMultipleAliases()
    {
        $this->assertSame(
            'DELETE alt1, alt2 FROM test AS alt1 LEFT JOIN test2 AS alt2 ON alt2.id = alt.id',
            $this->db->builder()
                ->table([
                    'alt1' => 'test'
                ])
                ->delete([
                    'alt1',
                    'alt2'
                ])
                ->join([
                    'alt2' => [
                        'table' => 'test2',
                        'type' => 'LEFT',
                        'conditions' => [
                            'alt2.id = alt.id'
                        ]
                    ]
                ])
                ->sql()
        );
    }

    public function testDeleteMultipleTables()
    {
        $this->assertSame(
            'DELETE alt, alt2 FROM test AS alt, test2 AS alt2',
            $this->db->builder()
                ->table([
                    'alt' => 'test',
                    'alt2' => 'test2'
                ])
                ->delete()
                ->sql()
        );
    }

    public function testDeleteJoin()
    {
        $this->assertSame(
            'DELETE FROM test INNER JOIN test2 ON test2.id = test.id',
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

    public function testDeleteWhere()
    {
        $this->assertSame(
            'DELETE FROM test WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testDeleteOrderBy()
    {
        $this->assertSame(
            'DELETE FROM test ORDER BY id ASC',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->orderBy('id ASC')
                ->sql()
        );
    }

    public function testDeleteOrderByArray()
    {
        $this->assertSame(
            'DELETE FROM test ORDER BY id ASC, value DESC',
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

    public function testDeleteLimit()
    {
        $this->assertSame(
            'DELETE FROM test LIMIT 1',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->limit(1)
                ->sql()
        );
    }

    public function testDeleteOffset()
    {
        $this->assertSame(
            'DELETE FROM test LIMIT 10, 20',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->limit(20, 10)
                ->sql()
        );
    }

    public function testDeleteFull()
    {
        $this->assertSame(
            'DELETE FROM test INNER JOIN test2 ON test2.id = test.id WHERE test.name = \'test\' ORDER BY test.id ASC LIMIT 10, 20',
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
