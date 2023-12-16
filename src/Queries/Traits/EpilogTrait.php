<?php
declare(strict_types=1);

namespace Fyre\DB\Queries\Traits;

/**
 * EpilogTrait
 */
trait EpilogTrait
{

    protected string $epilog = '';

    /**
     * Set the epilog.
     * @param string $epilog The epilog.
     * @return Query The Query.
     */
    public function epilog(string $epilog = ''): static
    {
        $this->epilog = $epilog;
        $this->dirty();

        return $this;
    }

    /**
     * Get the epilog.
     * @return string The epilog.
     */
    public function getEpilog(): string
    {
        return $this->epilog;
    }

}
