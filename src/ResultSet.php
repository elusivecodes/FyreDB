<?php
declare(strict_types=1);

namespace Fyre\DB;

use Countable;
use Fyre\DB\Types\Type;
use Iterator;
use PDO;
use PDOStatement;

use function array_filter;
use function array_keys;
use function array_merge;
use function count;

/**
 * ResultSet
 */
abstract class ResultSet implements Countable, Iterator
{

    protected PDOStatement $result;

    protected array|null $columnMeta = null;

    protected array|null $buffer = null;

    protected int $index = 0;

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
     * Get the result at the current index.
     * @return array|null The result at the current index.
     */
    public function current(): array|null
    {
        return $this->fetch($this->index);
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
     * Get the first result.
     * @return array|null The first result.
     */
    public function first(): array|null
    {
        return $this->fetch();
    }

    /**
     * Free the result from memory.
     */
    public function free(): void
    {
        $this->result->closeCursor();
    }

    /**
     * Get a Type class for a column.
     * @param string $name The column name.
     * @return Type|null The Type.
     */
    public function getType(string $name): Type|null
    {
        $type = $this->getColumnType($name);

        if (!$type) {
            return null;
        }

        return TypeParser::use($type);
    }

    /**
     * Get the current index.
     * @return int The current index.
     */
    public function key(): int
    {
        return $this->index;
    }

    /**
     * Get the last result.
     * @return array|null The last result.
     */
    public function last(): array|null
    {
        return $this->fetch($this->count() - 1);
    }

    /**
     * Progress the index.
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * Reset the index.
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * Get the current result.
     * @return array|null The current result.
     */
    public function row(): array|null
    {
        return $this->fetch($this->index++);
    }

    /**
     * Determine if the current index is valid.
     * @return bool TRUE if the current index is valid, otherwise FALSE.
     */
    public function valid(): bool
    {
        return $this->index < $this->count();
    }

    /**
     * Get column meta data.
     * @return array The column meta data.
     */
    protected function getColumnMeta(): array
    {
        if ($this->columnMeta === null) {
            $columnCount = $this->columnCount();

            $this->columnMeta = [];

            for ($i = 0; $i < $columnCount; $i++) {
                $column = $this->result->getColumnMeta($i);
                $name = $column['name'];

                $this->columnMeta[$name] = $column;
            }
        }

        return $this->columnMeta;
    }

    /**
     * Get the database type for a column.
     * @param string $name The column name.
     * @return string|null The database type.
     */
    abstract protected function getColumnType(string $name): string|null;

}
