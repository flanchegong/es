<?php


$client = new swoole_client(SWOOLE_TCP | SWOOLE_ASYNC);
$client->on("connect", function(swoole_client $cli) {

});
$client->on("receive", function(swoole_client $cli, $data){
    $cli->send(str_repeat('A', 1024*1024*4)."\n");
});
$client->on("error", function(swoole_client $cli){
    echo "error\n";
});
$client->on("close", function(swoole_client $cli){
    echo "Connection close\n";
});
$client->on("bufferEmpty", function(swoole_client $cli){
    $cli->close();
});
$client->connect('127.0.0.1', 9501);