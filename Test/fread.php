<?php
namespace Flanche\Test;

use Swoole\Coroutine as co;
$fp = fopen(__DIR__ . "/client.php", "r");
co::create(function () use ($fp)
{
    fseek($fp, 256);
    $r =  co::fread($fp);
    var_dump($r);
});