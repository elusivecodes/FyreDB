<?php
declare(strict_types=1);

namespace Tests\Postgres\Query;

use BadMethodCallException;

trait ReplaceTestTrait
{
    public function testReplace(): void
    {
        $this->expectException(BadMethodCallException::class);

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
