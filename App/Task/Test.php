<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 3/9/2019
 * Time: 16:54
 */

namespace App\Task;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;
class Test extends AbstractAsyncTask
{
    protected function finish($result, $task_id)
    {
        echo "执行模板异步任务完成\n";
        // TODO: Implement finish() method.
    }
    protected function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        echo "执行模板异步任务中\n";
        return true;//必须要return true,代表完成
        // TODO: Implement run() method.
    }
}