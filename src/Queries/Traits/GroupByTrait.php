<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

use function array_merge;

/**
 * GroupByTrait
 */
trait GroupByTrait
{
    protected array $groupBy = [];

    /**
     * Get the GROUP BY fields.
     *
     * @return array The GROUP BY fields.
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * Set the GROUP BY fields.
     *
     * @param array|string $fields The fields.
     * @param bool $overwrite Whether to overwrite the existing fields.
     * @return Query The Query.
     */
    public function groupBy(array|string $fields, bool $overwrite = false): static
    {
        $fields = (array) $fields;

        if ($overwrite) {
            $this->groupBy = $fields;
        } else {
            $this->groupBy = array_merge($this->groupBy, $fields);
        }

        $this->dirty();

        return $this;
    }
}
