<?php
use Swoole\Coroutine as co;


go(function(){
    $ip = co::gethostbyname("www.baidu.com", AF_INET, 0.5);
    echo $ip."\n";
    $ip = swoole_async_dns_lookup_coro("www.baidu.com");
    echo $ip."\n";
    $array = co::getaddrinfo("www.baidu.com");
    var_dump($array);
});