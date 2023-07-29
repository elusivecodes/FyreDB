<?php
declare(strict_types=1);

namespace Tests\QueryBuilder;

use Fyre\DB\QueryBuilder;

trait GetTestTrait
{

    public function testGetData()
    {
        $this->assertSame(
            [
                'value' => 1
            ],
            $this->db->builder()
                ->insert([
                    'value' => 1
                ])
                ->getData()
        );
    }

    public function testGetDistinct()
    {
        $this->assertSame(
            true,
            $this->db->builder()
                ->distinct()
                ->getDistinct()
        );
    }

    public function testGetGroupBy()
    {
        $this->assertSame(
            [
                'value'
            ],
            $this->db->builder()
                ->groupBy([
                    'value'
                ])
                ->getGroupBy()
        );
    }

    public function testGetHaving()
    {
        $this->assertSame(
            [
                'value' => 1
            ],
            $this->db->builder()
                ->having([
                    'value' => 1
                ])
                ->getHaving()
        );
    }

    public function testGetJoin()
    {
        $this->assertSame(
            [
                'test2' => [
                    'table' => 'test2',
                    'using' => 'id'
                ]
            ],
            $this->db->builder()
                ->join([
                    [
                        'table' => 'test2',
                        'using' => 'id'
                    ]
                ])
                ->getJoin()
        );
    }

    public function testGetLimit()
    {
        $this->assertSame(
            1,
            $this->db->builder()
                ->limit(1)
                ->getLimit()
        );
    }

    public function testGetOffset()
    {
        $this->assertSame(
            1,
            $this->db->builder()
                ->offset(1)
                ->getOffset()
        );
    }

    public function testGetOrderBy()
    {
        $this->assertSame(
            [
                'value' => 'ASC'
            ],
            $this->db->builder()
                ->orderBy([
                    'value' => 'ASC'
                ])
                ->getOrderBy()
        );
    }

    public function testGetSelect()
    {
        $this->assertSame(
            [
                'value'
            ],
            $this->db->builder()
                ->select([
                    'value'
                ])
                ->getSelect()
        );
    }

    public function testGetTable()
    {
        $this->assertSame(
            [
                'value'
            ],
            $this->db->builder()
                ->table([
                    'value'
                ])
                ->getTable()
        );
    }

    public function testGetUnion()
    {
        $query = $this->db->builder()
            ->table('test')
            ->select();

        $this->assertSame(
            [
                [
                    'type' => 'distinct',
                    'query' => $query
                ]
            ],
            $this->db->builder()
                ->union($query)
                ->getUnion()
        );
    }

    public function testGetWhere()
    {
        $this->assertSame(
            [
                'value' => 1
            ],
            $this->db->builder()
                ->where([
                    'value' => 1
                ])
                ->getWhere()
        );
    }

    public function testGetWith()
    {
        $query = $this->db->builder()
            ->table('test')
            ->select();

        $this->assertSame(
            [
                [
                    'cte' => [
                        'alt' => $query
                    ],
                    'recursive' => false
                ]
            ],
            $this->db->builder()
                ->with([
                    'alt' => $query
                ])
                ->getWith()
        );
    }

}
