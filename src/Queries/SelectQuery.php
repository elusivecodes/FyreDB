<?php
declare(strict_types=1);

namespace Fyre\DB\Queries;

use Fyre\DB\Connection;
use Fyre\DB\Queries\Traits\DistinctTrait;
use Fyre\DB\Queries\Traits\EpilogTrait;
use Fyre\DB\Queries\Traits\FromTrait;
use Fyre\DB\Queries\Traits\GroupByTrait;
use Fyre\DB\Queries\Traits\HavingTrait;
use Fyre\DB\Queries\Traits\JoinTrait;
use Fyre\DB\Queries\Traits\LimitOffsetTrait;
use Fyre\DB\Queries\Traits\OrderByTrait;
use Fyre\DB\Queries\Traits\SelectTrait;
use Fyre\DB\Queries\Traits\UnionTrait;
use Fyre\DB\Queries\Traits\WhereTrait;
use Fyre\DB\Queries\Traits\WithTrait;
use Fyre\DB\Query;
use Fyre\DB\ValueBinder;

/**
 * SelectQuery
 */
class SelectQuery extends Query
{
    use DistinctTrait;
    use EpilogTrait;
    use FromTrait;
    use GroupByTrait;
    use HavingTrait;
    use JoinTrait;
    use LimitOffsetTrait;
    use OrderByTrait;
    use SelectTrait;
    use UnionTrait;
    use WhereTrait;
    use WithTrait;

    protected static bool $multipleTables = true;

    protected static bool $tableAliases = true;

    protected static bool $virtualTables = true;

    /**
     * New SelectQuery constructor.
     *
     * @param Connection $connection The connection.
     * @param string|array $fields The fields.
     */
    public function __construct(Connection $connection, array|string $fields = '*')
    {
        parent::__construct($connection);

        $this->select($fields);
    }

    /**
     * Generate the SQL query.
     *
     * @return string The SQL query.
     */
    public function sql(ValueBinder|null $binder = null): string
    {
        $generator = $this->connection->generator();

        $query = $generator->buildWith($this->with, $binder);
        $query .= $generator->buildSelect($this->table, $this->fields, $this->distinct, $binder);
        $query .= $generator->buildJoin($this->joins, $binder);
        $query .= $generator->buildWhere($this->conditions, $binder);

        if ($this->unions !== []) {
            $query = '('.$query.')';
            $query .= $generator->buildUnion($this->unions);
        }

        $query .= $generator->buildGroupBy($this->groupBy);
        $query .= $generator->buildOrderBy($this->orderBy);
        $query .= $generator->buildHaving($this->having, $binder);
        $query .= $generator->buildLimit($this->limit, $this->offset);
        $query .= $generator->buildEpilog($this->epilog);

        return $query;
    }
}
