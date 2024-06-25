<?php
declare(strict_types=1);

namespace Fyre\DB\Queries;

use Fyre\DB\Queries\Traits\EpilogTrait;
use Fyre\DB\Query;
use Fyre\DB\ValueBinder;

use function array_merge;
use function array_unique;

/**
 * UpdateBatchQuery
 */
class UpdateBatchQuery extends Query
{
    use EpilogTrait;

    protected static bool $tableAliases = true;

    protected array $data = [];

    protected array $keys = [];

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
     * Set the UPDATE batch data.
     *
     * @param array $data The data.
     * @param string|array $keys The key to use for updating.
     * @param bool $overwrite Whether to overwrite the existing data.
     * @return UpdateBatchQuery The UpdateBatchQuery.
     */
    public function set(array $data, array|string $keys, bool $overwrite = false): static
    {
        $keys = (array) $keys;

        if ($overwrite) {
            $this->data = $data;
            $this->keys = $keys;
        } else {
            $this->data = array_merge($this->data, $data);
            $this->keys = array_merge($this->keys, $keys);
        }

        $this->keys = array_unique($this->keys);

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
        $generator = $this->connection->generator();

        $query = $generator->buildUpdateBatch($this->table, $this->data, $this->keys, $binder);
        $query .= $generator->buildEpilog($this->epilog);

        return $query;
    }
}
