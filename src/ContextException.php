<?php declare(strict_types=1);

namespace Swoole\Coroutine\Context;

final class ContextException extends \Exception
{
    public static function notInCoroutine(): self
    {
        return new self('You must be inside Coroutines to use Context API');
    }

    public static function notFound(string $key): self
    {
        return new self(sprintf('A value for key (%s) was not found on the Coroutine\'s parents contexts.', $key));
    }

    public static function parentNotFound(): self
    {
        return new self('Parent Coroutine not found. Are you calling from the root Coroutine?');
    }
}
