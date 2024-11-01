<?php
declare(strict_types=1);

namespace Tests\Mysql\Query;

use PHPUnit\Framework\TestCase;
use Tests\Mysql\MysqlConnectionTrait;

final class QueryTest extends TestCase
{
    use DeleteTestTrait;
    use ExecuteTestTrait;
    use GetTestTrait;
    use InsertTestTrait;
    use MysqlConnectionTrait;
    use ReplaceTestTrait;
    use TransactionTestTrait;
    use UpdateTestTrait;
}
