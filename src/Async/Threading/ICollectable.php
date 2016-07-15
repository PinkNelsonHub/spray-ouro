<?php

namespace Mhwk\Ouro\Async\Threading;

interface ICollectable
{
    /**
     * @return bool
     */
    function isGarbage(): bool;
}
