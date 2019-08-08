<?php
$serv = new Swoole\Http\Server("127.0.0.1", 9504);

$serv->on('Request', function($request, $response) {
    //等待200ms后向浏览器发送响应
    Swoole\Coroutine::sleep(0.2);
    $response->end("<h1>Hello Swoole!</h1>");
});

$serv->start();