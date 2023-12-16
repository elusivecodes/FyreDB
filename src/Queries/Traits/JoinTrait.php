<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

use Fyre\DB\Exceptions\DbException;

use function array_merge;
use function is_numeric;
use function is_string;

/**
 * JoinTrait
 */
trait JoinTrait
{

    protected array $joins = [];

    /**
     * Get the JOIN tables.
     * @return array The JOIN tables.
     */
    public function getJoin(): array
    {
        return $this->joins;
    }

    /**
     * Set the JOIN tables.
     * @param array $joins The joins.
     * @param bool $overwrite Whether to overwrite the existing joins.
     * @return Query The Query.
     */
    public function join(array $joins, bool $overwrite = false): static
    {
        $joins = static::normalizeJoins($joins);

        if ($overwrite) {
            $this->joins = $joins;
        } else {
            $this->joins = array_merge($this->joins, $joins);
        }

        $this->dirty();

        return $this;
    }

    /**
     * Normalize a joins array.
     * @param array $joins The joins.
     * @return array The normalize joins.
     * @throws DbException if an alias is not valid.
     */
    protected static function normalizeJoins(array $joins): array
    {
        $results = [];
        foreach ($joins AS $alias => $join) {
            if (is_numeric($alias)) {
                $alias = $join['alias'] ?? $join['table'] ?? null;
            }

            if (!is_string($alias)) {
                throw DbException::forInvalidJoinAlias();
            }

            $join['table'] ??= $alias;

            $results[$alias] = $join;
        }

        return $results;
    }

}
