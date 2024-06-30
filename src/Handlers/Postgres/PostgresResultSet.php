<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\Postgres;

use Fyre\DB\ResultSet;

/**
 * PostgresResultSet
 */
class PostgresResultSet extends ResultSet
{
    protected static array $types = [
        'bool' => 'boolean',
        'date' => 'date',
        'float4' => 'float',
        'float8' => 'float',
        'int2' => 'integer',
        'int4' => 'integer',
        'int8' => 'integer',
        'money' => 'decimal',
        'numeric' => 'decimal',
        'time' => 'time',
        'timestamp' => 'datetime',
        'timestamptz' => 'datetime-timezone',
    ];
}
