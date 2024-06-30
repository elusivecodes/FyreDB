<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

use function array_merge;

/**
 * WhereTrait
 */
trait WhereTrait
{
    protected array $conditions = [];

    /**
     * Get the WHERE conditions.
     *
     * @return array The WHERE conditions.
     */
    public function getWhere(): array
    {
        return $this->conditions;
    }

    /**
     * Set the WHERE conditions.
     *
     * @param array|string $conditions The conditions.
     * @param bool $overwrite Whether to overwrite the existing conditions.
     * @return Query The Query.
     */
    public function where(array|string $conditions, bool $overwrite = false): static
    {
        $conditions = (array) $conditions;

        if ($overwrite) {
            $this->conditions = $conditions;
        } else {
            $this->conditions = array_merge($this->conditions, $conditions);
        }

        $this->dirty();

        return $this;
    }
}
