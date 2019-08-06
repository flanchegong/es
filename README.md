# es
main server                   SWOOLE_WEB
listen address                127.0.0.1
listen port                   9502
ip@enp0s3                     192.168.109.58
worker_num                    4
max_request                   10000
task_worker_num               4
task_max_request              5000
log_level                     3
enable_coroutine              1
max_coroutine                 3000
task_enable_coroutine         1
tcp_fastopen                  1
enable_reuse_port             1
tcp_defer_accept              5
open_tcp_nodelay              1
package_max_length            67108864
enable_static_handler         
pid_file                      /data/wwwroot/default/flanche/Temp/pid.pid
log_file                      /data/wwwroot/default/flanche/Log/EasySwoole.swoole.log
run at user                   w
daemonize                     false
swoole version                4.4.0
php version                   7.1.30
easy swoole                   3.2.6
develop/produce               develop
temp dir                      /data/wwwroot/default/flanche/Temp
log dir                       /data/wwwroot/default/flanche/Log
