<?php
declare(strict_types=1);

namespace Fyre\DB;

use
    Closure;

use const
    FILTER_VALIDATE_FLOAT;

use function
    array_column,
    array_filter,
    array_key_exists,
    array_keys,
    array_map,
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
     * @return string The query string.
     */
    public function buildDelete(array $tables): string
    {
        $deletes = array_map(
            function($alias, $table) {
                if (is_numeric($alias)) {
                    return $table;
                }

                return $alias;
            },
            array_keys($tables),
            $tables
        );

        $query = 'DELETE FROM ';
        $query .= $this->buildTables($tables);

        $query .= ' USING ';
        $query .= implode(', ', $deletes);

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
            function($values) {
                $values = array_map(fn($value) => $this->parseExpression($value), $values);
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
        $query .= $this->parseExpression($insertQuery, false, false);

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

        foreach ($joins AS $table => $join) {
            $join['table'] ??= $table;
            $join['alias'] ??= $table;
            $join['type'] ??= 'INNER';
            $join['using'] ??= null;
            $join['conditions'] ??= [];

            $query .= ' '.strtoupper($join['type']).' JOIN ';
            $query .= $this->buildTables([
                $join['alias'] => $join['table']
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
            fn($field, $dir) => is_numeric($field) ?
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
     * Generate the SELECT BY portion of the query.
     * @param array $tables The tables.
     * @param array $fields The fields.
     * @param bool $distinct Whether to use a DISTINCT clause.
     * @return string The query string.
     */
    public function buildSelect(array $tables, array $fields, bool $distinct): string
    {
        $fields = array_map(
            function($key, $value) {
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
            function($field, $value) {
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
     * @param string $updateKey The key to use for updating.
     * @return string The query string.
     */
    public function buildUpdateBatch(array $tables, array $data, string $updateKey): string
    {
        $columns = array_filter(
            array_keys($data[0] ?? []),
            fn($column) => $column !== $updateKey
        );

        $values = array_map(
            function($column) use ($data, $updateKey) {
                $sql = $column.' = CASE';

                $useElse = false;
                foreach ($data AS $values) {
                    if (!array_key_exists($column, $values)) {
                        $useElse = true;
                        continue;
                    }

                    $sql .= ' WHEN '.$updateKey.' = ';
                    $sql .= $this->parseExpression($values[$updateKey]);
                    $sql .= ' THEN ';
                    $sql .= $this->parseExpression($values[$column]);
                }

                if ($useElse) {
                    $sql .= ' ELSE '.$column;
                }

                $sql .= ' END';

                return $sql;
            },
            $columns
        );

        $query = 'UPDATE ';
        $query .= $this->buildTables($tables);
        $query .= ' SET ';
        $query .= implode(', ', $values);

        $query .= $this->buildWhere([
            $updateKey.' IN' => array_column($data, $updateKey)
        ]);

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

            $query .= $this->parseExpression($union['query'], false, false);
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
                } else {
                    $field = trim($field);

                    preg_match('/^(.+?)\s+((?:NOT )?IN)$/i', $field, $match);

                    if ($match) {
                        $field = $match[1];
                        $comparison = strtoupper($match[2]);
                    } else {
                        $comparison = '=';
                    }

                    $value = array_map(fn($val) => $this->parseExpression($val), $value);

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
     * @return string The table string.
     */
    protected function buildTables(array $tables): string
    {
        $tables = array_map(
            function($alias, $table) {
                $query = $this->parseExpression($table, false);

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
     * Parse an expression string.
     * @param mixed $value The value to parse.
     * @param bool $quote Whether to quote the string.
     * @param bool $groupQuery Whether to group subqueries.
     * @return string The expression string.
     */
    protected function parseExpression($value, bool $quote = true, bool $groupQuery = true): string
    {
        if ($value instanceof Closure) {
            $builder = new QueryBuilder($this->connection);
            $value = $value($builder);
        }

        if ($value instanceof QueryBuilder) {
            $sql = $value->sql();
            return $groupQuery ? '('.$sql.')' : $sql;
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

}
