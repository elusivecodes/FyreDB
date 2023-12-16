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
     * @return string|null The table.
     */
    public function getInto(): string|null
    {
        return $this->table[0] ?? null;
    }

    /**
     * Set the INTO clause.
     * @param string $table The table.
     * @return Query The Query.
     */
    public function into(string $table): static
    {
        return $this->table($table);
    }

}
