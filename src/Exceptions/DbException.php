<?php
declare(strict_types=1);

namespace Fyre\DB\Exceptions;

use RuntimeException;

/**
 * DbException
 */
class DbException extends RuntimeException
{
    public static function forConfigExists(string $key): static
    {
        return new static('Database handler config already exists: '.$key);
    }

    public static function forConnectionFailed(string $error): static
    {
        return new static('Unable to connect to database: '.$error);
    }

    public static function forInvalidClass(string $className = ''): static
    {
        return new static('Database handler class not found: '.$className);
    }

    public static function forInvalidConfig(string $key): static
    {
        return new static('Database handler invalid config: '.$key);
    }

    public static function forInvalidJoinAlias(): static
    {
        return new static('Database error: invalid join alias');
    }

    public static function forMultipleTablesNotSupported(): static
    {
        return new static('Multiple tables are not supported for this query.');
    }

    public static function forQueryError(string $error): static
    {
        return new static('Database error: '.$error);
    }

    public static function forTableAliasesNotSupported(): static
    {
        return new static('Table aliases are not supported for this query.');
    }

    public static function forVirtualTablesNotSupported(): static
    {
        return new static('Virtual tables are not supported for this query.');
    }
}
