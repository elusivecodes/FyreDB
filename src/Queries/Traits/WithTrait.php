<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

/**
 * WithTrait
 */
trait WithTrait
{
    protected array $with = [];

    /**
     * Get the WITH queries.
     *
     * @return array The WITH queries.
     */
    public function getWith(): array
    {
        return $this->with;
    }

    /**
     * Set the WITH clause.
     *
     * @param array $cte The common table expressions.
     * @param bool $overwrite Whether to overwrite the existing expressions.
     * @param bool $recursive Whether the WITH is recursive.
     * @return Query The Query.
     */
    public function with(array $cte, bool $overwrite = false, bool $recursive = false): static
    {
        $with = [
            'cte' => $cte,
            'recursive' => $recursive,
        ];

        if ($overwrite) {
            $this->with = [$with];
        } else {
            $this->with[] = $with;
        }

        $this->dirty();

        return $this;
    }

    /**
     * Set the WITH RECURSIVE clause.
     *
     * @param array $cte The common table expressions.
     * @param bool $overwrite Whether to overwrite the existing common table expressions.
     * @return Query The Query.
     */
    public function withRecursive(array $cte, bool $overwrite = false): static
    {
        return $this->with($cte, $overwrite, true);
    }
}
