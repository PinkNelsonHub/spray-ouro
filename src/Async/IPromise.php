<?php

namespace Mhwk\Ouro\Async;

interface IPromise
{
    function then(callable $resolve = null, callable $reject = null);
}
