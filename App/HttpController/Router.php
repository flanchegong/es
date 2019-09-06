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
    public function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/task','/Index/task');
        $routeCollector->get('/template/task','/Index/templateTask');
        $routeCollector->get('/quick/task','/Index/quickTask');
        $routeCollector->get('/multi/task/concurrency','/Index/multiTaskConcurrency');
        $routeCollector->get('/push','/Index/push');
        $routeCollector->get('/atomic','/Index/atomic');
        $routeCollector->get('/go','/Index/go');
        $routeCollector->get('/csp','/Index/csp');
        $routeCollector->get('/meta','/Metadata/index');
        $routeCollector->get('/info','/Message/index');
        $routeCollector->post('/login','/Admin/Auth/login');
        $routeCollector->get('/','WebSocket/index');
    }
}