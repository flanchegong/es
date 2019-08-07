<?php
$atomic = new swoole_atomic(123);
echo $atomic->add(12)."\n";
echo $atomic->sub(11)."\n";
echo $atomic->cmpset(122, 999)."\n";
echo $atomic->cmpset(124, 999)."\n";
echo $atomic->get()."\n";

$n = new swoole_atomic;
if (pcntl_fork() > 0) {
    echo "master start\n";
    $n->wait(1.5);
    echo "master end\n";
} else {
    echo "child start\n";
    sleep(1);
    $n->wakeup();
    echo "child end\n";
}