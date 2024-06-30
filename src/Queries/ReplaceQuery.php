<?php
declare(strict_types=1);

namespace Fyre\DB\Queries;

use Fyre\DB\Queries\Traits\EpilogTrait;
use Fyre\DB\Queries\Traits\IntoTrait;
use Fyre\DB\Query;
use Fyre\DB\ValueBinder;

use function array_merge;

/**
 * ReplaceQuery
 */
class ReplaceQuery extends Query
{
    use EpilogTrait;
    use IntoTrait;

    protected array $values = [];

    /**
     * Get the values.
     *
     * @return array The values.
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Generate the SQL query.
     *
     * @return string The SQL query.
     */
    public function sql(ValueBinder|null $binder = null): string
    {
        return $this->connection->generator()
            ->compileReplace($this, $binder);
    }

    /**
     * Set the REPLACE batch values.
     *
     * @param array $values The values.
     * @param bool $overwrite Whether to overwrite the existing values.
     * @return ReplaceQuery The ReplaceQuery.
     */
    public function values(array $values, bool $overwrite = false): static
    {
        if ($overwrite) {
            $this->values = $values;
        } else {
            $this->values = array_merge($this->values, $values);
        }

        $this->dirty();

        return $this;
    }
}
