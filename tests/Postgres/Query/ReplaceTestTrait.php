<?php
declare(strict_types=1);

namespace Tests\Postgres\Query;

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
                    'name' => 'Test',
                ],
            ])
            ->execute();
    }
}
