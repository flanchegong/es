<?php

/**
 * Created by PhpStorm.
 * User: flanche
 * Date: 2016/12/7
 * Time: 16:16
 */

class taskServer
{
    private $serv;

    /**
     * [__construct description]
     * 构造方法中,初始化 $serv 服务
     */
    public function __construct()
    {
        $this->serv = new Swoole\Server('0.0.0.0', 9504);
        //初始化swoole服务
        $this->serv->set(array(
            'worker_num' => 8,
            'daemonize' => false, //是否作为守护进程,此配置一般配合log_file使用
            'max_request' => 1000,
            'log_file' => './swoole.log',
            'task_worker_num' => 8
        ));

        //设置监听
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on("Receive", array($this, 'onReceive'));
        $this->serv->on("Close", array($this, 'onClose'));
        $this->serv->on("Task", array($this, 'onTask'));
        $this->serv->on("Finish", array($this, 'onFinish'));

        //开启
        $this->serv->start();
    }

    public function onStart($serv)
    {
        echo SWOOLE_VERSION . " onStart\n";
    }

    public function onConnect($serv, $fd)
    {
        echo $fd . "Client Connect.\n";
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        echo "Get Message From Client {$fd}:{$data}\n";
        // send a task to task worker.
        $param = array(
            'fd' => $fd
        );
        // start a task
        $serv->task(json_encode($param));

        echo "Continue Handle Worker\n";
    }

    public function onClose($serv, $fd)
    {
        echo "Client Close.\n";
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        echo "This Task {$task_id} from Worker {$from_id}\n";
        echo "Data: {$data}\n";
        for ($i = 0; $i < 200; $i++) {
            sleep(1);
            echo "Task {$task_id} Handle {$i} times...\n";
        }
        $fd = json_decode($data, true);
        $serv->send($fd['fd'], "Data in Task {$task_id}");
        return "Task {$task_id}'s result";
    }

    public function onFinish($serv, $task_id, $data)
    {
        echo "Task {$task_id} finish\n";
        echo "Result: {$data}\n";
    }
}

$server = new taskServer();