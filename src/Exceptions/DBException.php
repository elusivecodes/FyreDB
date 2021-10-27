<?php
declare(strict_types=1);

namespace Fyre\DB\Exceptions;

use
    RunTimeException;

/**
 * DBException
 */
class DBException extends RunTimeException
{

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

}