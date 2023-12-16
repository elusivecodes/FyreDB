<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

use function array_merge;

/**
 * FromTrait
 */
trait FromTrait
{

    /**
     * Get the FROM clause.
     * @return array The table.
     */
    public function getFrom(): array
    {
        return $this->table;
    }

    /**
     * Set the FROM clause.
     * @param string|array $table The table.
     * @param bool $overwrite Whether to overwrite the existing table.
     * @return Query The Query.
     */
    public function from(string|array $table, bool $overwrite = false): static
    {
        $table = (array) $table;

        if ($overwrite) {
            $this->table = $table;
        } else {
            $this->table = array_merge($this->table, $table);
        }

        $this->dirty();

        return $this;
    }

}
