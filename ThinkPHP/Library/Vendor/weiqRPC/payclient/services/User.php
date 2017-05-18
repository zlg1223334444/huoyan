<?php

namespace client;

class User{
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

    public static function getUserByUid($uid){

        $user_client = \RpcClient::instance('User');
        $ret_sync = $user_client->getInfoByUid($uid);

        return $ret_sync;
    }

    public static function testRedis(){

        $user_client = \RpcClient::instance('User');
        $ret_sync = $user_client->testRedis();

        return $ret_sync;
    }

    public static function testMysql($uid){

        $user_client = \RpcClient::instance('User');
        $ret_sync = $user_client->testMysql($uid);

        return $ret_sync;
    }
}


