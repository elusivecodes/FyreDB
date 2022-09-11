<?php
declare(strict_types=1);

namespace Fyre\DB;

use
    Closure;

use function
    array_filter,
    array_merge,
    array_unique,
    is_numeric,
    is_string;

/**
 * QueryBuilder
 */
class QueryBuilder
{

    protected Connection $connection;

    protected string $action = 'select';
    protected bool $dirty = false;

    protected array $with = [];
    protected array $tables = [];
    protected array $data = [];

    protected bool $distinct = false;
    protected array $fields = [];
    protected array $joins = [];
    protected array $conditions = [];
    protected array $orderBy = [];
    protected array $groupBy = [];
    protected array $having = [];
    protected int $offset = 0;
    protected int|null $limit = null;
    protected string $epilog = '';
    protected array $unions = [];

    protected array $deleteAliases = [];
    protected array $insertColumns = [];
    protected Closure|QueryBuilder|QueryLiteral|string $insertQuery = '';
    protected array $updateKeys = [];

    /**
     * New QueryBuilder constructor.
     * @param Connection $connection The connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Generate the SQL query.
     * @return string The SQL query.
     */
    public function __toString(): string
    {
        return $this->sql();
    }

    /**
     * Set query as DELETE.
     * @param string|array $aliases The table aliases to delete.
     * @param bool $overwrite Whether to overwrite the existing aliases.
     * @return QueryBuilder The QueryBuilder.
     */
    public function delete(string|array|null $aliases = null, bool $overwrite = false): static
    {
        $aliases = (array) $aliases;
        $aliases = array_filter($aliases);

        if ($overwrite) {
            $this->deleteAliases = $aliases;
        } else {
            $this->deleteAliases = array_merge($this->deleteAliases, $aliases);
        }

        $this->action = 'delete';
        $this->dirty();

        return $this;
    }

    /**
     * Set the DISTINCT clause.
     * @param bool $distinct Whether to set the DISTINCT clause.
     * @return QueryBuilder The QueryBuilder.
     */
    public function distinct(bool $distinct = true): static
    {
        $this->distinct = $distinct;
        $this->dirty();

        return $this;
    }

    /**
     * Set the epilog.
     * @param string $epilog The epilog.
     * @return QueryBuilder The QueryBuilder.
     */
    public function epilog(string $epilog = ''): static
    {
        $this->epilog = $epilog;
        $this->dirty();

        return $this;
    }

    /**
     * Add an EXCEPT query.
     * @param Closure|QueryBuilder|QueryLiteral|string $query The query.
     * @param bool $overwrite Whether to overwrite the existing unions.
     * @return QueryBuilder The QueryBuilder.
     */
    public function except(Closure|QueryBuilder|QueryLiteral|string $query, bool $overwrite = false): static
    {
        return $this->union($query, $overwrite, 'except');
    }

    /**
     * Execute the query.
     * @return ResultSet|bool The query result.
     * @throws DbException if the query failed.
     */
    public function execute(): ResultSet|bool
    {
        $query = $this->sql();
        $result = $this->connection->query($query);

        $this->dirty = false;

        return $result;
    }

    /**
     * Get the Connection.
     * @return Connection The Connection.
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Get the INSERT/UPDATE data.
     * @return array The INSERT/UPDATE data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get the DISTINCT clause.
     * @return bool The DISTINCT clause.
     */
    public function getDistinct(): bool
    {
        return $this->distinct;
    }

    /**
     * Get the GROUP BY fields.
     * @return array The GROUP BY fields.
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * Get the HAVING conditions.
     * @return array The HAVING conditions.
     */
    public function getHaving(): array
    {
        return $this->having;
    }

    /**
     * Get the JOIN tables.
     * @return array The JOIN tables.
     */
    public function getJoin(): array
    {
        return $this->joins;
    }

    /**
     * Get the LIMIT clause.
     * @return int|null The LIMIT clause.
     */
    public function getLimit(): int|null
    {
        return $this->limit;
    }

    /**
     * Get the OFFSET clause.
     * @return int The OFFSET clause.
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Get the ORDER BY fields.
     * @return array The ORDER BY fields.
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * Get the SELECT fields.
     * @return array The SELECT fields.
     */
    public function getSelect(): array
    {
        return $this->fields;
    }

    /**
     * Get the tables.
     * @return array The tables.
     */
    public function getTable(): array
    {
        return $this->tables;
    }

    /**
     * Get the UNION queries.
     * @return array The UNION queries.
     */
    public function getUnion(): array
    {
        return $this->unions;
    }

    /**
     * Get the WHERE conditions.
     * @return array The WHERE conditions.
     */
    public function getWhere(): array
    {
        return $this->conditions;
    }

    /**
     * Get the WITH queries.
     * @return array The WITH queries.
     */
    public function getWith(): array
    {
        return $this->with;
    }

    /**
     * Set the GROUP BY fields.
     * @param string|array $fields The fields.
     * @param bool $overwrite Whether to overwrite the existing fields.
     * @return QueryBuilder The QueryBuilder.
     */
    public function groupBy(string|array $fields, bool $overwrite = false): static
    {
        $fields = (array) $fields;

        if ($overwrite) {
            $this->groupBy = $fields;
        } else {
            $this->groupBy = array_merge($this->groupBy, $fields);
        }

        $this->dirty();

        return $this;
    }

    /**
     * Set the HAVING conditions.
     * @param string|array $conditions The conditions.
     * @param bool $overwrite Whether to overwrite the existing conditions.
     * @return QueryBuilder The QueryBuilder.
     */
    public function having(string|array $conditions, bool $overwrite = false): static
    {
        $conditions = (array) $conditions;

        if ($overwrite) {
            $this->having = $conditions;
        } else {
            $this->having = array_merge($this->having, $conditions);
        }

        $this->dirty();

        return $this;
    }

    /**
     * Set query as an INSERT.
     * @param array $data The data.
     * @param bool $overwrite Whether to overwrite the existing data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function insert(array $data, bool $overwrite = false): static
    {
        if ($overwrite) {
            $this->data = $data;
        } else {
            $this->data = array_merge($this->data, $data);
        }

        $this->action = 'insert';
        $this->dirty();

        return $this;
    }

    /**
     * Set query as a batch INSERT.
     * @param array $data The data.
     * @param bool $overwrite Whether to overwrite the existing data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function insertBatch(array $data, bool $overwrite = false): static
    {
        if ($overwrite) {
            $this->data = $data;
        } else {
            $this->data = array_merge($this->data, $data);
        }

        $this->action = 'insertBatch';
        $this->dirty();

        return $this;
    }

    /**
     * Set query as an INSERT from another query.
     * @param Closure|QueryBuilder|QueryLiteral|string $query The query.
     * @param array| $columns The columns.
     * @return QueryBuilder The QueryBuilder.
     */
    public function insertFrom(Closure|QueryBuilder|QueryLiteral|string $query, array $columns = []): static
    {
        $this->insertQuery = $query;
        $this->insertColumns = $columns;

        $this->action = 'insertFrom';
        $this->dirty();

        return $this;
    }

    /**
     * Add an INTERSECT query.
     * @param Closure|QueryBuilder|QueryLiteral|string $query The query.
     * @param bool $overwrite Whether to overwrite the existing unions.
     * @return QueryBuilder The QueryBuilder.
     */
    public function intersect(Closure|QueryBuilder|QueryLiteral|string $query, bool $overwrite = false): static
    {
        return $this->union($query, $overwrite, 'intersect');
    }

    /**
     * Set the JOIN tables.
     * @param array $joins The joins.
     * @param bool $overwrite Whether to overwrite the existing joins.
     * @return QueryBuilder The QueryBuilder.
     */
    public function join(array $joins, bool $overwrite = false): static
    {
        $joins = static::normalizeJoins($joins);

        if ($overwrite) {
            $this->joins = $joins;
        } else {
            $this->joins = array_merge($this->joins, $joins);
        }

        $this->dirty();

        return $this;
    }

    /**
     * Set the LIMIT and OFFSET clauses.
     * @param int|null $limit The limit.
     * @param int|null $offset The offset.
     * @return QueryBuilder The QueryBuilder.
     */
    public function limit(int|null $limit = null, int|null $offset = null): static
    {
        $this->limit = $limit;

        if ($offset !== null) {
            $this->offset = $offset;
        }

        $this->dirty();

        return $this;
    }

    /**
     * Create a QueryLiteral.
     * @param string $string The literal string.
     * @return QueryLiteral A new QueryLiteral.
     */
    public function literal(string $string): QueryLiteral
    {
        return new QueryLiteral($string);
    }

    /**
     * Set the LIMIT and OFFSET clauses.
     * @param int $offset The offset.
     * @return QueryBuilder The QueryBuilder.
     */
    public function offset(int $offset = 0): static
    {
        $this->offset = $offset;

        $this->dirty();

        return $this;
    }

    /**
     * Set the ORDER BY fields.
     * @param string|array $fields The fields.
     * @param bool $overwrite Whether to overwrite the existing fields.
     * @return QueryBuilder The QueryBuilder.
     */
    public function orderBy(string|array $fields, bool $overwrite = false): static
    {
        $fields = (array) $fields;

        if ($overwrite) {
            $this->orderBy = $fields;
        } else {
            $this->orderBy = array_merge($this->orderBy, $fields);
        }

        $this->dirty();

        return $this;
    }

    /**
     * Set query as a REPLACE.
     * @param array $data The data.
     * @param bool $overwrite Whether to overwrite the existing data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function replace(array $data, bool $overwrite = false): static
    {
        if ($overwrite) {
            $this->data = $data;
        } else {
            $this->data = array_merge($this->data, $data);
        }

        $this->action = 'replace';
        $this->dirty();

        return $this;
    }

    /**
     * Set query as a batch REPLACE.
     * @param array $data The data.
     * @param bool $overwrite Whether to overwrite the existing data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function replaceBatch(array $data, bool $overwrite = false): static
    {
        if ($overwrite) {
            $this->data = $data;
        } else {
            $this->data = array_merge($this->data, $data);
        }

        $this->action = 'replaceBatch';
        $this->dirty();

        return $this;
    }

    /**
     * Set the SELECT fields.
     * @param string|array $fields The fields.
     * @param bool $overwrite Whether to overwrite the existing fields.
     * @return QueryBuilder The QueryBuilder.
     */
    public function select(string|array $fields = '*', bool $overwrite = false): static
    {
        $fields = (array) $fields;

        if ($overwrite) {
            $this->fields = $fields;
        } else {
            $this->fields = array_merge($this->fields, $fields);
        }

        $this->action = 'select';
        $this->dirty();

        return $this;
    }

    /**
     * Generate the SQL query.
     * @return string The SQL query.
     */
    public function sql(): string
    {
        $generator = $this->connection->generator();

        switch ($this->action) {
            case 'insert':
                $query = $generator->buildInsert($this->tables, $this->data);
                break;
            case 'insertBatch':
                $query = $generator->buildInsertBatch($this->tables, $this->data);
                break;
            case 'insertFrom':
                $query = $generator->buildInsertFrom($this->tables, $this->insertQuery, $this->insertColumns);
                break;
            case 'replace':
                $query = $generator->buildReplace($this->tables, $this->data);
                break;
            case 'replaceBatch':
                $query = $generator->buildReplaceBatch($this->tables, $this->data);
                break;
            case 'update':
                $query = $generator->buildWith($this->with);
                $query .= $generator->buildUpdate($this->tables, $this->data);
                $query .= $generator->buildJoin($this->joins);
                $query .= $generator->buildWhere($this->conditions);
                break;
            case 'updateBatch':
                $query = $generator->buildUpdateBatch($this->tables, $this->data, $this->updateKeys);
                break;
            case 'delete':
                $query = $generator->buildWith($this->with);
                $query .= $generator->buildDelete($this->tables, $this->deleteAliases);
                $query .= $generator->buildJoin($this->joins);
                $query .= $generator->buildWhere($this->conditions);
                $query .= $generator->buildOrderBy($this->orderBy);
                $query .= $generator->buildLimit($this->limit, $this->offset);
                break;
            case 'select':
                $query = $generator->buildWith($this->with);
                $query .= $generator->buildSelect($this->tables, $this->fields, $this->distinct);
                $query .= $generator->buildJoin($this->joins);
                $query .= $generator->buildWhere($this->conditions);

                if ($this->unions !== []) {
                    $query = '('.$query.')';
                    $query .= $generator->buildUnion($this->unions);
                }

                $query .= $generator->buildOrderBy($this->orderBy);
                $query .= $generator->buildGroupBy($this->groupBy);
                $query .= $generator->buildHaving($this->having);
                $query .= $generator->buildLimit($this->limit, $this->offset);
                $query .= $generator->buildEpilog($this->epilog);
                break;
        }

        return $query;
    }

    /**
     * Set the tables.
     * @param string|array $tables The tables.
     * @param bool $overwrite Whether to overwrite the existing tables.
     * @return QueryBuilder The QueryBuilder.
     */
    public function table(string|array $tables, bool $overwrite = false): static
    {
        $tables = (array) $tables;

        if ($overwrite) {
            $this->tables = $tables;
        } else {
            $this->tables = array_merge($this->tables, $tables);
        }

        $this->dirty();

        return $this;
    }

    /**
     * Set query as an UPDATE.
     * @param array $data The data.
     * @param bool $overwrite Whether to overwrite the existing data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function update(array $data, bool $overwrite = false): static
    {
        if ($overwrite) {
            $this->data = $data;
        } else {
            $this->data = array_merge($this->data, $data);
        }

        $this->action = 'update';
        $this->dirty();

        return $this;
    }

    /**
     * Set query as a batch UPDATE.
     * @param array $data The data.
     * @param string|array $updateKeys The key to use for updating.
     * @param bool $overwrite Whether to overwrite the existing data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function updateBatch(array $data, string|array $updateKeys, bool $overwrite = false): static
    {
        $updateKeys = (array) $updateKeys;

        if ($overwrite) {
            $this->data = $data;
            $this->updateKeys = $updateKeys;
        } else {
            $this->data = array_merge($this->data, $data);
            $this->updateKeys = array_merge($this->updateKeys, $updateKeys);
        }

        $this->updateKeys = array_unique($this->updateKeys);

        $this->action = 'updateBatch';
        $this->dirty();

        return $this;
    }

    /**
     * Add an UNION DISTINCT query.
     * @param Closure|QueryBuilder|QueryLiteral|string $query The query.
     * @param bool $overwrite Whether to overwrite the existing unions.
     * @param string $type The union type.
     * @return QueryBuilder The QueryBuilder.
     */
    public function union(Closure|QueryBuilder|QueryLiteral|string $query, bool $overwrite = false, string $type = 'distinct'): static
    {
        $union = [
            'type' => $type,
            'query' => $query
        ];

        if ($overwrite) {
            $this->unions = [$union];
        } else {
            $this->unions[] = $union;
        }

        $this->dirty();

        return $this;
    }

    /**
     * Add an UNION ALL query.
     * @param Closure|QueryBuilder|QueryLiteral|string $query The query.
     * @param bool $overwrite Whether to overwrite the existing unions.
     * @return QueryBuilder The QueryBuilder.
     */
    public function unionAll(Closure|QueryBuilder|QueryLiteral|string $query, bool $overwrite = false): static
    {
        return $this->union($query, $overwrite, 'all');
    }

    /**
     * Set the WHERE conditions.
     * @param string|array $conditions The conditions.
     * @param bool $overwrite Whether to overwrite the existing conditions.
     * @return QueryBuilder The QueryBuilder.
     */
    public function where(string|array $conditions, bool $overwrite = false): static
    {
        $conditions = (array) $conditions;

        if ($overwrite) {
            $this->conditions = $conditions;
        } else {
            $this->conditions = array_merge($this->conditions, $conditions);
        }

        $this->dirty();

        return $this;
    }

    /**
     * Set the WITH clause.
     * @param array $cte The common table expressions.
     * @param bool $overwrite Whether to overwrite the existing expressions.
     * @param bool $recursive Whether the WITH is recursive.
     * @return QueryBuilder The QueryBuilder.
     */
    public function with(array $cte, bool $overwrite = false, bool $recursive = false): static
    {
        $with = [
            'cte' => $cte,
            'recursive' => $recursive
        ];

        if ($overwrite) {
            $this->with = [$with];
        } else {
            $this->with[] = $with;
        }
    
        $this->dirty();

        return $this;
    }

    /**
     * Set the WITH RECURSIVE clause.
     * @param array $cte The common table expressions.
     * @param bool $overwrite Whether to overwrite the existing common table expressions.
     * @return QueryBuilder The QueryBuilder.
     */
    public function withRecursive(array $cte, bool $overwrite = false): static
    {
        return $this->with($cte, $overwrite, true);
    }

    /**
     * Mark the query as dirty.
     */
    protected function dirty(): void
    {
        $this->dirty = true;
    }

    /**
     * Normalize a joins array.
     * @param array $joins The joins.
     * @return array The normalize joins.
     */
    protected static function normalizeJoins(array $joins): array
    {
        $results = [];
        foreach ($joins AS $alias => $join) {
            if (is_numeric($alias)) {
                $alias = $join['alias'] ?? $join['table'] ?? null;
            }

            if (!is_string($alias)) {
                throw DbException::forInvalidJoinAlias();
            }

            $join['table'] ??= $alias;

            $results[$alias] = $join;
        }

        return $results;
    }

}
