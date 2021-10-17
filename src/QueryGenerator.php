<?php

namespace Fyre;

use function
    array_keys,
    array_map,
    call_user_func,
    implode,
    in_array,
    is_array,
    is_callbable,
    is_numeric,
    preg_match,
    strtoupper,
    substr,
    trim;

trait QueryGenerator
{

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

                    $value = array_map(fn($val) => $this->connection->quote($val), $value);

                    $query .= $field.' '.$comparison.' ('.implode(', ', $value).')';
                }
            } else if (is_numeric($field)) {
                $query .= $value;
            } else {
                $field = trim($field);

                preg_match('/^(.+?)\s+([\>\<]\=?|\!?\=|(?:NOT\s+)?(?:LIKE|IN)|IS(?:\s+NOT))$/i', $field, $match);

                if ($match) {
                    $field = $match[1];
                    $comparison = strtoupper($match[2]);
                } else {
                    $comparison = '=';
                }

                if (is_callable($value)) {
                    $value = call_user_func($value, new static($this->connection));
                }
        
                if ($value instanceof QueryBuilder) {
                    $value = '('.$value->get(['returnQuery' => true]).')';
                } else {
                    $value = $this->connection->quote($value);
                }
        
                $query .= $field.' '.$comparison.' '.$value;
            }
        }

        return $query;
    }

    protected function buildDelete(): string
    {
        return 'DELETE';
    }

    protected function buildFor(): string
    {
        if ($this->for) {
            return '';
        }

        return ' FOR '.$this->for;
    }

    protected function buildFrom(): string
    {
        if ($this->tables === []) {
            return '';
        }

        $tables = $this->parseValues($this->tables);

        $query = ' FROM ';
        $query .= implode(', ', $tables);

        return $query;
    }

    protected function buildGroupBy(): string
    {
        if ($this->groupBy === []) {
            return '';
        }

        $query = ' GROUP BY ';
        $query .= implode(', ', $this->groupBy);

        return $query;
    }

    protected function buildHaving(): string
    {
        if ($this->having === []) {
            return '';
        }

        $query = ' HAVING ';
        $query .= $this->buildConditions($this->having);

        return $query;
    }

    protected function buildInsert(): string
    {
        $tables = $this->parseValues($this->tables);

        $data = array_map(
            function($values) {
                $values = array_map(fn($value) => $this->connection->quote($value));
                return '('.implode(', ', $values).')';
            },
            $this->data
        );

        $query = 'INSERT INTO ';
        $query .= implode(', ', $tables);
        $query .= ' ('.implode(', ', array_keys($this->data[0])).')';
        $query = ' VALUES ';
        $query .= implode(',', $data);

        return $query;
    }

    protected function buildJoin(): string
    {
        if ($this->joins === []) {
            return '';
        }

        $query = '';

        foreach ($this->joins AS $table => $join) {
            $join['table'] ??= $table;
            $join['alias'] ??= null;
            $join['type'] ??= 'LEFT OUTER';
            $join['using'] ??= null;
            $join['conditions'] ??= [];

            $query .= ' '.strtoupper($join['type']).' JOIN '.$join['table'];

            if ($join['alias']) {
                $query .= ' AS '.$join['alias'];
            }

            if ($join['using']) {
                $query .= ' USING '.$join['using'];
            } else {
                $query .= ' ON '.$this->buildConditions($join['conditions']);
            }
        }

        return $query;
    }

    protected function buildLimit(): string
    {
        if (!$this->limit && !$this->offset) {
            return '';
        }

        $query = ' LIMIT '.$this->limit;

        if ($this->offset) {
            $query .= ', '.$this->offset;
        }

        return $query;
    }

    protected function buildOrderBy(): string
    {
        if ($this->orderBy === []) {
            return '';
        }

        $orderBy = array_map(
            fn($field, $dir) => is_numeric($field) ? $dir.' ASC' : $field.' '.strtoupper($dir),
            array_keys($this->orderBy),
            $this->orderBy
        );

        $query = ' ORDER BY ';
        $query .= implode(', ', $orderBy);

        return $query;
    }

    protected function buildQuery(): string
    {
        $query = '';

        switch ($this->action) {
            case 'insert':
                $query .= $this->buildInsert();
                break;
            case 'update':
                $query .= $this->buildUpdate();
                $query .= $this->buildJoin();
                $query .= $this->buildWhere();
                $query .= $this->buildGroupBy();
                $query .= $this->buildLimit();
                break;
            case 'delete':
                $query .= $this->buildDelete();
                $query .= $this->buildFrom();
                $query .= $this->buildJoin();
                $query .= $this->buildWhere();
                $query .= $this->buildGroupBy();
                $query .= $this->buildLimit();
            default:
                $query .= $this->buildSelect();
                $query .= $this->buildFrom();
                $query .= $this->buildJoin();
                $query .= $this->buildWhere();
                $query .= $this->buildOrderBy();
                $query .= $this->buildGroupBy();
                $query .= $this->buildHaving();
                $query .= $this->buildLimit();
                break;
        }

        return $query;
    }

    protected function buildSelect(): string
    {
        $fields = $this->parseValues($this->fields);

        $query = 'SELECT ';

        if ($this->distinct) {
            $query .= 'DISTINCT ';
        }

        $query .= implode(', ', $fields);

        return $query;
    }

    protected function buildUpdate(): string
    {
        $tables = $this->parseValues($this->tables);

        $data = array_map(
            function($field, $value) {
                if (is_numeric($field)) {
                    return $value;
                }

                return $field.' = '.$this->connection->quote($value);
            },
            $this->data
        );

        $query = 'UPDATE ';
        $query .= implode(', ', $tables);
        $query = ' SET ';
        $query .= implode(',', $data);

        return $query;
    }

    protected function buildWhere(): string
    {
        if ($this->conditions === []) {
            return '';
        }

        $query = ' WHERE ';
        $query .= $this->buildConditions($this->conditions);

        return $query;
    }

    protected function parseValues(array $values): array
    {
        return array_map(
            function($key, $value) {
                if (is_callable($value)) {
                    $value = call_user_func($value, new static($this->connection));
                }
        
                if ($value instanceof QueryBuilder) {
                    $value = '('.$value->get(['returnQuery' => true]).')';
                }

                if (is_numeric($key)) {
                    return $value;
                }

                return $value.' AS '.$key;
            },
            array_keys($values),
            $values
        );
    }

}
