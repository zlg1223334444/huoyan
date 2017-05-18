<?php

namespace client;

class Search{
    //配置服务端列表
    protected static $addressArray = [
        '127.0.0.1:9501'
    ];

    public function __construct(){
        \RpcClient::config(self::$addressArray);

        if (extension_loaded('swoole')) {
            \RpcClient::setSwooleClient();
        }

    }

    public static function search($id){

        $user_client = \RpcClient::instance('Search');
        $ret_sync = $user_client->search($id);

        return $ret_sync;
    }
}


