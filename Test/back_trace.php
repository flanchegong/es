<?php

function test1() {
    test2();
}

function test2() {
    while(true) {
        co::sleep(10);
        echo __FUNCTION__." \n";
    }
}

$cid = go(function () {
    test1();
});

go(function () use ($cid) {
    while(true) {
        echo "BackTrace[$cid]:\n-----------------------------------------------\n";
        //返回数组，需要自行格式化输出
        var_dump(co::getBackTrace($cid))."\n";
        co::sleep(3);
    }
});
