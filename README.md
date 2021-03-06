# Parable GetSet

[![Workflow Status](https://github.com/parable-php/getset/workflows/Tests/badge.svg)](https://github.com/parable-php/getset/actions?query=workflow%3ATests)
[![Latest Stable Version](https://poser.pugx.org/parable-php/getset/v/stable)](https://packagist.org/packages/parable-php/getset)
[![Latest Unstable Version](https://poser.pugx.org/parable-php/getset/v/unstable)](https://packagist.org/packages/parable-php/getset)
[![License](https://poser.pugx.org/parable-php/getset/license)](https://packagist.org/packages/parable-php/getset)

Parable GetSet allows for clean and intuitive handling of global variables such as `$_GET` and `$_SERVER` using value
collections, in addition to resource objects with their own scope.

The major benefit is dot-notation (`key.string`) for nested values, such as `$getCollection->get('this.is.nested');`, which will
look in the `GetCollection`'s stored values for `['this']['is']['nested']`.

Collections always return the requested value or `null` by default, allowing the predictable use of null coalescing.

## Install

Php 8.0+ and [composer](https://getcomposer.org) are required.

```bash
$ composer require parable-php/getset
```

## Usage

Assuming a query string like: `?id=345&user[name]=username`:

```php
$getCollection = new \Parable\GetSet\GetCollection();

echo $getCollection->get('id');
echo ':';
echo $getCollection->get('user.name');
```

Would output `345:username`

Obviously you can also set values directly:

```php
$getCollection->set('settings.mail.from', 'me@system');

// later

$mail_from = $getCollection->get('settings.mail.from') ?? 'nobody';
```

## The Collections

#### Global Collections
These correspond to their `$_NAME` global variables, also found in the super-global `$GLOBALS` variable.

- `CookieCollection`, handles `$_COOKIE` data.
- `FilesCollection`, handles `$_FILES` data.
- `GetCollection`, handles `$_GET` data.
- `PostCollection`, handles `$_POST` data.
- `ServerCollection`, handles `$_SERVER` data.
- `SessionCollection`, handles `$_SESSION` data.

#### Local Resource Collections

- `DataCollection`, a generic collection using only a local resource, useful for passing data throughout an application. Think configuration.
- `InputStreamCollection`, to read out an input stream. Can import/parse `json` or query string data.

#### Your own custom Collections!

You can easily build your own collections by extending the `BaseCollection` class and co-opting the easy
dot-notation get/set logic. 

## API

- `getAll(): array` - returns all values currently stored
- `getAllAndClear(): array` - returns all values currently stored and then clears them
- `get(string $key, $default = null): mixed` - returns value by key or `$default` if not found
- `getAndRemove(string $key): mixed` - returns value by key and then removes it, or throw exception if it doesn't exist
- `set(string $key, $value): void` - set a value by key
- `setMany(array $values): void` - set all values passed one-by-one, overwriting pre-existing values
- `setAll(array $values): void` - set all values, overwriting any pre-existing ones
- `remove(string $key): void` - remove by key, if already removed, throws
- `clear(): void` - removes all values
- `has(string $key): bool` - returns whether the passed key (or `key.string`) currently exists
- `count(): int` - returns number of root value items (not all nested values)

## Contributing

Any suggestions, bug reports or general feedback is welcome. Use github issues and pull requests, or find me over at [devvoh.com](https://devvoh.com).

## License

All Parable components are open-source software, licensed under the MIT license.
