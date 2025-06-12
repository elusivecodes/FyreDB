<?php
declare(strict_types=1);

namespace Fyre\DB;

use Fyre\DB\Exceptions\DbException;

use function array_is_list;
use function array_merge;
use function count;
use function is_string;

/**
 * Query
 */
abstract class Query
{
    protected static bool $tableAliases = false;

    protected static bool $virtualTables = false;

    protected bool $dirty = false;

    protected bool $multipleTables = false;

    protected array $table = [];

    protected bool $useBinder = true;

    /**
     * New Query constructor.
     *
     * @param Connection $connection The connection.
     * @param array|string|null $table The table.
     */
    public function __construct(
        protected Connection $connection,
        array|string|null $table = null
    ) {
        if ($table) {
            $this->table($table);
        }
    }

    /**
     * Generate the SQL query.
     *
     * @return string The SQL query.
     */
    public function __toString(): string
    {
        return $this->sql();
    }

    /**
     * Execute the query.
     *
     * @return ResultSet The query result.
     */
    public function execute(ValueBinder|null $binder = null): ResultSet
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
     *
     * @return Connection The Connection.
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Get the table.
     *
     * @return array|null The table.
     */
    public function getTable(): array|null
    {
        return $this->table;
    }

    /**
     * Generate the SQL query.
     *
     * @return string The SQL query.
     */
    abstract public function sql(ValueBinder|null $binder = null): string;

    /**
     * Set the table.
     *
     * @param array|string $table The table.
     * @param bool $overwrite Whether to overwrite the existing table.
     * @return Query The Query.
     */
    public function table(array|string $table, bool $overwrite = false): static
    {
        $table = (array) $table;

        if (!static::$virtualTables) {
            foreach ($table as $test) {
                if (!is_string($test)) {
                    throw DbException::forVirtualTablesNotSupported();
                }
            }
        }

        if (!static::$tableAliases && !array_is_list($table)) {
            throw DbException::forTableAliasesNotSupported();
        }

        if ($overwrite) {
            $this->table = $table;
        } else {
            $this->table = array_merge($this->table, $table);
        }

        if (!$this->multipleTables && count($this->table) > 1) {
            throw DbException::forMultipleTablesNotSupported();
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
