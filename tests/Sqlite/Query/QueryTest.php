<?php
declare(strict_types=1);

namespace Tests\Sqlite\Query;

use PHPUnit\Framework\TestCase;
use Tests\Sqlite\SqliteConnectionTrait;

final class QueryTest extends TestCase
{
    use DeleteTestTrait;
    use ExecuteTestTrait;
    use GetTestTrait;
    use InsertTestTrait;
    use ReplaceTestTrait;
    use SqliteConnectionTrait;
    use TransactionTestTrait;
    use UpdateTestTrait;
}
