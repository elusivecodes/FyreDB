<?php
declare(strict_types=1);

namespace Fyre\DB;

use
    Fyre\DB\Connection,
    Fyre\DB\Exceptions\DBException,
    Fyre\DB\Handlers\MySQL\MySQLConnection;

use function
    array_key_exists,
    array_search,
    class_exists;

/**
 * ConnectionManager
 */
abstract class ConnectionManager
{

    protected static array $config = [];

    protected static array $instances = [];

    /**
     * Clear instances.
     */
    public static function clear(): void
    {
        static::$instances = [];
    }

    /**
     * Get the key for a connection instance.
     * @param Connection $connection The Connection.
     * @return string|null The connection key.
     */
    public static function getKey(Connection $connection): string|null
    {
        return array_search($connection, static::$instances, true) ?: null;
    }

    /**
     * Load a handler.
     * @param array $options Options for the handler.
     * @return Connection The handler.
     * @throws DBException if the handler is invalid.
     */
    public static function load(array $options = []): Connection
    {
        if (!array_key_exists('className', $options)) {
            throw DBException::forInvalidClass();
        }

        if (!class_exists($options['className'], true)) {
            throw DBException::forInvalidClass($options['className']);
        }

        return new $options['className']($options);
    }

    /**
     * Set handler config.
     * @param string $key The config key.
     * @param array $options The config options.
     */
    public static function setConfig(string $key, array $options): void
    {
        static::$config[$key] = $options;
    }

    /**
     * Load a shared handler instance.
     * @param string $key The config key.
     * @return Connection The handler.
     */
    public static function use(string $key = 'default'): Connection
    {
        return static::$instances[$key] ??= static::load(static::$config[$key] ?? []);
    }

}
