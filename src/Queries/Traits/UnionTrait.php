<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

use Closure;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\QueryLiteral;

/**
 * UnionTrait
 */
trait UnionTrait
{

    protected array $unions = [];

    /**
     * Add an EXCEPT query.
     * @param Closure|SelectQuery|QueryLiteral|string $union The query.
     * @param bool $overwrite Whether to overwrite the existing unions.
     * @return SelectQuery The SelectQuery.
     */
    public function except(Closure|SelectQuery|QueryLiteral|string $union, bool $overwrite = false): static
    {
        return $this->union($union, $overwrite, 'except');
    }

    /**
     * Get the UNION queries.
     * @return array The UNION queries.
     */
    public function getUnion(): array
    {
        return $this->unions;
    }

    /**
     * Add an INTERSECT query.
     * @param Closure|SelectQuery|QueryLiteral|string $union The query.
     * @param bool $overwrite Whether to overwrite the existing unions.
     * @return SelectQuery The SelectQuery.
     */
    public function intersect(Closure|SelectQuery|QueryLiteral|string $union, bool $overwrite = false): static
    {
        return $this->union($union, $overwrite, 'intersect');
    }

    /**
     * Add an UNION DISTINCT query.
     * @param Closure|SelectQuery|QueryLiteral|string $union The query.
     * @param bool $overwrite Whether to overwrite the existing unions.
     * @param string $type The union type.
     * @return SelectQuery The SelectQuery.
     */
    public function union(Closure|SelectQuery|QueryLiteral|string $union, bool $overwrite = false, string $type = 'distinct'): static
    {
        $union = [
            'type' => $type,
            'query' => $union
        ];

        if ($overwrite) {
            $this->unions = [$union];
        } else {
            $this->unions[] = $union;
        }

        $this->dirty();

        return $this;
    }

    /**
     * Add an UNION ALL query.
     * @param Closure|SelectQuery|QueryLiteral|string $union The query.
     * @param bool $overwrite Whether to overwrite the existing unions.
     * @return SelectQuery The SelectQuery.
     */
    public function unionAll(Closure|SelectQuery|QueryLiteral|string $union, bool $overwrite = false): static
    {
        return $this->union($union, $overwrite, 'all');
    }

}
