<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

use function array_merge;

/**
 * SelectTrait
 */
trait SelectTrait
{
    protected array $fields = [];

    /**
     * Get the SELECT fields.
     *
     * @return array The SELECT fields.
     */
    public function getSelect(): array
    {
        return $this->fields;
    }

    /**
     * Set the SELECT fields.
     *
     * @param array|string $fields The fields.
     * @param bool $overwrite Whether to overwrite the existing fields.
     * @return Query The Query.
     */
    public function select(array|string $fields = '*', bool $overwrite = false): static
    {
        $fields = (array) $fields;

        if ($overwrite) {
            $this->fields = $fields;
        } else {
            $this->fields = array_merge($this->fields, $fields);
        }

        $this->dirty();

        return $this;
    }
}
