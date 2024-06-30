<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

/**
 * FromTrait
 */
trait FromTrait
{
    /**
     * Set the FROM clause.
     *
     * @param array|string $table The table.
     * @param bool $overwrite Whether to overwrite the existing table.
     * @return Query The Query.
     */
    public function from(array|string $table, bool $overwrite = false): static
    {
        return $this->table($table, $overwrite);
    }

    /**
     * Get the FROM clause.
     *
     * @return array|string|null The table.
     */
    public function getFrom(): array|string|null
    {
        return $this->getTable();
    }
}
