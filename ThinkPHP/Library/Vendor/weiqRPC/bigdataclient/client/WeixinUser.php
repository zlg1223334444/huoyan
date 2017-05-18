<?php

namespace client;

class WeixinUser
{
    private static $userClient;

    public function __construct()
    {
        self::$userClient = \RpcClient::instance('WeixinUser');
    }

    /**
     * @desc 获取微信相关分析数据
     * @param $wxNumber int/string 微信公众号的账号
     * @return mixed
     */
    public static function getAnalysisData($wxNumber){
        $result = self::$userClient->getAnalysisData($wxNumber);
        return $result;
    }
}