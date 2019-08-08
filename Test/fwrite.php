<?php
namespace Flanche\Test;

use Swoole\Coroutine as co;
$fp = fopen(__DIR__ . "/test.txt", "a+");
co::create(function () use ($fp)
{
    $r =  co::fwrite($fp, "hello world\n", 5);
    var_dump($r);
});