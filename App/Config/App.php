<?php
/**
 * App 配置文件
 */

return [
    'name' => 'easyswoole3',
    'version' => '4.1.0',
    'token' => [
        'key' => '',
        'timeout' => 60
    ],
    'slow_log' => [
        'enable' => true,
        'second' => 3
    ],
    'dingtalk' => [
        'enable' => true,
        'uri' => 'https://oapi.dingtalk.com/robot/send?access_token='
    ],
    'throw_check_rate' => 20, //单位秒，检测异常并推送消息定时任务的检测频率
];