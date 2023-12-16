<?php
declare(strict_types=1);

namespace Fyre\DB;

use function array_merge;

/**
 * Query
 */
abstract class Query
{

    protected static bool $multipleTables = false;

    protected Connection $connection;

    protected array $table = [];

    protected bool $dirty = false;
    protected bool $useBinder = true;

    /**
     * New Query constructor.
     * @param Connection $connection The connection.
     * @param string|array|null $table The table.
     */
    public function __construct(Connection $connection, string|array|null $table = null)
    {
        $this->connection = $connection;

        if ($table) {
            $this->table($table);
        }
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
     * Execute the query.
     * @return ResultSet|bool The query result.
     */
    public function execute(ValueBinder|null $binder = null): ResultSet|bool
    {
        if ($this->useBinder) {
            $binder ??= new ValueBinder();
        }

        $query = $this->sql($binder);

        $bindings = $binder ? 
            $binder->bindings() :
            [];

        $result = $this->connection->execute($query, $bindings);

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
     * Get the table.
     * @return array The table.
     */
    public function getTable(): array
    {
        return $this->table;
    }

    /**
     * Generate the SQL query.
     * @return string The SQL query.
     */
    abstract public function sql(ValueBinder|null $binder = null): string;

    /**
     * Set the table.
     * @param string|array $table The table.
     * @param bool $overwrite Whether to overwrite the existing table.
     * @return Query The Query.
     */
    public function table(string|array $table, bool $overwrite = false): static
    {
        $table = (array) $table;

        if (!static::$multipleTables) {
            $this->table = array_slice($table, 0, 1);
        } else if ($overwrite) {
            $this->table = $table;
        } else {
            $this->table = array_merge($this->table, $table);
        }

        $this->dirty();

        return $this;
    }

    /**
     * Mark the query as dirty.
     */
    protected function dirty(): void
    {
        $this->dirty = true;
    }

}
