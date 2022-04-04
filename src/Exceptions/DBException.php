<?php
declare(strict_types=1);

namespace Fyre\DB\Exceptions;

use
    RuntimeException;

/**
 * DbException
 */
class DbException extends RuntimeException
{

    public static function forConfigExists(string $key)
    {
        return new static('Database handler config already exists: '.$key);
    }

    public static function forConnectionFailed(string $error)
    {
        return new static('Unable to connect to database: '.$error);
    }

    public static function forQueryError(string $error)
    {
        return new static('Database error: '.$error);
    }

    public static function forInvalidClass(string $className = '')
    {
        return new static('Database handler class not found: '.$className);
    }

    public static function forInvalidConfig(string $key)
    {
        return new static('Database handler invalid config: '.$key);
    }

}