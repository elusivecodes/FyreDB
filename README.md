# FyreDB

**FyreDB** is a free, database library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Methods](#methods)
- [Connections](#connections)
    - [MySQL](#mysql)
- [Queries](#queries)
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


## Methods

**Clear**

Clear and close connections.

```php
ConnectionManager::clear();
```

**Get Config**

Set a [*Connection*](#connections) config.

- `$key` is a string representing the [*Connection*](#connections) key.

```php
$config = ConnectionManager::getConfig($key);
```

Alternatively, if the `$key` argument is omitted an array containing all configurations will be returned.

```php
$config = ConnectionManager::getConfig();
```

**Get Key**

Get the key for a [*Connection*](#connections) instance.

- `$connection` is a [*Connection*](#connections).

```php
$key = ConnectionManager::getKey($connection);
```

**Load**

Load a [*Connection*](#connections).

- `$options` is an array containing configuration options.

```php
$connection = ConnectionManager::load($options);
```

**Set Config**

Set the [*Connection*](#connections) config.

- `$key` is a string representing the [*Connection*](#connections) key.
- `$options` is an array containing configuration options.

```php
ConnectionManager::setConfig($key, $options);
```

Alternatively, a single array can be provided containing key/value of configuration options.

```php
ConnectionManager::setConfig($config);
```

**Unload**

Unload a [*Connection*](#connections).

- `$key` is a string representing the [*Connection*](#connections) key, and will default to *"default"*.

```php
ConnectionManager::unload($key);
```

**Use**

Load a shared [*Connection*](#connections) instance.

- `$key` is a string representing the [*Connection*](#connections) key, and will default to *"default"*.

```php
$connection = ConnectionManager::use($key);
```


## Connections

You can load a specific connection handler by specifying the `className` option of the `$options` variable above.

Custom connection handlers can be created by extending `\Fyre\DB\Connection`, ensuring all below methods are implemented.

Custom handlers should also implement a `results` method that returns a new [*ResultSet*](#results) and a `generator` method that returns a new *QueryGenerator* (if required).

**Affected Rows**

Get the number of affected rows.

```php
$affectedRows = $connection->affectedRows();
```

**Begin**

Begin a transaction.

```php
$connection->begin();
```

**Builder**

Create a [*QueryBuilder*](#queries).

```php
$builder = $connection->builder();
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

**Disconnect**

Disconnect from the database.

```php
$connection->disconnect();
```

**Execute**

Execute a SQL query with bound parameters.

- `$sql` is a string representing the SQL query.
- `$params` is an array containing the bound parameters.

```php
$result = $connection->execute($sql, $params);
```

The SQL query can use either *?* as a placeholder (for numerically indexed paramaters), or the array key prefixed with *:*.

This method will return a [*ResultSet*](#results) for SELECT queries. Other query types will return a boolean value.

**Get Charset**

Get the connection character set.

```php
$charset = $connection->getCharset();
```

**Get Collation**

Get the connection collation.

```php
$collation = $connection->getCollation();
```

**Get Error**

Get the last connection error.

```php
$error = $connection->getError();
```

**Insert ID**

Get the last inserted ID.

```php
$id = $connection->insertId();
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

**Rollback**

Rollback a transaction.

```php
$connection->rollback();
```

**Transactional**

Execute a callback inside a database transaction.

- `$callback` is a *Closure* that will be executed inside the transaction.

```php
$result = $connection->transactional($callback);
```

If the callback returns *false* or throws an *Exception* the transaction will be rolled back, otherwise it will be committed.


### MySQL

The MySQL connection can be loaded using custom configuration.

- `$key` is a string representing the connection key.
- `$options` is an array containing configuration options.
    - `className` must be set to `\Fyre\DB\Handlers\MySQL\MySQLConnection`.
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

```php
ConnectionManager::setConfig($key, $options);

$connection = ConnectionManager::use($key);
```


## Queries

**Delete**

Set query as DELETE.

- `$aliases` is a string or array containing the table aliases to delete, and will default to *null*.

```php
$builder->delete($aliases);
```

**Distinct**

Set the DISTINCT clause.

- `$distinct` is a boolean indicating whether to set the query as DISTINCT, and will default to *true*.

```php
$builder->distinct($distinct);
```

**Epilog**

Set the epilog.

- `$epilog` is a string representing the epilog for the query.

```php
$builder->epilog($epilog);
```

**Except**

Add an EXCEPT query.

- `$query` is a *Closure*, *QueryBuilder*, *QueryLiteral* or string representing the query.

```php
$builder->except($query);
```

**Execute**

Execute the query.

```php
$result = $builder->execute();
```

This method will return a [*ResultSet*](#results) for SELECT queries. Other query types will return a boolean value.

**Group By**

Set the GROUP BY fields.

- `$fields` is an array or string representing the fields to group by.

```php
$builder->groupBy($fields);
```

**Having**

Set the HAVING conditions.

- `$conditions` is an array or string representing the having conditions.

```php
$builder->having($conditions);
```

Array conditions can contain:
- Literal values with numeric keys.
- Key/value pairs where the key is the field (and comparison operator) and the value(s) will be escaped automatically.
- Array values containing a group of conditions. These will be joined using the *AND* operator unless the array key is "*OR*" or "*NOT*".

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**Insert**

Set query as an INSERT.

- `$data` is an array of values to insert.

```php
$builder->insert($data);
```

Array keys will be used for the column names, and the values will be escaped automatically.

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**Insert Batch**

Set query as a batch INSERT.

- `$data` is a 2-dimensional array of values to insert.

```php
$builder->insertBatch($data);
```

Array keys will be used for the column names, and the values will be escaped automatically.

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**Insert From**

Set query as an INSERT from another query.

- `$query` is a *Closure*, *QueryBuilder*, *QueryLiteral* or string representing the query.
- `$columns` is an array of column names.

```php
$builder->insertFrom($query, $columns);
```

**Intersect**

Add an INTERSECT query.

- `$query` is a *Closure*, *QueryBuilder*, *QueryLiteral* or string representing the query.

```php
$builder->intersect($query);
```

**Join**

Set the JOIN tables.

- `$joins` is a 2-dimensional array of joins.

```php
$builder->join($joins);
```

Each join array can contain a `table`, `alias`, `type` and an array of `conditions`. If the `type` is not specified it will default to INNER.

**Limit**

Set the LIMIT and OFFSET clauses.

- `$limit` is a number indicating the query limit.
- `$offset` is a number indicating the query offset.

```php
$builder->limit($limit, $offset);
```

**Literal**

Create a *QueryLiteral*.

- `$string` is a string representing the literal string.

```php
$literal = $builder->literal($string);
```

**Offset**

Set the OFFSET clause.

- `$offset` is a number indicating the query offset.

```php
$builder->offset($offset);
```

**Order By**

Set the ORDER BY fields.

- `$fields` is an array or string representing the fields to order by.

```php
$builder->orderBy($fields);
```

**Replace**

Set query as a REPLACE.

- `$data` is an array of values to replace.

```php
$builder->replace($data);
```

Array keys will be used for the column names, and the values will be escaped automatically.

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**Replace Batch**

Set query as a batch REPLACE.

- `$data` is a 2-dimensional array of values to replace.

```php
$builder->replaceBatch($data);
```

Array keys will be used for the column names, and the values will be escaped automatically.

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**Select**

Set the SELECT fields.

- `$fields` is an array or string representing the fields to select.

```php
$builder->select($fields);
```

Non-numeric array keys will be used as field aliases.

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**Sql**

Generate the SQL query.

```php
$query = $builder->sql();
```

**Table**

Set the table(s).

- `$tables` is an array or string representing the tables.

```php
$builder->table($tables);
```

Non-numeric array keys will be used as table aliases.

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**Update**

Set query as an UPDATE.

- `$data` is an array of values to update.

```php
$builder->update($data);
```

Array keys will be used for the column names, and the values will be escaped automatically.

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**Update Batch**

Set query as a batch UPDATE.

- `$data` is a 2-dimensional array of values to update.
- `$updateKeys` is a string or array containing the keys to use for updating.

```php
$builder->updateBatch($data, $updateKeys);
```

Array keys will be used for the column names, and the values will be escaped automatically.

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**Union**

Add a UNION DISTINCT query.

- `$query` is a *Closure*, *QueryBuilder*, *QueryLiteral* or string representing the query.

```php
$builder->union($query);
```

**Union All**

Add a UNION ALL query.

- `$query` is a *Closure*, *QueryBuilder*, *QueryLiteral* or string representing the query.

```php
$builder->unionAll($query);
```

**Where**

Set the WHERE conditions.

- `$conditions` is an array or string representing the where conditions.

```php
$builder->where($conditions);
```

Array conditions can contain:
- Literal values with numeric keys.
- Key/value pairs where the key is the field (and comparison operator) and the value(s) will be escaped.
- Array values containing a group of conditions. These will be joined using the *AND* operator unless the array key is "*OR*" or "*NOT*".

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**With**

Set the WITH clause.

- `$with` is an array of common table expressions.

```php
$builder->with($with);
```

Array keys will be used as table aliases.

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.

**With Recursive**

Set the WITH RECURSIVE clause.

- `$with` is an array of common table expressions.

```php
$builder->withRecursive($with);
```

Array keys will be used as table aliases.

If a *QueryBuilder* or *QueryLiteral* is supplied as an array value they will be converted to a string and not escaped.

A *Closure* can also be supplied as an array value, where a new *QueryBuilder* will be passed as the first argument.


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

Get the [*Type*](https://github.com/elusivecodes/FyreTypeParser) parser for a column.

- `$name` is a string representing the column name.

```php
$parser = $result->getType($name);
```

**Last**

Get the last result.

```php
$last = $result->last();
```