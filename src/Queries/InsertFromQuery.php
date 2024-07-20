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
     * @param Closure|QueryLiteral|SelectQuery|string $from The query.
     * @param array $columns The columns.
     */
    public function __construct(Connection $connection, Closure|QueryLiteral|SelectQuery|string $from, array $columns = [])
    {
        parent::__construct($connection);

        $this->from = $from;
        $this->columns = $columns;
    }

    /**
     * Get the columns to insert.
     *
     * @return array The columns to insert.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the query to insert from.
     *
     * @return Closure|QueryLiteral|SelectQuery|string The query to insert from.
     */
    public function getFrom(): Closure|QueryLiteral|SelectQuery|string
    {
        return $this->from;
    }

    /**
     * Generate the SQL query.
     *
     * @return string The SQL query.
     */
    public function sql(ValueBinder|null $binder = null): string
    {
        return $this->connection->generator()
            ->compileInsertFrom($this, $binder);
    }
}
