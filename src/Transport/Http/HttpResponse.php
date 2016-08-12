<?php

namespace Spray\Ouro\Transport\Http;

final class HttpResponse
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $body;

    /**
     * @param int $statusCode
     * @param string $body
     */
    function __construct(int $statusCode, string $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    /**
     * @return int
     */
    function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    function getBody(): string
    {
        return $this->body;
    }
}
