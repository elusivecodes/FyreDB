<?php
declare(strict_types=1);

namespace Tests\Query;

use Fyre\DB\Exceptions\DbException;

trait ReplaceTestTrait
{

    public function testReplace(): void
    {
        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test'
                ]
            ])
            ->execute();

        $this->assertTrue(
            $this->db->replace()
                ->into('test')
                ->values([
                    [
                        'id' => 1,
                        'name' => 'Test 2'
                    ]
                ])
                ->execute()
        );

        $this->assertSame(
            [
                'id' => 1,
                'name' => 'Test 2'
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->first()
        );
    }

    public function testReplaceBatch(): void
    {
        $this->db->insert()
            ->into('test')
            ->values([
                [
                    'name' => 'Test 1'
                ],
                [
                    'name' => 'Test 2'
                ]
            ])
            ->execute();

        $this->assertTrue(
            $this->db->replace()
                ->into('test')
                ->values([
                    [
                        'id' => 1,
                        'name' => 'Test 3'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test 4'
                    ]
                ])
                ->execute()
        );

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => 'Test 3'
                ],
                [
                    'id' => 2,
                    'name' => 'Test 4'
                ]
            ],
            $this->db->select()
                ->from('test')
                ->execute()
                ->all()
        );
    }

    public function testReplaceMultipleTables(): void
    {
        $this->expectException(DbException::class);

        $this->db->replace()
            ->table([
                'alt' => 'test',
                'alt2' => 'test2'
            ]);
    }

    public function testReplaceVirtualTables(): void
    {
        $this->expectException(DbException::class);

        $this->db->replace()
            ->table([
                'alt' => $this->db->select()
                    ->from('test')
            ]);
    }

    public function testReplaceTableAliases(): void
    {
        $this->expectException(DbException::class);

        $this->db->replace()
            ->table([
                'alt' => 'test'
            ]);
    }

}
