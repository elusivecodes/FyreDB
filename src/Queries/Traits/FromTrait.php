<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

/**
 * FromTrait
 */
trait FromTrait
{

    /**
     * Get the FROM clause.
     * @return string|array|null The table.
     */
    public function getFrom(): string|array|null
    {
        return $this->getTable();
    }

    /**
     * Set the FROM clause.
     * @param string|array $table The table.
     * @param bool $overwrite Whether to overwrite the existing table.
     * @return Query The Query.
     */
    public function from(string|array $table, bool $overwrite = false): static
    {
        return $this->table($table, $overwrite);
    }

}
