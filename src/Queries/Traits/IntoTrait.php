<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

/**
 * IntoTrait
 */
trait IntoTrait
{
    /**
     * Get the INTO clause.
     *
     * @return string|array|null The table.
     */
    public function getInto(): array|string|null
    {
        return $this->getTable();
    }

    /**
     * Set the INTO clause.
     *
     * @param string $table The table.
     * @param bool $overwrite Whether to overwrite the existing table.
     * @return Query The Query.
     */
    public function into(string $table, bool $overwrite = false): static
    {
        return $this->table($table, $overwrite);
    }
}
