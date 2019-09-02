<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 2/9/2019
 * Time: 11:42
 */
namespace App\Log;
use EasySwoole\Trace\AbstractInterface\LoggerInterface;
class MyLogHandle implements LoggerInterface {
    public function console(string $str, $category = null, $saveLog = true): ?string
    {
        // TODO: Implement console() method.
        echo "这是自定义的log处理,输出:$str\n";
        return "这是自定义的log处理,输出:$str\n";
    }
    public function log(string $str, $logCategory = null, int $timestamp = null): ?string
    {
        // TODO: Implement log() method.
        echo "这是自定义的log处理,模拟写入:[$logCategory]$str\n";
        return "这是自定义的log处理,模拟写入:[$logCategory]$str\n";
    }
}