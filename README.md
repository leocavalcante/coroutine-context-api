# Coroutine Context API

Using Coroutines Contexts as Hierarchic Service Locators and Dependency Injection Containers.

## Install
```shell
composer require leocavalcante/coroutine-context-api
```

## Usage

### Provide
#### `\Swoole\Coroutine\Context\provide(string $key, mixed $value): void`
```php
use function Swoole\Coroutine\{run, Context\provide};

run(static function(): void {
    provide('message', 'Hello, World!');
});
```

### Consume
#### `\Swoole\Coroutine\Context\consume(string $key, [mixed $default]): mixed`
```php
use function Swoole\Coroutine\{run, Context\provide, Context\consume};

run(static function(): void {
    provide('message', 'Hello, World!');
    go(static fn() => print(consume('message') . PHP_EOL));
});
```

> Why is this different from passing parameters to function arguments?
