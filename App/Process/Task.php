<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 3/9/2019
 * Time: 16:56
 */

namespace App\Process;
use App\Task\ProcessTest;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
class Task extends AbstractProcess
{
    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
    }
    public function run($arg)
    {
        echo "自定义进程开启\n";
// 直接投递闭包
        TaskManager::processAsync(function () {
            echo "自定义进程 异步任务执行中 \n";
        });
        // 投递任务类
        $taskClass = new ProcessTest('task data');
        TaskManager::processAsync($taskClass);
        // TODO: Implement run() method.
    }
    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }
}