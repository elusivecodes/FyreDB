<?php

namespace Fyre;

use
    Countable,
    Iterator,
    mysqli_result;

use const
    MYSQLI_ASSOC;

class Query implements Countable, Iterator
{

    protected mysqli_result $result;

    protected int $index = 0;

    public function __construct(mysqli_result $result)
    {
        $this->result = $result;
    }

    public function all(): array
    {
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

    public function columnCount(): int
    {
        return $this->result->field_count;
    }

    public function columns(): array
    {
        return $this->result->fetch_fields();
    }

    public function count(): int
    {
        return $this->result->num_rows;
    }

    public function current(): object|null
    {
        return $this->fetch($this->index);
    }

    public function fetch(int $index = 0): object|null
    {
        $this->result->data_seek($index);

        return $this->row();
    }

    public function free()
    {
        return $this->result->free();
    }

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        $this->index++;
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function row(): array|null
    {
        return $this->result->fetch_array(MYSQLI_ASSOC);
    }

    public function valid(): bool
    {
        return $this->index < $this->count();
    }

}
