<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 2/9/2019
 * Time: 16:46
 */
namespace App\HttpController;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;
class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
//        $this->setGlobalMode(true);
//        $this->setGlobalMode(false);
//        $this->setMethodNotAllowCallBack(function (Request $request,Response $response){
//            $response->write('未找到处理方法');
//        });
//        $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
//            $response->write('未找到路由匹配');
//        });
        // TODO: Implement initialize() method.
       // echo 'abc';
        //$routeCollector->get('/test','/Index/index');
    }
}