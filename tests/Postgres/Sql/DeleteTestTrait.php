<?php
declare(strict_types=1);

namespace Tests\Postgres\Sql;

trait DeleteTestTrait
{
    public function testDelete(): void
    {
        $this->assertSame(
            'DELETE FROM "test"',
            $this->db->delete()
                ->from('test')
                ->sql()
        );
    }

    public function testDeleteAlias(): void
    {
        $this->assertSame(
            'DELETE "alt" FROM "test" AS "alt"',
            $this->db->delete('alt')
                ->from([
                    'alt' => 'test',
                ])
                ->sql()
        );
    }

    public function testDeleteFull(): void
    {
        $this->assertSame(
            'DELETE FROM "test" INNER JOIN "test2" ON "test2"."id" = "test"."id" WHERE "test"."name" = \'test\' ORDER BY "test"."id" ASC LIMIT 20',
            $this->db->delete()
                ->from('test')
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
                ->limit(20)
                ->sql()
        );
    }

    public function testDeleteJoin(): void
    {
        $this->assertSame(
            'DELETE FROM "test" INNER JOIN "test2" ON "test2"."id" = "test"."id"',
            $this->db->delete()
                ->from('test')
                ->join([
                    [
                        'table' => 'test2',
                        'conditions' => [
                            'test2.id = test.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testDeleteLimit(): void
    {
        $this->assertSame(
            'DELETE FROM "test" LIMIT 1',
            $this->db->delete()
                ->from('test')
                ->limit(1)
                ->sql()
        );
    }

    public function testDeleteMerge(): void
    {
        $this->assertSame(
            'DELETE "alt1", "alt2" FROM "test" AS "alt1" LEFT JOIN "test2" AS "alt2" ON "alt2"."id" = "alt"."id"',
            $this->db->delete()
                ->alias('alt1')
                ->alias('alt2')
                ->from([
                    'alt1' => 'test',
                ])
                ->join([
                    'alt2' => [
                        'table' => 'test2',
                        'type' => 'LEFT',
                        'conditions' => [
                            'alt2.id = alt.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testDeleteMultipleAliases(): void
    {
        $this->assertSame(
            'DELETE "alt1", "alt2" FROM "test" AS "alt1" LEFT JOIN "test2" AS "alt2" ON "alt2"."id" = "alt"."id"',
            $this->db->delete()
                ->alias([
                    'alt1',
                    'alt2',
                ])
                ->from([
                    'alt1' => 'test',
                ])
                ->join([
                    'alt2' => [
                        'table' => 'test2',
                        'type' => 'LEFT',
                        'conditions' => [
                            'alt2.id = alt.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testDeleteMultipleTables(): void
    {
        $this->assertSame(
            'DELETE "alt", "alt2" FROM "test" AS "alt", "test2" AS "alt2"',
            $this->db->delete()
                ->from([
                    'alt' => 'test',
                    'alt2' => 'test2',
                ])
                ->sql()
        );
    }

    public function testDeleteOrderBy(): void
    {
        $this->assertSame(
            'DELETE FROM "test" ORDER BY id ASC',
            $this->db->delete()
                ->from('test')
                ->orderBy('id ASC')
                ->sql()
        );
    }

    public function testDeleteOrderByArray(): void
    {
        $this->assertSame(
            'DELETE FROM "test" ORDER BY "id" ASC, "value" DESC',
            $this->db->delete()
                ->from('test')
                ->orderBy([
                    'id' => 'ASC',
                    'value' => 'DESC',
                ])
                ->sql()
        );
    }

    public function testDeleteOverwrite(): void
    {
        $this->assertSame(
            'DELETE "alt2" FROM "test" AS "alt1" LEFT JOIN "test2" AS "alt2" ON "alt2"."id" = "alt"."id"',
            $this->db->delete()
                ->alias('alt1')
                ->alias('alt2', true)
                ->from([
                    'alt1' => 'test',
                ])
                ->join([
                    'alt2' => [
                        'table' => 'test2',
                        'type' => 'LEFT',
                        'conditions' => [
                            'alt2.id = alt.id',
                        ],
                    ],
                ])
                ->sql()
        );
    }

    public function testDeleteTables(): void
    {
        $this->assertSame(
            'DELETE FROM "test" AS "alt"',
            $this->db->delete()
                ->from([
                    'alt' => 'test',
                ])
                ->sql()
        );
    }

    public function testDeleteWhere(): void
    {
        $this->assertSame(
            'DELETE FROM "test" WHERE "id" = 1',
            $this->db->delete()
                ->from('test')
                ->where([
                    'id' => 1,
                ])
                ->sql()
        );
    }
}
