<?php
declare(strict_types=1);

namespace Tests\Postgres\Sql;

use Fyre\DB\Exceptions\DbException;

trait ReplaceTestTrait
{
    public function testReplace(): void
    {
        $this->expectException(DbException::class);

        $this->db->replace()
            ->into('test')
            ->values([
                [
                    'id' => 1,
                    'name' => 'Test 1',
                    'value' => 1,
                ],
                [
                    'id' => 2,
                    'name' => 'Test 2',
                    'value' => 2,
                ],
            ])
            ->sql();
    }
}
