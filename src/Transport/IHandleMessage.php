<?php

namespace Spray\Ouro\Transport;

interface IHandleMessage
{
    /**
     * @param object $message
     *
     * @return mixed
     */
    function handle($message);
}
