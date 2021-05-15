<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use function Swoole\Coroutine\{run, Context\provide, Context\consume};

run(static function(): void {
    provide('message', 'Hello, World!');
    go(static fn() => print(consume('message') . PHP_EOL));
});
