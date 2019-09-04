<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 4/9/2019
 * Time: 15:51
 */
namespace App\Utility;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Queue\Job;

class QueueProcess extends AbstractProcess
{

    protected function run($arg)
    {
        Queue::getInstance()->consumer()->listen(function (Job $job){
            var_dump($job->toArray());
        });
    }

    protected function onShutDown()
    {
        Queue::getInstance()->consumer()->stopListen();
    }
}