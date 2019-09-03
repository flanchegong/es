<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use EasySwoole\Http\Message\Status;
use EasySwoole\EasySwoole\Trigger;
class Base extends Controller
{


    function __construct(Request $request, Response $response)
    {
        $this->header();
    }

    function index()
    {
        $this->actionNotFound('index');
        // TODO: Implement index() method.
    }

    function header()
    {
        $this->response()->withHeader('Content-type', 'text/html;charset=utf-8');
    }

    protected function onRequest(?string $action): ?bool
    {
        //模拟拦截
        //当没有传code的时候则拦截
        if (empty($this->request()->getRequestParam('code'))) {
            $this->writeJson(Status::CODE_BAD_REQUEST, ['errorCode' => 1, 'data' => []], 'code不存在');
            return false;
        }
        return true;
    }

    protected function onException(\Throwable $throwable): void
    {
        //拦截错误进日志,使控制器继续运行
        Trigger::getInstance()->throwable($throwable);
        $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR, null, $throwable->getMessage());
    }

}