<?php
/**
 * Created by PhpStorm.
 * User: qiaojw
 * Date: 2017/3/15
 * Time: 14:02
 */
class Weiq
{
    //配置服务端列表
    protected static $addressArray = [
        //'122.112.13.200:9501',
        // '127.0.0.1:9501'
        '122.112.13.206:9502'
        //'122.112.13.218:9501'
    ];

    private static $userClient;

    public function __construct(){
        include_once(__DIR__.'/../RpcpayClient.php');
        RpcpayClient::config(self::$addressArray);
        if (extension_loaded('swoole')) {
            RpcpayClient::setSwooleClient();
        }

        self::$userClient = RpcpayClient::instance('Weiq');
    }

    // 保存单条订单信息，订单信息如下
    /*
    [
        'order_id'       => '订单自增ID',
        'order_idstr'    => '订单唯一ID',
        'weiq_user_id'   => '自媒体主ID',
        'weiq_media_id'  => 'weiq的自媒体ID'
        'weiq_platid'    => '第三方平台的ID，如微博uid'
        'nick'           => '接单媒体名称',
        'plat_type'      => '媒体分类', // (同weiq当前数据库说明) 0微博，1腾讯微博，2微信（公众号）， 3qq空间，4微信（朋友圈）,6技能，7此刻
        'task_id'        => '任务自增ID',
        'task_idstr'     => '任务唯一ID',
        'task_name'      => '任务名称',
        'start_time'     => '订单开始时间',
        'expect_income'  => '预计收入',
        'fact_income'    => '实际收入',
        //'master_pro'     => '主比例',
        //'slave_pro'      => '从比例',
        'out_createtime'   => '订单的出账时间',
        'settlementtime' => '订单结算时间'
    ]
    */
    public static function sendOrderinfo($info)
    {
        return self::$userClient->sendOrderinfo($info);
    }

    // 获取所有已签约的自媒体信息
    public static function getAllMediaFromSign()
    {
        return self::$userClient->getAllMediaFromSign();
    }

    // 获取所有已签约的自媒体主信息
    public static function getAllWeiqUserFromSign($starttime=0,$endtime=0)
    {
        return self::$userClient->getAllWeiqUserFromSign($starttime,$endtime);
    }
    /**
     * 更新是否自动接单
     *
     * @param $media_id
     * @param $auto_accept  1是0否
     * @return array
     */
    public function updateAutoAccept($media_id, $auto_accept)
    {
        return self::$userClient->updateAutoAccept($media_id, $auto_accept);
    }

    // 获取当前自媒体信息的主从分成比例
    public function getMediaPro($media_id)
    {
        return self::$userClient->getMediaPro($media_id);
    }

    // 获取当前自媒体的支付宝信息
    /*
    'plat_type' => 支付平台， 1：支付宝
    'account' => 支付宝帐号
    'real_name' => 支付宝实名
    */
    public function getPayAccount($media_id)
    {
        return self::$userClient->getPayAccount($media_id);
    }

    // 自媒体主 增加了新的自媒体账号
    /*
    [
        'uid'            => '自媒体主 id',
        'weiq_media_id'  => 'media_id  自媒体的自增ID',
        'weiq_platid'    => '自媒体的platid',
        'nick'           => '自媒体昵称',
        'plat_type'      => '媒体分类',
        'is_accept_auto' => '是否自动接单 1是0否',
    ];
    */
    public function AddNewMedia($info)
    {
        return self::$userClient->AddNewMedia($info);
    }

    // 自媒体主解绑自媒体
    public function delMedia($media_id)
    {
        return self::$userClient->delMedia($media_id);
    }

	// 同步游戏充值明细
	public function orderLists($param)
	{
		return self::$userClient->orderLists($param);
	}

	// 同步返佣比例
	public function syncProp($param)
	{
		return self::$userClient->syncProp($param);
	}

	// 同步WEIQ订单信息
    public function syncWeiqOrderInfo($param)
    {
        return self::$userClient->syncWeiqOrderInfo($param);
    }
}