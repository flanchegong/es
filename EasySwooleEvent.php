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
use EasySwoole\Actor\Actor;
use App\Utility\Pool\RedisPool;
use EasySwoole\Component\Pool\PoolManager;
use App\Utility\LogPusher;
use EasySwoole\Console\Console;
use App\Utility\Queue;
use App\Utility\QueueProcess;
use EasySwoole\Component\Timer;
use EasySwoole\Queue\Job;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Socket\Dispatcher;
use App\WebSocket\WebSocketParser;
use App\WebSocket\WebSocketEvent;
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

        PoolManager::getInstance()->register(RedisPool::class, Config::getInstance()->getConf('REDIS.POOL_MAX_NUM'));

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


        //注册tcpserver
        self::registerTcpServer();
        self::registerTcpSer($swooleServer);

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

        //注册队列
        self::registerQueue($register);

        //控制台服务注册
        self::registerConsole();

        //websocket
        self::registerWebsocket($register);
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

    private static function registerTcpServer(){
        ################# tcp 服务器1 没有处理粘包 #####################
        $tcp1ventRegister = $subPort1 = ServerManager::getInstance()->addServer('tcp1', 9502, SWOOLE_TCP, '0.0.0.0', [
            'open_length_check' => false,//不验证数据包
        ]);
        $tcp1ventRegister->set(EventRegister::onConnect,function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务1  fd:{$fd} 已连接\n";
            $str = '恭喜你连接成功服务器1';
            $server->send($fd, $str);
        });
        $tcp1ventRegister->set(EventRegister::onClose,function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务1  fd:{$fd} 已关闭\n";
        });
        $tcp1ventRegister->set(EventRegister::onReceive,function (\swoole_server $server, int $fd, int $reactor_id, string $data) {
            echo "tcp服务1  fd:{$fd} 发送消息:{$data}\n";
        });
    }

    private static function registerTcpSer($server){

        $subPort3 = $server->addListener(Config::getInstance()->getConf('MAIN_SERVER.LISTEN_ADDRESS'), 9504, SWOOLE_TCP);

        $socketConfig = new \EasySwoole\Socket\Config();
        $socketConfig->setType($socketConfig::TCP);
        $socketConfig->setParser(new \App\TcpController\Parser());
        //设置解析异常时的回调,默认将抛出异常到服务器
        $socketConfig->setOnExceptionHandler(function ($server, $throwable, $raw, $client, $response) {
            echo  "tcp服务3  fd:{$client->getFd()} 发送数据异常 \n";
            $server->close($client->getFd());
        });
        $dispatch = new \EasySwoole\Socket\Dispatcher($socketConfig);

        $subPort3->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) use ($dispatch) {
            echo "tcp服务3  fd:{$fd} 发送消息:{$data}\n";
            $dispatch->dispatch($server, $data, $fd, $reactor_id);
        });
        $subPort3->set(
            [
                'open_length_check'     => true,
                'package_max_length'    => 81920,
                'package_length_type'   => 'N',
                'package_length_offset' => 0,
                'package_body_offset'   => 4,
            ]
        );
        $subPort3->on('connect', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务3  fd:{$fd} 已连接\n";
        });
        $subPort3->on('close', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务3  fd:{$fd} 已关闭\n";
        });
    }

    private static function registerConsole(){
        ServerManager::getInstance()->addServer('consoleTcp','9700',SWOOLE_TCP,'0.0.0.0',[
            'open_eof_check'=>false
        ]);
        $consoleTcp = ServerManager::getInstance()->getSwooleServer('consoleTcp');
        /**
        密码为123456
         */
        $console = new Console("MyConsole",'123456');
        /*
         * 注册日志模块
         */
        $console->moduleContainer()->set(new LogPusher());
        $console->protocolSet($consoleTcp)->attachToServer(ServerManager::getInstance()->getSwooleServer());
        /*
         * 给es的日志推送加上hook
         */
        Logger::getInstance()->onLog()->set('remotePush',function ($msg,$logLevel,$category)use($console){
            if(Config::getInstance()->getConf('logPush')){
                /*
                 * 可以在 LogPusher 模型的exec方法中，对loglevel，category进行设置，从而实现对日志等级，和分类的过滤推送
                 */
                foreach ($console->allFd() as $item){
                    $console->send($item['fd'],$msg);
                }
            }
        });
    }

    private static function registerQueue($register){
        Redis::getInstance()->register('queue',new \EasySwoole\RedisPool\Config());
        $driver = new \EasySwoole\Queue\Driver\Redis('queue','queue');
        Queue::getInstance($driver);
        $process = new QueueProcess('QueueProcess',null,false,2,true);
        /*
         * 需要多个进程消费，则new 多个QueueProcess 即可
         */
        ServerManager::getInstance()->addProcess($process);

        $register->add($register::onWorkerStart,function ($ser,$workerId){
            if($workerId == 0){
                Timer::getInstance()->loop(3000,function (){
                    $job = new Job();
                    $job->setJobData(time());
                    Queue::getInstance()->producer()->push($job);
                });
            }
        });

    }

    private static function registerWebsocket($register){
        /**
         * **************** websocket控制器 **********************
         */
        // 创建一个 Dispatcher 配置
        $conf = new \EasySwoole\Socket\Config();
        // 设置 Dispatcher 为 WebSocket 模式
        $conf->setType(\EasySwoole\Socket\Config::WEB_SOCKET);
        // 设置解析器对象
        $conf->setParser(new WebSocketParser());
        // 创建 Dispatcher 对象 并注入 config 对象
        $dispatch = new Dispatcher($conf);

        // 给server 注册相关事件 在 WebSocket 模式下  on message 事件必须注册 并且交给 Dispatcher 对象处理
        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });

        //自定义握手事件
        $websocketEvent = new WebSocketEvent();
        $register->set(EventRegister::onHandShake, function (\swoole_http_request $request, \swoole_http_response $response) use ($websocketEvent) {
            $websocketEvent->onHandShake($request, $response);
        });

        //自定义关闭事件
        $register->set(EventRegister::onClose, function (\swoole_server $server, int $fd, int $reactorId) use ($websocketEvent) {
            $websocketEvent->onClose($server, $fd, $reactorId);
        });
    }
}