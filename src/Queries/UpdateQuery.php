<?php
declare(strict_types=1);

namespace Fyre\DB\Queries;

use Fyre\DB\Queries\Traits\EpilogTrait;
use Fyre\DB\Queries\Traits\JoinTrait;
use Fyre\DB\Queries\Traits\WhereTrait;
use Fyre\DB\Query;
use Fyre\DB\ValueBinder;

/**
 * UpdateQuery
 */
class UpdateQuery extends Query
{
    use EpilogTrait;
    use JoinTrait;
    use WhereTrait;

    protected static bool $multipleTables = true;

    protected static bool $tableAliases = true;

    protected array $data = [];

    /**
     * Get the data.
     *
     * @return array The data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set the UPDATE data.
     *
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
     *
     * @return string The SQL query.
     */
    public function sql(ValueBinder|null $binder = null): string
    {
        return $this->connection->generator()
            ->compileUpdate($this, $binder);
    }
}
