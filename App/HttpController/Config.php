<?php

namespace App\HttpController;

class Config extends Base
{
    function index()
    {
        $instance = \EasySwoole\EasySwoole\Config::getInstance();

        // 获取配置 按层级用点号分隔
        $instance->getConf('MAIN_SERVER.SETTING.task_worker_num');

        // 设置配置 按层级用点号分隔
        $instance->setConf('DATABASE.host', 'localhost');

        // 获取全部配置
        $conf = $instance->getConf();

        // 用一个数组覆盖当前配置项
        $conf['DATABASE'] = [
            'host' => '127.0.0.1',
            'port' => 13306
        ];
        $instance->load($conf);
    }

}
