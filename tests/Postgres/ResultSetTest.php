<?php
declare(strict_types=1);

namespace Tests\Postgres;

use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use Fyre\DB\Types\BooleanType;
use Fyre\DB\Types\DateTimeTimeZoneType;
use Fyre\DB\Types\DateTimeType;
use Fyre\DB\Types\DateType;
use Fyre\DB\Types\DecimalType;
use Fyre\DB\Types\FloatType;
use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\StringType;
use Fyre\DB\Types\TimeType;
use PHPUnit\Framework\TestCase;

final class ResultSetTest extends TestCase
{
    use PostgresConnectionTrait;

    protected Connection $db;

    public function testAll(): void
    {
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

    public function testCount(): void
    {
        $this->assertSame(
            3,
            $this->db->select()
                ->from('test')
                ->execute()
                ->count()
        );
    }

    public function testFetch(): void
    {
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

    public function testType(): void
    {
        $this->assertInstanceOf(
            StringType::class,
            $this->db->select()
                ->from('test')
                ->execute()
                ->getType('name')
        );
    }

    public function testTypeVirtualField(): void
    {
        $result = $this->db->select([
            'v_bigint' => 'CAST(9223372036854775807 AS BIGINT)',
            'v_boolean' => 'CAST(1 AS BOOLEAN)',
            'v_date' => 'CAST(LOCALTIMESTAMP(0) AS DATE)',
            'v_double' => 'CAST(1 AS DOUBLE PRECISION)',
            'v_integer' => 'CAST(2147483647 AS INTEGER)',
            'v_money' => 'CAST(1 AS MONEY)',
            'v_numeric' => 'CAST(1 AS NUMERIC)',
            'v_real' => 'CAST(1 AS REAL)',
            'v_smallint' => 'CAST(32767 AS SMALLINT)',
            'v_time' => 'LOCALTIME(0)',
            'v_timestamp' => 'LOCALTIMESTAMP(0)',
            'v_timestamp_tz' => 'CURRENT_TIMESTAMP(0)',
        ])
            ->execute();

        $this->assertInstanceOf(
            IntegerType::class,
            $result->getType('v_bigint')
        );

        $this->assertInstanceOf(
            BooleanType::class,
            $result->getType('v_boolean')
        );

        $this->assertInstanceOf(
            DateType::class,
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
            DecimalType::class,
            $result->getType('v_money')
        );

        $this->assertInstanceOf(
            DecimalType::class,
            $result->getType('v_numeric')
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
            TimeType::class,
            $result->getType('v_time')
        );

        $this->assertInstanceOf(
            DateTimeType::class,
            $result->getType('v_timestamp')
        );

        $this->assertInstanceOf(
            DateTimeTimeZoneType::class,
            $result->getType('v_timestamp_tz')
        );
    }

    protected function setUp(): void
    {
        $this->db = ConnectionManager::use();
        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 1',
                ],
                [
                    'name' => 'Test 2',
                ],
                [
                    'name' => 'Test 3',
                ],
            ])
            ->execute();
    }

    protected function tearDown(): void
    {
        $this->db->query('TRUNCATE test RESTART IDENTITY');
    }
}
