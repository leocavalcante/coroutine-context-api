<?php declare(strict_types=1);

namespace Swoole\Coroutine\Context;

use Swoole\Coroutine;

/**
 * Provides a value based on a key for the child Coroutines.
 *
 * @template T
 * @param string $key
 * @param T $value
 * @return T
 * @throws ContextException
 */
function provide(string $key, $value)
{
    if (Coroutine::getCid() === -1) {
        throw ContextException::notInCoroutine();
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
 * @throws ContextException
 */
function consume(string $key, $default = null, int $cid = 0)
{
    if (Coroutine::getCid() === -1) {
        throw ContextException::notInCoroutine();
    }

    /** @var int $cid */
    $cid = $cid === 0 ? Coroutine::getCid() : $cid;
    /** @var int|false $parent_cid */
    $parent_cid = Coroutine::getPcid($cid);

    if ($parent_cid === false) {
        throw ContextException::parentNotFound();
    }

    /** @var array $parent_context */
    $parent_context = Coroutine::getContext($parent_cid);
    /** @var T|null $value */
    $value = $parent_context[$key] ?? null;

    if ($value !== null) {
        return $value;
    }

    if ($parent_cid > 1) {
        return consume($key, $default, $parent_cid);
    }

    if ($default !== null) {
        return $default;
    }

    throw ContextException::notFound($key);
}
