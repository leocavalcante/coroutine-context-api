<?php declare(strict_types=1);

namespace Swoole\Coroutine\Context\Test;

use Swoole\Coroutine;
use Swoole\Coroutine\Context\ContextException;
use function Swoole\Coroutine\Context\{provides, uses};

it('only works inside coroutines (provides)', function (): void {
    provides('foo', 'bar');
})->throws(ContextException::class, 'You must be inside Coroutines to use Context API');

it('only works inside coroutines (uses)', function (): void {
    uses('foo');
})->throws(ContextException::class, 'You must be inside Coroutines to use Context API');

it('grabs values from the parent context', function (): void {
    Coroutine\run(static function(): void {
       provides('test', 'value');

       Coroutine::create(static function(): void {
           expect(uses('test'))->toBe('value');
       });
    });
});

it('deeply searches on the parent coroutines for the value', function (): void {
    Coroutine\run(static function(): void {
        provides('test', 'value');

        Coroutine::create(static function(): void {
            Coroutine::create(static function(): void {
                Coroutine::create(static function(): void {
                    expect(uses('test'))->toBe('value');
                });
            });
        });
    });
});

it('uses default values', function (): void {
    Coroutine\run(static function(): void {
        Coroutine::create(static function(): void {
            expect(uses('test', 'default'))->toBe('default');
        });
    });
});

it('throws when a value is not found and no default was provided', function (): void {
    Coroutine\run(static function(): void {
        Coroutine::create(static function(): void {
            try {
                uses('test');
            } catch (ContextException $err) {
                expect($err->getMessage())->toBe('A value for key (test) was not found on the Coroutine\'s parents contexts.');
            }
        });
    });
});
