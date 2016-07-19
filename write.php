<?php

use Mhwk\Ouro\Message\NewEvent;
use Mhwk\Ouro\Message\WriteEvents;
use Mhwk\Ouro\Transport\Http\HttpTransport;
use Icicle\Loop;
use Icicle\Coroutine;
use Ramsey\Uuid\Uuid;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
require 'vendor/autoload.php';

$transport = HttpTransport::factory('eventstore:2113', 'admin', 'changeit');

$coroutine = Coroutine\create(function() use ($transport) {
    for ($i = 0; $i < 100; $i++) {
        yield from $transport->handle(new WriteEvents(
            'foo',
            -2,
            [
                new NewEvent(
                    Uuid::uuid4(),
                    'Foo',
                    ['foo'=>'bar'],
                    []
                )
            ],
            false
        ));
    }
});
$coroutine->then(
    function() {},
    function(Exception $e) {
        echo $e->getMessage() . "\n";
    }
);

Loop\run();
