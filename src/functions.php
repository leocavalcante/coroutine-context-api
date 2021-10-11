<?php declare(strict_types=1);

namespace Swoole\Coroutine\Context;

use RuntimeException;
use Swoole\Coroutine;
use Swoole\Error;

/**
 * Used to know if the current context is not inside a coroutine
 * or if there is no parent Coroutine
 */
const INVALID_CID = -1;

/**
 * Provides a value based on a key for the child Coroutines.
 *
 * @template T
 * @param string $key
 * @param T $value
 * @return T
 */
function provide(string $key, $value)
{
    if (Coroutine::getCid() === INVALID_CID) {
        throw new Error('API must be called in the coroutine');
    }

    /** @var Coroutine\Context $context */
    $context = Coroutine::getContext();
    return $context[$key] = $value;
}

/**
 * Uses a value based on a key provided by the parent Coroutines.
 *
 * @template T
 * @param string $key
 * @param T|null $default
 * @param int $cid
 * @return T
 */
function consume(string $key, $default = null, int $cid = 0)
{
    /** @var int $cid */
    $cid = $cid === 0 ? Coroutine::getCid() : $cid;
    if ($cid === INVALID_CID) {
        throw new Error('API must be called in the coroutine');
    }

    /** @var Coroutine\Context|null $context */
    $context = Coroutine::getContext($cid);
    if ($context !== null && $context->offsetExists($key)) {
        /** @var T */
        return $context[$key];
    }

    /** @var int|false $parent_cid */
    $parent_cid = Coroutine::getPcid($cid);
    if ($parent_cid === false || $parent_cid === INVALID_CID) {
        if ($default !== null) {
            return $default;
        }
        
        throw new RuntimeException(sprintf('A value for (%s) was not found in the coroutine context tree', $key));
    }

    return consume($key, $default, $parent_cid);
}
