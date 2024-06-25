<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

use function array_merge;

/**
 * OrderByTrait
 */
trait OrderByTrait
{
    protected array $orderBy = [];

    /**
     * Get the ORDER BY fields.
     *
     * @return array The ORDER BY fields.
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * Set the ORDER BY fields.
     *
     * @param string|array $fields The fields.
     * @param bool $overwrite Whether to overwrite the existing fields.
     * @return Query The Query.
     */
    public function orderBy(array|string $fields, bool $overwrite = false): static
    {
        $fields = (array) $fields;

        if ($overwrite) {
            $this->orderBy = $fields;
        } else {
            $this->orderBy = array_merge($this->orderBy, $fields);
        }

        $this->dirty();

        return $this;
    }
}
