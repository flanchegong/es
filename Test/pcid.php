<?php
echo Co::getPcid(), "\n";
go(function () {
    echo Co::getPcid(), "\n";
    go(function () {
        echo Co::getPcid(), "\n";
        go(function () {
            echo Co::getPcid(), "\n";
            go(function () {
                echo Co::getPcid(), "\n";
            });
            go(function () {
                echo Co::getPcid(), "\n";
            });
            go(function () {
                echo Co::getPcid(), "\n";
            });
        });
        echo Co::getPcid(), "\n";
    });
    echo Co::getPcid(), "\n";
});
echo Co::getPcid(), "\n";