<?php
declare(strict_types=1);

namespace Fyre\DB;

use
    Closure;

use const
    FILTER_VALIDATE_FLOAT;

use function
    array_filter,
    array_key_exists,
    array_keys,
    array_map,
    array_shift,
    array_slice,
    array_values,
    count,
    filter_var,
    implode,
    in_array,
    is_array,
    is_numeric,
    preg_match,
    strtoupper,
    trim;

/**
 * QueryGenerator
 */
class QueryGenerator
{

    protected Connection $connection;

    /**
     * New QueryGenerator constructor.
     * @param Connection $connection The connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Generate the DELETE portion of the query.
     * @param array $tables The tables.
     * @param array $aliases The table aliases to delete.
     * @return string The query string.
     */
    public function buildDelete(array $tables, array $aliases = []): string
    {
        $query = 'DELETE';

        if ($aliases === [] && count($tables) > 1) {
            $aliases = array_map(
                function(mixed $alias, string $table): string {
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
     * @param string $string The string.
     * @return string The query string.
     */
    public function buildEpilog(string $string): string
    {
        if (!$string) {
            return '';
        }

        return ' '.$string;
    }

    /**
     * Generate the GROUP BY portion of the query.
     * @param array $fields The fields.
     * @return string The query string.
     */
    public function buildGroupBy(array $fields): string
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
     * @param array $conditions The conditions.
     * @return string The query string.
     */
    public function buildHaving(array $conditions): string
    {
        if ($conditions === []) {
            return '';
        }

        $query = ' HAVING ';
        $query .= $this->buildConditions($conditions);

        return $query;
    }

    /**
     * Generate an INSERT query.
     * @param array $tables The tables.
     * @param array $data The data.
     * @return string The query string.
     */
    public function buildInsert(array $tables, array $data): string
    {
        return $this->buildInsertBatch($tables, [$data]);
    }

    /**
     * Generate a batch INSERT query.
     * @param array $tables The tables.
     * @param array $data The data.
     * @param string $type The type of INSERT query.
     * @return string The query string.
     */
    public function buildInsertBatch(array $tables, array $data, string $type = 'INSERT'): string
    {
        $columns = array_keys($data[0] ?? []);
        $values = array_map(
            function(array $values): string {
                $values = array_map(fn(mixed $value): string => $this->parseExpression($value), $values);
                return '('.implode(', ', $values).')';
            },
            $data
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
     * @param array $tables The tables.
     * @param Closure|QueryBuilder|QueryLiteral|string $insertQuery The query.
     * @param array $columns The columns.
     * @return string The query string.
     */
    public function buildInsertFrom(array $tables, Closure|QueryBuilder|QueryLiteral|string $insertQuery, array $columns): string
    {
        $query = 'INSERT INTO ';
        $query .= $this->buildTables($tables);

        if ($columns !== []) { 
            $query .= ' ('.implode(', ', $columns).')';
        }

        $query .= ' VALUES ';

        $query .= $this->parseExpression($insertQuery, false);

        return $query;
    }

    /**
     * Generate the JOIN portion of the query.
     * @param array $joins The joins.
     * @return string The query string.
     */
    public function buildJoin(array $joins): string
    {
        if ($joins === []) {
            return '';
        }

        $query = '';

        foreach ($joins AS $alias => $join) {
            $join['type'] ??= 'INNER';
            $join['using'] ??= null;
            $join['conditions'] ??= [];

            $query .= ' '.strtoupper($join['type']).' JOIN ';
            $query .= $this->buildTables([
                $alias => $join['table']
            ]);

            if ($join['using']) {
                $query .= ' USING '.$join['using'];
            } else {
                $query .= ' ON '.$this->buildConditions($join['conditions']);
            }
        }

        return $query;
    }

    /**
     * Generate the LIMIT portion of the query.
     * @param int|null $limit The limit.
     * @param int $offset The offset.
     * @return string The query string.
     */
    public function buildLimit(int|null $limit, int $offset): string
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
     * @param array $fields The fields.
     * @return string The query string.
     */
    public function buildOrderBy(array $fields): string
    {
        if ($fields === []) {
            return '';
        }

        $fields = array_map(
            fn(mixed $field, string $dir): string => is_numeric($field) ?
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
     * Generate a REPLACE query.
     * @param array $tables The tables.
     * @param array $data The data.
     * @return string The query string.
     */
    public function buildReplace(array $tables, array $data): string
    {
        return $this->buildInsertBatch($tables, [$data], 'REPLACE');
    }

    /**
     * Generate a batch REPLACE query.
     * @param array $tables The tables.
     * @param array $data The data.
     * @return string The query string.
     */
    public function buildReplaceBatch(array $tables, array $data): string
    {
        return $this->buildInsertBatch($tables, $data, 'REPLACE');
    }

    /**
     * Generate the SELECT portion of the query.
     * @param array $tables The tables.
     * @param array $fields The fields.
     * @param bool $distinct Whether to use a DISTINCT clause.
     * @return string The query string.
     */
    public function buildSelect(array $tables, array $fields, bool $distinct = false): string
    {
        $fields = array_map(
            function(mixed $key, mixed $value) {
                $value = $this->parseExpression($value, false);

                if (is_numeric($key)) {
                    return $value;
                }

                return $value.' AS '.$key;
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
            $query .= $this->buildTables($tables);
        }

        return $query;
    }

    /**
     * Generate the UPDATE portion of the query.
     * @param array $tables The tables.
     * @param array $data The data.
     * @return string The query string.
     */
    public function buildUpdate(array $tables, array $data): string
    {
        $data = array_map(
            function(mixed $field, mixed $value): string {
                if (is_numeric($field)) {
                    return $this->parseExpression($value, false);
                }

                return $field.' = '.$this->parseExpression($value);
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
     * @param array $tables The tables.
     * @param array $data The data.
     * @param array $updateKeys The key to use for updating.
     * @return string The query string.
     */
    public function buildUpdateBatch(array $tables, array $data, array $updateKeys): string
    {
        $columns = array_filter(
            array_keys($data[0] ?? []),
            fn(string $column): bool => !in_array($column, $updateKeys)
        );

        $columns = array_values($columns);

        $allConditions = [];
        $allValues = [];
        $updateData = [];

        foreach ($columns AS $i => $column) {
            $sql = $column.' = CASE';

            $useElse = false;
            foreach ($data AS $j => $values) {
                if (!array_key_exists($column, $values)) {
                    $useElse = true;
                    continue;
                }

                if ($i === 0) {
                    $updateValues = array_map(
                        fn(string $column): mixed => $values[$column] ?? null,
                        $updateKeys
                    );

                    $rowConditions = static::combineConditions($updateKeys, $updateValues);

                    $allConditions[] = $rowConditions;
                    $allValues[] = $updateValues;
                } else {
                    $rowConditions = $allConditions[$j];
                }

                $sql .= ' WHEN ';
                $sql .= $this->buildConditions($rowConditions);
                $sql .= ' THEN ';
                $sql .= $this->parseExpression($values[$column]);
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

        $conditions = static::normalizeConditions($updateKeys, $allValues);
        $query .= $this->buildWhere($conditions);

        return $query;
    }

    /**
     * Generate the UNION portion of the query.
     * @param array $unions The unions.
     * @return string The query string.
     */
    public function buildUnion(array $unions): string
    {
        if ($unions === []) {
            return '';
        }

        $query = '';

        foreach ($unions AS $union) {
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

            $query .= $this->parseExpression($union['query'], false);
        }

        return  $query;
    }

    /**
     * Generate the WHERE portion of the query.
     * @param array $conditions The conditions.
     * @return string The query string.
     */
    public function buildWhere(array $conditions): string
    {
        if ($conditions === []) {
            return '';
        }

        $query = ' WHERE ';
        $query .= $this->buildConditions($conditions);

        return $query;
    }

    /**
     * Generate the WITH portion of the query.
     * @param array $withs The common table expressions.
     * @return string The query string.
     */
    public function buildWith(array $withs): string
    {
        if ($withs === []) {
            return '';
        }

        $query = 'WITH ';

        foreach ($withs AS $with) {
            if (!$with['recursive']) {
                continue;
            }

            $query .= 'RECURSIVE ';
            break;
        }

        $withs = array_map(
            fn(array $with): string => $this->buildTables($with['cte'], true),
            $withs
        );

        $query .= implode(', ', $withs);
        $query .= ' ';

        return $query;
    }

    /**
     * Recursively build conditions.
     * @param array $conditions The conditions.
     * @param string $type The condition separator.
     * @return string The conditions.
     */
    protected function buildConditions(array $conditions, string $type = 'AND'): string
    {
        $query = '';

        foreach ($conditions AS $field => $value) {
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
                    $query .= '('.$this->buildConditions($value, $subType).')';
                } else if ($subType === 'NOT') {
                    $query .= 'NOT ('.$this->buildConditions($value).')';
                } else {
                    $field = trim($field);

                    preg_match('/^(.+?)\s+((?:NOT )?IN)$/i', $field, $match);

                    if ($match) {
                        $field = $match[1];
                        $comparison = strtoupper($match[2]);
                    } else {
                        $comparison = '=';
                    }

                    $value = array_map(fn(mixed $val): string => $this->parseExpression($val), $value);

                    $query .= $field.' '.$comparison.' ('.implode(', ', $value).')';
                }
            } else if (is_numeric($field)) {
                $query .= $this->parseExpression($value, false);
            } else {
                $field = trim($field);

                preg_match('/^(.+?)\s+([\>\<]\=?|\!?\=|(?:NOT\s+)?(?:LIKE|IN)|IS(?:\s+NOT)?)$/i', $field, $match);

                if ($match) {
                    $field = $match[1];
                    $comparison = strtoupper($match[2]);
                } else {
                    $comparison = '=';
                }

                $value = $this->parseExpression($value);
        
                $query .= $field.' '.$comparison.' '.$value;
            }
        }

        return $query;
    }

    /**
     * Build query tables.
     * @param array $tables The tables.
     * @param bool $with Whether this is a WITH clause.
     * @return string The table string.
     */
    protected function buildTables(array $tables, bool $with = false): string
    {
        $tables = array_map(
            function(mixed $alias, mixed $table) use ($with): string {
                $query = $this->parseExpression($table, $with);

                if ($with) {
                    $query = $alias.' AS '.$query;
                } else if ($alias !== $table && !is_numeric($alias)) {
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
     * Parse an expression string.
     * @param mixed $value The value to parse.
     * @param bool $quote Whether to quote the string.
     * @return string The expression string.
     */
    protected function parseExpression(mixed $value, bool $quote = true): string
    {
        if ($value instanceof Closure) {
            $builder = new QueryBuilder($this->connection);
            $value = $value($builder);
        }

        if ($value instanceof QueryBuilder) {
            return '('.$value->sql().')';
        }

        if ($value instanceof QueryLiteral) {
            return (string) $value;
        }

        if ($value === null) {
            return 'NULL';
        }

        if ($value === false) {
            return '0';
        }

        if ($value === true) {
            return '1';
        }

        if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
            return (string) (float) $value;
        }

        $value = (string) $value;

        if (!$quote) {
            return $value;
        }

        return $this->connection->quote($value);
    }

    /**
     * Combine conditions.
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

        foreach ($fields AS $i => $field) {
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
     * @param array $fields The fields.
     * @param array $allValues The values.
     * @return array The normalized conditions.
     */
    public static function normalizeConditions(array $fields, array $allValues): array
    {
        if ($fields === [] || $allValues === []) {
            return [];
        }

        $allConditions =  array_map(
            fn(array $values): array => static::combineConditions($fields, $values),
            $allValues
        );

        if (count($allConditions) === 1) {
            return array_shift($allConditions);
        }

        if (count($fields) > 1) {
            return [
                'or' => $allConditions
            ];
        }

        $nullCondition = null;
        $values = [];

        foreach ($allConditions AS $conditions) {
            foreach ($conditions AS $key => $value) {
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
                'or' => $conditions
            ];
        }

        return $conditions;
    }

}
