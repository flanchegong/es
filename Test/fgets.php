<?php
namespace Flanche\Test;
use Co;

$fp = fopen(__DIR__ . "/test.txt", "r");
go(function () use ($fp)
{
    $r =  co::fgets($fp);
    var_dump($r);
});