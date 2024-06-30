<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\Postgres;

use Fyre\DB\Queries\InsertQuery;
use Fyre\DB\QueryGenerator;
use Fyre\DB\ValueBinder;

/**
 * PostgresQueryGenerator
 */
class PostgresQueryGenerator extends QueryGenerator
{
    public function compileInsert(InsertQuery $query, ValueBinder|null $binder = null): string
    {
        $epilog = $query->getEpilog();

        if (!$epilog) {
            $query->epilog('RETURNING *');
        }

        return parent::compileInsert($query, $binder);
    }
}
