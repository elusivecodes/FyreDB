<?php
declare(strict_types=1);

namespace Tests\Sqlite\Query;

use Exception;
use Fyre\DB\Connection;

trait TransactionTestTrait
{
    public function testAfterCommit(): void
    {
        $this->db->begin();

        $test = 0;
        $this->db->afterCommit(function() use (&$test) {
            $this->assertFalse(
                $this->db->inTransaction()
            );

            $test++;
        });

        $this->assertSame(
            0,
            $test
        );

        $this->db->commit();

        $this->assertSame(
            1,
            $test
        );
    }

    public function testAfterCommitDeep(): void
    {
        $this->db->begin();
        $this->db->begin();

        $test = 0;
        $this->db->afterCommit(function() use (&$test) {
            $this->assertFalse(
                $this->db->inTransaction()
            );

            $test++;
        });

        $this->db->commit();

        $this->assertSame(
            0,
            $test
        );

        $this->db->commit();

        $this->assertSame(
            1,
            $test
        );
    }

    public function testAfterCommitKey(): void
    {
        $this->db->begin();

        $test = 0;
        $this->db->afterCommit(function() use (&$test) {
            $test++;
        }, key: 'test');
        $this->db->afterCommit(function() use (&$test) {
            $test++;
        }, key: 'test');

        $this->db->commit();

        $this->assertSame(
            1,
            $test
        );
    }

    public function testAfterCommitPriority(): void
    {
        $this->db->begin();

        $test = [];
        $this->db->afterCommit(function() use (&$test) {
            $test[] = 1;
        }, 2);
        $this->db->afterCommit(function() use (&$test) {
            $test[] = 2;
        }, 1);

        $this->db->commit();

        $this->assertSame(
            [2, 1],
            $test
        );
    }

    public function testAfterCommitRollback(): void
    {
        $this->db->begin();

        $test = 0;
        $this->db->afterCommit(function() use (&$test) {
            $test++;
        });

        $this->db->rollback();

        $this->assertSame(
            0,
            $test
        );

        $this->db->begin();
        $this->db->commit();

        $this->assertSame(
            0,
            $test
        );
    }

    public function testAfterCommitRollbackDeep(): void
    {
        $this->db->begin();

        $test = 0;
        $this->db->afterCommit(function() use (&$test) {
            $this->assertFalse(
                $this->db->inTransaction()
            );

            $test++;
        });

        $this->db->begin();

        $this->db->afterCommit(function() use (&$test) {
            $test++;
        });

        $this->db->rollback();

        $this->assertSame(
            0,
            $test
        );

        $this->db->commit();

        $this->assertSame(
            1,
            $test
        );
    }

    public function testAfterCommitWithoutTransaction(): void
    {
        $test = 0;
        $this->db->afterCommit(function() use (&$test) {
            $this->assertFalse(
                $this->db->inTransaction()
            );

            $test++;
        });

        $this->assertSame(
            1,
            $test
        );
    }

    public function testInTransaction(): void
    {
        $this->db->begin();

        $this->assertTrue(
            $this->db->inTransaction()
        );

        $this->db->rollback();
    }

    public function testTransactionalCommit(): void
    {
        $this->assertTrue(
            $this->db->transactional(function(Connection $db) {
                $db->insert()
                    ->into('test')
                    ->values([
                        [
                            'name' => 'Test 1',
                        ],
                        [
                            'name' => 'Test 2',
                        ],
                    ])
                    ->execute();
            })
        );

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
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testTransactionalRollback(): void
    {
        $this->assertFalse(
            $this->db->transactional(function(Connection $db) {
                $db->insert()
                    ->into('test')
                    ->values([
                        [
                            'name' => 'Test 1',
                        ],
                        [
                            'name' => 'Test 2',
                        ],
                    ])
                    ->execute();

                return false;
            })
        );

        $this->assertSame(
            [],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testTransactionalRollbackException(): void
    {
        try {
            $this->db->transactional(function(Connection $db) {
                $db->insert()
                    ->into('test')
                    ->values([
                        [
                            'name' => 'Test 1',
                        ],
                        [
                            'name' => 'Test 2',
                        ],
                    ])
                    ->execute();

                throw new Exception();
            });
        } catch (Exception $e) {
        }

        $this->assertSame(
            [],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testTransactionalRollbackExceptionThrown(): void
    {
        $this->expectException(Exception::class);

        $this->db->transactional(function(Connection $db) {
            throw new Exception();
        });
    }

    public function testTransactionCommit(): void
    {
        $this->db->begin();

        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 1',
                ],
                [
                    'name' => 'Test 2',
                ],
            ])
            ->execute();

        $this->db->commit();

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
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testTransactionNested(): void
    {
        $this->db->begin();

        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 1',
                ],
            ])
            ->execute();

        $this->db->begin();

        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 2',
                ],
            ])
            ->execute();

        $this->db->rollback();

        $this->db->commit();

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => 'Test 1',
                ],
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testTransactionNestedRollback(): void
    {
        $this->db->begin();

        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 1',
                ],
            ])
            ->execute();

        $this->db->begin();

        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 2',
                ],
            ])
            ->execute();

        $this->db->rollback();

        $this->db->rollback();

        $this->assertSame(
            [],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testTransactionRollback(): void
    {
        $this->db->begin();

        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 1',
                ],
                [
                    'name' => 'Test 2',
                ],
            ])
            ->execute();

        $this->db->rollback();

        $this->assertSame(
            [],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }
}
