<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\Postgres;

use Fyre\DB\QueryGenerator;
use Fyre\DB\ValueBinder;

use function array_map;
use function is_numeric;
use function str_replace;

/**
 * PostgresQueryGenerator
 */
class PostgresQueryGenerator extends QueryGenerator
{
    /**
     * Build the SELECT fields.
     *
     * @param array $fields The fields.
     * @param ValueBinder|null $binder The value binder.
     * @return array The SELECT fields.
     */
    protected function buildSelectFields(array $fields, ValueBinder|null $binder): array
    {
        return array_map(
            function(int|string $key, mixed $value) use ($binder): string {
                $value = $this->parseExpression($value, $binder, false);

                if (is_numeric($key)) {
                    return $value;
                }

                return $value.' AS "'.str_replace('"', '""', $key).'"';
            },
            array_keys($fields),
            $fields
        );
    }
}
