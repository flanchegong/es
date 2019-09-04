<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 4/9/2019
 * Time: 14:57
 */
namespace App\Service;
use EasySwoole\Annotation\Annotation;
use EasySwoole\Annotation\AbstractAnnotationTag;

/*
 * 定义param渲染方法
 */

class Param extends AbstractAnnotationTag
{

    public function tagName(): string
    {
        return 'param';
    }

    public function assetValue(?string $raw)
    {
        $list = explode(',',$raw);
        foreach ($list as $item){
            parse_str($item,$ret);
            foreach ($ret as $key => $value){
                $this->$key = trim($value," \t\n\r\0\x0B\"\'");
            }
        }
    }
}
