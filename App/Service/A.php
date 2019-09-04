<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 4/9/2019
 * Time: 14:59
 */
namespace App\Service;
use EasySwoole\Annotation\Annotation;
use EasySwoole\Annotation\AbstractAnnotationTag;
class A
{
    /** @var  */
    protected $a;

    /**
     * @param(name=a,type=string,value=2)
     * @param(name=b)
     * @timeout_Alias(0.5)
     * @fuck(easyswoole)
     * 这是我的其他说明啊啊啊啊啊
     */
    function test()
    {

    }
}