<?php


namespace EasySwoole\Policy;


use EasySwoole\Spl\SplBean;

class PolicyNode extends SplBean
{
    const EFFECT_ALLOW = 'allow';
    const EFFECT_DENY = 'deny';
    const EFFECT_UNKNOWN = 'unknown';
    /*
     * 当前节点路径
     */
    protected $name;
    /*
     * 子节点列表
     */
    protected $leaves = [];
    /*
     * 当前节点权限
     */
    protected $allow = self::EFFECT_UNKNOWN;

    function __construct($info = '*')
    {
        $array = [];
        if(is_string($info)){
            $this->name = $info;
        }else if(is_array($info)){
            $array = $info;
        }
        parent::__construct($array);
    }

    function addChild(string $nodeName):PolicyNode
    {
        if(isset($this->leaves[$nodeName])){
            $node = $this->leaves[$nodeName];
        }else{
            $node = new PolicyNode($nodeName);
            $this->leaves[$nodeName] = $node;
        }
        return $node;
    }

    /**
     * 获取节点名称
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 设置节点名称
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * 获取节点的权限
     * @return array
     */
    public function getLeaves(): array
    {
        return $this->leaves;
    }

    /**
     * 设置节点权限
     * @param array $leaves
     */
    public function setLeaves(array $leaves): void
    {
        $this->leaves = $leaves;
    }

    /**
     * 判断是否允许
     * @return string
     */
    public function isAllow()
    {
        return $this->allow;
    }

    /**
     * 设置是否允许
     * @param string $allow
     */
    public function setAllow(string $allow): void
    {
        $this->allow = $allow;
    }

    public function toArray(array $columns = null, $filter = null): array
    {
        $list = parent::toArray($columns, $filter);
        foreach ($list['leaves'] as $key => $item){
            $list['leaves'][$key] = $item->toArray();
        }
        return $list;
    }

    /*
     * 必须搜索实体路径
     */
    public function search(string $path,PolicyNode $parentNode = null):?PolicyNode
    {
        /*
         * 避免搜索/路径
         */
        $path = trim($path,'/');
        $list = explode('/',$path);
        $name = array_shift($list);
        if($name == $this->name){
            return $this;
        }
        if(empty($name) && $this->name == '*'){
            return $this;
        }
        if(!empty($name) && !empty($parentNode)){
            return $parentNode;
        }
        /*
         * 若存在该叶子节点
         */
        if(isset($this->leaves[$name])){
            /*
             * 说明需要继续搜索
             */
            if(!empty($list)){
                return $this->leaves[$name]->search(implode('/',$list));
            }else{
                return $this->leaves[$name];
            }
        }
        if(isset($this->leaves['*'])){
            /*
            * 说明需要继续搜索
            */
            if(!empty($list)){
                return $this->leaves['*']->search(implode('/',$list), $this->leaves['*']);
            }else{
                return $this->leaves['*'];
            }
        }
        return null;
    }
}