<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Swoole\Coroutine;
use function Swoole\Coroutine\Context\{provide, consume};

$print_message = static fn() => print(consume('message') . PHP_EOL);

Coroutine\run(static function() use ($print_message): void {
    provide('message', 'Hello,');

    go(static function () use ($print_message): void {
        $print_message();
        go($print_message);
        provide('message', 'World!');
        go($print_message);
    });
});
