<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\Mysql;

use Fyre\DB\ResultSet;

/**
 * MysqlResultSet
 */
class MysqlResultSet extends ResultSet
{
    protected static array $types = [
        'DATE' => 'date',
        'DATETIME' => 'datetime',
        'DOUBLE' => 'float',
        'FLOAT' => 'float',
        'LONG' => 'integer',
        'LONGLONG' => 'integer',
        'NEWDECIMAL' => 'decimal',
        'SHORT' => 'integer',
        'TIME' => 'time',
        'TIMESTAMP' => 'datetime',
        'TINY' => 'integer',
    ];
}
