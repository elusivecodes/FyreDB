<?php
declare(strict_types=1);

namespace Tests\Query;

use Exception;
use Fyre\DB\Connection;

trait TransactionTestTrait
{

    public function testTransactionCommit(): void
    {
        $this->db->begin();

        $this->db->builder()
            ->table('test')
            ->insertBatch([
                [
                    'name' => 'Test 1'
                ],
                [
                    'name' => 'Test 2'
                ]
            ])
            ->execute();

        $this->db->commit();

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => 'Test 1'
                ],
                [
                    'id' => 2,
                    'name' => 'Test 2'
                ]
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testTransactionRollback(): void
    {
        $this->db->begin();

        $this->db->builder()
            ->table('test')
            ->insertBatch([
                [
                    'name' => 'Test 1'
                ],
                [
                    'name' => 'Test 2'
                ]
            ])
            ->execute();

        $this->db->rollback();

        $this->assertSame(
            [],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testTransactionNested(): void
    {
        $this->db->begin();

        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test 1'
            ])
            ->execute();

        $this->db->begin();

        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test 2'
            ])
            ->execute();

        $this->db->rollback();

        $this->db->commit();

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => 'Test 1'
                ]
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testTransactionNestedRollback(): void
    {
        $this->db->begin();

        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test 1'
            ])
            ->execute();

        $this->db->begin();

        $this->db->builder()
            ->table('test')
            ->insert([
                'name' => 'Test 2'
            ])
            ->execute();

        $this->db->rollback();

        $this->db->rollback();

        $this->assertSame(
            [],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testTransactionalCommit(): void
    {
        $this->assertTrue(
            $this->db->transactional(function(Connection $db) {
                $db->builder()
                    ->table('test')
                    ->insertBatch([
                        [
                            'name' => 'Test 1'
                        ],
                        [
                            'name' => 'Test 2'
                        ]
                    ])
                    ->execute();
            })
        );

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => 'Test 1'
                ],
                [
                    'id' => 2,
                    'name' => 'Test 2'
                ]
            ],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testTransactionalRollback(): void
    {
        $this->assertFalse(
            $this->db->transactional(function(Connection $db) {
                $db->builder()
                    ->table('test')
                    ->insertBatch([
                        [
                            'name' => 'Test 1'
                        ],
                        [
                            'name' => 'Test 2'
                        ]
                    ])
                    ->execute();
    
                return false;
            })
        );

        $this->assertSame(
            [],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testTransactionalRollbackException(): void
    {
        try {
            $this->db->transactional(function(Connection $db) {
                $db->builder()
                    ->table('test')
                    ->insertBatch([
                        [
                            'name' => 'Test 1'
                        ],
                        [
                            'name' => 'Test 2'
                        ]
                    ])
                    ->execute();

                throw new Exception();
            });
        } catch (Exception $e) { }

        $this->assertSame(
            [],
            $this->db->builder()
                ->table('test')
                ->select()
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

}
