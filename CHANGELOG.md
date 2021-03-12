# Parable GetSet

## 1.0.0

Just a re-release locking the interface in place. First final release!

## 0.3.0

_Changes_
- Add static analysis using psalm.
- `Exception` has been renamed to `GetSetException` for clarity.
- `getMultiple()` has been removed from all collections.
- When removing a non-existing key, since the end result is reached, no exception will be thrown anymore.

## 0.2.0

_Changes_
- Dropped support for php7, php8 only from now on.

## 0.1.4

_Changes_
- Added `getMultiple(string ...$keys): array` so you can request multiple keys in one go. The default value if not found is always `null`.

```php
// Example of getMultiple:
[$title, $content] = $postCollection->getMultiple('title', 'content');
```

## 0.1.3

_Changes_
- `get()` returns `null` or a custom default value when a key doesn't exist. `remove()` throws an exception. `getAndRemove()` also threw an exception, but the primary goal is to get the value. This behavior has been flipped. Now `getAndRemove()` will return `null`/default if the key doesn't exist and not throw anymore.

## 0.1.2

No changes, code style fix.

## 0.1.1

_Changes_
- Add `string` type-hinting to `$key` for `get()` and `getAndRemove()`, in line with the rest.
- Add `$default = null` parameter to `get()`, so that the scenarios of 'not set' and 'set but `null`' can be distinguished between.

## 0.1.0

_Changes_
- First release.
