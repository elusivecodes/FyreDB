<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

/**
 * DistinctTrait
 */
trait DistinctTrait
{

    protected bool $distinct = false;

    /**
     * Set the DISTINCT clause.
     * @param bool $distinct Whether to set the DISTINCT clause.
     * @return Query The Query.
     */
    public function distinct(bool $distinct = true): static
    {
        $this->distinct = $distinct;
        $this->dirty();

        return $this;
    }

    /**
     * Get the DISTINCT clause.
     * @return bool The DISTINCT clause.
     */
    public function getDistinct(): bool
    {
        return $this->distinct;
    }

}
