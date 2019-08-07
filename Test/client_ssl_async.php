<?php

$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
$client->on("connect", function(swoole_client $cli) {
    $cli->send("hello world\n");
});
$client->on("receive", function(swoole_client $cli, $data) {
    echo "Receive: $data";
    $cli->send(str_repeat('A', 10)."\n");
    //启用SSL加密
    $cli->enableSSL(function($client) {
        //握手完成，此时发送和接收的数据是加密的
        $client->send("hello");
    });
});
$client->on("error", function(swoole_client $cli){
    echo "error\n";
});
$client->on("close", function(swoole_client $cli){
    echo "Connection close\n";
});
$client->connect('127.0.0.1', 9501);