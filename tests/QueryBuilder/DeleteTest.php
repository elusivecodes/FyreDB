<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

trait DeleteTest
{

    public function testDelete()
    {
        $this->assertSame(
            'DELETE FROM test USING test',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->sql()
        );
    }

    public function testDeleteAlias()
    {
        $this->assertSame(
            'DELETE FROM test AS alt USING alt',
            $this->db->builder()
                ->table([
                    'alt' => 'test'
                ])
                ->delete()
                ->sql()
        );
    }

    public function testDeleteMultipleTables()
    {
        $this->assertSame(
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

    public function testDeleteJoin()
    {
        $this->assertSame(
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

    public function testDeleteWhere()
    {
        $this->assertSame(
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

    public function testDeleteOrderBy()
    {
        $this->assertSame(
            'DELETE FROM test USING test ORDER BY id ASC',
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

    public function testDeleteLimit()
    {
        $this->assertSame(
            'DELETE FROM test USING test LIMIT 1',
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
            'DELETE FROM test USING test LIMIT 10, 20',
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
