<?php
declare(strict_types=1);

namespace Fyre\DB\Queries;

use Fyre\DB\Connection;
use Fyre\DB\DbFeature;
use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Queries\Traits\EpilogTrait;
use Fyre\DB\Queries\Traits\JoinTrait;
use Fyre\DB\Queries\Traits\WhereTrait;
use Fyre\DB\Query;
use Fyre\DB\ValueBinder;

use function array_merge;

/**
 * UpdateQuery
 */
class UpdateQuery extends Query
{
    use EpilogTrait;
    use JoinTrait {
        JoinTrait::join as protected _join;
    }
    use WhereTrait;

    protected static bool $tableAliases = true;

    protected array $data = [];

    protected array $from = [];

    /**
     * New UpdateQuery constructor.
     *
     * @param Connection $connection The connection.
     * @param array|string|null $alias The alias to delete.
     */
    public function __construct(Connection $connection, array|string|null $table = null)
    {
        $this->multipleTables = $connection->supports(DbFeature::DeleteMultipleTables);

        parent::__construct($connection, $table);
    }

    /**
     * Set the from table.
     *
     * @param array|string $table The table.
     * @param bool $overwrite Whether to overwrite the existing table.
     * @return Query The Query.
     */
    public function from(array|string $table, bool $overwrite = false): static
    {
        if (!$this->connection->supports(DbFeature::UpdateFrom)) {
            throw DbException::forUnsupportedFeature('UPDATE FROM');
        }

        $table = (array) $table;

        if ($overwrite) {
            $this->from = $table;
        } else {
            $this->from = array_merge($this->from, $table);
        }

        $this->dirty();

        return $this;
    }

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
     * Get the from table.
     *
     * @return array|null The from table.
     */
    public function getFrom(): array|null
    {
        return $this->from;
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
            throw DbException::forUnsupportedFeature('UPDATE JOIN');
        }

        $this->_join($joins, $overwrite);

        return $this;
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
