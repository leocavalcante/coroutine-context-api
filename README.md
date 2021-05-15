# Coroutine Context API

Using Coroutines Contexts as Hierarchic Service Locators and Dependency Injection Containers.

> Inspired by https://reactjs.org/docs/context.html

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

## Why is this different from passing parameters to function arguments?

The `consume` function can lookup through the nearest provided key in the Coroutine tree.

```php
use function Swoole\Coroutine\{run, Context\provide, Context\consume};

run(static function(): void {
    provide('message', 'Hello, World!');
    go(static fn() =>
        go(static fn() =>
            go(static fn() =>
                print(consume('message') . PHP_EOL)
            )
        )
    );
});
```

## Why is this different from globals?

It is not about global space being polluted, it is based on parent-child "Coroutine tree".

```php
run(static function(): void {
    provide('message', 'Hello, World!');
    go(static fn() => print(consume('message') . PHP_EOL));
});

run(static function(): void {
    provide('message', 'Olá, Mundo!');
    go(static fn() => print(consume('message') . PHP_EOL));
});

run(static function(): void {
    go(static fn() => print(consume('message', $default = '你好, 世界!') . PHP_EOL));
});
```
