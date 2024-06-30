<?php
declare(strict_types=1);

namespace Fyre\DB\Queries;

use Fyre\DB\Connection;
use Fyre\DB\Queries\Traits\EpilogTrait;
use Fyre\DB\Queries\Traits\FromTrait;
use Fyre\DB\Queries\Traits\JoinTrait;
use Fyre\DB\Queries\Traits\LimitTrait;
use Fyre\DB\Queries\Traits\OrderByTrait;
use Fyre\DB\Queries\Traits\WhereTrait;
use Fyre\DB\Query;
use Fyre\DB\ValueBinder;

use function array_filter;
use function array_merge;

/**
 * DeleteQuery
 */
class DeleteQuery extends Query
{
    use EpilogTrait;
    use FromTrait;
    use JoinTrait;
    use LimitTrait;
    use OrderByTrait;
    use WhereTrait;

    protected static bool $multipleTables = true;

    protected static bool $tableAliases = true;

    protected array $alias = [];

    /**
     * New DeleteQuery constructor.
     *
     * @param Connection $connection The connection.
     * @param array|string|null $alias The alias to delete.
     */
    public function __construct(Connection $connection, array|string|null $alias = null)
    {
        parent::__construct($connection);

        if ($alias) {
            $this->alias($alias);
        }
    }

    /**
     * Set the delete alias.
     *
     * @return DeleteQuery The DeleteQuery.
     */
    public function alias(array|string $alias, bool $overwrite = false): static
    {
        $alias = (array) $alias;
        $alias = array_filter($alias);

        if ($overwrite) {
            $this->alias = $alias;
        } else {
            $this->alias = array_merge($this->alias, $alias);
        }

        return $this;
    }

    /**
     * Get the delete alias.
     *
     * @return array The delete alias.
     */
    public function getAlias(): array
    {
        return $this->alias;
    }

    /**
     * Generate the SQL query.
     *
     * @return string The SQL query.
     */
    public function sql(ValueBinder|null $binder = null): string
    {
        return $this->connection->generator()
            ->compileDelete($this, $binder);
    }
}
