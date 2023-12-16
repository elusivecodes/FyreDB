<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

/**
 * LimitTrait
 */
trait LimitTrait
{

    protected int|null $limit = null;

    /**
     * Get the LIMIT clause.
     * @return int|null The LIMIT clause.
     */
    public function getLimit(): int|null
    {
        return $this->limit;
    }

    /**
     * Set the LIMIT clauses.
     * @param int|null $limit The limit.
     * @return Query The Query.
     */
    public function limit(int|null $limit = null,): static
    {
        $this->limit = $limit;

        $this->dirty();

        return $this;
    }

}
