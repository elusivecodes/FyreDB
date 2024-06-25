<?php
declare(strict_types=1);

namespace Fyre\DB;

/**
 * QueryLiteral
 */
class QueryLiteral
{
    protected string $string;

    /**
     * New QueryLiteral constructor.
     *
     * @param string $string The literal string.
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * Get the literal string.
     *
     * @return string The literal string.
     */
    public function __toString(): string
    {
        return $this->string;
    }
}
