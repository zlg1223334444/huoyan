<?php

define('BASE_DIR',__DIR__);

/*
 * 自动加载类
 * @param unknown $class
 */
function autoload($class)
{
    $filename = BASE_DIR.'/'.str_replace('\\','/',$class).'.php';

    if(file_exists($filename)) {
        include $filename;
    }
}
spl_autoload_register('autoload');

//rpc服务
$rpcApiHost = [
    '122.112.13.200:9502'
];

\RpcClient::config($rpcApiHost);
if (extension_loaded('swoole')) {
    \RpcClient::setSwooleClient();
}







