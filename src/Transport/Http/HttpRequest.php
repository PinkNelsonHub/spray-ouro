<?php

namespace Spray\Ouro\Transport\Http;

final class HttpRequest
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $query;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $body;

    /**
     * @param string $method
     * @param string $path
     * @param array $query
     * @param array $headers
     * @param string $body
     */
    private function __construct(
        string $method,
        string $path,
        array $query,
        array $headers,
        string $body)
    {
        $this->method = $method;
        $this->path = $path;
        $this->query = $query;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @param string $path
     *
     * @return HttpRequest
     */
    static function get(string $path)
    {
        return new HttpRequest('GET', $path, [], [], '');
    }

    /**
     * @param string $path
     *
     * @return HttpRequest
     */
    static function post(string $path)
    {
        return new HttpRequest('POST', $path, [], [], '');
    }

    /**
     * @param string $path
     *
     * @return HttpRequest
     */
    static function put(string $path)
    {
        return new HttpRequest('PUT', $path, [], [], '');
    }

    /**
     * @param string $path
     *
     * @return HttpRequest
     */
    static function delete(string $path)
    {
        return new HttpRequest('DELETE', $path, [], [], '');
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return HttpRequest
     */
    function withQuery($key, $value)
    {
        return new HttpRequest(
            $this->method,
            $this->path,
            array_replace($this->query, [$key => $value]),
            $this->headers,
            $this->body
        );
    }

    /**
     * @param array $body
     *
     * @return HttpRequest
     */
    function withJsonBody(array $body)
    {
        return new HttpRequest(
            $this->method,
            $this->path,
            $this->query,
            $this->headers,
            json_encode($body)
        );
    }

    /**
     * @param string $contentType
     *
     * @return HttpRequest
     */
    function withContentType(string $contentType)
    {
        return new HttpRequest(
            $this->method,
            $this->path,
            $this->query,
            array_replace($this->headers, ['Content-type' => $contentType]),
            $this->body
        );
    }

    /**
     * @param string $accept
     *
     * @return HttpRequest
     */
    function withAccept(string $accept)
    {
        return new HttpRequest(
            $this->method,
            $this->path,
            $this->query,
            array_replace($this->headers, ['Accept' => $accept]),
            $this->body
        );
    }

    /**
     * @param int $pollTimeoutInSeconds
     *
     * @return HttpRequest
     */
    function withLongPoll(int $pollTimeoutInSeconds)
    {
        return new HttpRequest(
            $this->method,
            $this->path,
            $this->query,
            array_replace($this->headers, ['ES-LongPoll' => $pollTimeoutInSeconds]),
            $this->body
        );
    }

    /**
     * @return string
     */
    function method()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    function path(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    function query(): string
    {
        return http_build_query($this->query);
    }

    /**
     * @return string
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * @return array
     */
    function headers(): array
    {
        return $this->headers;
    }
}
