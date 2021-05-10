<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Swoole\Coroutine;
use function Swoole\Coroutine\Context\{provides, uses};

Coroutine\run(static function(): void {
    provides('message', 'Hello,');

    Coroutine::create(static function(): void {
        print(uses('message') . PHP_EOL);

        Coroutine::create(static fn() => print(uses('message') . PHP_EOL));

        provides('message', 'World!');

        Coroutine::create(static fn() => print(uses('message') . PHP_EOL));
    });
});

echo Coroutine::getCid();
