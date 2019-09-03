<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
class Base extends Controller
{


    function __construct(Request $request, Response $response)
    {
        $this->header();
    }

    function index()
    {
        // TODO: Implement index() method.
    }

    function header()
    {
        $this->response()->withHeader('Content-type', 'text/html;charset=utf-8');
    }

}