<?php
declare(strict_types=1);

namespace Tests\Sqlite;

use Fyre\DB\ConnectionManager;
use Fyre\DB\Handlers\Sqlite\SqliteConnection;

trait SqliteConnectionTrait
{
    public static function setUpBeforeClass(): void
    {
        ConnectionManager::clear();

        ConnectionManager::setConfig([
            'default' => [
                'className' => SqliteConnection::class,
                'persist' => true,
            ],
        ]);

        $connection = ConnectionManager::use();

        $connection->query('DROP TABLE IF EXISTS "test"');

        $connection->query(<<<'EOT'
            CREATE TABLE "test" (
                "id" INTEGER NOT NULL,
                "name" VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY ("id")
            )
        EOT);
    }

    public static function tearDownAfterClass(): void
    {
        $connection = ConnectionManager::use();
        $connection->query('DELETE FROM "test"');
    }
}
