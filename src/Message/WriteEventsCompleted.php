<?php

namespace Mhwk\Ouro\Message;

final class WriteEventsCompleted
{
    /**
     * @var OperationResult
     */
    private $result;

    /**
     * @var string
     */
    private $message;

    /**
     * @param OperationResult $result
     * @param string $message
     */
    public function __construct(OperationResult $result, string $message)
    {
        $this->result = $result;
        $this->message = $message;
    }

    /**
     * @return OperationResult
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
