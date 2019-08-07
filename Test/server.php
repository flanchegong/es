<?php

$http_server = new swoole_http_server('0.0.0.0',9503);
$http_server->set(array('daemonize'=> false));

//......设置各个回调......
//多监听一个tcp端口，对外开启tcp服务，并设置tcp服务器的回调
$tcp_server = $http_server->addListener('0.0.0.0', 9503, SWOOLE_SOCK_TCP);
//默认新监听的端口 9503 会继承主服务器的设置，也是 Http 协议
//需要调用 set 方法覆盖主服务器的设置
$tcp_server->set(array());
$tcp_server->on("receive", function ($serv, $fd, $threadId, $data) {
    echo $data;
});