<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 4/9/2019
 * Time: 10:32
 */
namespace App\Utility\Pool;
use Co\Redis;
use EasySwoole\Component\Pool\PoolObjectInterface;
class RedisObject extends Redis implements PoolObjectInterface
{
    function gc()
    {
        // TODO: Implement gc() method.
        $this->close();
    }
    function objectRestore()
    {
        // TODO: Implement objectRestore() method.
    }
    function beforeUse(): bool
    {
        // TODO: Implement beforeUse() method.
        return true;
    }
}