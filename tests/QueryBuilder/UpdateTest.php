<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use
    Fyre\DB\QueryBuilder;

trait UpdateTest
{

    public function testUpdate()
    {
        $this->assertSame(
            'UPDATE test SET value = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateAlias()
    {
        $this->assertSame(
            'UPDATE test AS alt SET value = 1',
            $this->db->builder()
                ->table([
                    'alt' => 'test'
                ])
                ->update([
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateMultipleTables()
    {
        $this->assertSame(
            'UPDATE test AS alt, test2 AS alt2 SET alt.value = 1, alt2.value = 2',
            $this->db->builder()
                ->table([
                    'alt' => 'test',
                    'alt2' => 'test2'
                ])
                ->update([
                    'alt.value' => 1,
                    'alt2.value' => 2
                ])
                ->sql()
        );
    }

    public function testUpdateJoin()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1 INNER JOIN test2 ON test2.id = test.id',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => 1
                ])
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

    public function testUpdateWhere()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1 WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => 1
                ])
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateQueryBuilder()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = (SELECT id FROM test LIMIT 1) WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => $this->db->builder()
                        ->table('test')
                        ->select(['id'])
                        ->limit(1)
                ])
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateClosure()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = (SELECT id FROM test LIMIT 1) WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => function(QueryBuilder $builder) {
                        return $builder
                            ->table('test')
                            ->select(['id'])
                            ->limit(1);
                    }
                ])
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateLiteral()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 2 * 10 WHERE id = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => function(QueryBuilder $builder) {
                        return $builder->literal('2 * 10');
                    }
                ])
                ->where([
                    'id' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateFull()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1 INNER JOIN test2 ON test2.id = test.id WHERE test.name = \'test\'',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test',
                    'value' => 1
                ])
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
                ->sql()
        );
    }

    public function testUpdateMerge()
    {
        $this->assertSame(
            'UPDATE test SET name = \'Test\', value = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test'
                ])
                ->update([
                    'value' => 1
                ])
                ->sql()
        );
    }

    public function testUpdateOverwrite()
    {
        $this->assertSame(
            'UPDATE test SET value = 1',
            $this->db->builder()
                ->table('test')
                ->update([
                    'name' => 'Test'
                ])
                ->update([
                    'value' => 1
                ], true)
                ->sql()
        );
    }

    public function testUpdateWith()
    {
        $query = $this->db->builder()
            ->table('test')
            ->select();

        $this->assertSame(
            'WITH alt AS (SELECT * FROM test) UPDATE alt SET value = 1',
            $this->db->builder()
                ->with([
                    'alt' => $query
                ])
                ->table('alt')
                ->update([
                    'value' => 1
                ])
                ->sql()
        );
    }

}
