<?php


use Mhwk\Ouro\Async\Async;
use Mhwk\Ouro\Async\Threading\Deferred;
use Mhwk\Ouro\Async\Threading\Future;
use Mhwk\Ouro\Async\Threading\ThreadPool;
use Mhwk\Ouro\Async\Threading\Utility;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
require 'vendor/autoload.php';

$deferred = Async::deferred(function() {
    sleep(1);
    echo "Resolving...\n";
});
$deferred->promise()->then(
    function() {
        echo "Promise success\n";
    },
    function() {
        echo "Promise failure\n";
    }
);

ThreadPool::run();
