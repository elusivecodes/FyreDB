<?php

namespace Fyre;

use function
    array_merge,
    is_string;

class QueryBuilder
{

    protected Connection $connection;

    use QueryGenerator;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->reset();
    }

    public function delete(array $options = []): string|bool
    {
        $this->action = 'delete';

        return $this->execute($options);
    }

    public function distinct(bool $distinct = true): self
    {
        $this->distinct = $distinct;

        return $this;
    }

    public function for(string|null $for = null): self
    {
        $this->for = $for;

        return $this;
    }

    public function from(string|array $tables): self
    {
        if (is_string($tables)) {
            $tables = [$tables];
        }

        $this->tables = array_merge($this->tables, $tables);

        return $this;
    }

    public function get(array $options = []): Query|string|bool
    {
        return $this->execute($options);
    }

    public function groupBy(string|array $fields): self
    {
        if (is_string($fields)) {
            $fields = [$fields];
        }

        $this->groupBy = array_merge($this->groupBy, $fields);

        return $this;
    }

    public function having(array $conditions): self
    {
        $this->having = array_merge($this->having, $conditions);

        return $this;
    }

    public function insert(array $data, array $options = []): string|bool
    {
        return $this->insertBatch([$data], $options);
    }

    public function insertBatch(array $data, array $options = []): string|bool
    {
        $this->action = 'insert';
        $this->data = $data;

        return $this->execute($options);
    }

    public function join(array $joins): self
    {
        $this->joins = array_merge($this->joins, $joins);

        return $this;
    }

    public function limit(int|null $limit = null, int $offset = 0): self
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function orderBy(array $fields): self
    {
        $this->orderBy = array_merge($this->orderBy, $fields);

        return $this;
    }

    // public function replace(array $data, array $options = []): string|bool
    // {
    //     $this->action = 'replace';
    //     $this->data = $data;

    //     return $this->execute($options);
    // }

    public function reset(): self
    {
        $this->action = 'select';
        $this->tables = [];
        $this->distinct = false;
        $this->fields = [];
        $this->joins = [];
        $this->conditions = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->having = [];
        $this->offset = 0;
        $this->limit = null;
        $this->for = null;
        $this->data = [];

        return $this;
    }

    public function select(array $fields): self
    {
        $this->fields = array_merge($this->fields, $fields);

        return $this;
    }

    public function update(array $data, array $options = []): string|bool
    {
        $this->action = 'update';
        $this->data = $data;

        return $this->execute($options);
    }

    // public function updateBatch(array $data, string $lookupKey, array $options = []): string|bool
    // {
    //     $this->action = 'update';
    //     $this->data = $data;

    //     return $this->execute($options);
    // }

    public function where(array $conditions): self
    {
        $this->conditions = array_merge($this->conditions, $conditions);

        return $this;
    }

    protected function execute(array $options = []): Query|string|bool
    {
        $options['reset'] ??= true;
        $options['returnQuery'] ??= false;

        $query = $this->buildQuery();

        if ($options['reset']) {
            $this->reset();
        }

        if ($options['returnQuery']) {
            return $query;
        }

        return $this->connection->query($query);
    }

}
