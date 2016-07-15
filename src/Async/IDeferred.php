<?php

namespace Mhwk\Ouro\Async;

interface IDeferred
{
    function promise(): IPromise;
}
