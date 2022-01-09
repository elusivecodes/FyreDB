<?php
declare(strict_types=1);

namespace Tests\Query;

use
    Exception,
    Fyre\DB\Connection;

trait TransactionTest
{

    public function testTransactionCommit()
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
                    'id' => '1',
                    'name' => 'Test 1'
                ],
                [
                    'id' => '2',
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

    public function testTransactionRollback()
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

    public function testTransactionalCommit()
    {
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
        });

        $this->assertSame(
            [
                [
                    'id' => '1',
                    'name' => 'Test 1'
                ],
                [
                    'id' => '2',
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

    public function testTransactionalRollback()
    {
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
        });

        $this->assertSame(
            [],
            $this->db->builder()
                ->table('test')
                ->select()
                ->execute()
                ->all()
        );
    }

    public function testTransactionalRollbackException()
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

    public function testTransactionalRollbackExceptionThrown()
    {
        $this->expectException(Exception::class);

        $this->db->transactional(function(Connection $db) {
            throw new Exception();
        });
    }

}
