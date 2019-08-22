<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/15
 * Time: 上午10:39
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        // TODO: Implement initialize() method.
        $routeCollector->get('/index', '/Index/index');
        $routeCollector->get('/user', '/Test/user');

        $routeCollector->get('/', function (Request $request, Response $response) {
            $response->write('this router index');
        });
//        $routeCollector->get('/test', function (Request $request, Response $response) {
//            $response->write('this router test');
//            return '/a';//重新定位到/a方法
//        });
//        $routeCollector->get('/user/{id:\d+}', function (Request $request, Response $response) {
//            $response->write("this is router user ,your id is {$request->getQueryParam('id')}");//获取到路由匹配的id
//            return false;//不再往下请求,结束此次响应
//        });

    }
}