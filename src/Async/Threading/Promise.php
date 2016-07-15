<?php

namespace Mhwk\Ouro\Async\Threading;

use Mhwk\Ouro\Async\IPromise;
use Threaded;

final class Promise extends Threaded implements IPromise
{
    private $resolvers = [];
    private $rejectors = [];

    function then(callable $resolve = null, callable $reject = null)
    {
        if (null !== $resolve) {
            $this->resolvers[] = $resolve;
        }
        if (null !== $reject) {
            $this->rejectors[] = $reject;
        }
    }

    function resolve($value)
    {
        foreach ($this->resolvers as $resolver) {
            $value = $resolver($value);
        }
    }

    function reject($error)
    {
        foreach ($this->rejectors as $rejector) {
            $error = $rejector($error);
        }
    }
}
