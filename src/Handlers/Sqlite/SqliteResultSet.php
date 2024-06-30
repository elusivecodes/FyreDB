<?php
declare(strict_types=1);

namespace Fyre\DB\Handlers\Sqlite;

use Fyre\DB\ResultSet;

use function count;
use function preg_match;

/**
 * SqliteResultSet
 */
class SqliteResultSet extends ResultSet
{
    protected static array $types = [
        'double' => 'float',
        'integer' => 'integer',
    ];

    protected int $count;

    /**
     * Get the results as an array.
     *
     * @return array The results.
     */
    public function all(): array
    {
        $this->getColumnMeta();

        return parent::all();
    }

    /**
     * Get the result count.
     *
     * @return int The result count.
     */
    public function count(): int
    {
        if (preg_match('/^SELECT/i', $this->result->queryString)) {
            return $this->count ??= count($this->all());
        }

        return parent::count();
    }

    /**
     * Get a result by index.
     *
     * @param int $index The index.
     * @return array|null The result.
     */
    public function fetch(int $index = 0): array|null
    {
        $this->getColumnMeta();

        return parent::fetch($index);
    }
}
