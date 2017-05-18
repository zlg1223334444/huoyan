<?php

namespace rpc\client\services;

class WeiboUser{
    //配置服务端列表
    protected static $addressArray = [
        //'122.112.13.200:9501',
        // '127.0.0.1:9501'
        '122.112.13.206:9501'
    ];

    private static $userClient;

    public function __construct(){
        \rpc\client\RpcClient::config(self::$addressArray);

        if (extension_loaded('swoole')) {
            \rpc\client\RpcClient::setSwooleClient();
        }

        self::$userClient = \rpc\client\RpcClient::instance('WeiboUser');
    }

    /**
     * @desc 新增账号信息
     * @param $info array 账号信息
     *              weibo_uid                   int         微博账号uid
     *              weiq_class                  string      weiq分类
     *              weiq_post_price             int         weiq原发价格
     *              weiq_post_price_adver       int         广告主原发价格
     *              weiq_forward_price          int         weiq转发价格
     *              weiq_forward_price_adver    int         广告主转发价格
     *              weiq_status                 int         weiq中自媒体状态
     *              weiq_orderset               int         自媒体接单设置：0手动接单，1自动接单，2暂不接单
     * @return bool
     */
    public static function addUser($info){

        $result = self::$userClient->addUser($info);

        return $result;
    }

    /**
     * @desc 更新账号信息
     * @param $info array 账号信息
     *              weibo_uid                   int         微博账号uid
     *              weiq_class                  string      weiq分类
     *              weiq_post_price             int         weiq原发价格
     *              weiq_post_price_adver       int         广告主原发价格
     *              weiq_forward_price          int         weiq转发价格
     *              weiq_forward_price_adver    int         广告主转发价格
     *              weiq_status                 int         weiq中自媒体状态
     *              weiq_orderset               int         自媒体接单设置：0手动接单，1自动接单，2暂不接单
     * @return bool
     */
    public static function updateUser($info){

        $result = self::$userClient->updateUser($info);

        return $result;

    }



}


