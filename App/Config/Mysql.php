<?php
/**
 * Mysql 配置文件
 */

return [
    'enjoythin' => [
        'host' => '192.168.109.151',
        'port' => 63307,
        'user' => 'goodcang_test',
        'timeout' =>5,
        'charset' =>'UTF8',
        'password' =>'0z77dXyvE89wb5Is',
        'database' =>'goodcang_wms_sbx_172',
        'pool' => [
            'maxnum' => 8, // 最大连接数
            'minnum' => 2, // 最小连接数
            'timeout' => 0.5, // 获取对象超时时间，单位秒
            'idletime' => 30, // 连接池对象存活时间，单位秒
            'checktime' => 60000, // 多久执行一次回收检测，单位毫秒
        ],
    ]
];
