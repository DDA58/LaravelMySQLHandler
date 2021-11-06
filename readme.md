# LaravelMySQLHandler

## Description

This package adds [MySQL Handler Statement](https://dev.mysql.com/doc/refman/8.0/en/handler.html) to Laravel based project

## Installation

Add repository to the service composer.json file

```json
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/DDA58/LaravelMySQLHandler"
    }
]
```

And then require `dda58/laravelmysqlhandler` package

```bash
$ composer require dda58/laravelmysqlhandler
```

## Usage

### Usage by DB facade

```php
<?php

$handler = DB::table($tableName)->openHandler();
$result = $handler->readPrimary($indexValue, $keyword)->get();
$handler->close();

$handler = DB::table($tableName)->openHandler();
$result = $handler->read($indexName, $indexValue, $keyword)->get();
$handler->close();
```

You can also add where, limit and offset to query

```php
<?php

$handler = DB::table($tableName)->openHandler();
$result = $handler->where($where)->limit($limit)->offset($offset)->readFirst($indexValue)->get();
$handler->close();
```

### Usage by Model

```php
<?php

$handler = YourModel::openHandler();
$result = $handler->where($where)->limit($limit)->offset($offset)->readPrev($indexValue)->get();
$handler->close();
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ ./vendor/bin/phpunit tests/
```

## Security

If you discover any security related issues, please email dda58denisov@gmail.com instead of using the issue tracker.

## Credits

- [Dmitrii Denisov][link-author]

## License

LaravelMySQLHandler is open-sourced software licensed under the [MIT license](license.md).

[link-author]: https://github.com/dda58
