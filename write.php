<?php

use Spray\Ouro\Client\Connection;
use Spray\Ouro\Transport\Message\NewEvent;
use Icicle\Awaitable;
use Icicle\Coroutine;
use Icicle\Loop;
use Ramsey\Uuid\Uuid;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
require 'vendor/autoload.php';

$coroutine = Coroutine\create(function() {
    $connection = Connection::connect('eventstore:2113', 'admin', 'changeit');
    for ($i = 0; $i < 100; $i++) {
        yield $connection->writeEventsAsync('bar', -2, [
            new NewEvent(
                Uuid::uuid4(),
                'Bar',
                ['foo' => 'bar'],
                ['foo' => 'bar']
            ),
            new NewEvent(
                Uuid::uuid4(),
                'Bar',
                ['foo' => 'bar'],
                ['foo' => 'bar']
            )
        ]);
    }
});
$coroutine->capture(function(Throwable $e) {
    echo sprintf(
        "%s on line %s in file %s\n%s\n",
        $e->getMessage(),
        $e->getLine(),
        $e->getFile(),
        $e->getTraceAsString()
    );
});


Loop\run();
