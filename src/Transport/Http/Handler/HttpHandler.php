<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mhwk\Ouro\Transport\IHandleMessage;

abstract class HttpHandler implements IHandleMessage
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param object $message
     *
     * @return Response
     */
    public function handle($message)
    {
        $this->assert($message);
        return $this->request($message);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function send(Request $request): Response
    {
        return $this->client->send($request);
    }

    /**
     * @param Response $response
     */
    protected function assertResponse(Response $response)
    {
        Assertion::inArray(
            $response->getStatusCode(),
            [200, 201, 202],
            sprintf('Failed request [%s]: %s', $response->getStatusCode(), $response->getReasonPhrase())
        );
    }

    /**
     * Assert that the command can be handled.
     *
     * @param object $command
     *
     * @return void
     */
    abstract function assert($command);

    /**
     * Handle the command.
     *
     * @param object $command
     *
     * @return object
     */
    abstract function request($command);
}
