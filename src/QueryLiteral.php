<?php
declare(strict_types=1);

namespace Fyre\DB;

/**
 * QueryLiteral
 */
class QueryLiteral
{
    /**
     * New QueryLiteral constructor.
     *
     * @param string $string The literal string.
     */
    public function __construct(
        protected string $string
    ) {}

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
