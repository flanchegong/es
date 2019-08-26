# EasySwoole RPC
很多传统的Phper并不懂RPC是什么，RPC全称Remote Procedure Call，中文译为远程过程调用,其实你可以把它理解为是一种架构性上的设计，或者是一种解决方案。
例如在某庞大商场系统中，你可以把整个商场拆分为N个微服务（理解为N个独立的小模块也行），例如：
    
- 订单系统
- 用户管理系统
- 商品管理系统
- 等等 

那么在这样的架构中，就会存在一个Api网关的概念，或者是叫服务集成者。我的Api网关的职责，就是把一个请求
，拆分成N个小请求，分发到各个小服务里面，再整合各个小服务的结果，返回给用户。例如在某次下单请求中，那么大概
发送的逻辑如下：
- Api网关接受请求
- Api网关提取用户参数，请求用户管理系统，获取用户余额等信息，等待结果
- Api网关提取商品参数，请求商品管理系统，获取商品剩余库存和价格等信息，等待结果。
- Api网关融合用户管理系统、商品管理系统的返回结果，进行下一步调用（假设满足购买条件）
- Api网关调用用户管理信息系统进行扣款，调用商品管理系统进行库存扣减，调用订单系统进行下单（事务逻辑和撤回可以用请求id保证，或者自己实现其他逻辑调度）
- APi网关返回综合信息给用户

而在以上发生的行为，就称为远程过程调用。而调用过程实现的通讯协议可以有很多，比如常见的HTTP协议。而EasySwoole RPC采用自定义短链接的TCP协议实现，每个请求包，都是一个JSON，从而方便实现跨平台调用。

什么是服务熔断？
 
粗暴来理解，一般是某个服务故障或者是异常引起的，类似现实世界中的‘保险丝’，当某个异常条件被触发，直接熔断整个服务，而不是一直等到此服务超时。

什么是服务降级?

粗暴来理解，一般是从整体负荷考虑，就是当某个服务熔断之后，服务器将不再被调用，此时客户端可以自己准备一个本地的fallback回掉，返回一个缺省值，这样做，虽然服务水平下降，但好歹，比直接挂掉要强。
服务降级处理是在客户端实现完成的，与服务端没有关系。

什么是服务限流？

粗暴来理解，例如某个服务器最多同时仅能处理100个请求，或者是cpu负载达到百分之80的时候，为了保护服务的稳定性，则不在希望继续收到
新的连接。那么此时就要求客户端不再对其发起请求。因此EasySwoole RPC提供了NodeManager接口，你可以以任何的形式来
监控你的服务提供者，在getServiceNode方法中，返回对应的服务器节点信息即可。  

<<<<<<< HEAD

## Composer安装
```
composer require easyswoole/rpc=4.x
``` 
## 示例代码
#### EasySwoole 封装实现
```php
$config = new Config();
$config->setServerIp('127.0.0.1');
$config->setNodeManager(new RedisManager('127.0.0.1', 6379));//设置节点管理器
$config->getBroadcastConfig()->setEnableBroadcast(true);//启用广播
$config->getBroadcastConfig()->setEnableListen(true);   //启用监听
$config->getBroadcastConfig()->setSecretKey('zhongguo');//设置加密秘钥

$rpc = new Rpc($config);
$rpc->add(new UserService());    //注册服务
$rpc->add(new OrderService()); 
$server=ServerManager::getInstance()->getSwooleServer();
$rpc->attachToServer($server);  
```
### Test-Server
######使用redis节点管理器,swoole-table节点管理器使用参考test里面的ServerTable
```php
use EasySwoole\Rpc\Config;
use EasySwoole\Rpc\Rpc;
use EasySwoole\Rpc\Test\UserService;
use EasySwoole\Rpc\NodeManager\RedisManager;
use EasySwoole\Rpc\Test\OrderService;


$config = new Config();
$config->setServerIp('127.0.0.1');
$config->setNodeManager(new RedisManager('127.0.0.1', 6379));//设置节点管理器
$config->getBroadcastConfig()->setEnableBroadcast(true);//启用广播
$config->getBroadcastConfig()->setEnableListen(true);   //启用监听
$config->getBroadcastConfig()->setSecretKey('zhongguo');//设置加密秘钥

$rpc = new Rpc($config);
$rpc->add(new UserService());    //注册服务
$rpc->add(new OrderService()); 

$list = $rpc->generateProcess(); //获取注册的rpc worker自定义进程
foreach ($list['worker'] as $p){
    $p->getProcess()->start();
}

foreach ($list['tickWorker'] as $p){//获取注册的rpc tick自定义进程(ps:处理广播和监听广播)
    $p->getProcess()->start();
}

while($ret = \Swoole\Process::wait()) {
    echo "PID={$ret['pid']}\n";
}

```

### Test-client
```php
use EasySwoole\Rpc\Config;
use EasySwoole\Rpc\Rpc;
use EasySwoole\Rpc\NodeManager\RedisManager;
use EasySwoole\Rpc\Response;

$config = new Config();
$nodeManager = new RedisManager('127.0.0.1', 6379);
$config->setNodeManager($nodeManager);
$rpc = new Rpc($config);

go(function () use ($rpc) {
    $client = $rpc->client();
    $client->addCall('UserService', 'register', ['arg1', 'arg2'])
        ->setOnFail(function (Response $response) {
            print_r($response->toArray());
        })
        ->setOnSuccess(function (Response $response) {
            print_r($response->toArray());
        });

    $client->exec();
});
```