<?php
/**
 * @noinspection StaticClosureCanBeUsedInspection
 * @noinspection PhpUnhandledExceptionInspection
 */
declare(strict_types=1);

namespace Swoole\Coroutine\Context\Test;

use RuntimeException;
use Swoole\Coroutine;
use Swoole\Error;
use function Swoole\Coroutine\Context\{provide, consume};

it('only works inside coroutines (provides)', function (): void {
    provide('foo', 'bar');
})->throws(Error::class, 'API must be called in the coroutine');

it('only works inside coroutines (uses)', function (): void {
    consume('foo');
})->throws(Error::class, 'API must be called in the coroutine');

it('grabs values from the parent context', function (): void {
    Coroutine\run(static function(): void {
       provide('test', 'value');

       Coroutine::create(static function(): void {
           expect(consume('test'))->toBe('value');
       });
    });
});

it('deeply searches on the parent coroutines for the value', function (): void {
    Coroutine\run(static function(): void {
        provide('test', 'value');

        Coroutine::create(static function(): void {
            Coroutine::create(static function(): void {
                Coroutine::create(static function(): void {
                    expect(consume('test'))->toBe('value');
                });
            });
        });
    });
});

it('uses default values', function (): void {
    Coroutine\run(static function(): void {
        Coroutine::create(static function(): void {
            expect(consume('test', 'default'))->toBe('default');
        });
    });
});

it('throws when a value is not found and no default was provided', function (): void {
    Coroutine\run(static function(): void {
        Coroutine::create(static function(): void {
            try {
                consume('test');
            } catch (RuntimeException $err) {
                expect($err->getMessage())->toBe('A value for (test) was not found in the coroutine context tree');
            }
        });
    });
});

it('consumes from the current coroutine as well', function (): void {
   Coroutine\run(static function(): void {
      provide('test', 'test');
      expect(consume('test'))->toBe('test');
   });
});
