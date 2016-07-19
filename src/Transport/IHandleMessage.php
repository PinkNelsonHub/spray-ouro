<?php

namespace Mhwk\Ouro\Transport;

interface IHandleMessage
{
    /**
     * @param object $message
     *
     * @return mixed
     */
    function handle($message);
}
