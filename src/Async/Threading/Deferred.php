<?php

namespace Mhwk\Ouro\Async\Threading;

use Mhwk\Ouro\Async\IDeferred;
use Mhwk\Ouro\Async\IPromise;
use Thread;

final class Deferred extends Thread implements IDeferred, ICollectable
{
    /**
     * @var Promise
     */
    private $promise;

    /**
     * @var callable
     */
    private $callable;

    /**
     * @var array
     */
    private $args;

    /**
     * @var bool
     */
    private $garbage = false;

    /**
     * @param callable $callable
     * @param array $args
     */
    private function __construct(callable $callable, array $args = [])
    {
        $this->promise = new Promise();
        $this->callable = $callable;
        $this->args = $args;
    }

    static function for(callable $callable, array $args = [])
    {
        $deferred = new self($callable, $args);
        ThreadPool::queueThread($deferred);
        return $deferred;
    }

    function promise(): IPromise
    {
        return $this->promise;
    }

    function run()
    {
        $this->synchronized(function () {
            $this->promise()->resolve(
                Future::for($this->callable, (array) $this->args)->result()
            );
            $this->garbage = true;
        });
    }

    /**
     * @return bool
     */
    function isGarbage(): bool
    {
        return $this->garbage;
    }
}
