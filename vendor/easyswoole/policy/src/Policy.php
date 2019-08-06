<?php
/**
 *
 * Copyright  FaShop
 * License    http://www.fashop.cn
 * link       http://www.fashop.cn
 * Created by FaShop.
 * User: hanwenbo
 * Date: 2019-02-20
 * Time: 11:31
 *
 */

namespace EasySwoole\Policy;


class Policy
{
    protected $root;

    function __construct()
    {
        /**
         * 表示通配,根节点
         */
        $this->root = new PolicyNode("*");
    }

    /**
     * 添加路劲并设置权限
     * @param string $path
     * @param string $allow
     */
    public function addPath(string $path, string $allow = PolicyNode::EFFECT_ALLOW)
    {
        $list = explode('/', trim($path, '/'));
        $temp = $this->root;
        foreach ($list as $path) {
            $temp = $temp->addChild($path);//递归设置节点
        }
        $temp->setAllow($allow);
    }

    /**
     * 检测权限
     * @param string $path
     * @return string
     */
    public function check(string $path)
    {
        $node = $this->root->search($path);
        if ($node) {
            return $node->isAllow();
        } else {
            return PolicyNode::EFFECT_UNKNOWN;
        }
    }

    /**
     * 所有节点
     * @return array
     */
    public function toArray()
    {
        return $this->root->toArray();
    }
}

