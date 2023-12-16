<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

/**
 * LimitOffsetTrait
 */
trait LimitOffsetTrait
{

    protected int $offset = 0;
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
     * Get the OFFSET clause.
     * @return int The OFFSET clause.
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Set the LIMIT and OFFSET clauses.
     * @param int|null $limit The limit.
     * @param int|null $offset The offset.
     * @return Query The Query.
     */
    public function limit(int|null $limit = null, int|null $offset = null): static
    {
        $this->limit = $limit;

        if ($offset !== null) {
            $this->offset = $offset;
        }

        $this->dirty();

        return $this;
    }

    /**
     * Set the LIMIT and OFFSET clauses.
     * @param int $offset The offset.
     * @return Query The Query.
     */
    public function offset(int $offset = 0): static
    {
        $this->offset = $offset;

        $this->dirty();

        return $this;
    }

}
