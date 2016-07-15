<?php

namespace Mhwk\Ouro\Async;

interface IFuture
{
    /**
     * Get the future result. Causes the current thread to block.
     *
     * @return mixed
     */
    function result();
}
