<?php
declare(strict_types=1);

namespace Fyre\DB;

use
    Closure;

use function
    array_merge,
    is_array;

/**
 * QueryBuilder
 */
class QueryBuilder
{

    protected Connection $connection;

    protected string $action = 'select';
    protected array $with = [];
    protected bool $recursive = false;
    protected array|null $deleteAliases = null;
    protected array $tables = [];
    protected array $data = [];
    protected Closure|QueryBuilder|QueryLiteral|string $insertQuery = '';
    protected array $insertColumns = [];
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
    protected bool $unionAll = false;
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
     * Set query as DELETE.
     * @param string|array $aliases The table aliases to delete.
     * @return QueryBuilder The QueryBuilder.
     */
    public function delete(string|array|null $aliases = null): static
    {
        $this->action = 'delete';

        if (is_array($aliases)) {
            $this->deleteAliases = array_merge($this->deleteAliases ?? [], $aliases);
        } else if ($aliases) {
            $this->deleteAliases ??= [];
            $this->deleteAliases[] = $aliases;
        }


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

        return $this;
    }

    /**
     * Add an EXCEPT query.
     * @param Closure|QueryBuilder|QueryLiteral|string $query The query.
     * @return QueryBuilder The QueryBuilder.
     */
    public function except(Closure|QueryBuilder|QueryLiteral|string $query): static
    {
        $this->unions[] = [
            'type' => 'except',
            'query' => $query,
        ];

        return $this;
    }

    /**
     * Execute the query.
     * @return ResultSet|bool The query result.
     * @throws DbException if the query failed.
     */
    public function execute(): ResultSet|bool
    {
        $query = $this->sql();

        return $this->connection->query($query);
    }

    /**
     * Set the GROUP BY fields.
     * @param string|array $fields The fields.
     * @return QueryBuilder The QueryBuilder.
     */
    public function groupBy(string|array $fields): static
    {
        if (is_array($fields)) {
            $this->groupBy = array_merge($this->groupBy, $fields);
        } else {
            $this->groupBy[] = $fields;
        }

        return $this;
    }

    /**
     * Set the HAVING conditions.
     * @param string|array $conditions The conditions.
     * @return QueryBuilder The QueryBuilder.
     */
    public function having(string|array $conditions): static
    {
        if (is_array($conditions)) {
            $this->having = array_merge($this->having, $conditions);
        } else {
            $this->having[] = $conditions;
        }

        return $this;
    }

    /**
     * Set query as an INSERT.
     * @param array $data The data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function insert(array $data): static
    {
        $this->action = 'insert';
        $this->data = $data;

        return $this;
    }

    /**
     * Set query as a batch INSERT.
     * @param array $data The data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function insertBatch(array $data): static
    {
        $this->action = 'insertBatch';
        $this->data = $data;

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
        $this->action = 'insertFrom';
        $this->insertQuery = $query;
        $this->insertColumns = $columns;

        return $this;
    }

    /**
     * Add an INTERSECT query.
     * @param Closure|QueryBuilder|QueryLiteral|string $query The query.
     * @return QueryBuilder The QueryBuilder.
     */
    public function intersect(Closure|QueryBuilder|QueryLiteral|string $query): static
    {
        $this->unions[] = [
            'type' => 'intersect',
            'query' => $query,
        ];

        return $this;
    }

    /**
     * Set the JOIN tables.
     * @param array $joins The joins.
     * @return QueryBuilder The QueryBuilder.
     */
    public function join(array $joins): static
    {
        $this->joins = array_merge($this->joins, $joins);

        return $this;
    }

    /**
     * Set the LIMIT and OFFSET clauses.
     * @param int|null $limit The limit.
     * @param int $offset The offset.
     * @return QueryBuilder The QueryBuilder.
     */
    public function limit(int|null $limit = null, int $offset = 0): static
    {
        $this->limit = $limit;
        $this->offset = $offset;

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

        return $this;
    }

    /**
     * Set the ORDER BY fields.
     * @param string|array $fields The fields.
     * @return QueryBuilder The QueryBuilder.
     */
    public function orderBy(string|array $fields): static
    {
        if (is_array($fields)) {
            $this->orderBy = array_merge($this->orderBy, $fields);
        } else {
            $this->orderBy[] = $fields;
        }

        return $this;
    }

    /**
     * Set query as a REPLACE.
     * @param array $data The data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function replace(array $data): static
    {
        $this->action = 'replace';
        $this->data = $data;

        return $this;
    }

    /**
     * Set query as a batch REPLACE.
     * @param array $data The data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function replaceBatch(array $data): static
    {
        $this->action = 'replaceBatch';
        $this->data = $data;

        return $this;
    }

    /**
     * Set the SELECT fields.
     * @param string|array $fields The fields.
     * @return QueryBuilder The QueryBuilder.
     */
    public function select(string|array $fields = '*'): static
    {
        $this->action = 'select';

        if (is_array($fields)) {
            $this->fields = array_merge($this->fields, $fields);
        } else {
            $this->fields[] = $fields;
        }

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
                $query = $generator->buildUpdate($this->tables, $this->data, $this->with, $this->recursive);
                $query .= $generator->buildJoin($this->joins);
                $query .= $generator->buildWhere($this->conditions);
                break;
            case 'updateBatch':
                $query = $generator->buildUpdateBatch($this->tables, $this->data, $this->updateKeys);
                break;
            case 'delete':
                $query = $generator->buildDelete($this->tables, $this->deleteAliases, $this->with, $this->recursive);
                $query .= $generator->buildJoin($this->joins);
                $query .= $generator->buildWhere($this->conditions);
                $query .= $generator->buildOrderBy($this->orderBy);
                $query .= $generator->buildLimit($this->limit, $this->offset);
                break;
            case 'select':
                $query = $generator->buildSelect($this->tables, $this->fields, $this->distinct, $this->with, $this->recursive);
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
     * @return QueryBuilder The QueryBuilder.
     */
    public function table(string|array $tables): static
    {
        if (is_array($tables)) {
            $this->tables = array_merge($this->tables, $tables);
        } else {
            $this->tables[] = $tables;
        }

        return $this;
    }

    /**
     * Set query as an UPDATE.
     * @param array $data The data.
     * @return QueryBuilder The QueryBuilder.
     */
    public function update(array $data): static
    {
        $this->action = 'update';
        $this->data = $data;

        return $this;
    }

    /**
     * Set query as a batch UPDATE.
     * @param array $data The data.
     * @param string|array $updateKeys The key to use for updating.
     * @return QueryBuilder The QueryBuilder.
     */
    public function updateBatch(array $data, string|array $updateKeys): static
    {
        $this->action = 'updateBatch';
        $this->data = $data;
        $this->updateKeys = (array) $updateKeys;

        return $this;
    }

    /**
     * Add an UNION DISTINCT query.
     * @param Closure|QueryBuilder|QueryLiteral|string $query The query.
     * @return QueryBuilder The QueryBuilder.
     */
    public function union(Closure|QueryBuilder|QueryLiteral|string $query): static
    {
        $this->unions[] = [
            'type' => 'distinct',
            'query' => $query,
        ];

        return $this;
    }

    /**
     * Add an UNION ALL query.
     * @param Closure|QueryBuilder|QueryLiteral|string $query The query.
     * @return QueryBuilder The QueryBuilder.
     */
    public function unionAll(Closure|QueryBuilder|QueryLiteral|string $query): static
    {
        $this->unions[] = [
            'type' => 'all',
            'query' => $query
        ];

        return $this;
    }

    /**
     * Set the WHERE conditions.
     * @param string|array $conditions The conditions.
     * @return QueryBuilder The QueryBuilder.
     */
    public function where(string|array $conditions): static
    {
        if (is_array($conditions)) {
            $this->conditions = array_merge($this->conditions, $conditions);
        } else {
            $this->conditions[] = $conditions;
        }

        return $this;
    }

    /**
     * Set the WITH clause.
     * @param array $with The common table expressions.
     * @param bool $recursive Whether the WITH is recursive.
     * @return QueryBuilder The QueryBuilder.
     */
    public function with(array $with, bool $recursive = false): static
    {
        $this->with = $with;
        $this->recursive = $recursive;
    
        return $this;
    }

    /**
     * Set the WITH RECURSIVE clause.
     * @param array $with The common table expressions.
     * @return QueryBuilder The QueryBuilder.
     */
    public function withRecursive(array $with): static
    {
        return $this->with($with, true);
    }

}
