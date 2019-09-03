<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 2/9/2019
 * Time: 10:21
 */
namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{
    function index()
    {
        echo '123';
        // TODO: Implement index() method.
    }

    function test()
    {

//        $instance = \EasySwoole\EasySwoole\Config::getInstance();
//
//// 获取配置 按层级用点号分隔
//        $mysql=$instance->getConf('MYSQL');
//        var_dump($mysql);
//
//        // TODO: Implement index() method.
        echo 'abc';
//        $this->response()->write('hello world');
    }
}