<?php
declare(strict_types=1);

namespace Fyre\DB\Queries;

use Closure;
use Fyre\DB\Connection;
use Fyre\DB\Queries\Traits\EpilogTrait;
use Fyre\DB\Queries\Traits\IntoTrait;
use Fyre\DB\Query;
use Fyre\DB\QueryLiteral;
use Fyre\DB\ValueBinder;

/**
 * InsertFromQuery
 */
class InsertFromQuery extends Query
{
    use EpilogTrait;
    use IntoTrait;

    protected array $columns = [];

    protected Closure|QueryLiteral|SelectQuery|string $from = '';

    /**
     * New SelectQuery constructor.
     *
     * @param Connection $connection The connection.
     * @param Closure|SelectQuery|QueryLiteral|string $from The query.
     * @param array $columns The columns.
     */
    public function __construct(Connection $connection, Closure|QueryLiteral|SelectQuery|string $from, array $columns = [])
    {
        parent::__construct($connection);

        $this->from = $from;
        $this->columns = $columns;
    }

    /**
     * Generate the SQL query.
     *
     * @return string The SQL query.
     */
    public function sql(ValueBinder|null $binder = null): string
    {
        $generator = $this->connection->generator();

        $query = $generator->buildInsertFrom($this->table, $this->from, $this->columns, $binder);
        $query .= $generator->buildEpilog($this->epilog);

        return $query;
    }
}
