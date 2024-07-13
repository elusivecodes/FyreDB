<?php
declare(strict_types=1);

namespace Fyre\DB;

use Closure;
use Fyre\DateTime\DateTime;
use Fyre\DB\Queries\DeleteQuery;
use Fyre\DB\Queries\InsertFromQuery;
use Fyre\DB\Queries\InsertQuery;
use Fyre\DB\Queries\ReplaceQuery;
use Fyre\DB\Queries\SelectQuery;
use Fyre\DB\Queries\UpdateBatchQuery;
use Fyre\DB\Queries\UpdateQuery;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_shift;
use function array_slice;
use function array_values;
use function count;
use function filter_var;
use function implode;
use function in_array;
use function is_array;
use function is_numeric;
use function preg_match;
use function preg_replace;
use function strtoupper;
use function trim;

use const FILTER_VALIDATE_FLOAT;

/**
 * QueryGenerator
 */
class QueryGenerator
{
    protected Connection $connection;

    /**
     * Combine conditions.
     *
     * @param array $fields The fields.
     * @param array $values The values.
     * @return array The combined conditions.
     */
    public static function combineConditions(array $fields, array $values): array
    {
        $fields = array_values($fields);
        $values = array_values($values);

        $fields = array_slice($fields, 0, count($values));

        $conditions = [];

        foreach ($fields as $i => $field) {
            $value = $values[$i] ?? null;

            if ($value === null) {
                $conditions[] = $field.' IS NULL';
            } else {
                $conditions[$field] = $value;
            }
        }

        return $conditions;
    }

    /**
     * Normalize conditions.
     *
     * @param array $fields The fields.
     * @param array $allValues The values.
     * @return array The normalized conditions.
     */
    public static function normalizeConditions(array $fields, array $allValues): array
    {
        if ($fields === [] || $allValues === []) {
            return [];
        }

        $allConditions = array_map(
            fn(array $values): array => static::combineConditions($fields, $values),
            $allValues
        );

        if (count($allConditions) === 1) {
            return array_shift($allConditions);
        }

        if (count($fields) > 1) {
            return [
                'or' => $allConditions,
            ];
        }

        $nullCondition = null;
        $values = [];

        foreach ($allConditions as $conditions) {
            foreach ($conditions as $key => $value) {
                if (is_numeric($key)) {
                    $nullCondition ??= $value;
                } else if (!in_array($value, $values)) {
                    $values[] = $value;
                }
            }
        }

        $valueCount = count($values);

        $conditions = [];

        $field = array_shift($fields);
        if ($valueCount === 1) {
            $conditions[$field] = array_shift($values);
        } else if ($valueCount > 1) {
            $conditions[$field.' IN'] = $values;
        }

        if ($nullCondition) {
            $conditions[] = $nullCondition;
        }

        if (count($conditions) > 1) {
            return [
                'or' => $conditions,
            ];
        }

        return $conditions;
    }

    /**
     * New QueryGenerator constructor.
     *
     * @param Connection $connection The connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Compile a DeleteQuery to SQL.
     *
     * @param DeleteQuery $query The DeleteQuery.
     * @param ValueBinder|null $binder The ValueBinder.
     * @return string The compiled SQL query.
     */
    public function compileDelete(DeleteQuery $query, ValueBinder|null $binder = null): string
    {
        $sql = $this->buildDelete($query->getTable(), $query->getAlias());
        $sql .= $this->buildJoin($query->getJoin(), $binder);
        $sql .= $this->buildWhere($query->getWhere(), $binder);
        $sql .= $this->buildOrderBy($query->getOrderBy());
        $sql .= $this->buildLimit($query->getLimit(), 0);
        $sql .= $this->buildEpilog($query->getEpilog());

        return $sql;
    }

    /**
     * Compile an InsertQuery to SQL.
     *
     * @param InsertQuery $query The InsertQuery.
     * @param ValueBinder|null $binder The ValueBinder.
     * @return string The compiled SQL query.
     */
    public function compileInsert(InsertQuery $query, ValueBinder|null $binder = null): string
    {
        $sql = $this->buildInsert($query->getTable(), $query->getValues(), $binder);
        $sql .= $this->buildEpilog($query->getEpilog());

        return $sql;
    }

    /**
     * Compile an InsertFromQuery to SQL.
     *
     * @param InsertFromQuery $query The InsertFromQuery.
     * @param ValueBinder|null $binder The ValueBinder.
     * @return string The compiled SQL query.
     */
    public function compileInsertFrom(InsertFromQuery $query, ValueBinder|null $binder = null): string
    {
        $sql = $this->buildInsertFrom($query->getTable(), $query->getFrom(), $query->getColumns(), $binder);
        $sql .= $this->buildEpilog($query->getEpilog());

        return $sql;
    }

    /**
     * Compile a ReplaceQuery to SQL.
     *
     * @param ReplaceQuery $query The ReplaceQuery.
     * @param ValueBinder|null $binder The ValueBinder.
     * @return string The compiled SQL query.
     */
    public function compileReplace(ReplaceQuery $query, ValueBinder|null $binder = null): string
    {
        $sql = $this->buildInsert($query->getTable(), $query->getValues(), $binder, 'REPLACE');
        $sql .= $this->buildEpilog($query->getEpilog());

        return $sql;
    }

    /**
     * Compile a SelectQuery to SQL.
     *
     * @param SelectQuery $query The SelectQuery.
     * @param ValueBinder|null $binder The ValueBinder.
     * @return string The compiled SQL query.
     */
    public function compileSelect(SelectQuery $query, ValueBinder|null $binder = null): string
    {
        $sql = $this->buildWith($query->getWith(), $binder);
        $sql .= $this->buildSelect($query->getTable(), $query->getSelect(), $query->getDistinct(), $binder);
        $sql .= $this->buildJoin($query->getJoin(), $binder);
        $sql .= $this->buildWhere($query->getWhere(), $binder);

        $unions = $query->getUnion();
        if ($unions !== []) {
            $sql = '('.$sql.')';
            $sql .= $this->buildUnion($unions, $binder);
        }

        $sql .= $this->buildGroupBy($query->getGroupBy());
        $sql .= $this->buildOrderBy($query->getOrderBy());
        $sql .= $this->buildHaving($query->getHaving(), $binder);
        $sql .= $this->buildLimit($query->getLimit(), $query->getOffset());
        $sql .= $this->buildEpilog($query->getEpilog());

        return $sql;
    }

    /**
     * Compile an UpdateQuery to SQL.
     *
     * @param UpdateQuery $query The UpdateQuery.
     * @param ValueBinder|null $binder The ValueBinder.
     * @return string The compiled SQL query.
     */
    public function compileUpdate(UpdateQuery $query, ValueBinder|null $binder = null): string
    {
        $sql = $this->buildUpdate($query->getTable(), $query->getData(), $binder);
        $sql .= $this->buildJoin($query->getJoin(), $binder);
        $sql .= $this->buildWhere($query->getWhere(), $binder);
        $sql .= $this->buildEpilog($query->getEpilog());

        return $sql;
    }

    /**
     * Compile an UpdateBatchQuery to SQL.
     *
     * @param UpdateBatchQuery $query The UpdateBatchQuery.
     * @param ValueBinder|null $binder The ValueBinder.
     * @return string The compiled SQL query.
     */
    public function compileUpdateBatch(UpdateBatchQuery $query, ValueBinder|null $binder = null): string
    {
        $sql = $this->buildUpdateBatch($query->getTable(), $query->getData(), $query->getKeys(), $binder);
        $sql .= $this->buildEpilog($query->getEpilog());

        return $sql;
    }

    /**
     * Recursively build conditions.
     *
     * @param array $conditions The conditions.
     * @param ValueBinder|null $binder The value binder.
     * @param string $type The condition separator.
     * @return string The conditions.
     */
    protected function buildConditions(array $conditions, ValueBinder|null $binder = null, string $type = 'AND'): string
    {
        $query = '';

        foreach ($conditions as $field => $value) {
            if ($query) {
                $query .= ' '.$type.' ';
            }

            if (is_array($value)) {
                if (is_numeric($field)) {
                    $subType = 'AND';
                } else {
                    $subType = strtoupper($field);
                }

                if (in_array($subType, ['AND', 'OR'])) {
                    $query .= '('.$this->buildConditions($value, $binder, $subType).')';
                } else if ($subType === 'NOT') {
                    $query .= 'NOT ('.$this->buildConditions($value, $binder).')';
                } else {
                    $field = trim($field);

                    if (preg_match('/^(.+?)\s+((?:NOT\s+)?IN)$/i', $field, $match)) {
                        $field = $match[1];
                        $comparison = strtoupper($match[2]);
                        $comparison = preg_replace('/\s+/', ' ', $comparison);
                    } else {
                        $comparison = 'IN';
                    }

                    $value = array_map(fn(mixed $val): string => $this->parseExpression($val, $binder), $value);

                    $query .= $field.' '.$comparison.' ('.implode(', ', $value).')';
                }
            } else if (is_numeric($field)) {
                $query .= $this->parseExpression($value, $binder, false);

            } else {
                $field = trim($field);

                if (preg_match('/^(.+?)\s+([\>\<]\=?|\!?\=|(?:NOT\s+)?(?:LIKE|IN)|IS(?:\s+NOT)?)$/i', $field, $match)) {
                    $field = $match[1];
                    $comparison = strtoupper($match[2]);
                    $comparison = preg_replace('/\s+/', ' ', $comparison);
                } else {
                    $comparison = '=';
                }

                $query .= $field.' '.$comparison.' '.$this->parseExpression($value, $binder);
            }
        }

        return $query;
    }

    /**
     * Generate the DELETE portion of the query.
     *
     * @param array $tables The tables.
     * @param array $aliases The table aliases to delete.
     * @return string The query string.
     */
    protected function buildDelete(array $tables, array $aliases = []): string
    {
        $query = 'DELETE';

        if ($aliases === [] && count($tables) > 1) {
            $aliases = array_map(
                function(int|string $alias, string $table): string {
                    if (is_numeric($alias)) {
                        return $table;
                    }

                    return $alias;
                },
                array_keys($tables),
                $tables
            );
        }

        if ($aliases !== []) {
            $query .= ' ';
            $query .= implode(', ', $aliases);
        }

        $query .= ' FROM ';
        $query .= $this->buildTables($tables);

        return $query;
    }

    /**
     * Generate the epilog portion of the query.
     *
     * @param string $string The string.
     * @return string The query string.
     */
    protected function buildEpilog(string $string): string
    {
        if (!$string) {
            return '';
        }

        return ' '.$string;
    }

    /**
     * Generate the GROUP BY portion of the query.
     *
     * @param array $fields The fields.
     * @return string The query string.
     */
    protected function buildGroupBy(array $fields): string
    {
        if ($fields === []) {
            return '';
        }

        $query = ' GROUP BY ';
        $query .= implode(', ', $fields);

        return $query;
    }

    /**
     * Generate the HAVING portion of the query.
     *
     * @param array $conditions The conditions.
     * @param ValueBinder|null $binder The value binder.
     * @return string The query string.
     */
    protected function buildHaving(array $conditions, ValueBinder|null $binder = null): string
    {
        if ($conditions === []) {
            return '';
        }

        $query = ' HAVING ';
        $query .= $this->buildConditions($conditions, $binder);

        return $query;
    }

    /**
     * Generate an INSERT query.
     *
     * @param array $tables The tables.
     * @param array $values The values.
     * @param ValueBinder|null $binder The value binder.
     * @param string $type The type of INSERT query.
     * @return string The query string.
     */
    protected function buildInsert(array $tables, array $values, ValueBinder|null $binder = null, string $type = 'INSERT'): string
    {
        $columns = array_keys($values[0] ?? []);

        $values = array_map(
            function(array $values) use ($binder): string {
                $values = array_map(fn(mixed $value): string => $this->parseExpression($value, $binder), $values);

                return '('.implode(', ', $values).')';
            },
            $values
        );

        $query = $type;
        $query .= ' INTO ';
        $query .= $this->buildTables($tables);
        $query .= ' ('.implode(', ', $columns).')';
        $query .= ' VALUES ';
        $query .= implode(', ', $values);

        return $query;
    }

    /**
     * Generate an INSERT query from another query.
     *
     * @param array $tables The tables.
     * @param Closure|QueryLiteral|SelectQuery|string $from The query.
     * @param array $columns The columns.
     * @param ValueBinder|null $binder The value binder.
     * @return string The query string.
     */
    protected function buildInsertFrom(array $tables, Closure|QueryLiteral|SelectQuery|string $from, array $columns, ValueBinder|null $binder = null): string
    {
        $query = 'INSERT INTO ';
        $query .= $this->buildTables($tables);

        if ($columns !== []) {
            $query .= ' ('.implode(', ', $columns).')';
        }

        $query .= ' VALUES ';

        $query .= $this->parseExpression($from, $binder, false);

        return $query;
    }

    /**
     * Generate the JOIN portion of the query.
     *
     * @param array $joins The joins.
     * @param ValueBinder|null $binder The value binder.
     * @return string The query string.
     */
    protected function buildJoin(array $joins, ValueBinder|null $binder = null): string
    {
        if ($joins === []) {
            return '';
        }

        $query = '';

        foreach ($joins as $alias => $join) {
            $join['type'] ??= 'INNER';
            $join['using'] ??= null;
            $join['conditions'] ??= [];

            $query .= ' '.strtoupper($join['type']).' JOIN ';
            $query .= $this->buildTables([
                $alias => $join['table'],
            ], $binder);

            if ($join['using']) {
                $query .= ' USING '.$join['using'];
            } else {
                $query .= ' ON '.$this->buildConditions($join['conditions'], $binder);
            }
        }

        return $query;
    }

    /**
     * Generate the LIMIT portion of the query.
     *
     * @param int|null $limit The limit.
     * @param int $offset The offset.
     * @return string The query string.
     */
    protected function buildLimit(int|null $limit, int $offset): string
    {
        if (!$limit && !$offset) {
            return '';
        }

        $query = ' LIMIT ';

        if ($offset) {
            $query .= $offset.', ';
        }

        $query .= $limit ?? 'NULL';

        return $query;
    }

    /**
     * Generate the ORDER BY portion of the query.
     *
     * @param array $fields The fields.
     * @return string The query string.
     */
    protected function buildOrderBy(array $fields): string
    {
        if ($fields === []) {
            return '';
        }

        $fields = array_map(
            fn(int|string $field, string $dir): string => is_numeric($field) ?
                $dir :
                $field.' '.strtoupper($dir),
            array_keys($fields),
            $fields
        );

        $query = ' ORDER BY ';
        $query .= implode(', ', $fields);

        return $query;
    }

    /**
     * Generate the SELECT portion of the query.
     *
     * @param array $tables The tables.
     * @param array $fields The fields.
     * @param bool $distinct Whether to use a DISTINCT clause.
     * @param ValueBinder|null $binder The value binder.
     * @return string The query string.
     */
    protected function buildSelect(array $tables, array $fields, bool $distinct = false, ValueBinder|null $binder = null): string
    {
        $fields = array_map(
            function(int|string $key, mixed $value) use ($binder): string {
                $value = $this->parseExpression($value, $binder, false);

                if (is_numeric($key)) {
                    return $value;
                }

                return static::buildSelectAs($value, $key);
            },
            array_keys($fields),
            $fields
        );

        $query = 'SELECT ';

        if ($distinct) {
            $query .= 'DISTINCT ';
        }

        $query .= implode(', ', $fields);

        if ($tables !== []) {
            $query .= ' FROM ';
            $query .= $this->buildTables($tables, $binder);
        }

        return $query;
    }

    /**
     * Build query tables.
     *
     * @param array $tables The tables.
     * @param ValueBinder|null $binder The value binder.
     * @param bool $with Whether this is a WITH clause.
     * @return string The table string.
     */
    protected function buildTables(array $tables, ValueBinder|null $binder = null, bool $with = false): string
    {
        $tables = array_map(
            function(int|string $alias, mixed $table) use ($binder, $with): string {
                if ($with) {
                    return $alias.' AS '.$this->parseExpression($table, $binder, false);
                }

                $fullTable = $this->parseExpression($table, $binder, false);

                $query = $fullTable;

                if ($alias !== $table && !is_numeric($alias)) {
                    $query .= ' AS '.$alias;
                }

                return $query;
            },
            array_keys($tables),
            $tables
        );

        return implode(', ', $tables);
    }

    /**
     * Generate the UNION portion of the query.
     *
     * @param array $unions The unions.
     * @param ValueBinder|null $binder The value binder.
     * @return string The query string.
     */
    protected function buildUnion(array $unions, ValueBinder|null $binder = null): string
    {
        if ($unions === []) {
            return '';
        }

        $query = '';

        foreach ($unions as $union) {
            switch ($union['type']) {
                case 'all':
                    $query .= ' UNION ALL ';
                    break;
                case 'distinct':
                    $query .= ' UNION DISTINCT ';
                    break;
                case 'except':
                    $query .= ' EXCEPT ';
                    break;
                case 'intersect':
                    $query .= ' INTERSECT ';
                    break;
            }

            $query .= $this->parseExpression($union['query'], $binder, false);
        }

        return $query;
    }

    /**
     * Generate the UPDATE portion of the query.
     *
     * @param array $tables The tables.
     * @param array $data The data.
     * @param ValueBinder|null $binder The value binder.
     * @return string The query string.
     */
    protected function buildUpdate(array $tables, array $data, ValueBinder|null $binder = null): string
    {
        $data = array_map(
            function(int|string $field, mixed $value) use ($binder): string {
                if (is_numeric($field)) {
                    return $this->parseExpression($value, $binder, false);
                }

                return $field.' = '.$this->parseExpression($value, $binder);
            },
            array_keys($data),
            $data
        );

        $query = 'UPDATE ';
        $query .= $this->buildTables($tables);
        $query .= ' SET ';
        $query .= implode(', ', $data);

        return $query;
    }

    /**
     * Generate a batch UPDATE query.
     *
     * @param array $tables The tables.
     * @param array $data The data.
     * @param array $keys The key to use for updating.
     * @param ValueBinder|null $binder The value binder.
     * @return string The query string.
     */
    protected function buildUpdateBatch(array $tables, array $data, array $keys, ValueBinder|null $binder = null): string
    {
        $columns = array_filter(
            array_keys($data[0] ?? []),
            fn(string $column): bool => !in_array($column, $keys)
        );

        $columns = array_values($columns);

        $allConditions = [];
        $allValues = [];
        $updateData = [];

        foreach ($columns as $i => $column) {
            $sql = $column.' = CASE';

            $useElse = false;
            foreach ($data as $j => $values) {
                if (!array_key_exists($column, $values)) {
                    $useElse = true;

                    continue;
                }

                if ($i === 0) {
                    $updateValues = array_map(
                        fn(string $column): mixed => $values[$column] ?? null,
                        $keys
                    );

                    $rowConditions = static::combineConditions($keys, $updateValues);

                    $allConditions[] = $rowConditions;
                    $allValues[] = $updateValues;
                } else {
                    $rowConditions = $allConditions[$j];
                }

                $sql .= ' WHEN ';
                $sql .= $this->buildConditions($rowConditions, $binder);
                $sql .= ' THEN ';
                $sql .= $this->parseExpression($values[$column], $binder);
            }

            if ($useElse) {
                $sql .= ' ELSE '.$column;
            }

            $sql .= ' END';

            $updateData[] = $sql;
        }

        $query = 'UPDATE ';
        $query .= $this->buildTables($tables);
        $query .= ' SET ';
        $query .= implode(', ', $updateData);

        $conditions = static::normalizeConditions($keys, $allValues);
        $query .= $this->buildWhere($conditions, $binder);

        return $query;
    }

    /**
     * Generate the WHERE portion of the query.
     *
     * @param array $conditions The conditions.
     * @param ValueBinder|null $binder The value binder.
     * @return string The query string.
     */
    protected function buildWhere(array $conditions, ValueBinder|null $binder = null): string
    {
        if ($conditions === []) {
            return '';
        }

        $query = ' WHERE ';
        $query .= $this->buildConditions($conditions, $binder);

        return $query;
    }

    /**
     * Generate the WITH portion of the query.
     *
     * @param array $withs The common table expressions.
     * @param ValueBinder|null $binder The value binder.
     * @return string The query string.
     */
    protected function buildWith(array $withs, ValueBinder|null $binder = null): string
    {
        if ($withs === []) {
            return '';
        }

        $query = 'WITH ';

        foreach ($withs as $with) {
            if (!$with['recursive']) {
                continue;
            }

            $query .= 'RECURSIVE ';
            break;
        }

        $withs = array_map(
            fn(array $with): string => $this->buildTables($with['cte'], $binder, true),
            $withs
        );

        $query .= implode(', ', $withs);
        $query .= ' ';

        return $query;
    }

    /**
     * Parse an expression string.
     *
     * @param mixed $value The value to parse.
     * @param ValueBinder|null $binder The value binder.
     * @param bool $quote Whether to quote the string.
     * @return string The expression string.
     */
    protected function parseExpression(mixed $value, ValueBinder|null $binder = null, bool $quote = true): string
    {
        if ($value instanceof Closure) {
            $value = $value($this->connection, $binder);
        }

        if ($value instanceof SelectQuery) {
            return '('.$value->sql($binder).')';
        }

        if ($value instanceof QueryLiteral) {
            return (string) $value;
        }

        if ($value instanceof DateTime) {
            $value = TypeParser::use('datetime')
                ->toDatabase($value);
        }

        if ($value === null) {
            return 'NULL';
        }

        if ($value === false) {
            $value = '0';
        }

        if ($value === true) {
            $value = '1';
        }

        if (!$quote) {
            return (string) $value;
        }

        if ($binder) {
            return $binder->bind($value);
        }

        $value = (string) $value;

        if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
            return $value;
        }

        return $this->connection->quote($value);
    }

    /**
     * Build the SELECT field AS alias portion of a SELECT query.
     *
     * @param string $field The field.
     * @param string $alias The field alias.
     * @return string The SELECT field AS alias portion of a SELECT query.
     */
    protected static function buildSelectAs(string $field, string $alias): string
    {
        return $field.' AS '.$alias;
    }
}
