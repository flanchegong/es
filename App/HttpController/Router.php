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
        $routeCollector->get('/test','/Index/test');
        $routeCollector->get('/index','/Index/index');
        $routeCollector->post('/login','/Admin/Auth/login');
    }
}