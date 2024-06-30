<?php
declare(strict_types=1);

namespace Tests\Mysql\Query;

trait GetTestTrait
{
    public function testGetAlias(): void
    {
        $this->assertSame(
            [
                'alt',
            ],
            $this->db->delete()
                ->alias('alt')
                ->getAlias()
        );
    }

    public function testGetData(): void
    {
        $this->assertSame(
            [
                'value' => 1,
            ],
            $this->db->update()
                ->set([
                    'value' => 1,
                ])
                ->getData()
        );
    }

    public function testGetDistinct(): void
    {
        $this->assertSame(
            true,
            $this->db->select()
                ->distinct()
                ->getDistinct()
        );
    }

    public function testGetEpilog(): void
    {
        $this->assertSame(
            'FOR UPDATE',
            $this->db->select()
                ->epilog('FOR UPDATE')
                ->getEpilog()
        );
    }

    public function testGetFrom(): void
    {
        $this->assertSame(
            [
                'test',
            ],
            $this->db->select()
                ->from('test')
                ->getFrom()
        );
    }

    public function testGetGroupBy(): void
    {
        $this->assertSame(
            [
                'value',
            ],
            $this->db->select()
                ->groupBy([
                    'value',
                ])
                ->getGroupBy()
        );
    }

    public function testGetHaving(): void
    {
        $this->assertSame(
            [
                'value' => 1,
            ],
            $this->db->select()
                ->having([
                    'value' => 1,
                ])
                ->getHaving()
        );
    }

    public function testGetInto(): void
    {
        $this->assertSame(
            'test',
            $this->db->insert()
                ->into('test')
                ->getInto()
        );
    }

    public function testGetJoin(): void
    {
        $this->assertSame(
            [
                'test2' => [
                    'table' => 'test2',
                    'using' => 'id',
                ],
            ],
            $this->db->select()
                ->join([
                    [
                        'table' => 'test2',
                        'using' => 'id',
                    ],
                ])
                ->getJoin()
        );
    }

    public function testGetLimit(): void
    {
        $this->assertSame(
            1,
            $this->db->select()
                ->limit(1)
                ->getLimit()
        );
    }

    public function testGetOffset(): void
    {
        $this->assertSame(
            1,
            $this->db->select()
                ->offset(1)
                ->getOffset()
        );
    }

    public function testGetOrderBy(): void
    {
        $this->assertSame(
            [
                'value' => 'ASC',
            ],
            $this->db->select()
                ->orderBy([
                    'value' => 'ASC',
                ])
                ->getOrderBy()
        );
    }

    public function testGetSelect(): void
    {
        $this->assertSame(
            [
                'value',
            ],
            $this->db->select([
                'value',
            ])
                ->getSelect()
        );
    }

    public function testGetTable(): void
    {
        $this->assertSame(
            [
                'value',
            ],
            $this->db->select()
                ->from([
                    'value',
                ])
                ->getTable()
        );
    }

    public function testGetUnion(): void
    {
        $query = $this->db->select()
            ->from('test');

        $this->assertSame(
            [
                [
                    'type' => 'distinct',
                    'query' => $query,
                ],
            ],
            $this->db->select()
                ->union($query)
                ->getUnion()
        );
    }

    public function testGetValues(): void
    {
        $this->assertSame(
            [
                [
                    'value' => 1,
                ],
            ],
            $this->db->insert()
                ->values([
                    [
                        'value' => 1,
                    ],
                ])
                ->getValues()
        );
    }

    public function testGetWhere(): void
    {
        $this->assertSame(
            [
                'value' => 1,
            ],
            $this->db->select()
                ->where([
                    'value' => 1,
                ])
                ->getWhere()
        );
    }

    public function testGetWith(): void
    {
        $query = $this->db->select()
            ->from('test');

        $this->assertSame(
            [
                [
                    'cte' => [
                        'alt' => $query,
                    ],
                    'recursive' => false,
                ],
            ],
            $this->db->select()
                ->with([
                    'alt' => $query,
                ])
                ->getWith()
        );
    }
}
