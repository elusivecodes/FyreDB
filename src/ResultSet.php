<?php
declare(strict_types=1);

namespace Fyre\DB;

use
    Countable,
    Iterator;

/**
 * ResultSet
 */
abstract class ResultSet implements Countable, Iterator
{

    protected int $index = 0;

    /**
     * Get the results as an array.
     * @return array The results.
     */
    abstract public function all(): array;

    /**
     * Get the column count.
     * @return int The column count.
     */
    abstract public function columnCount(): int;

    /**
     * Get the result columns.
     * @return array The result columns.
     */
    abstract public function columns(): array;

    /**
     * Get the result count.
     * @return int The result count.
     */
    abstract public function count(): int;

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
    abstract public function fetch(int $index = 0): array|null;

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
    abstract public function free(): void;

    /**
     * Get the current index.
     * @return int The current index.
     */
    public function key()
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
    public function next()
    {
        $this->index++;
    }

    /**
     * Reset the index.
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Get the current result.
     * @return array|null The current result.
     */
    abstract public function row(): array|null;

    /**
     * Determine if the current index is valid.
     * @return bool TRUE if the current index is valid, otherwise FALSE.
     */
    public function valid(): bool
    {
        return $this->index < $this->count();
    }

}
