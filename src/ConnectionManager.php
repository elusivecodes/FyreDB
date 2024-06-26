<?php
declare(strict_types=1);

namespace Fyre\DB;

use Fyre\DB\Exceptions\DbException;

use function array_key_exists;
use function array_search;
use function class_exists;
use function is_array;

/**
 * ConnectionManager
 */
abstract class ConnectionManager
{
    public const DEFAULT = 'default';

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
     *
     * @param string|null $key The config key.
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
     *
     * @param Connection $connection The Connection.
     * @return string|null The connection key.
     */
    public static function getKey(Connection $connection): string|null
    {
        return array_search($connection, static::$instances, true) ?: null;
    }

    /**
     * Determine if a config exists.
     *
     * @param string $key The config key.
     * @return bool TRUE if the config exists, otherwise FALSE.
     */
    public static function hasConfig(string $key = self::DEFAULT): bool
    {
        return array_key_exists($key, static::$config);
    }

    /**
     * Determine if a handler is loaded.
     *
     * @param string $key The config key.
     * @return bool TRUE if the handler is loaded, otherwise FALSE.
     */
    public static function isLoaded(string $key = self::DEFAULT): bool
    {
        return array_key_exists($key, static::$instances);
    }

    /**
     * Load a handler.
     *
     * @param array $options Options for the handler.
     * @return Connection The handler.
     *
     * @throws DbException if the handler is not valid.
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
     *
     * @param array|string $key The config key.
     * @param array|null $options The config options.
     *
     * @throws DbException if the config is not valid.
     */
    public static function setConfig(array|string $key, array|null $options = null): void
    {
        if (is_array($key)) {
            foreach ($key as $k => $value) {
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
     *
     * @param string $key The config key.
     * @return bool TRUE if the handler was removed, otherwise FALSE.
     */
    public static function unload(string $key = self::DEFAULT): bool
    {
        if (!array_key_exists($key, static::$config)) {
            return false;
        }

        unset(static::$instances[$key]);
        unset(static::$config[$key]);

        return true;
    }

    /**
     * Load a shared handler instance.
     *
     * @param string $key The config key.
     * @return Connection The handler.
     */
    public static function use(string $key = self::DEFAULT): Connection
    {
        return static::$instances[$key] ??= static::load(static::$config[$key] ?? []);
    }
}
