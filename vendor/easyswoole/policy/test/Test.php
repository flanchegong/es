<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2019/7/3
 * Time: 21:06
 */
require_once "vendor/autoload.php";

use EasySwoole\Policy\Policy;
use EasySwoole\Policy\PolicyNode;

//$policy = new Policy();
//
////添加节点权限
//$policy->addPath('/user/add', PolicyNode::EFFECT_ALLOW);    //添加允许的单节点
//$policy->addPath('/user/update', PolicyNode::EFFECT_ALLOW);
//$policy->addPath('/user/delete', PolicyNode::EFFECT_DENY);  //添加拒绝的单节点
//$policy->addPath('/user/*', PolicyNode::EFFECT_DENY);       //添加拒绝的通配节点
//
////验证权限
//var_dump($policy->check('user/asdasd/dsad'));   //deny
//var_dump($policy->check('user/add'));           //allow
//var_dump($policy->check('user/update'));        //allow
//var_dump($policy->check('user/delete'));        //deny
//
//print_r($policy->toArray());//树形结构


//对象添加授权
$root = new PolicyNode('*');

$userChild = $root->addChild('user');
$userAddChild = $userChild->addChild('add');
$userAddChild->addChild('aaaaaa')->setAllow(PolicyNode::EFFECT_ALLOW);
$userChild->addChild('update')->setAllow(PolicyNode::EFFECT_DENY);
$userChild->addChild('*')->setAllow(PolicyNode::EFFECT_ALLOW);

$apiChild = $root->addChild('charge');
$apiChild->addChild('*');

$node = $root->search('/user/add/aaaa');
if ($node) {
    var_dump($node->isAllow());
}
