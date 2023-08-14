<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

trait DeleteTestTrait
{

    public function testDelete(): void
    {
        $this->assertSame(
            'DELETE FROM test',
            $this->db->builder()
                ->table('test')
                ->delete()
                ->sql()
        );
    }

    public function testDeleteTables(): void
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

    public function testDeleteAlias(): void
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


    public function testDeleteMultipleAliases(): void
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

    public function testDeleteMultipleTables(): void
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

    public function testDeleteJoin(): void
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

    public function testDeleteWhere(): void
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

    public function testDeleteOrderBy(): void
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

    public function testDeleteOrderByArray(): void
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

    public function testDeleteLimit(): void
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

    public function testDeleteOffset(): void
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

    public function testDeleteFull(): void
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

    public function testDeleteMerge(): void
    {
        $this->assertSame(
            'DELETE alt1, alt2 FROM test AS alt1 LEFT JOIN test2 AS alt2 ON alt2.id = alt.id',
            $this->db->builder()
                ->table([
                    'alt1' => 'test'
                ])
                ->delete('alt1')
                ->delete('alt2')
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

    public function testDeleteOverwrite(): void
    {
        $this->assertSame(
            'DELETE alt2 FROM test AS alt1 LEFT JOIN test2 AS alt2 ON alt2.id = alt.id',
            $this->db->builder()
                ->table([
                    'alt1' => 'test'
                ])
                ->delete('alt1')
                ->delete('alt2', true)
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

    public function testDeleteWith(): void
    {
        $query = $this->db->builder()
            ->table('test')
            ->select();

        $this->assertSame(
            'WITH alt AS (SELECT * FROM test) DELETE FROM alt',
            $this->db->builder()
                ->with([
                    'alt' => $query
                ])
                ->table('alt')
                ->delete()
                ->sql()
        );
    }

}
