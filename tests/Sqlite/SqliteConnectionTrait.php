<?php
declare(strict_types=1);

namespace Tests\Sqlite;

use Fyre\Config\Config;
use Fyre\Container\Container;
use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use Fyre\DB\Handlers\Sqlite\SqliteConnection;
use Fyre\DB\TypeParser;
use Fyre\Event\EventManager;

trait SqliteConnectionTrait
{
    protected ConnectionManager $connection;

    protected Connection $db;

    protected function insert(): void
    {
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

    protected function setUp(): void
    {
        $container = new Container();
        $container->singleton(TypeParser::class);
        $container->singleton(Config::class);
        $container->singleton(EventManager::class);
        $container->use(Config::class)->set('Database', [
            'default' => [
                'className' => SqliteConnection::class,
                'persist' => true,
            ],
        ]);

        $this->connection = $container->use(ConnectionManager::class);

        $this->db = $this->connection->use();

        $this->db->query('DROP TABLE IF EXISTS test');

        $this->db->query(<<<'EOT'
            CREATE TABLE test (
                id INTEGER NOT NULL,
                name VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (id)
            )
        EOT);
    }

    protected function tearDown(): void
    {
        $this->db->query('DROP TABLE IF EXISTS test');
    }
}
