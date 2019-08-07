<?php

/**
 * Created by PhpStorm.
 * User: flanche
 * Date: 2016/12/7
 * Time: 16:18
 */
class taskClient
{
    private $client;

    public function __construct()
    {
        $this->client = new Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $this->client->on('Connect', array($this, 'onConnect'));
        $this->client->on('Receive', array($this, 'onReceive'));
        $this->client->on('Close', array($this, 'onClose'));
        $this->client->on('Error', array($this, 'onError'));
    }

    public function connect()
    {
        if (!$fp = $this->client->connect("127.0.0.1", 9504, 1)) {
            echo "Error: {$fp->errMsg}[{$fp->errCode}]\n";
            return;
        }
    }

    //connect之后,会调用onConnect方法
    public function onConnect($cli)
    {
        fwrite(STDOUT, "Enter Msg:");
        swoole_event_add(STDIN, function () {
            fwrite(STDOUT, "Enter Msg:");
            $msg = trim(fgets(STDIN));
            $this->send($msg);
        });
    }

    public function onClose($cli)
    {
        echo "Client close connection\n";
    }

    public function onError()
    {

    }

    public function onReceive($cli, $data)
    {
        echo "Received: " . $data . "\n";
    }

    public function send($data)
    {
        $this->client->send($data);
    }

    public function isConnected($cli)
    {
        return $this->client->isConnected();
    }

}

$client = new taskClient();
$client->connect();