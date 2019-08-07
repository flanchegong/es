<?php


$client = new swoole_client(SWOOLE_TCP | SWOOLE_ASYNC); //异步非阻塞

$client->on("connect", function($cli) {
    $cli->send("hello world\n");
});

$client->on("receive", function($cli, $data) {
    echo "received: $data\n";
    sleep(1);
    $cli->send("hello\n");
});

$client->on("close", function($cli){
    echo "closed\n";
});

$client->on("error", function($cli){
    exit("error\n");
});

$client->connect('127.0.0.1', 9501, 0.5);