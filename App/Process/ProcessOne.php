<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 2/9/2019
 * Time: 14:24
 */

namespace App\Process;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Logger;
class ProcessOne extends AbstractProcess
{
    public function run($arg)
    {
        // TODO: Implement run() method.
        Logger::getInstance()->console($this->getProcessName()." start");
        while (1){
            \co::sleep(5);
            Logger::getInstance()->console($this->getProcessName()." run");
        }
    }
    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }
    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
    }
}