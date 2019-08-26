<?php


namespace EasySwoole\Rpc;


use EasySwoole\Spl\SplBean;

class BroadcastCommand extends SplBean
{
    const COMMAND_HEART_BEAT = 1;
    const COMMAND_OFF_LINE = 2;
    protected $command = self::COMMAND_HEART_BEAT;
    /** @var ServiceNode */
    protected $serviceNode;

    /**
     * @return int
     */
    public function getCommand(): int
    {
        return $this->command;
    }

    /**
     * @param int $command
     */
    public function setCommand(int $command): void
    {
        $this->command = $command;
    }

    /**
     * @return ServiceNode
     */
    public function getServiceNode(): ?ServiceNode
    {
        return $this->serviceNode;
    }

    /**
     * @param ServiceNode $serviceNode
     */
    public function setServiceNode(ServiceNode $serviceNode): void
    {
        $this->serviceNode = $serviceNode;
    }

}