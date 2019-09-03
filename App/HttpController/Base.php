<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
class Base extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
    }

    function __construct()
    {
        $this->header();
    }

    function header()
    {
        $this->response()->withHeader('Content-type', 'text/html;charset=utf-8');
    }

}