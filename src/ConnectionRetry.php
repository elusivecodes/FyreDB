<?php
declare(strict_types=1);

namespace Fyre\DB;

use Closure;
use PDOException;
use Throwable;

/**
 * ConnectionRetry
 */
class ConnectionRetry
{
    protected const RECONNECT_ERRORS = [
        1317, // interrupted
        2002, // refused
        2006, // gone away
    ];

    protected Connection $connection;

    protected int $maxRetries;

    protected int $reconnectDelay;

    protected int $retries = 0;

    /**
     * New Retry constructor.
     *
     * @param Connection $connection The Connection.
     * @param int $reconnectDelay The number of milliseconds to wait before reconnecting.
     * @param int $maxRetries The maximum number of retries.
     */
    public function __construct(Connection $connection, int $reconnectDelay = 100, int $maxRetries = 1)
    {
        $this->connection = $connection;
        $this->reconnectDelay = $reconnectDelay;
        $this->maxRetries = $maxRetries;
    }

    /**
     * Get the number of retry attempts.
     *
     * @return int The number of retry attempts.
     */
    public function getRetries(): int
    {
        return $this->retries;
    }

    /**
     * Run a callback and retry if an exception is thrown.
     *
     * @param Closure $action The callback to execute.
     * @return mixed The callback result.
     *
     * @throws Throwable The last exception thrown.
     */
    public function run(Closure $action): mixed
    {
        $this->retries = 0;
        while (true) {
            try {
                return $action();
            } catch (PDOException $e) {
                if ($this->shouldRetry($e)) {
                    $this->retries++;

                    continue;
                }

                throw $e;
            }
        }
    }

    /**
     * Re-establish the connection.
     *
     * @return bool TRUE if the connection was re-established, otherwise FALSE.
     */
    protected function reconnect(): bool
    {
        usleep($this->reconnectDelay * 1000);

        try {
            $this->connection->disconnect();
            $this->connection->connect();

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Determine whether a retry attempt should be made.
     *
     * @param PDOException $exception The Exception.
     * @return bool TRUE if a retry attempt should be made, otherwise FALSE.
     */
    protected function shouldRetry(PDOException $exception): bool
    {
        if (
            $this->retries < $this->maxRetries &&
            $this->connection->getSavePointLevel() === 0 &&
            $exception->errorInfo &&
            in_array($exception->errorInfo[1], static::RECONNECT_ERRORS)
        ) {
            return $this->reconnect();
        }

        return false;
    }
}
