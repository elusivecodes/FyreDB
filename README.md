# FyreDB

**FyreDB** is a free, open-source database library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
- [Connections](#connections)
    - [MySQL](#mysql)
    - [Postgres](#postgres)
    - [Sqlite](#sqlite)
- [Queries](#queries)
    - [Delete](#delete)
    - [Insert](#insert)
    - [Insert From](#insert-from)
    - [Replace](#replace)
    - [Select](#select)
    - [Update](#update)
    - [Update Batch](#update-batch)
- [Results](#results)



## Installation

**Using Composer**

```
composer require fyre/db
```

In PHP:

```php
use Fyre\DB\ConnectionManager;
```


## Basic Usage

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).
- `$config` is a [*Config*](https://github.com/elusivecodes/FyreConfig).

```php
$connectionManager = new ConnectionManager($container, $config);
```

Default configuration options will be resolved from the "*Database*" key in the [*Config*](https://github.com/elusivecodes/FyreConfig).

**Autoloading**

It is recommended to bind the *ConnectionManager* to the [*Container*](https://github.com/elusivecodes/FyreContainer) as a singleton.

```php
$container->singleton(ConnectionManager::class);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$connectionManager = $container->use(ConnectionManager::class);
```


## Methods

**Build**

Build a [*Connection*](#connections).

- `$options` is an array containing configuration options.

```php
$connection = $connectionManager->build($options);
```

[*Connection*](#connections) dependencies will be resolved automatically from the [*Container*](https://github.com/elusivecodes/FyreContainer).

**Clear**

Clear and close connections.

```php
$connectionManager->clear();
```

**Get Config**

Set a [*Connection*](#connections) config.

- `$key` is a string representing the [*Connection*](#connections) key.

```php
$config = $connectionManager->getConfig($key);
```

Alternatively, if the `$key` argument is omitted an array containing all configurations will be returned.

```php
$config = $connectionManager->getConfig();
```

**Has Config**

Determine whether a [*Connection*](#connections) config exists.

- `$key` is a string representing the [*Connection*](#connections) key, and will default to `ConnectionManager::DEFAULT`.

```php
$hasConfig = $connectionManager->hasConfig($key);
```

**Is Loaded**

Determine whether a [*Connection*](#connections) instance is loaded.

- `$key` is a string representing the [*Connection*](#connections) key, and will default to `ConnectionManager::DEFAULT`.

```php
$isLoaded = $connectionManager->isLoaded($key);
```

**Set Config**

Set the [*Connection*](#connections) config.

- `$key` is a string representing the [*Connection*](#connections) key.
- `$options` is an array containing configuration options.

```php
$connectionManager->setConfig($key, $options);
```

**Unload**

Unload a [*Connection*](#connections).

- `$key` is a string representing the [*Connection*](#connections) key, and will default to `ConnectionManager::DEFAULT`.

```php
$connectionManager->unload($key);
```

**Use**

Load a shared [*Connection*](#connections) instance.

- `$key` is a string representing the [*Connection*](#connections) key, and will default to `ConnectionManager::DEFAULT`.

```php
$connection = $connectionManager->use($key);
```

[*Connection*](#connections) dependencies will be resolved automatically from the [*Container*](https://github.com/elusivecodes/FyreContainer).


## Connections

You can load a specific connection handler by specifying the `className` option of the `$options` variable above.

Custom connection handlers can be created by extending `\Fyre\DB\Connection`, ensuring all below methods are implemented.

Custom handlers should also implement a `generator` method that returns a new *QueryGenerator* (if required) and a `resultSetClass` static method that returns the class name to use for [results](#results).

**Affected Rows**

Get the number of affected rows.

```php
$affectedRows = $connection->affectedRows();
```

**After Commit**

Queue a callback to execute after the transaction is committed.

- `$callback` is a *Closure*.
- `$priority` is a number representing the callback priority, and will default to *1*.
- `$key` is a string representing a unique identifier for the callback, and will default to *null*.

```php
$connection->afterCommit($callback, $priority, $key);
```

The callback will be executed immediately if there is no active transaction.

**Begin**

Begin a transaction.

```php
$connection->begin();
```

**Commit**

Commit a transaction.

```php
$connection->commit();
```

**Connect**

Connect to the database.

```php
$connection->connect();
```

This method is called automatically when the *Connection* is created.

**Delete**

Create a [*DeleteQuery*](#delete).

- `$alias` is a string or array containing the table aliases to delete, and will default to *null*.

```php
$query = $connection->delete($alias);
```

**Disable Foreign Keys**

Disable foreign key checks.

```php
$connection->disableForeignKeys();
```

**Disable Query Logging**

Disable query logging.

```php
$connection->disableQueryLogging();
```

**Disconnect**

Disconnect from the database.

```php
$connection->disconnect();
```

**Enable Foreign Keys**

Enable foreign key checks.

```php
$connection->enableForeignKeys();
```

**Enable Query Logging**

Enable query logging.

```php
$connection->enableQueryLogging();
```

**Execute**

Execute a SQL query with bound parameters.

- `$sql` is a string representing the SQL query.
- `$params` is an array containing the bound parameters.

```php
$result = $connection->execute($sql, $params);
```

The SQL query can use either *?* as a placeholder (for numerically indexed parameters), or the array key prefixed with *:*.

This method will return a [*ResultSet*](#results) for SELECT queries. Other query types will return a boolean value.

**Get Charset**

Get the connection character set.

```php
$charset = $connection->getCharset();
```

**Get Error**

Get the last connection error.

```php
$error = $connection->getError();
```

**Get Savepoint Level**

Get the transaction save point level.

```php
$savePointLevel = $connection->getSavePointLevel();
```

**In Transaction**

Determine whether a transaction is in progress.

```php
$inTransaction = $connection->inTransaction();
```

**Insert**

Create an [*InsertQuery*](#insert).

```php
$query = $connection->insert();
```

**Insert From**

Create an [*InsertFromQuery*](#insert-from).

- `$from` is a *Closure*, *SelectQuery*, *QueryLiteral* or string representing the query.
- `$columns` is an array of column names.

```php
$query = $connection->insertFrom($from, $columns);
```

**Insert ID**

Get the last inserted ID.

```php
$id = $connection->insertId();
```

When performing bulk inserts, this method will return the first ID for [*MySQL*](#mysql) connections, and the last ID for [*Postgres*](#postgres) and [*Sqlite*](#sqlite).

**Literal**

Create a *QueryLiteral*.

- `$string` is a string representing the literal string.

```php
$literal = $connection->literal($string);
```

**Query**

Execute a SQL query.

- `$sql` is a string representing the SQL query.

```php
$result = $connection->query($sql);
```

This method will return a [*ResultSet*](#results) for SELECT queries. Other query types will return a boolean value.

**Quote**

Quote a string for use in SQL queries.

- `$value` is a string representing the value to quote.

```php
$quoted = $connection->quote($value);
```

**Replace**

Create a [*ReplaceQuery*](#replace).

```php
$query = $connection->replace();
```

This method is only supported for queries using a *MysqlConnection* or *SqliteConnection*.

**Rollback**

Rollback a transaction.

```php
$connection->rollback();
```

**Select**

Create a [*SelectQuery*](#select).

- `$fields` is an array or string representing the fields to select, and will default to "*".

```php
$query = $connection->select($fields);
```

Non-numeric array keys will be used as field aliases.

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection* and the second argument will be the *ValueBinder*.

**Set Charset**

Set the connection character set.

- `$charset` is a string representing the connection character set.

```php
$connection->setCharset($charset);
```

**Transactional**

Execute a callback inside a database transaction.

- `$callback` is a *Closure* that will be executed inside the transaction.

```php
$result = $connection->transactional($callback);
```

If the callback returns *false* or throws an *Exception* the transaction will be rolled back, otherwise it will be committed.

**Truncate**

Truncate a table.

- `$tableName` is a string representing the table name.

```php
$connection->truncate($tableName);
```

**Update**

Create an [*UpdateQuery*](#update).

- `$table` is an array or string representing the table(s).

```php
$query = $connection->update($table);
```

Non-numeric array keys will be used as table aliases.

**Update Batch**

Create an [*UpdateBatchQuery*](#update-batch).

- `$table` is an array or string representing the table(s).

```php
$query = $connection->updateBatch($table);
```

Non-numeric array keys will be used as table aliases.

**Version**

Get the server version.

```php
$version = $connection->version();
```


### MySQL

The MySQL connection can be loaded using custom configuration.

- `$options` is an array containing configuration options.
    - `className` must be set to `\Fyre\DB\Handlers\Mysql\MysqlConnection`.
    - `host` is a string representing the MySQL host, and will default to "*127.0.0.1*".
    - `username` is a string representing the MySQL username.
    - `password` is a string representing the MySQL password.
    - `database` is a string representing the MySQL database.
    - `port` is a number indicating the MySQL port, and will default to *3306*.
    - `collation` is a string representing the collation, and will default to "*utf8mb4_unicode_ci*".
    - `charset` is a string representing the character set, and will default to "*utf8mb4*".
    - `compress` is a boolean indicating whether to enable compression, and will default to *false*.
    - `persist` is a boolean indicating whether to use a persistent connection, and will default to *false*.
    - `timeout` is a number indicating the connection timeout.
    - `ssl` is an array containing SSL options.
        - `key` is a string representing the path to the key file.
        - `cert` is a string representing the path to the certificate file.
        - `ca` is a string representing the path to the certificate authority file.
        - `capath` is a string representing the path to a directory containing CA certificates.
        - `cipher` is a string representing a list of allowable ciphers to use for encryption.
    - `flags` is an array containing PDO connection options.
    - `log` is a boolean indicating whether to log queries, and will default to *false*.

```php
$container->use(Config::class)->set('Database.mysql', $options);
```

**Get Collation**

Get the connection collation.

```php
$collation = $connection->getCollation();
```

### Postgres

The Postgres connection can be loaded using custom configuration.

- `$options` is an array containing configuration options.
    - `className` must be set to `\Fyre\DB\Handlers\Postgres\PostgresConnection`.
    - `host` is a string representing the Postgres host, and will default to "*127.0.0.1*".
    - `username` is a string representing the Postgres username.
    - `password` is a string representing the Postgres password.
    - `database` is a string representing the Postgres database.
    - `port` is a number indicating the Postgres port, and will default to *5432*.
    - `charset` is a string representing the character set, and will default to "*utf8*".
    - `schema` is a string representing the character set, and will default to "*public*".
    - `persist` is a boolean indicating whether to use a persistent connection, and will default to *false*.
    - `timeout` is a number indicating the connection timeout.
    - `flags` is an array containing PDO connection options.
    - `log` is a boolean indicating whether to log queries, and will default to *false*.

```php
$container->use(Config::class)->set('Database.postgres', $options);
```

**Set Schema**

Set the connection schema.

- `$schema` is a string representing the connection schema.

```php
$connection->setSchema($schema);
```

### Sqlite

The Sqlite connection can be loaded using custom configuration.

- `$options` is an array containing configuration options.
    - `className` must be set to `\Fyre\DB\Handlers\Sqlite\SqliteConnection`.
    - `database` is a string representing the Sqlite database file, and will default to "*:memory:*".
    - `mask ` is a number indicating the database file permissions, and will default to 0644.
    - `cache` is a string representing the cache flag.
    - `mode` is a string representing the mode flag.
    - `persist` is a boolean indicating whether to use a persistent connection, and will default to *false*.
    - `flags` is an array containing PDO connection options.
    - `log` is a boolean indicating whether to log queries, and will default to *false*.

```php
$container->use(Config::class)->set('Database.sqlite', $options);
```


## Queries

The `\Fyre\DB\Query` class provides base methods related to building queries, and is extended by the query type classes below.

**Execute**

Execute the query.

```php
$result = $query->execute();
```

This method will return a [*ResultSet*](#results) for SELECT queries. Other query types will return a boolean value.

**Connection**

Get the [*Connection*](#connections).

```php
$connection = $query->getConnection();
```

**Get Table**

Get the table(s).

```php
$table = $query->getTable();
```

**Sql**

Generate the SQL query.

```php
$sql = $query->sql();
```

**Table**

Set the table(s).

- `$table` is an array or string representing the table(s).
- `$overwrite` is a boolean indicating whether to overwrite existing tables, and will default to *false*.

```php
$query->table($table, $overwrite);
```

Non-numeric array keys will be used as table aliases.

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection*.

### Delete

The `\Fyre\DB\Queries\DeleteQuery` class extends the [*Query*](#queries) class, while providing additional methods for executing DELETE queries.

```php
$connection
    ->delete($alias)
    ->from($table)
    ->where($conditions)
    ->execute();
```

Multiple tables is only supported for queries using a *MysqlConnection*.

**Alias**

Set the delete alias(es).

- `$alias` is a string or array containing the table aliases to delete, and will default to *null*.
- `$overwrite` is a boolean indicating whether to overwrite existing aliases, and will default to *false*.

```php
$query->alias($alias, $overwrite);
```

This method is only supported for queries using a *MysqlConnection*.

**Epilog**

Set the epilog.

- `$epilog` is a string representing the epilog for the query.

```php
$query->epilog($epilog);
```

**From**

Set the FROM table(s).

- `$table` is an array or string representing the table(s).
- `$overwrite` is a boolean indicating whether to overwrite existing tables, and will default to *false*.

```php
$query->from($table, $overwrite);
```

Non-numeric array keys will be used as table aliases.

**Get Alias**

Get the delete alias(es).

```php
$alias = $query->getAlias();
```

**Get Epilog**

Get the epilog.

```php
$epilog = $query->getEpilog();
```

**Get From**

Get the FROM table(s).

```php
$table = $query->getFrom();
```

**Get Join**

Get the JOIN tables.

```php
$joins = $query->getJoin();
```

**Get Limit**

Get the LIMIT clause.

```php
$limit = $query->getLimit();
```

**Get Order By**

Get the ORDER BY fields.

```php
$orderBy = $query->getOrderBy();
```

**Get Using**

Get the USING tables.

```php
$table = $query->getUsing();
```

**Get Where**

Get the WHERE conditions.

```php
$conditions = $query->getWhere();
```

**Join**

Set the JOIN tables.

- `$joins` is a 2-dimensional array of joins.
- `$overwrite` is a boolean indicating whether to overwrite existing joins, and will default to *false*.

```php
$query->join($joins, $overwrite);
```

Each join array can contain a `table`, `alias`, `type` and an array of `conditions`. If the `type` is not specified it will default to INNER.

This method is only supported for queries using a *MysqlConnection*.

**Limit**

Set the LIMIT clause.

- `$limit` is a number indicating the query limit.

```php
$query->limit($limit);
```

**Order By**

Set the ORDER BY fields.

- `$fields` is an array or string representing the fields to order by.
- `$overwrite` is a boolean indicating whether to overwrite existing fields, and will default to *false*.

```php
$query->orderBy($fields, $overwrite);
```

**Using**

Set the USING tables.

- `$table` is an array or string representing the using table(s).
- `$overwrite` is a boolean indicating whether to overwrite existing using tables, and will default to *false*.

```php
$query->using($table, $overwrite);
```

This method is only supported for queries using a *PostgresConnection*.

**Where**

Set the WHERE conditions.

- `$conditions` is an array or string representing the where conditions.
- `$overwrite` is a boolean indicating whether to overwrite existing conditions, and will default to *false*.

```php
$query->where($conditions, $overwrite);
```

Array conditions can contain:
- Literal values with numeric keys.
- Key/value pairs where the key is the field (and comparison operator) and the value(s) will be escaped.
- Array values containing a group of conditions. These will be joined using the *AND* operator unless the array key is "*OR*" or "*NOT*".

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection* and the second argument will be the *ValueBinder*.

### Insert

The `\Fyre\DB\Queries\InsertQuery` class extends the [*Query*](#queries) class, while providing additional methods for executing INSERT queries.

```php
$connection
    ->insert()
    ->into($table)
    ->values($values)
    ->execute();
```

**Epilog**

Set the epilog.

- `$epilog` is a string representing the epilog for the query.

```php
$query->epilog($epilog);
```

**Get Epilog**

Get the epilog.

```php
$epilog = $query->getEpilog();
```

**Get Into**

Get the INTO table.

```php
$table = $query->getInto();
```

**Get Values**

Get the REPLACE data.

```php
$values = $query->getValues();
```

**Into**

Set the INTO table.

- `$table` is a string representing the table.
- `$overwrite` is a boolean indicating whether to overwrite existing tables, and will default to *false*.

```php
$query->into($table, $overwrite);
```

**Values**

- `$values` is a 2-dimensional array of values to insert.
- `$overwrite` is a boolean indicating whether to overwrite existing data, and will default to *false*.

```php
$query->values($values, $overwrite);
```

Array keys will be used for the column names, and the values will be escaped automatically.

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection* and the second argument will be the *ValueBinder*.

### Insert From

The `\Fyre\DB\Queries\InsertFromQuery` class extends the [*Query*](#queries) class, while providing additional methods for executing INSERT queries from SELECT queries.

```php
$connection
    ->insertFrom($from, $columns)
    ->into($table)
    ->execute();
```

**Epilog**

Set the epilog.

- `$epilog` is a string representing the epilog for the query.

```php
$query->epilog($epilog);
```

**Get Epilog**

Get the epilog.

```php
$epilog = $query->getEpilog();
```

**Get Into**

Get the INTO table.

```php
$table = $query->getInto();
```

**Into**

Set the INTO table.

- `$table` is a string representing the table.
- `$overwrite` is a boolean indicating whether to overwrite existing tables, and will default to *false*.

```php
$query->into($table, $overwrite);
```

### Replace

The `\Fyre\DB\Queries\ReplaceQuery` class extends the [*Query*](#queries) class, while providing additional methods for executing REPLACE queries.

```php
$connection
    ->replace()
    ->into($table)
    ->values($values)
    ->execute();
```

**Epilog**

Set the epilog.

- `$epilog` is a string representing the epilog for the query.

```php
$query->epilog($epilog);
```

**Get Epilog**

Get the epilog.

```php
$epilog = $query->getEpilog();
```

**Get Into**

Get the INTO table.

```php
$table = $query->getInto();
```

**Get Values**

Get the REPLACE data.

```php
$values = $query->getValues();
```

**Into**

Set the INTO table.

- `$table` is a string representing the table.
- `$overwrite` is a boolean indicating whether to overwrite existing tables, and will default to *false*.

```php
$query->into($table, $overwrite);
```

**Values**

- `$values` is a 2-dimensional array of values to insert.
- `$overwrite` is a boolean indicating whether to overwrite existing data, and will default to *false*.

```php
$query->values($values, $overwrite);
```

Array keys will be used for the column names, and the values will be escaped automatically.

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection* and the second argument will be the *ValueBinder*.

### Select

The `\Fyre\DB\Queries\SelectQuery` class extends the [*Query*](#queries) class, while providing additional methods for executing SELECT queries.

```php
$results = $connection
    ->select($fields)
    ->from($table)
    ->where($conditions)
    ->execute();
```

**Distinct**

Set the DISTINCT clause.

- `$distinct` is a boolean indicating whether to set the query as DISTINCT, and will default to *true*.

```php
$query->distinct($distinct);
```

**Epilog**

Set the epilog.

- `$epilog` is a string representing the epilog for the query.

```php
$query->epilog($epilog);
```

**Except**

Add an EXCEPT query.

- `$union` is a *Closure*, *SelectQuery*, *QueryLiteral* or string representing the query.
- `$overwrite` is a boolean indicating whether to overwrite existing unions, and will default to *false*.

```php
$query->except($union, $overwrite);
```

**From**

Set the FROM table(s).

- `$table` is an array or string representing the table(s).
- `$overwrite` is a boolean indicating whether to overwrite existing tables, and will default to *false*.

```php
$query->from($table, $overwrite);
```

Non-numeric array keys will be used as table aliases.

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection*.

**Get Distinct**

Get the DISTINCT clause.

```php
$distinct = $query->getDistinct();
```

**Get Epilog**

Get the epilog.

```php
$epilog = $query->getEpilog();
```

**Get Group By**

Get the GROUP BY fields.

```php
$groupBy = $query->getGroupBy();
```

**Get From**

Get the FROM table(s).

```php
$table = $query->getFrom();
```

**Get Having**

Get the HAVING conditions.

```php
$having = $query->getHaving();
```

**Get Join**

Get the JOIN tables.

```php
$joins = $query->getJoin();
```

**Get Limit**

Get the LIMIT clause.

```php
$limit = $query->getLimit();
```

**Get Offset**

Get the OFFSET clause.

```php
$offset = $query->getOffset();
```

**Get Order By**

Get the ORDER BY fields.

```php
$orderBy = $query->getOrderBy();
```

**Get Select**

Get the SELECT fields.

```php
$fields = $query->getSelect();
```

**Get Union**

Get the UNION queries.

```php
$unions = $query->getUnion();
```

**Get Where**

Get the WHERE conditions.

```php
$conditions = $query->getWhere();
```

**Get With**

Get the WITH queries.

```php
$with = $query->getWith();
```

**Group By**

Set the GROUP BY fields.

- `$fields` is an array or string representing the fields to group by.
- `$overwrite` is a boolean indicating whether to overwrite existing fields, and will default to *false*.

```php
$query->groupBy($fields, $overwrite);
```

**Having**

Set the HAVING conditions.

- `$conditions` is an array or string representing the having conditions.
- `$overwrite` is a boolean indicating whether to overwrite existing conditions, and will default to *false*.

```php
$query->having($conditions, $overwrite);
```

Array conditions can contain:
- Literal values with numeric keys.
- Key/value pairs where the key is the field (and comparison operator) and the value(s) will be escaped automatically.
- Array values containing a group of conditions. These will be joined using the *AND* operator unless the array key is "*OR*" or "*NOT*".

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection* and the second argument will be the *ValueBinder*.

**Intersect**

Add an INTERSECT query.

- `$union` is a *Closure*, *SelectQuery*, *QueryLiteral* or string representing the query.
- `$overwrite` is a boolean indicating whether to overwrite existing unions, and will default to *false*.

```php
$query->intersect($union, $overwrite);
```

**Join**

Set the JOIN tables.

- `$joins` is a 2-dimensional array of joins.
- `$overwrite` is a boolean indicating whether to overwrite existing joins, and will default to *false*.

```php
$query->join($joins, $overwrite);
```

Each join array can contain a `table`, `alias`, `type` and an array of `conditions`. If the `type` is not specified it will default to INNER.

**Limit**

Set the LIMIT and OFFSET clauses.

- `$limit` is a number indicating the query limit.
- `$offset` is a number indicating the query offset.

```php
$query->limit($limit, $offset);
```

**Offset**

Set the OFFSET clause.

- `$offset` is a number indicating the query offset.

```php
$query->offset($offset);
```

**Order By**

Set the ORDER BY fields.

- `$fields` is an array or string representing the fields to order by.
- `$overwrite` is a boolean indicating whether to overwrite existing fields, and will default to *false*.

```php
$query->orderBy($fields, $overwrite);
```

**Select**

Set the SELECT fields.

- `$fields` is an array or string representing the fields to select, and will default to "*".
- `$overwrite` is a boolean indicating whether to overwrite existing fields, and will default to *false*.

```php
$query->select($fields, $overwrite);
```

Non-numeric array keys will be used as field aliases.

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection* and the second argument will be the *ValueBinder*.

**Union**

Add a UNION DISTINCT query.

- `$union` is a *Closure*, *SelectQuery*, *QueryLiteral* or string representing the query.
- `$overwrite` is a boolean indicating whether to overwrite existing unions, and will default to *false*.

```php
$query->union($union, $overwrite);
```

**Union All**

Add a UNION ALL query.

- `$union` is a *Closure*, *SelectQuery*, *QueryLiteral* or string representing the query.
- `$overwrite` is a boolean indicating whether to overwrite existing unions, and will default to *false*.

```php
$query->unionAll($union, $overwrite);
```

**Where**

Set the WHERE conditions.

- `$conditions` is an array or string representing the where conditions.
- `$overwrite` is a boolean indicating whether to overwrite existing conditions, and will default to *false*.

```php
$query->where($conditions, $overwrite);
```

Array conditions can contain:
- Literal values with numeric keys.
- Key/value pairs where the key is the field (and comparison operator) and the value(s) will be escaped.
- Array values containing a group of conditions. These will be joined using the *AND* operator unless the array key is "*OR*" or "*NOT*".

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection* and the second argument will be the *ValueBinder*.

**With**

Set the WITH clause.

- `$with` is an array of common table expressions.
- `$overwrite` is a boolean indicating whether to overwrite existing expressions, and will default to *false*.

```php
$query->with($with, $overwrite);
```

Array keys will be used as table aliases.

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection*.

**With Recursive**

Set the WITH RECURSIVE clause.

- `$with` is an array of common table expressions.
- `$overwrite` is a boolean indicating whether to overwrite existing expressions, and will default to *false*.

```php
$query->withRecursive($with, $overwrite);
```

Array keys will be used as table aliases.

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection*.

### Update

The `\Fyre\DB\Queries\UpdateQuery` class extends the [*Query*](#queries) class, while providing additional methods for executing UPDATE queries.

```php
$connection
    ->update($table)
    ->set($data)
    ->where($conditions)
    ->execute();
```

Multiple tables is only supported for queries using a *MysqlConnection*.

**Epilog**

Set the epilog.

- `$epilog` is a string representing the epilog for the query.

```php
$query->epilog($epilog);
```

**From**

Set the FROM tables.

- `$table` is an array or string representing the from table(s).
- `$overwrite` is a boolean indicating whether to overwrite existing from tables, and will default to *false*.

```php
$query->from($table, $overwrite);
```

This method is only supported for queries using a *PostgresConnection* or *SqliteConnection*.

**Get Data**

Get the UPDATE data.

```php
$data = $query->getData();
```

**Get Epilog**

Get the epilog.

```php
$epilog = $query->getEpilog();
```

**Get From**

Get the FROM table(s).

```php
$table = $query->getFrom();
```

**Get Join**

Get the JOIN tables.

```php
$joins = $query->getJoin();
```

**Get Where**

Get the WHERE conditions.

```php
$conditions = $query->getWhere();
```

**Join**

Set the JOIN tables.

- `$joins` is a 2-dimensional array of joins.
- `$overwrite` is a boolean indicating whether to overwrite existing joins, and will default to *false*.

```php
$query->join($joins, $overwrite);
```

Each join array can contain a `table`, `alias`, `type` and an array of `conditions`. If the `type` is not specified it will default to INNER.

This method is only supported for queries using a *MysqlConnection*.

**Set**

- `$data` is an array of values to update.
- `$overwrite` is a boolean indicating whether to overwrite existing data, and will default to *false*.

```php
$query->set($data, $overwrite);
```

Array keys will be used for the column names, and the values will be escaped automatically.

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection* and the second argument will be the *ValueBinder*.

**Where**

Set the WHERE conditions.

- `$conditions` is an array or string representing the where conditions.
- `$overwrite` is a boolean indicating whether to overwrite existing conditions, and will default to *false*.

```php
$query->where($conditions, $overwrite);
```

Array conditions can contain:
- Literal values with numeric keys.
- Key/value pairs where the key is the field (and comparison operator) and the value(s) will be escaped.
- Array values containing a group of conditions. These will be joined using the *AND* operator unless the array key is "*OR*" or "*NOT*".

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection* and the second argument will be the *ValueBinder*.

### Update Batch

The `\Fyre\DB\Queries\UpdateBatchQuery` class extends the [*Query*](#queries) class, while providing additional methods for executing batch UPDATE queries.

```php
$connection
    ->updateBatch($table)
    ->set($data, $keys)
    ->execute();
```

**Epilog**

Set the epilog.

- `$epilog` is a string representing the epilog for the query.

```php
$query->epilog($epilog);
```

**Get Data**

Get the UPDATE data.

```php
$data = $query->getData();
```

**Get Epilog**

Get the epilog.

```php
$epilog = $query->getEpilog();
```

**Set**

- `$data` is a 2-dimensional array of values to update.
- `$keys` is a string or array containing the keys to use for updating.
- `$overwrite` is a boolean indicating whether to overwrite existing data, and will default to *false*.

```php
$query->set($data, $keys, $overwrite);
```

Array keys will be used for the column names, and the values will be escaped automatically.

If a *SelectQuery* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where the first argument will be the *Connection* and the second argument will be the *ValueBinder*.


## Results

SELECT queries will return a new *ResultSet* containing the results of the query.

The *ResultSet* is an implementation of an *Iterator* and can be used in a *foreach* loop.

```php
foreach ($result AS $row) { }
```

**All**

Get the results as an array.

```php
$array = $result->all();
```

**Clear Buffer**

Clear the results from the buffer.

- `$index` is a number representing the index of the result to clear.

```php
$result->clearBuffer($index);
```

Alternatively, if the `$index` argument is omitted, the entire buffer will be cleared.

```php
$result->clearBuffer();
```

**Column Count**

Get the column count.

```php
$columnCount = $result->columnCount();
```

**Columns**

Get the result columns.

```php
$columns = $result->columns();
```

**Count**

Get the result count.

```php
$count = $result->count();
```

**Fetch**

Get a result by index.

- `$index` is a number indicating the row index.

```php
$row = $result->fetch($index);
```

**First**

Get the first result.

```php
$first = $result->first();
```

**Free**

Free the result from memory.

```php
$result->free();
```

**Get Type**

Get the [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) for a column.

- `$name` is a string representing the column name.

```php
$parser = $result->getType($name);
```

**Last**

Get the last result.

```php
$last = $result->last();
```