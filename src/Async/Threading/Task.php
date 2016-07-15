<?php

namespace Mhwk\Ouro\Async\Threading;

use Closure;
use Mhwk\Ouro\Async\ITask;

final class Task extends Future implements ITask
{
    function isCanceled()
    {

    }

    function isCompleted()
    {

    }

    function isFaulted()
    {

    }

    function continueWith(Closure $closure): ITask
    {
        return Task::for($closure, [$this->result()]);
    }
}
