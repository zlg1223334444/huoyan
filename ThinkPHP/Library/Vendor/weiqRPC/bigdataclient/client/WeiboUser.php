<?php

namespace client;

class WeiboUser{

    private static $userClient;

    public function __construct(){
        self::$userClient = \RpcClient::instance('WeiboUser');
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
     * weiq_star                                             星级
     * weiq_status                                           1: 存在 0：删除
     *              weiq_shop_id                int         店铺id
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
     *      * weiq_star                                             星级
     * weiq_status                                           1: 存在 0：删除
     *              weiq_shop_id                int         店铺id
     * @return bool
     */
    public static function updateUser($info){
        $result = self::$userClient->updateUser($info);
        return $result;
    }

    /**
     * @desc 获取微博相关分析数据
     * @param $weiboUid int/string 微博账号id
     * @return mixed
     */
    public static function getAnalysisData($weiboUid){
        write_log($weiboUid,'web_interface_log'); 
        $result = self::$userClient->getAnalysisData($weiboUid);
        write_log(@json_encode($result),'web_interface_log');
        return $result;
    }


}


