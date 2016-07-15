<?php

namespace Mhwk\Ouro\Async;

use Closure;

interface ITask extends IFuture
{
    function isCanceled();

    function isCompleted();

    function isFaulted();

    function continueWith(Closure $closure): ITask;
}
