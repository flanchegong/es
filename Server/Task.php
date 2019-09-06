<?php
/**
 * Created by 纵腾网络.
 * User: flanche
 * Date: 2019/9/6
 * Time: 10:10
 */
use EasySwoole\Task\Config;
use EasySwoole\Task\Task;

/*
    配置项中可以修改工作进程数、临时目录，进程名，最大并发执行任务数，异常回调等
*/
$config = new Config();
$task = new Task($config);

$http = new swoole_http_server("127.0.0.1", 9501);
/*
添加服务
*/
$task->attachToServer($http);

$http->on("request", function ($request, $response)use($task){
    if(isset($request->get['sync'])){
        $ret = $task->sync(function ($taskId,$workerIndex){
            return "{$taskId}.{$workerIndex}";
        });
        $response->end("sync result ".$ret);
    }else if(isset($request->get['status'])) {
        var_dump($task->status());
    }else{
        $id = $task->async(function ($taskId,$workerIndex){
            \co::sleep(1);
            var_dump("async id {$taskId} task run");
        });
        $response->end("async id {$id} ");
    }
});

$http->start();