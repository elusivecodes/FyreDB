<?php
declare(strict_types=1);

namespace Fyre\DB;

use
    Fyre\DB\Connection,
    Fyre\DB\Exceptions\DbException,
    Fyre\DB\Handlers\MySQL\MySQLConnection;

use function
    array_key_exists,
    array_search,
    class_exists,
    is_array;

/**
 * ConnectionManager
 */
abstract class ConnectionManager
{

    protected static array $config = [];

    protected static array $instances = [];

    /**
     * Clear configs and instances.
     */
    public static function clear(): void
    {
        static::$config = [];
        static::$instances = [];
    }

    /**
     * Get the handler config.
     * @param string|null $key The config key.
     * @return array|null
     */
    public static function getConfig(string|null $key = null): array|null
    {
        if (!$key) {
            return static::$config;
        }

        return static::$config[$key] ?? null;
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
     * @throws DbException if the handler is invalid.
     */
    public static function load(array $options = []): Connection
    {
        if (!array_key_exists('className', $options)) {
            throw DbException::forInvalidClass();
        }

        if (!class_exists($options['className'], true)) {
            throw DbException::forInvalidClass($options['className']);
        }

        return new $options['className']($options);
    }

    /**
     * Set handler config.
     * @param string|array $key The config key.
     * @param array|null $options The config options.
     * @throws DbException if the config is invalid.
     */
    public static function setConfig(string|array $key, array|null $options = null): void
    {
        if (is_array($key)) {
            foreach ($key AS $k => $value) {
                static::setConfig($k, $value);
            }

            return;
        }

        if (!is_array($options)) {
            throw DbException::forInvalidConfig($key);
        }

        if (array_key_exists($key, static::$config)) {
            throw DbException::forConfigExists($key);
        }

        static::$config[$key] = $options;
    }

    /**
     * Unload a handler.
     * @param string $key The config key.
     */
    public static function unload(string $key = 'default'): void
    {
        unset(static::$instances[$key]);
        unset(static::$config[$key]);
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
