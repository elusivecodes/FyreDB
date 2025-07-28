<?php
declare(strict_types=1);

namespace Fyre\DB\Queries;

use Fyre\DB\Connection;
use Fyre\DB\DbFeature;
use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Queries\Traits\EpilogTrait;
use Fyre\DB\Queries\Traits\FromTrait;
use Fyre\DB\Queries\Traits\JoinTrait;
use Fyre\DB\Queries\Traits\LimitTrait;
use Fyre\DB\Queries\Traits\OrderByTrait;
use Fyre\DB\Queries\Traits\WhereTrait;
use Fyre\DB\Query;
use Fyre\DB\ValueBinder;
use Fyre\Utility\Traits\MacroTrait;

use function array_filter;
use function array_merge;

/**
 * DeleteQuery
 */
class DeleteQuery extends Query
{
    use EpilogTrait;
    use FromTrait;
    use JoinTrait {
        JoinTrait::join as protected _join;
    }
    use LimitTrait;
    use MacroTrait;
    use OrderByTrait;
    use WhereTrait;

    protected static bool $tableAliases = true;

    protected array $alias = [];

    protected array $using = [];

    /**
     * New DeleteQuery constructor.
     *
     * @param Connection $connection The connection.
     * @param array|string|null $alias The alias to delete.
     */
    public function __construct(Connection $connection, array|string|null $alias = null)
    {
        $this->multipleTables = $connection->supports(DbFeature::DeleteMultipleTables);

        parent::__construct($connection);

        if ($alias) {
            $this->alias($alias);
        }
    }

    /**
     * Set the delete alias.
     *
     * @param array|string|null $alias The alias to delete.
     * @return DeleteQuery The DeleteQuery.
     */
    public function alias(array|string $alias, bool $overwrite = false): static
    {
        if (!$this->connection->supports(DbFeature::DeleteAlias)) {
            throw DbException::forUnsupportedFeature('DELETE alias');
        }

        $alias = (array) $alias;
        $alias = array_filter($alias);

        if ($overwrite) {
            $this->alias = $alias;
        } else {
            $this->alias = array_merge($this->alias, $alias);
        }

        $this->dirty();

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
     * Get the using table.
     *
     * @return array|null The using table.
     */
    public function getUsing(): array|null
    {
        return $this->using;
    }

    /**
     * Set the JOIN tables.
     *
     * @param array $joins The joins.
     * @param bool $overwrite Whether to overwrite the existing joins.
     * @return Query The Query.
     */
    public function join(array $joins, bool $overwrite = false): static
    {
        if (!$this->connection->supports(DbFeature::DeleteJoin)) {
            throw DbException::forUnsupportedFeature('DELETE JOIN');
        }

        $this->_join($joins, $overwrite);

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
            ->compileDelete($this, $binder);
    }

    /**
     * Set the using table.
     *
     * @param array|string $table The table.
     * @param bool $overwrite Whether to overwrite the existing table.
     * @return Query The Query.
     */
    public function using(array|string $table, bool $overwrite = false): static
    {
        if (!$this->connection->supports(DbFeature::DeleteUsing)) {
            throw DbException::forUnsupportedFeature('DELETE USING');
        }

        $table = (array) $table;

        if ($overwrite) {
            $this->using = $table;
        } else {
            $this->using = array_merge($this->using, $table);
        }

        $this->dirty();

        return $this;
    }
}
