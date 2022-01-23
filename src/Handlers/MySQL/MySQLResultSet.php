<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\MySQL;

use
    Fyre\DB\ResultSet,
    PDO,
    PDOStatement;

use function
    array_filter,
    array_keys,
    array_merge,
    count;

class MySQLResultSet extends ResultSet
{

    protected static array $types = [
        'DATE' => 'date',
        'DATETIME' => 'datetime',
        'DOUBLE' => 'decimal',
        'FLOAT' => 'float',
        'LONG' => 'integer',
        'LONGLONG' => 'integer',
        'SHORT' => 'integer',
        'TIME' => 'time',
        'TIMESTAMP' => 'datetime',
        'TINY' => 'integer'
    ];

    protected PDOStatement $result;

    protected array|null $buffer = null;

    /**
     * New MySQLResultSet constructor.
     * @param PDOStatement $result The raw result.
     */
    public function __construct(PDOStatement $result)
    {
        $this->result = $result;
    }

    /**
     * Get the results as an array.
     * @return array The results.
     */
    public function all(): array
    {
        if (!$this->buffer) {
            $this->buffer = $this->result->fetchAll(PDO::FETCH_ASSOC);
        } else if (count($this->buffer) < $this->count()) {
            $results = $this->result->fetchAll(PDO::FETCH_ASSOC);
            $results = array_filter($results);
            $this->buffer = array_merge($this->buffer, $results);
        }

        return $this->buffer;
    }

    /**
     * Get the column count.
     * @return int The column count.
     */
    public function columnCount(): int
    {
        return $this->result->columnCount();
    }

    /**
     * Get the result columns.
     * @return array The result columns.
     */
    public function columns(): array
    {
        $columns = $this->getColumnMeta();

        return array_keys($columns);
    }

    /**
     * Get the result count.
     * @return int The result count.
     */
    public function count(): int
    {
        return $this->result->rowCount();
    }

    /**
     * Get a result by index.
     * @param int $index The index.
     * @return array|null The result.
     */
    public function fetch(int $index = 0): array|null
    {
        if ($index >= $this->count()) {
            return null;
        }

        if ($index === $this->count() - 1) {
            return $this->all()[$index];
        }

        $this->buffer ??= [];

        while (count($this->buffer) <= $index) {
            $this->buffer[] = $this->result->fetch(PDO::FETCH_ASSOC);
        }

        return $this->buffer[$index] ?? null;
    }

    /**
     * Free the result from memory.
     */
    public function free(): void
    {
        $this->result->closeCursor();
    }

    /**
     * Get column meta data.
     * @return array The column meta data.
     */
    protected function getColumnMeta(): array
    {
        $columnCount = $this->columnCount();

        $columns = [];

        for ($i = 0; $i < $columnCount; $i++) {
            $column = $this->result->getColumnMeta($i);
            $name = $column['name'];

            $columns[$name] = $column;
        }

        return $columns;
    }

    /**
     * Get the database type for a column.
     * @param string $name The column name.
     * @return string|null The database type.
     */
    protected function getColumnType(string $name): string|null
    {
        $columns = $this->getColumnMeta();
        $column = $columns[$name];

        if (!$column) {
            return null;
        }

        $nativeType = $column['native_type'];

        if ($nativeType === 'TINY' && $column['length'] === 1) {
            return 'boolean';
        }

        return static::$types[$nativeType] ?? 'string';
    }

}
