<?php
declare(strict_types=1);

namespace Fyre\DB;

use Countable;
use Fyre\Utility\Traits\MacroTrait;

use function count;

/**
 * ValueBinder
 */
class ValueBinder implements Countable
{
    use MacroTrait;

    protected array $bindings = [];

    /**
     * Bind a value.
     *
     * @param mixed $value The value to bind.
     * @return string The parameter placeholder.
     */
    public function bind(mixed $value): string
    {
        $nextIndex = $this->count();

        $this->bindings['p'.$nextIndex] = $value;

        return ':p'.$nextIndex;
    }

    /**
     * Get the bound values.
     */
    public function bindings(): array
    {
        return $this->bindings;
    }

    /**
     * Get the number of bound values.
     *
     * @return int The number of bound values.
     */
    public function count(): int
    {
        return count($this->bindings);
    }
}
