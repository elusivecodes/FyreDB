<?php
declare(strict_types=1);

namespace Tests\Postgres\Query;

use PHPUnit\Framework\TestCase;
use Tests\Postgres\PostgresConnectionTrait;

final class QueryTest extends TestCase
{
    use DeleteTestTrait;
    use ExecuteTestTrait;
    use GetTestTrait;
    use InsertTestTrait;
    use PostgresConnectionTrait;
    use ReplaceTestTrait;
    use TransactionTestTrait;
    use UpdateTestTrait;
}
