<?php
declare(strict_types=1);

namespace Tests\Sqlite;

use Fyre\DB\Types\FloatType;
use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\StringType;
use PHPUnit\Framework\TestCase;

final class ResultSetTest extends TestCase
{
    use SqliteConnectionTrait;

    public function testAll(): void
    {
        $this->insert();

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => 'Test 1',
                ],
                [
                    'id' => 2,
                    'name' => 'Test 2',
                ],
                [
                    'id' => 3,
                    'name' => 'Test 3',
                ],
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testClearBuffer(): void
    {
        $this->insert();

        $result = $this->db->select()
            ->from('test')
            ->execute();

        $result->all();
        $result->clearBuffer(1);

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => 'Test 1',
                ],
                null,
                [
                    'id' => 3,
                    'name' => 'Test 3',
                ],
            ],
            $result->all()
        );
    }

    public function testClearBufferAll(): void
    {
        $this->insert();

        $result = $this->db->select()
            ->from('test')
            ->execute();

        $result->first();
        $result->clearBuffer();

        $this->assertSame(
            [
                null,
                [
                    'id' => 2,
                    'name' => 'Test 2',
                ],
                [
                    'id' => 3,
                    'name' => 'Test 3',
                ],
            ],
            $result->all()
        );
    }

    public function testColumnCount(): void
    {
        $this->insert();

        $this->assertSame(
            2,
            $this->db->select()
                ->from('test')
                ->execute()
                ->columnCount()
        );
    }

    public function testColumns(): void
    {
        $this->insert();

        $this->assertSame(
            [
                'id',
                'name',
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->columns()
        );
    }

    public function testFetch(): void
    {
        $this->insert();

        $this->assertSame(
            [
                'id' => 2,
                'name' => 'Test 2',
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->fetch(1)
        );
    }

    public function testFirst(): void
    {
        $this->insert();

        $this->assertSame(
            [
                'id' => 1,
                'name' => 'Test 1',
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->first()
        );
    }

    public function testIteration(): void
    {
        $this->insert();

        $query = $this->db->select()
            ->from('test')
            ->execute();

        $results = [];

        foreach ($query as $row) {
            $results[] = $row;
        }

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => 'Test 1',
                ],
                [
                    'id' => 2,
                    'name' => 'Test 2',
                ],
                [
                    'id' => 3,
                    'name' => 'Test 3',
                ],
            ],
            $results
        );
    }

    public function testLast(): void
    {
        $this->insert();

        $this->assertSame(
            [
                'id' => 3,
                'name' => 'Test 3',
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->last()
        );
    }

    public function testRowCount(): void
    {
        $this->insert();

        $this->assertSame(
            3,
            $this->db->select()
                ->from('test')
                ->execute()
                ->count()
        );
    }

    public function testType(): void
    {
        $this->insert();

        $this->assertInstanceOf(
            StringType::class,
            $this->db->select()
                ->from('test')
                ->execute()
                ->getType('name')
        );
    }

    public function testTypeVirtualField2(): void
    {
        $result = $this->db->select([
            'v_bigint' => 'CAST(9223372036854775807 AS BIGINT)',
            'v_boolean' => 'CAST(1 AS BOOLEAN)',
            'v_date' => 'DATE()',
            'v_double' => 'CAST(1 AS DOUBLE PRECISION)',
            'v_integer' => 'CAST(2147483647 AS INTEGER)',
            'v_numeric' => 'CAST(1 AS NUMERIC)',
            'v_real' => 'CAST(1 AS REAL)',
            'v_smallint' => 'CAST(32767 AS SMALLINT)',
            'v_time' => 'TIME()',
            'v_timestamp' => 'CURRENT_TIMESTAMP',
        ])
            ->execute();

        $this->assertInstanceOf(
            IntegerType::class,
            $result->getType('v_bigint')
        );

        $this->assertInstanceOf(
            IntegerType::class,
            $result->getType('v_boolean')
        );

        $this->assertInstanceOf(
            StringType::class,
            $result->getType('v_date')
        );

        $this->assertInstanceOf(
            FloatType::class,
            $result->getType('v_double')
        );

        $this->assertInstanceOf(
            IntegerType::class,
            $result->getType('v_integer')
        );

        $this->assertInstanceOf(
            FloatType::class,
            $result->getType('v_real')
        );

        $this->assertInstanceOf(
            IntegerType::class,
            $result->getType('v_smallint')
        );

        $this->assertInstanceOf(
            StringType::class,
            $result->getType('v_time')
        );

        $this->assertInstanceOf(
            StringType::class,
            $result->getType('v_timestamp')
        );
    }
}
