<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 3/9/2019
 * Time: 16:55
 */

namespace App\Task;
use EasySwoole\EasySwoole\Swoole\Task\QuickTaskInterface;
class QuickTaskTest implements QuickTaskInterface
{
    static function run(\swoole_server $server, int $taskId, int $fromWorkerId, $flags = null)
    {
        // TODO: Implement run() method.
        echo "快速任务模板运行中\n";
        return true;
    }
}