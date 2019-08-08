<?php
namespace Flanche\Test;
use Swoole\Coroutine as co;
$id = go(function(){
    $id = co::getUid();
    echo "start coro $id\n";
    co::suspend();
    echo "resume coro $id @1\n";
    co::suspend();
    echo "resume coro $id @2\n";
});
echo "start to resume $id @1\n";
co::resume($id);
echo "start to resume $id @2\n";
co::resume($id);
echo "main\n";


/**
start coro 1
start to resume 1 @1
resume coro 1 @1
start to resume 1 @2
resume coro 1 @2
main
 */