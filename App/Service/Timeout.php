<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 4/9/2019
 * Time: 14:58
 */
/*
 * 定义timeout渲染方法
 */
namespace App\Service;
use EasySwoole\Annotation\Annotation;
use EasySwoole\Annotation\AbstractAnnotationTag;
class Timeout extends AbstractAnnotationTag
{
    public $timeout;

    public function tagName(): string
    {
        return 'timeout';
    }

    public function assetValue(?string $raw)
    {
        $this->timeout = floatval($raw);
    }

    public function aliasMap(): array
    {
        return [
            static::class,'timeout_alias'
        ];
    }
}
