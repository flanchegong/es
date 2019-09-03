<?php
/**
 * Created by PhpStorm.
 * User: flanche
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use App\Process\HotReload;
use App\Process\ProcessOne;
use App\ExceptionHandler;
use EasySwoole\Component\Di;
use App\Log\MyLogHandle;
use EasySwoole\Http\Message\Status;
use App\Utility\Pub;
use Error;
use EasySwoole\Trace\TrackerManager;
use App\Process\Test;
use EasySwoole\Component\Process\Config as p_config;
use EasySwoole\EasySwoole\Crontab\Crontab;
use App\Device\Command;
use App\Device\DeviceActor;
use App\Device\DeviceManager;
class EasySwooleEvent implements Event
{

    private static function setErrorReporting()
    {
        if (Pub::isDev()) {
            ini_set('display_errors', 'On');
            error_reporting(-1);
        } else {
            ini_set('display_errors', 'Off');
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        }
    }

    /**
     * 框架初始化
     */
    public static function initialize()
    {
//        //获得原先的config配置项,加载到新的配置项中
//        $config = Config::getInstance()->getConf();
//        Config::getInstance()->storageHandler(new SplArrayConfig())->load($config);

        $configData = Config::getInstance()->getConf('MYSQL');
        $config = new \EasySwoole\Mysqli\Config($configData);
        $poolConf = \EasySwoole\MysqliPool\Mysql::getInstance()->register('mysql', $config);
        $poolConf->setMaxObjectNum(20);

        // 设置错误显示级别
        self::setErrorReporting();
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        Di::getInstance()->set(SysConst::LOGGER_HANDLER,new MyLogHandle());
        Di::getInstance()->set(SysConst::ERROR_HANDLER,function (){
            $err=new Error();
           var_dump($err->getMessage());
        });//配置错误处理回调
        Di::getInstance()->set(SysConst::SHUTDOWN_FUNCTION, function () {//注册自定义代码终止回调
            $error = error_get_last();
            if (!empty($error)) {
                var_dump($error);
            }
        });
        Di::getInstance()->set(SysConst::HTTP_CONTROLLER_NAMESPACE,'App\\HttpController\\');//配置控制器命名空间
        Di::getInstance()->set(SysConst::HTTP_CONTROLLER_MAX_DEPTH,5);//配置http控制器最大解析层级
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER,[ExceptionHandler::class,'handle']);
        Di::getInstance()->set(SysConst::HTTP_CONTROLLER_POOL_MAX_NUM,15);//http控制器对象池最大数量
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $processConfig = new p_config();
        $processConfig->setProcessName('Test');
        /*
 * 传递给进程的参数
*/
        $processConfig->setArg([
            'arg1'=>time()
        ]);
        ServerManager::getInstance()->getSwooleServer()->addProcess((new Test($processConfig))->getProcess());

        $swooleServer = ServerManager::getInstance()->getSwooleServer();
        $swooleServer->addProcess((new HotReload('HotReload', ['disableInotify' => false]))->getProcess());
         //TODO: Implement mainServerCreate() method.
        $register->add($register::onWorkerStart,function (\swoole_server $server,int $workerId){
            var_dump($workerId.'start');
        });

        // 给server 注册相关事件 在 WebSocket 模式下  message 事件必须注册 并且交给
        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) {
            var_dump($frame);
        });

        /**
         * 除了进程名，其余参数非必须
         */
        $myProcess = new ProcessOne("processName",time(),false,2,true);
        ServerManager::getInstance()->getSwooleServer()->addProcess($myProcess->getProcess());

        $subPort = ServerManager::getInstance()->getSwooleServer()->addListener('0.0.0.0',9503,SWOOLE_TCP);
        $subPort->on('receive',function (\swoole_server $server, int $fd, int $reactor_id, string $data){
            var_dump($data);
        });

        //注册定时任务
        self::registerCrontabTask();

        //注册Actor
        self::registerDevice($register);
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        //不建议在这拦截请求,可增加一个控制器基类进行拦截
        //如果真要拦截,判断之后return false即可
//        $code = $request->getRequestParam('code');
//        if (empty($code)){
//            $data = Array(
//                "code" => Status::CODE_BAD_REQUEST,
//                "result" => [],
//                "msg" => '验证失败'
//            );
//            $response->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
//            $response->withHeader('Content-type', 'application/json;charset=utf-8');
//            $response->withStatus(Status::CODE_BAD_REQUEST);
//            return false;
//        }
        // TODO: Implement onRequest() method.
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        if ($request->getMethod() === 'OPTIONS') {
            $response->withStatus(Status::CODE_OK);
            return false;
        }
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        TrackerManager::getInstance()->getTracker()->endPoint('request');

        $responseMsg = $response->getBody()->__toString();
        Logger::getInstance()->console("响应内容:".$responseMsg);
        //响应状态码:
//        var_dump($response->getStatusCode());

        //tracker结束,结束之后,能看到中途设置的参数,调用栈的运行情况
        TrackerManager::getInstance()->closeTracker();
        // TODO: Implement afterAction() method.
    }

    private static function registerCrontabTask(): void
    {
        $Crontab = Crontab::getInstance();
        $nowRunMode = Pub::getRunMode();
        foreach (Config::getInstance()->getConf('crontab') as $task) {
            if (!isset($task['class']) || empty($task['class']) || !class_exists($task['class'])) {
                continue;
            }
            if (isset($task['runmode']) && $task['runmode'] != $nowRunMode) {
                continue;
            }
            if (isset($task['version']) && !Pub::versionCompare($task['version'])) {
                continue;
            }
            $Crontab->addTask($task['class']);
        }
    }

    private static function registerDevice($register){
        //注册Actor
        Actor::getInstance()->register(DeviceActor::class);
        Actor::getInstance()->setListenPort(9600)
            ->setTrigger(Trigger::getInstance())
            ->setListenAddress('0.0.0.0')
            ->setTempDir(EASYSWOOLE_TEMP_DIR);
        Actor::getInstance()->attachServer(ServerManager::getInstance()->getSwooleServer());
        //创建Table用来记录 fd与actor的映射关系
        DeviceManager::tableInit();
        $register->add($register::onOpen,function (\swoole_websocket_server $svr, \swoole_http_request $req){
            if(!isset($req->get['deviceId'])){
                ServerManager::getInstance()->getSwooleServer()->push($req->fd,'deviceId@length=8 is require');
                ServerManager::getInstance()->getSwooleServer()->close($req->fd);
                return;
            }
            $deviceId = $req->get['deviceId'];
            $info = DeviceManager::deviceInfo($deviceId);
            if($info){
                //说明是断线重连
                $command = new Command();
                $command->setCommand($command::RECONNECT);
                $command->setArg($req->fd);
                DeviceActor::client()->send($info->getActorId(),$command);
            }else{
                //第一次链接服务端
                DeviceActor::client()->create([
                    'deviceId'=>$deviceId,
                    'fd'=>$req->fd
                ]);
            }
        });

        $register->add($register::onMessage,function (\swoole_websocket_server  $server, \swoole_websocket_frame $frame){
            $info = DeviceManager::deviceInfoByFd($frame->fd);
            if($info){
                $com = new Command();
                $com->setCommand($com::WS_MSG);
                $com->setArg($frame->data);
                DeviceActor::client()->send($info->getActorId(),$com);
            }else{
                $server->close($frame->fd);
            }
        });
    }
}