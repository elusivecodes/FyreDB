<?php
declare(strict_types=1);

namespace Fyre\DB;

use Countable;
use Fyre\DB\Types\Type;
use Iterator;
use PDO;
use PDOStatement;

use function array_fill;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function count;

/**
 * ResultSet
 */
abstract class ResultSet implements Countable, Iterator
{
    protected static array $types = [];

    protected array $buffer = [];

    protected array|null $columnMeta = null;

    protected Connection $connection;

    protected int $index = 0;

    protected PDOStatement $result;

    /**
     * New ResultSet constructor.
     *
     * @param PDOStatement $result The raw result.
     */
    public function __construct(PDOStatement $result, Connection $connection)
    {
        $this->result = $result;
        $this->connection = $connection;
    }

    /**
     * Get the results as an array.
     *
     * @return array The results.
     */
    public function all(): array
    {
        return $this->buffer = array_merge($this->buffer, $this->result->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Clear results from the buffer.
     */
    public function clearBuffer(int|null $index = null): void
    {
        if ($index === null) {
            $count = count($this->buffer);
            $this->buffer = array_fill(0, $count, null);
        } else if (array_key_exists($index, $this->buffer)) {
            $this->buffer[$index] = null;
        }
    }

    /**
     * Get the column count.
     *
     * @return int The column count.
     */
    public function columnCount(): int
    {
        return $this->result->columnCount();
    }

    /**
     * Get the result columns.
     *
     * @return array The result columns.
     */
    public function columns(): array
    {
        $columns = $this->getColumnMeta();

        return array_keys($columns);
    }

    /**
     * Get the result count.
     *
     * @return int The result count.
     */
    public function count(): int
    {
        return $this->result->rowCount();
    }

    /**
     * Get the result at the current index.
     *
     * @return array|null The result at the current index.
     */
    public function current(): array|null
    {
        return $this->fetch($this->index);
    }

    /**
     * Get a result by index.
     *
     * @param int $index The index.
     * @return array|null The result.
     */
    public function fetch(int $index = 0): array|null
    {
        $bufferIndex = $index - count($this->buffer);

        while ($bufferIndex-- >= 0) {
            $row = $this->result->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                break;
            }

            $this->buffer[] = $row;
        }

        return $this->buffer[$index] ?? null;
    }

    /**
     * Get the first result.
     *
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
     *
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
     *
     * @return int The current index.
     */
    public function key(): int
    {
        return $this->index;
    }

    /**
     * Get the last result.
     *
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
     *
     * @return array|null The current result.
     */
    public function row(): array|null
    {
        return $this->fetch($this->index++);
    }

    /**
     * Determine if the current index is valid.
     *
     * @return bool TRUE if the current index is valid, otherwise FALSE.
     */
    public function valid(): bool
    {
        return $this->index < $this->count();
    }

    /**
     * Get column meta data.
     *
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
     *
     * @param string $name The column name.
     * @return string|null The database type.
     */
    protected function getColumnType(string $name): string|null
    {
        $columns = $this->getColumnMeta();
        $column = $columns[$name] ?? null;

        if (!$column) {
            return $column;
        }

        $nativeType = $column['native_type'];

        return static::$types[$nativeType] ?? 'string';
    }
}
