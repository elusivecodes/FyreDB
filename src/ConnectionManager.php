<?php
declare(strict_types=1);

namespace Fyre\DB;

use
    Fyre\DB\Connection,
    Fyre\DB\Exceptions\DBException,
    Fyre\DB\Handlers\MySQL\MySQLConnection;

use function
    array_key_exists,
    class_exists;

/**
 * ConnectionManager
 */
abstract class ConnectionManager
{

    protected static array $config = [];

    protected static array $instances = [];

    /**
     * Clear (and close) instances.
     */
    public static function clear(): void
    {
        foreach (static::$instances AS $instance) {
            $instance->close();
        };

        static::$instances = [];
    }

    /**
     * Load a handler.
     * @param array $config Options for the handler.
     * @return Connection The handler.
     * @throws DBException if the handler is invalid.
     */
    public static function load(array $config = []): Connection
    {
        if (!array_key_exists('className', $config)) {
            throw DBException::forInvalidClass();
        }

        if (!class_exists($config['className'], true)) {
            throw DBException::forInvalidClass($config['className']);
        }

        return new $config['className']($config);
    }

    /**
     * Set handler config.
     * @param string $key The config key.
     * @param array $config The config options.
     */
    public static function setConfig(string $key, array $config): void
    {
        static::$config[$key] = $config;
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
