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
        // TODO: Implement index() method.
        $this->response()->write('hello world');
    }
}