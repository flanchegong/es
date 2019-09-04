<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-01
 * Time: 20:06
 */

return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9501,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 8,
            'reload_async' => true,
            'max_wait_time'=>3
        ],
        'TASK'=>[
            'workerNum'=>4,
            'maxRunningNum'=>128,
            'timeout'=>15
        ]
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null,
    'CONSOLE'       => [
        'ENABLE'         => true,
        'LISTEN_ADDRESS' => '127.0.0.1',
        'HOST'           => '127.0.0.1',
        'PORT'           => 9500,
        'EXPIRE'         => '120',
        'PUSH_LOG'       => true,
        'AUTH'           => [
            [
                'USER'     => 'root',
                'PASSWORD' => '123456',
                'MODULES'  => [
                    'auth', 'server', 'help'
                ],
                'PUSH_LOG' => true,
            ]
        ]
    ],
    'FAST_CACHE'    => [
        'PROCESS_NUM' => 0,
        'BACKLOG'     => 256,
    ],
    'DISPLAY_ERROR' => true,
    /*################ REDIS CONFIG ##################*/

    'MYSQL' => [
        'host'          => '127.0.0.1',
        'port'          => '3306',
        'user'          => 'root',
        'timeout'       => '5',
        'charset'       => 'utf8mb4',
        'password'      => '111111',
        'database'      => 'test',
        'POOL_MAX_NUM'  => '20',
        'POOL_TIME_OUT' => '0.1',
    ],
    /*################ REDIS CONFIG ##################*/
    'REDIS' => [
        'host'          => '127.0.0.1',
        'port'          => '6379',
        'auth'          => '',
        'POOL_MAX_NUM'  => '20',
        'POOL_MIN_NUM'  => '5',
        'POOL_TIME_OUT' => '0.1',
    ],
    'RUN_MODE' => 'develop', //运行模式
];
