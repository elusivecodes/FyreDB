<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\Postgres;

use Fyre\DB\Queries\InsertQuery;
use Fyre\DB\QueryGenerator;
use Fyre\DB\ValueBinder;

use function str_replace;

/**
 * PostgresQueryGenerator
 */
class PostgresQueryGenerator extends QueryGenerator
{
    /**
     * Compile an InsertQuery to SQL.
     *
     * @param InsertQuery $query The InsertQuery.
     * @param ValueBinder|null $binder The ValueBinder.
     * @return string The compiled SQL query.
     */
    public function compileInsert(InsertQuery $query, ValueBinder|null $binder = null): string
    {
        $epilog = $query->getEpilog();

        if (!$epilog) {
            $query->epilog('RETURNING *');
        }

        return parent::compileInsert($query, $binder);
    }

    /**
     * Build the SELECT field AS alias portion of a SELECT query.
     * @param string $field The field.
     * @param string $alias The field alias.
     * @return string The SELECT field AS alias portion of a SELECT query.
     */
    protected static function buildSelectAs(string $field, string $alias): string
    {
        return $field.' AS "'.str_replace('"', '""', $alias).'"';
    }
}
