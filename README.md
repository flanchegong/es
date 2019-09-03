# es
swoole
```php
#!/usr/bin/php
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
        ```
