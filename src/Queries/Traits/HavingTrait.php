<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

use function array_merge;

/**
 * HavingTrait
 */
trait HavingTrait
{

    protected array $having = [];

    /**
     * Get the HAVING conditions.
     * @return array The HAVING conditions.
     */
    public function getHaving(): array
    {
        return $this->having;
    }

    /**
     * Set the HAVING conditions.
     * @param string|array $conditions The conditions.
     * @param bool $overwrite Whether to overwrite the existing conditions.
     * @return Query The Query.
     */
    public function having(string|array $conditions, bool $overwrite = false): static
    {
        $conditions = (array) $conditions;

        if ($overwrite) {
            $this->having = $conditions;
        } else {
            $this->having = array_merge($this->having, $conditions);
        }

        $this->dirty();

        return $this;
    }

}
