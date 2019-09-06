<?php
/**
 * Created by 纵腾网络.
 * User: flanche
 * Date: 2019/9/6
 * Time: 9:43
 */
namespace App\TcpController;
use App\Rpc\RpcServer;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use http\Env\Response;
class Index extends Controller{
    function actionNotFound(?string $actionName)
    {
        $this->response()->setMessage("{$actionName} not found \n");
    }
    public function index(){
        $this->response()->setMessage(time());
    }
    public function args()
    {
        $this->response()->setMessage('your args is:'.json_encode($this->caller()->getArgs()).PHP_EOL);
    }
    public function delay()
    {
        $client = $this->caller()->getClient();
        TaskManager::async(function ()use($client){
            sleep(1);
            ServerManager::getInstance()->getSwooleServer()->send($client->getFd(),'this is delay message at '.time());
        });
    }
    public function close()
    {
        $this->response()->setMessage('you are goging to close');
        $client = $this->caller()->getClient();
        TaskManager::async(function ()use($client){
            sleep(2);
            ServerManager::getInstance()->getSwooleServer()->send($client->getFd(),'this is delay message at '.time());
        });
    }
    public function who()
    {
        $this->response()->setMessage('you fd is '.$this->caller()->getClient()->getFd());
    }
}