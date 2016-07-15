<?php

namespace Mhwk\Ouro\Async;

final class Async
{
    /**
     * @param callable $callable
     * @param array $args
     *
     * @return IDeferred
     */
    static function deferred(callable $callable, array $args = []): IDeferred
    {
        return Threading\Deferred::for($callable, $args);
    }

    /**
     * @param callable $callable
     * @param array $args
     *
     * @return IFuture
     */
    static function future(callable $callable, array $args = []): IFuture
    {
        return Threading\Future::for($callable, $args);
    }

    /**
     * @param callable $callable
     * @param array $args
     *
     * @return ITask
     */
    static function task(callable $callable, array $args = []): ITask
    {
        return Threading\Task::for($callable, $args);
    }
}
