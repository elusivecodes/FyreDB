<?php
declare(strict_types=1);

namespace Tests\Mysql;

use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
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
    use MysqlConnectionTrait;

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
            'v_bigint' => 'CAST(18446744073709551615 AS UNSIGNED INT)',
            // 'v_boolean' => 'CAST(1 AS BOOLEAN)',
            'v_date' => 'CAST(LOCALTIMESTAMP() AS DATE)',
            'v_decimal' => 'CAST(1 AS DECIMAL)',
            'v_double' => 'CAST(1 AS DOUBLE)',
            'v_float' => 'CAST(1 AS FLOAT)',
            'v_integer' => 'CAST(4294967295 AS UNSIGNED INT)',
            'v_mediumint' => 'CAST(16777215 AS UNSIGNED INT)',
            'v_smallint' => 'CAST(65535 AS UNSIGNED INT)',
            'v_tinyint' => 'CAST(255 AS UNSIGNED INT)',
            'v_time' => 'CAST(LOCALTIMESTAMP() AS TIME)',
            'v_timestamp' => 'LOCALTIMESTAMP()',
        ])
            ->execute();

        $this->assertInstanceOf(
            IntegerType::class,
            $result->getType('v_bigint')
        );

        $this->assertInstanceOf(
            DateType::class,
            $result->getType('v_date')
        );

        $this->assertInstanceOf(
            DecimalType::class,
            $result->getType('v_decimal')
        );

        $this->assertInstanceOf(
            FloatType::class,
            $result->getType('v_double')
        );

        $this->assertInstanceOf(
            FloatType::class,
            $result->getType('v_float')
        );

        $this->assertInstanceOf(
            IntegerType::class,
            $result->getType('v_integer')
        );

        $this->assertInstanceOf(
            IntegerType::class,
            $result->getType('v_mediumint')
        );

        $this->assertInstanceOf(
            IntegerType::class,
            $result->getType('v_smallint')
        );

        $this->assertInstanceOf(
            IntegerType::class,
            $result->getType('v_tinyint')
        );

        $this->assertInstanceOf(
            TimeType::class,
            $result->getType('v_time')
        );

        $this->assertInstanceOf(
            DateTimeType::class,
            $result->getType('v_timestamp')
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
        $this->db->query('TRUNCATE `test`');
    }
}
