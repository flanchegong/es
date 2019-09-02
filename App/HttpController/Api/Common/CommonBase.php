<?php
/**
 * Created by PhpStorm.
 * User: gong
 * Date: 2/9/2019
 * Time: 15:03
 */

namespace App\HttpController\Api\Common;
use App\HttpController\Api\ApiBase;
use EasySwoole\Validate\Validate;
class CommonBase extends ApiBase
{
    function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action)) {
            return true;
        }
        return false;
    }

    protected function getValidateRule(?string $action): ?Validate
    {
        return null;
        // TODO: Implement getValidateRule() method.
    }
}