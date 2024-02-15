<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\MySQL;

use Fyre\DB\ResultSet;

class MySQLResultSet extends ResultSet
{

    protected static array $types = [
        'DATE' => 'date',
        'DATETIME' => 'datetime',
        'DOUBLE' => 'decimal',
        'FLOAT' => 'float',
        'LONG' => 'integer',
        'LONGLONG' => 'integer',
        'SHORT' => 'integer',
        'TIME' => 'time',
        'TIMESTAMP' => 'datetime',
        'TINY' => 'integer'
    ];

    /**
     * Get the database type for a column.
     * @param string $name The column name.
     * @return string|null The database type.
     */
    protected function getColumnType(string $name): string|null
    {
        $columns = $this->getColumnMeta();
        $column = $columns[$name];

        if (!$column) {
            return null;
        }

        $nativeType = $column['native_type'];

        if ($nativeType === 'TINY' && $column['len'] === 1) {
            return 'boolean';
        }

        return static::$types[$nativeType] ?? 'string';
    }

}
