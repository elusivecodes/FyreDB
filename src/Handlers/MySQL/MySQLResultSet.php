<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\MySQL;

use
    Fyre\DB\ResultSet,
    mysqli_result;

use const
    MYSQLI_ASSOC;

use function
    array_map;

class MySQLResultSet extends ResultSet
{

    protected mysqli_result $result;

    /**
     * New MySQLResultSet constructor.
     * @param mysqli_result $result The raw result.
     */
    public function __construct(mysqli_result $result)
    {
        $this->result = $result;
    }

    /**
     * Get the column count.
     * @return int The column count.
     */
    public function columnCount(): int
    {
        return $this->result->field_count;
    }

    /**
     * Get the result columns.
     * @return array The result columns.
     */
    public function columns(): array
    {
        return array_map(
            fn($field) => $field->name,
            $this->result->fetch_fields()
        );
    }

    /**
     * Get the result count.
     * @return int The result count.
     */
    public function count(): int
    {
        return $this->result->num_rows;
    }

    /**
     * Get a result by index.
     * @param int $index The index.
     * @return array|null The result.
     */
    public function fetch(int $index = 0): array|null
    {
        $this->result->data_seek($index);

        return $this->row();
    }

    /**
     * Free the result from memory.
     */
    public function free(): void
    {
        $this->result->free();
    }

    /**
     * Get the current result.
     * @return array|null The current result.
     */
    public function row(): array|null
    {
        return $this->result->fetch_array(MYSQLI_ASSOC);
    }

    /**
     * Get the results as an array.
     * @return array The results.
     */
    public function toArray(): array
    {
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

}
