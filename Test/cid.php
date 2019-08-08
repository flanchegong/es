<?php
co::set([
    'max_coroutine' => PHP_INT_MAX,
    'log_level' => SWOOLE_LOG_INFO,
    'trace_flags' => 0
]);
$map = [];
while (true) {
    if (empty($map)){
        $cid = go(function () {co::sleep(5);});
    }else{
        $cid = go(function () { });
    }
    if (!isset($map[$cid])) {
        $map[$cid] = $cid;
    } else {
        var_dump(end($map));
        var_dump($cid);
        exit;
    }
}