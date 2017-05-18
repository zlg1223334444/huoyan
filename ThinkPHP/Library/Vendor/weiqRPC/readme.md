# todo
1. 暂时未加入类的自动加载（server下的services只用去调用外部的业务处理方法）


# 注意
1. RpcServer.php和RpcClient.php中的 Json.php的路径（命名空间）
2. 后台进程可能需要sudo su权限才能执行（mac下是这样）

# 执行顺序
服务端
php think test -> RpcServer -> /server/services/.....

客户端
/client/services/.... -> RpcClient