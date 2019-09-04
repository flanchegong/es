<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 4/9/2019
 * Time: 15:50
 */
namespace App\Utility;

use EasySwoole\Component\Singleton;

class Queue extends \EasySwoole\Queue\Queue
{
    use Singleton;
}