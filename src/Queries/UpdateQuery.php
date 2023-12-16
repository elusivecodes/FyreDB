<?php
declare(strict_types=1);

namespace Fyre\DB\Queries;

use Fyre\DB\Queries\Traits\EpilogTrait;
use Fyre\DB\Queries\Traits\JoinTrait;
use Fyre\DB\Queries\Traits\WhereTrait;
use Fyre\DB\Queries\Traits\WithTrait;
use Fyre\DB\Query;
use Fyre\DB\ValueBinder;

/**
 * UpdateQuery
 */
class UpdateQuery extends Query
{

    protected static bool $multipleTables = true;

    protected array $data = [];

    use EpilogTrait;
    use JoinTrait;
    use WhereTrait;
    use WithTrait;

    /**
     * Get the data.
     * @return array The data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set the UPDATE data.
     * @param array $data The data.
     * @param bool $overwrite Whether to overwrite the existing data.
     * @return UpdateQuery The UpdateQuery.
     */
    public function set(array $data, bool $overwrite = false): static
    {
        if ($overwrite) {
            $this->data = $data;
        } else {
            $this->data = array_merge($this->data, $data);
        }

        $this->dirty();

        return $this;
    }

    /**
     * Generate the SQL query.
     * @return string The SQL query.
     */
    public function sql(ValueBinder|null $binder = null): string
    {
        $generator = $this->connection->generator();

        $query = $generator->buildWith($this->with);
        $query .= $generator->buildUpdate($this->table, $this->data, $binder);
        $query .= $generator->buildJoin($this->joins, $binder);
        $query .= $generator->buildWhere($this->conditions, $binder);
        $query .= $generator->buildEpilog($this->epilog);

        return $query;
    }

}
