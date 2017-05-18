<?php
/**
 *  Created by PhpStorm.
 *  User: Lizhen
 *
 *	微信公众平台PHP-SDK, 官方API部分
 */
namespace Wechat;

class Wechat{

    //消息类型
    const MSGTYPE_TEXT = 'text';
    const MSGTYPE_IMAGE = 'image';
    const MSGTYPE_LOCATION = 'location';
    const MSGTYPE_LINK = 'link';
    const MSGTYPE_EVENT = 'event';
    const MSGTYPE_MUSIC = 'music';
    const MSGTYPE_NEWS = 'news';
    const MSGTYPE_VOICE = 'voice';
    const MSGTYPE_VIDEO = 'video';
    const EVENT_SUBSCRIBE = 'subscribe';       //订阅
    const EVENT_UNSUBSCRIBE = 'unsubscribe';   //取消订阅
    const EVENT_SCAN = 'SCAN';                 //扫描带参数二维码
    const EVENT_LOCATION = 'LOCATION';         //上报地理位置
    const EVENT_MENU_VIEW = 'VIEW';                     //菜单 - 点击菜单跳转链接
    const EVENT_MENU_CLICK = 'CLICK';                   //菜单 - 点击菜单拉取消息
    const EVENT_MENU_SCAN_PUSH = 'scancode_push';       //菜单 - 扫码推事件(客户端跳URL)
    const EVENT_MENU_SCAN_WAITMSG = 'scancode_waitmsg'; //菜单 - 扫码推事件(客户端不跳URL)
    const EVENT_MENU_PIC_SYS = 'pic_sysphoto';          //菜单 - 弹出系统拍照发图
    const EVENT_MENU_PIC_PHOTO = 'pic_photo_or_album';  //菜单 - 弹出拍照或者相册发图
    const EVENT_MENU_PIC_WEIXIN = 'pic_weixin';         //菜单 - 弹出微信相册发图器
    const EVENT_MENU_LOCATION = 'location_select';      //菜单 - 弹出地理位置选择器
    const EVENT_SEND_MASS = 'MASSSENDJOBFINISH';        //发送结果 - 高级群发完成
    const EVENT_SEND_TEMPLATE = 'TEMPLATESENDJOBFINISH';//发送结果 - 模板消息发送结果
    const EVENT_CARD_PASS = 'card_pass_check';          //卡券 - 审核通过
    const EVENT_CARD_NOTPASS = 'card_not_pass_check';   //卡券 - 审核未通过
    const EVENT_CARD_USER_GET = 'user_get_card';        //卡券 - 用户领取卡券
    const EVENT_CARD_USER_DEL = 'user_del_card';        //卡券 - 用户删除卡券
    const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    const AUTH_URL = '/token?grant_type=client_credential&';
    const MENU_CREATE_URL = '/menu/create?';
    const MENU_GET_URL = '/menu/get?';
    const MENU_DELETE_URL = '/menu/delete?';
    const GET_TICKET_URL = '/ticket/getticket?';
    const CALLBACKSERVER_GET_URL = '/getcallbackip?';
    const QRCODE_CREATE_URL='/qrcode/create?';
    const QR_SCENE = 0;
    const QR_LIMIT_SCENE = 1;
    const QRCODE_IMG_URL='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';
    const SHORT_URL='/shorturl?';
    const USER_GET_URL='/user/get?';
    const USER_INFO_URL='/user/info?';
    const USER_UPDATEREMARK_URL='/user/info/updateremark?';
    const GROUP_GET_URL='/groups/get?';
    const USER_GROUP_URL='/groups/getid?';
    const GROUP_CREATE_URL='/groups/create?';
    const GROUP_UPDATE_URL='/groups/update?';
    const GROUP_MEMBER_UPDATE_URL='/groups/members/update?';
    const GROUP_MEMBER_BATCHUPDATE_URL='/groups/members/batchupdate?';
    const CUSTOM_SEND_URL='/message/custom/send?';
    const MEDIA_UPLOADNEWS_URL = '/media/uploadnews?';
    const MASS_SEND_URL = '/message/mass/send?';
    const TEMPLATE_SET_INDUSTRY_URL = '/message/template/api_set_industry?';
    const TEMPLATE_ADD_TPL_URL = '/message/template/api_add_template?';
    const TEMPLATE_SEND_URL = '/message/template/send?';
    const MASS_SEND_GROUP_URL = '/message/mass/sendall?';
    const MASS_DELETE_URL = '/message/mass/delete?';
    const MASS_PREVIEW_URL = '/message/mass/preview?';
    const MASS_QUERY_URL = '/message/mass/get?';
    const UPLOAD_MEDIA_URL = 'http://file.api.weixin.qq.com/cgi-bin';
    const MEDIA_UPLOAD_URL = '/media/upload?';
    const MEDIA_GET_URL = '/media/get?';
    const MEDIA_VIDEO_UPLOAD = '/media/uploadvideo?';
    const MEDIA_IMG_UPLOAD = '/media/uploadimg?';
    const MEDIA_FOREVER_UPLOAD_URL = '/material/add_material?';
    const MEDIA_FOREVER_NEWS_UPLOAD_URL = '/material/add_news?';
    const MEDIA_FOREVER_NEWS_UPDATE_URL = '/material/update_news?';
    const MEDIA_FOREVER_GET_URL = '/material/get_material?';
    const MEDIA_FOREVER_DEL_URL = '/material/del_material?';
    const MEDIA_FOREVER_COUNT_URL = '/material/get_materialcount?';
    const MEDIA_FOREVER_BATCHGET_URL = '/material/batchget_material?';
    const OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
    const OAUTH_AUTHORIZE_URL = '/authorize?';
    //授权接口
	const API_BASE_URL_PREFIX = 'https://api.weixin.qq.com'; //以下API接口URL需要使用此前缀
	const OAUTH_TOKEN_URL = '/sns/oauth2/access_token?';
	const OAUTH_REFRESH_URL = '/sns/oauth2/refresh_token?';
	const OAUTH_USERINFO_URL = '/sns/userinfo?';
	const OAUTH_AUTH_URL = '/sns/auth?';
	///卡券相关地址
	const CARD_CREATE                     = '/card/create?';
	const CARD_DELETE                     = '/card/delete?';
	const CARD_UPDATE                     = '/card/update?';
	const CARD_GET                        = '/card/get?';
	const CARD_BATCHGET                   = '/card/batchget?';
	const CARD_MODIFY_STOCK               = '/card/modifystock?';
	const CARD_LOCATION_BATCHADD          = '/card/location/batchadd?';
	const CARD_LOCATION_BATCHGET          = '/card/location/batchget?';
	const CARD_GETCOLORS                  = '/card/getcolors?';
	const CARD_QRCODE_CREATE              = '/card/qrcode/create?';
	const CARD_CODE_CONSUME               = '/card/code/consume?';
	const CARD_CODE_DECRYPT               = '/card/code/decrypt?';
	const CARD_CODE_GET                   = '/card/code/get?';
	const CARD_CODE_UPDATE                = '/card/code/update?';
	const CARD_CODE_UNAVAILABLE           = '/card/code/unavailable?';
	const CARD_TESTWHILELIST_SET          = '/card/testwhitelist/set?';
	const CARD_MEMBERCARD_ACTIVATE        = '/card/membercard/activate?';      //激活会员卡
	const CARD_MEMBERCARD_UPDATEUSER      = '/card/membercard/updateuser?';    //更新会员卡
	const CARD_MOVIETICKET_UPDATEUSER     = '/card/movieticket/updateuser?';   //更新电影票(未加方法)
	const CARD_BOARDINGPASS_CHECKIN       = '/card/boardingpass/checkin?';     //飞机票-在线选座(未加方法)
	const CARD_LUCKYMONEY_UPDATE          = '/card/luckymoney/updateuserbalance?';     //更新红包金额
	const SEMANTIC_API_URL = '/semantic/semproxy/search?'; //语义理解

    private $token;
    private $encodingAesKey;
    private $encrypt_type;
    public $appid;
    public $appsecret;
    private $access_token;
    private $jsapi_ticket;
    private $user_token;
    private $partnerid;
    private $partnerkey;
    private $paysignkey;
    private $postxml;
    private $_msg;
    private $_funcflag = false;
    private $_receive;
    private $_text_filter = true;
    public $debug =  false;
    public $errCode = 0;
    public $errMsg = "success";
    public $logcallback;

    public function __construct($options)
    {
        $this->token = isset($options['token'])?$options['token']:'';
        $this->encodingAesKey = isset($options['encodingaeskey'])?$options['encodingaeskey']:'';
        $this->appid = isset($options['appid'])?$options['appid']:'';
        $this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
        $this->debug = isset($options['debug'])?$options['debug']:false;
        $this->logcallback = isset($options['logcallback'])?$options['logcallback']:false;
    }

    /**
     * For weixin server validation
     */
    private function checkSignature($str='')
    {
        $signature = isset($_GET["signature"])?$_GET["signature"]:'';
        $signature = isset($_GET["msg_signature"])?$_GET["msg_signature"]:$signature; //如果存在加密验证则用加密验证段
        $timestamp = isset($_GET["timestamp"])?$_GET["timestamp"]:'';
        $nonce = isset($_GET["nonce"])?$_GET["nonce"]:'';

        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce,$str);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * For weixin server validation
     * @param bool $return 是否返回
     */
    public function valid($return=false)
    {
        $encryptStr="";
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $postStr = file_get_contents("php://input");//获取post数据
            $array = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->encrypt_type = isset($_GET["encrypt_type"]) ? $_GET["encrypt_type"]: '';
            if ($this->encrypt_type == 'aes') { //aes加密
                $this->log($postStr);
                $encryptStr = $array['Encrypt'];
                $pc = new Prpcrypt($this->encodingAesKey);
                $array = $pc->decrypt($encryptStr,$this->appid);
                if (!isset($array[0]) || ($array[0] != 0)) {
                    if (!$return) {
                        die('decrypt error!');
                    } else {
                        return false;
                    }
                }
                $this->postxml = $array[1];
                if (!$this->appid)
                    $this->appid = $array[2];//为了没有appid的订阅号。
            } else {
                $this->postxml = $postStr;
            }
        } elseif (isset($_GET["echostr"])) {
            $echoStr = $_GET["echostr"];
            if ($return) {
                if ($this->checkSignature())
                    return $echoStr;
                else
                    return false;
            } else {
                if ($this->checkSignature())
                    die($echoStr);
                else
                    die('no access');
            }
        }

        if (!$this->checkSignature($encryptStr)) {
            if ($return)
                return false;
            else
                die('no access');
        }
        return true;
    }

    /**
     * 日志记录，可被重载。
     * @param mixed $log 输入日志
     * @return mixed
     */
    protected function log($log){
        if ($this->debug) {
            if (function_exists($this->logcallback)) {
                if (is_array($log)) $log = print_r($log,true);
                return call_user_func($this->logcallback,$log);
            }elseif (class_exists('Log')) {
                Log::write('wechat：'.$log, Log::DEBUG);
                return true;
            }
        }
        return false;
    }

    /**
     * 设置发送消息
     * @param array $msg 消息数组
     * @param bool $append 是否在原消息数组追加
     */
    public function Message($msg = '',$append = false){
        if (is_null($msg)) {
            $this->_msg =array();
        }elseif (is_array($msg)) {
            if ($append)
                $this->_msg = array_merge($this->_msg,$msg);
            else
                $this->_msg = $msg;
            return $this->_msg;
        } else {
            return $this->_msg;
        }
    }

    /**
     * 获取微信服务器发来的信息
     */
    public function getRev()
    {
        if ($this->_receive) return $this;
        $postStr = !empty($this->postxml)?$this->postxml:file_get_contents("php://input");
        //兼顾使用明文又不想调用valid()方法的情况
        $this->log($postStr);
        if (!empty($postStr)) {
            $this->_receive = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        return $this;
    }

    /**
     * 获取微信服务器发来的信息
     */
    public function getRevData()
    {
        return $this->_receive;
    }

    /**
     * 获取消息发送者
     */
    public function getRevFrom() {
        if (isset($this->_receive['FromUserName']))
            return $this->_receive['FromUserName'];
        else
            return false;
    }

    /**
     * 获取消息接受者
     */
    public function getRevTo() {
        if (isset($this->_receive['ToUserName']))
            return $this->_receive['ToUserName'];
        else
            return false;
    }

    /**
     * 获取接收消息的类型
     */
    public function getRevType() {
        if (isset($this->_receive['MsgType']))
            return $this->_receive['MsgType'];
        else
            return false;
    }

    /**
     * 获取消息ID
     */
    public function getRevID() {
        if (isset($this->_receive['MsgId']))
            return $this->_receive['MsgId'];
        else
            return false;
    }

    /**
     * 获取消息发送时间
     */
    public function getRevCtime() {
        if (isset($this->_receive['CreateTime']))
            return $this->_receive['CreateTime'];
        else
            return false;
    }

    /**
     * 获取接收消息内容正文
     */
    public function getRevContent(){
        if (isset($this->_receive['Content']))
            return $this->_receive['Content'];
        else if (isset($this->_receive['Recognition'])) //获取语音识别文字内容，需申请开通
            return $this->_receive['Recognition'];
        else
            return false;
    }

    /**
     * 获取接收消息图片
     */
    public function getRevPic(){
        if (isset($this->_receive['PicUrl']))
            return array(
                'mediaid'=>$this->_receive['MediaId'],
                'picurl'=>(string)$this->_receive['PicUrl'],    //防止picurl为空导致解析出错
            );
        else
            return false;
    }

    /**
     * 获取接收消息链接
     */
    public function getRevLink(){
        if (isset($this->_receive['Url'])){
            return array(
                'url'=>$this->_receive['Url'],
                'title'=>$this->_receive['Title'],
                'description'=>$this->_receive['Description']
            );
        } else
            return false;
    }

    /**
     * 获取接收地理位置
     */
    public function getRevGeo(){
        if (isset($this->_receive['Location_X'])){
            return array(
                'x'=>$this->_receive['Location_X'],
                'y'=>$this->_receive['Location_Y'],
                'scale'=>$this->_receive['Scale'],
                'label'=>$this->_receive['Label']
            );
        } else
            return false;
    }

    /**
     * 获取上报地理位置事件
     */
    public function getRevEventGeo(){
        if (isset($this->_receive['Latitude'])){
            return array(
                'x'=>$this->_receive['Latitude'],
                'y'=>$this->_receive['Longitude'],
                'precision'=>$this->_receive['Precision'],
            );
        } else
            return false;
    }

    /**
     * 获取接收事件推送
     */
    public function getRevEvent(){
        if (isset($this->_receive['Event'])){
            $array['event'] = $this->_receive['Event'];
        }
        if (isset($this->_receive['EventKey'])){
            $array['key'] = $this->_receive['EventKey'];
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的扫码推事件信息
     *
     * 事件类型为以下两种时则调用此方法有效
     * Event	 事件类型，scancode_push
     * Event	 事件类型，scancode_waitmsg
     *
     * @return: array | false
     * array (
     *     'ScanType'=>'qrcode',
     *     'ScanResult'=>'123123'
     * )
     */
    public function getRevScanInfo(){
        if (isset($this->_receive['ScanCodeInfo'])){
            if (!is_array($this->_receive['ScanCodeInfo'])) {
                $array=(array)$this->_receive['ScanCodeInfo'];
                $this->_receive['ScanCodeInfo']=$array;
            }else {
                $array=$this->_receive['ScanCodeInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的图片发送事件信息
     *
     * 事件类型为以下三种时则调用此方法有效
     * Event	 事件类型，pic_sysphoto        弹出系统拍照发图的事件推送
     * Event	 事件类型，pic_photo_or_album  弹出拍照或者相册发图的事件推送
     * Event	 事件类型，pic_weixin          弹出微信相册发图器的事件推送
     *
     * @return: array | false
     * array (
     *   'Count' => '2',
     *   'PicList' =>array (
     *         'item' =>array (
     *             0 =>array ('PicMd5Sum' => 'aaae42617cf2a14342d96005af53624c'),
     *             1 =>array ('PicMd5Sum' => '149bd39e296860a2adc2f1bb81616ff8'),
     *         ),
     *   ),
     * )
     *
     */
    public function getRevSendPicsInfo(){
        if (isset($this->_receive['SendPicsInfo'])){
            if (!is_array($this->_receive['SendPicsInfo'])) {
                $array=(array)$this->_receive['SendPicsInfo'];
                if (isset($array['PicList'])){
                    $array['PicList']=(array)$array['PicList'];
                    $item=$array['PicList']['item'];
                    $array['PicList']['item']=array();
                    foreach ( $item as $key => $value ){
                        $array['PicList']['item'][$key]=(array)$value;
                    }
                }
                $this->_receive['SendPicsInfo']=$array;
            } else {
                $array=$this->_receive['SendPicsInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的地理位置选择器事件推送
     *
     * 事件类型为以下时则可以调用此方法有效
     * Event	 事件类型，location_select        弹出地理位置选择器的事件推送
     *
     * @return: array | false
     * array (
     *   'Location_X' => '33.731655000061',
     *   'Location_Y' => '113.29955200008047',
     *   'Scale' => '16',
     *   'Label' => '某某市某某区某某路',
     *   'Poiname' => '',
     * )
     *
     */
    public function getRevSendGeoInfo(){
        if (isset($this->_receive['SendLocationInfo'])){
            if (!is_array($this->_receive['SendLocationInfo'])) {
                $array=(array)$this->_receive['SendLocationInfo'];
                if (empty($array['Poiname'])) {
                    $array['Poiname']="";
                }
                if (empty($array['Label'])) {
                    $array['Label']="";
                }
                $this->_receive['SendLocationInfo']=$array;
            } else {
                $array=$this->_receive['SendLocationInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取接收语音推送
     */
    public function getRevVoice(){
        if (isset($this->_receive['MediaId'])){
            return array(
                'mediaid'=>$this->_receive['MediaId'],
                'format'=>$this->_receive['Format'],
            );
        } else
            return false;
    }

    /**
     * 获取接收视频推送
     */
    public function getRevVideo(){
        if (isset($this->_receive['MediaId'])){
            return array(
                'mediaid'=>$this->_receive['MediaId'],
                'thumbmediaid'=>$this->_receive['ThumbMediaId']
            );
        } else
            return false;
    }

    /**
     * 获取接收TICKET
     */
    public function getRevTicket(){
        if (isset($this->_receive['Ticket'])){
            return $this->_receive['Ticket'];
        } else
            return false;
    }

    /**
     * 获取二维码的场景值
     */
    public function getRevSceneId (){
        if (isset($this->_receive['EventKey'])){
            return str_replace('qrscene_','',$this->_receive['EventKey']);
        } else{
            return false;
        }
    }

    /**
     * 获取主动推送的消息ID
     * 经过验证，这个和普通的消息MsgId不一样
     * 当Event为 MASSSENDJOBFINISH 或 TEMPLATESENDJOBFINISH
     */
    public function getRevTplMsgID(){
        if (isset($this->_receive['MsgID'])){
            return $this->_receive['MsgID'];
        } else
            return false;
    }

    /**
     * 获取模板消息发送状态
     */
    public function getRevStatus(){
        if (isset($this->_receive['Status'])){
            return $this->_receive['Status'];
        } else
            return false;
    }

    /**
     * 获取群发或模板消息发送结果
     * 当Event为 MASSSENDJOBFINISH 或 TEMPLATESENDJOBFINISH，即高级群发/模板消息
     */
    public function getRevResult(){
        if (isset($this->_receive['Status'])) //发送是否成功，具体的返回值请参考 高级群发/模板消息 的事件推送说明
            $array['Status'] = $this->_receive['Status'];
        if (isset($this->_receive['MsgID'])) //发送的消息id
            $array['MsgID'] = $this->_receive['MsgID'];

        //以下仅当群发消息时才会有的事件内容
        if (isset($this->_receive['TotalCount']))     //分组或openid列表内粉丝数量
            $array['TotalCount'] = $this->_receive['TotalCount'];
        if (isset($this->_receive['FilterCount']))    //过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数
            $array['FilterCount'] = $this->_receive['FilterCount'];
        if (isset($this->_receive['SentCount']))     //发送成功的粉丝数
            $array['SentCount'] = $this->_receive['SentCount'];
        if (isset($this->_receive['ErrorCount']))    //发送失败的粉丝数
            $array['ErrorCount'] = $this->_receive['ErrorCount'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }


	/**
	 * 获取卡券事件推送 - 卡卷审核是否通过
	 * 当Event为 card_pass_check(审核通过) 或 card_not_pass_check(未通过)
	 * @return string|boolean  返回卡券ID
	 */
	public function getRevCardPass(){
	    if (isset($this->_receive['CardId']))
	        return $this->_receive['CardId'];
	    else
	        return false;
	}

	/**
	 * 获取卡券事件推送 - 领取卡券
	 * 当Event为 user_get_card(用户领取卡券)
	 * @return array|boolean
	 */
	public function getRevCardGet(){
	    if (isset($this->_receive['CardId']))     //卡券 ID
	        $array['CardId'] = $this->_receive['CardId'];
	    if (isset($this->_receive['IsGiveByFriend']))    //是否为转赠，1 代表是，0 代表否。
	        $array['IsGiveByFriend'] = $this->_receive['IsGiveByFriend'];
	    if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) //code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
	        $array['UserCardCode'] = $this->_receive['UserCardCode'];
	    if (isset($array) && count($array) > 0) {
	        return $array;
	    } else {
	        return false;
	    }
	}

	/**
	 * 获取卡券事件推送 - 删除卡券
	 * 当Event为 user_del_card(用户删除卡券)
	 * @return array|boolean
	 */
	public function getRevCardDel(){
	    if (isset($this->_receive['CardId']))     //卡券 ID
	        $array['CardId'] = $this->_receive['CardId'];
	    if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) //code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
	        $array['UserCardCode'] = $this->_receive['UserCardCode'];
	    if (isset($array) && count($array) > 0) {
	        return $array;
	    } else {
	        return false;
	    }
	}

    public static function xmlSafeStr($str)
    {
        return '<![CDATA['.preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$str).']]>';
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function data_to_xml($data) {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml    .=  "<$key>";
            $xml    .=  ( is_array($val) || is_object($val)) ? self::data_to_xml($val)  : self::xmlSafeStr($val);
            list($key, ) = explode(' ', $key);
            $xml    .=  "</$key>";
        }
        return $xml;
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public function xml_encode($data, $root='xml', $item='item', $attr='', $id='id', $encoding='utf-8') {
        if(is_array($attr)){
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr   = trim($attr);
        $attr   = empty($attr) ? '' : " {$attr}";
        $xml   = "<{$root}{$attr}>";
        $xml   .= self::data_to_xml($data, $item, $id);
        $xml   .= "</{$root}>";
        return $xml;
    }

    /**
     * 过滤文字回复\r\n换行符
     * @param string $text
     * @return string|mixed
     */
    private function _auto_text_filter($text) {
        if (!$this->_text_filter) return $text;
        return str_replace("\r\n", "\n", $text);
    }

    /**
     * 设置回复消息
     * Example: $obj->text('hello')->reply();
     * @param string $text
     */
    public function text($text='')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'MsgType'=>self::MSGTYPE_TEXT,
            'Content'=>$this->_auto_text_filter($text),
            'CreateTime'=>time(),
            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }
    /**
     * 设置回复消息
     * Example: $obj->image('media_id')->reply();
     * @param string $mediaid
     */
    public function image($mediaid='')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'MsgType'=>self::MSGTYPE_IMAGE,
            'Image'=>array('MediaId'=>$mediaid),
            'CreateTime'=>time(),
            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->voice('media_id')->reply();
     * @param string $mediaid
     */
    public function voice($mediaid='')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'MsgType'=>self::MSGTYPE_VOICE,
            'Voice'=>array('MediaId'=>$mediaid),
            'CreateTime'=>time(),
            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->video('media_id','title','description')->reply();
     * @param string $mediaid
     */
    public function video($mediaid='',$title='',$description='')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'MsgType'=>self::MSGTYPE_VIDEO,
            'Video'=>array(
                'MediaId'=>$mediaid,
                'Title'=>$title,
                'Description'=>$description
            ),
            'CreateTime'=>time(),
            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复音乐
     * @param string $title
     * @param string $desc
     * @param string $musicurl
     * @param string $hgmusicurl
     * @param string $thumbmediaid 音乐图片缩略图的媒体id，非必须
     */
    public function music($title,$desc,$musicurl,$hgmusicurl='',$thumbmediaid='') {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'CreateTime'=>time(),
            'MsgType'=>self::MSGTYPE_MUSIC,
            'Music'=>array(
                'Title'=>$title,
                'Description'=>$desc,
                'MusicUrl'=>$musicurl,
                'HQMusicUrl'=>$hgmusicurl
            ),
            'FuncFlag'=>$FuncFlag
        );
        if ($thumbmediaid) {
            $msg['Music']['ThumbMediaId'] = $thumbmediaid;
        }
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复图文
     * @param array $newsData
     * 数组结构:
     *  array(
     *  	"0"=>array(
     *  		'Title'=>'msg title',
     *  		'Description'=>'summary text',
     *  		'PicUrl'=>'http://www.domain.com/1.jpg',
     *  		'Url'=>'http://www.domain.com/1.html'
     *  	),
     *  	"1"=>....
     *  )
     */
    public function news($newsData=array())
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $count = count($newsData);

        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'MsgType'=>self::MSGTYPE_NEWS,
            'CreateTime'=>time(),
            'ArticleCount'=>$count,
            'Articles'=>$newsData,
            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }

    /**
     *
     * 回复微信服务器, 此函数支持链式操作
     * Example: $this->text('msg tips')->reply();
     * @param string $msg 要发送的信息, 默认取$this->_msg
     * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
     */
    public function reply($msg=array(),$return = false)
    {
        if (empty($msg)) {
            if (empty($this->_msg))   //防止不先设置回复内容，直接调用reply方法导致异常
                return false;
            $msg = $this->_msg;
        }
        $xmldata=  $this->xml_encode($msg);
        $this->log($xmldata);
        if ($this->encrypt_type == 'aes') { //如果来源消息为加密方式
            $pc = new Prpcrypt($this->encodingAesKey);
            $array = $pc->encrypt($xmldata, $this->appid);
            $ret = $array[0];
            if ($ret != 0) {
                $this->log('encrypt err!');
                return false;
            }
            $timestamp = time();
            $nonce = rand(77,999)*rand(605,888)*rand(11,99);
            $encrypt = $array[1];
            $tmpArr = array($this->token, $timestamp, $nonce,$encrypt);//比普通公众平台多了一个加密的密文
            sort($tmpArr, SORT_STRING);
            $signature = implode($tmpArr);
            $signature = sha1($signature);
            $xmldata = $this->generate($encrypt, $signature, $timestamp, $nonce);
            $this->log($xmldata);
        }
        if ($return)
            return $xmldata;
        else
            echo $xmldata;
    }

    /**
     * xml格式加密，仅请求为加密方式时再用
     */
    private function generate($encrypt, $signature, $timestamp, $nonce)
    {
        //格式化加密信息
        $format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }

    /**
     * GET 请求
     * @param string $url
     */
    private function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    private function http_post($url,$param,$post_file=false,$boundary=''){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        if($boundary){
            curl_setopt($oCurl,CURLOPT_HTTPHEADER,array(
                "Expect: 100-continue",
                "Content-Type: multipart/form-data; boundary={$boundary}",
            ));
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * 设置缓存，按需重载
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename,$value,$expired){
        return S($cachename,$value,$expired);
    }

    /**
     * 获取缓存，按需重载
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename){
        return S($cachename);
    }

    /**
     * 清除缓存，按需重载
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename){
        return S($cachename,null);
    }

    /**
     * 获取access_token
     * @param string $appid 如在类初始化时已提供，则可为空
     * @param string $appsecret 如在类初始化时已提供，则可为空
     * @param string $token 手动指定access_token，非必要情况不建议用
     */
    public function checkAuth($appid='',$appsecret='',$token=''){
        if (!$appid) {
            $appid = $this->appid;
        }
        if ($token) { //手动指定token，优先使用
            $this->access_token=$token;
            return $this->access_token;
        }

        $authname = 'weiq_wechat_access_token_'.$appid;
        if (!$appsecret && $rs = $this->getCache($authname))  {
            $this->access_token = $rs;
            return $rs;
        }

        if (!$appsecret) {
            $appsecret = $this->appsecret;
        }

        $result = $this->http_get(self::API_URL_PREFIX.self::AUTH_URL.'appid='.$appid.'&secret='.$appsecret);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->access_token = $json['access_token'];
            $expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
            $this->setCache($authname,$this->access_token,$expire);
            return $this->access_token;
        }else {
            return false;
        }
    }

    /**
     * 删除验证数据
     * @param string $appid
     */
    public function resetAuth($appid=''){
        if (!$appid) $appid = $this->appid;
        $this->access_token = '';
        $authname = 'weiq_wechat_access_token_'.$appid;
        $this->removeCache($authname);
        return true;
    }
    
	/**
	 * 删除JSAPI授权TICKET
	 * @param string $appid 用于多个appid时使用
	 */
	public function resetJsTicket($appid=''){
		if (!$appid) $appid = $this->appid;
		$this->jsapi_ticket = '';
		$authname = 'wechat_jsapi_ticket'.$appid;
		$this->removeCache($authname);
		return true;
	}
	/**
	 * 获取JSAPI授权TICKET
	 * @param string $appid 用于多个appid时使用,可空
	 * @param string $jsapi_ticket 手动指定jsapi_ticket，非必要情况不建议用
	 */
	public function getJsTicket($appid='',$jsapi_ticket=''){
		if (!$this->access_token && !$this->checkAuth()) return false;
		if (!$appid) $appid = $this->appid;
		if ($jsapi_ticket) { //手动指定token，优先使用
		    $this->jsapi_ticket = $jsapi_ticket;
		    return $this->jsapi_ticket;
		}
		$authname = 'wechat_jsapi_ticket'.$appid;
		if ($rs = $this->getCache($authname))  {
			$this->jsapi_ticket = $rs;
			return $rs;
		}
		$result = $this->http_get(self::API_URL_PREFIX.self::GET_TICKET_URL.'access_token='.$this->access_token.'&type=jsapi');
		if ($result)
		{
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			$this->jsapi_ticket = $json['ticket'];
			$expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
			$this->setCache($authname,$this->jsapi_ticket,$expire);
			return $this->jsapi_ticket;
		}
		return false;
	}


	/**
	 * 获取JsApi使用签名
	 * @param string $url 网页的URL，自动处理#及其后面部分
	 * @param string $timestamp 当前时间戳 (为空则自动生成)
	 * @param string $noncestr 随机串 (为空则自动生成)
	 * @param string $appid 用于多个appid时使用,可空
	 * @return array|bool 返回签名字串
	 */
	public function getJsSign($url, $timestamp=0, $noncestr='', $appid=''){
	    if (!$this->jsapi_ticket && !$this->getJsTicket($appid) || !$url) return false;
	    if (!$timestamp)
	        $timestamp = time();
	    if (!$noncestr)
	        $noncestr = $this->generateNonceStr();
	    $ret = strpos($url,'#');
	    if ($ret)
	        $url = substr($url,0,$ret);
	    $url = trim($url);
	    if (empty($url))
	        return false;
	    $arrdata = array("timestamp" => $timestamp, "noncestr" => $noncestr, "url" => $url, "jsapi_ticket" => $this->jsapi_ticket);
	    $sign = $this->getSignature($arrdata);
	    if (!$sign)
	        return false;
	    $signPackage = array(
	            "appid"     => $this->appid,
	            "noncestr"  => $noncestr,
	            "timestamp" => $timestamp,
	            "url"       => $url,
	            "signature" => $sign
	    );
	    return $signPackage;
    }

    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    static function json_encode($arr) {
        $parts = array ();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys ( $arr );
        $max_length = count ( $arr ) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ( $arr as $key => $value ) {
            if (is_array ( $value )) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::json_encode ( $value ); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
            } else {
                $str = '';
                if (! $is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (!is_string ( $value ) && is_numeric ( $value ) && $value<2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes ( $value ) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode ( ',', $parts );
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

    /**
     * 获取签名
     * @param array $arrdata 签名数组
     * @param string $method 签名方法
     * @return boolean|string 签名值
     */
    public function getSignature($arrdata,$method="sha1") {
        if (!function_exists($method)) return false;
        ksort($arrdata);
        $paramstring = "";
        foreach($arrdata as $key => $value)
        {
            if(strlen($paramstring) == 0)
                $paramstring .= $key . "=" . $value;
            else
                $paramstring .= "&" . $key . "=" . $value;
        }
        $Sign = $method($paramstring);
        return $Sign;
    }

    /**
     * 生成随机字串
     * @param number $length 长度，默认为16，最长为32字节
     * @return string
     */
    public function generateNonceStr($length=16){
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for($i = 0; $i < $length; $i++)
        {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    /**
     * 获取微信服务器IP地址列表
     * @return array('127.0.0.1','127.0.0.1')
     */
    public function getServerIp(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::CALLBACKSERVER_GET_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json['ip_list'];
        }
        return false;
    }

    /**
     * 创建菜单(认证后的订阅号可用)
     * @param array $data 菜单数组数据
     * example:
     * 	array (
     * 	    'button' => array (
     * 	      0 => array (
     * 	        'name' => '扫码',
     * 	        'sub_button' => array (
     * 	            0 => array (
     * 	              'type' => 'scancode_waitmsg',
     * 	              'name' => '扫码带提示',
     * 	              'key' => 'rselfmenu_0_0',
     * 	            ),
     * 	            1 => array (
     * 	              'type' => 'scancode_push',
     * 	              'name' => '扫码推事件',
     * 	              'key' => 'rselfmenu_0_1',
     * 	            ),
     * 	        ),
     * 	      ),
     * 	      1 => array (
     * 	        'name' => '发图',
     * 	        'sub_button' => array (
     * 	            0 => array (
     * 	              'type' => 'pic_sysphoto',
     * 	              'name' => '系统拍照发图',
     * 	              'key' => 'rselfmenu_1_0',
     * 	            ),
     * 	            1 => array (
     * 	              'type' => 'pic_photo_or_album',
     * 	              'name' => '拍照或者相册发图',
     * 	              'key' => 'rselfmenu_1_1',
     * 	            )
     * 	        ),
     * 	      ),
     * 	      2 => array (
     * 	        'type' => 'location_select',
     * 	        'name' => '发送位置',
     * 	        'key' => 'rselfmenu_2_0'
     * 	      ),
     * 	    ),
     * 	)
     * type可以选择为以下几种，其中5-8除了收到菜单事件以外，还会单独收到对应类型的信息。
     * 1、click：点击推事件
     * 2、view：跳转URL
     * 3、scancode_push：扫码推事件
     * 4、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框
     * 5、pic_sysphoto：弹出系统拍照发图
     * 6、pic_photo_or_album：弹出拍照或者相册发图
     * 7、pic_weixin：弹出微信相册发图器
     * 8、location_select：弹出地理位置选择器
     */
    public function createMenu($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MENU_CREATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 获取菜单(认证后的订阅号可用)
     * @return array('menu'=>array(....s))
     */
    public function getMenu(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::MENU_GET_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 删除菜单(认证后的订阅号可用)
     * @return boolean
     */
    public function deleteMenu(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::MENU_DELETE_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 上传临时素材，有效期为3天(认证后的订阅号可用)
     * 注意：上传大文件时可能需要先调用 set_time_limit(0) 避免超时
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * 注意：临时素材的media_id是可复用的！
     * @param array $data {"media":'@Path\filename.jpg'}
     * @param type 类型：图片:image 语音:voice 视频:video 缩略图:thumb
     * @return boolean|array
     */
    public function uploadMedia($data, $type){
        if (!$this->access_token && !$this->checkAuth()) return false;
        //原先的上传多媒体文件接口使用 self::UPLOAD_MEDIA_URL 前缀
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_UPLOAD_URL.'access_token='.$this->access_token.'&type='.$type,$data,true);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取临时素材(认证后的订阅号可用)
     * @param string $media_id 媒体文件id
     * @param boolean $is_video 是否为视频文件，默认为否
     * @return raw data
     */
    public function getMedia($media_id,$is_video=false){
        if (!$this->access_token && !$this->checkAuth()) return false;
        //原先的上传多媒体文件接口使用 self::UPLOAD_MEDIA_URL 前缀
        //如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        $url_prefix = $is_video?str_replace('https','http',self::API_URL_PREFIX):self::API_URL_PREFIX;
        $result = $this->http_get($url_prefix.self::MEDIA_GET_URL.'access_token='.$this->access_token.'&media_id='.$media_id);
        if ($result)
        {
            if (is_string($result)) {
                $json = json_decode($result,true);
                if (isset($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }
            }
            return $result;
        }
        return false;
    }


    /**
     * 上传永久素材(认证后的订阅号可用)
     * 新增的永久素材也可以在公众平台官网素材管理模块中看到
     * 注意：上传大文件时可能需要先调用 set_time_limit(0) 避免超时
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * @param array $data {"media":'@Path\filename.jpg'}
     * @param type 类型：图片:image 语音:voice 视频:video 缩略图:thumb
     * @param boolean $is_video 是否为视频文件，默认为否
     * @param array $video_info 视频信息数组，非视频素材不需要提供 array('title'=>'视频标题','introduction'=>'描述')
     * @return boolean|array
     */
    public function uploadForeverMedia($data, $type,$is_video=false,$video_info=array()){
        if (!$this->access_token && !$this->checkAuth()) return false;
        //#TODO 暂不确定此接口是否需要让视频文件走http协议
        //如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        //$url_prefix = $is_video?str_replace('https','http',self::API_URL_PREFIX):self::API_URL_PREFIX;
        //当上传视频文件时，附加视频文件信息
        if ($is_video) $data['description'] = self::json_encode($video_info);
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_UPLOAD_URL.'access_token='.$this->access_token.'&type='.$type,$data,true);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = ErrCode::getErrText($json['errcode']);
                return false;
            }
            return $json;
        }
        return false;
    }
    public function uploadImage($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_IMG_UPLOAD.'access_token='.$this->access_token,$data,true);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = ErrCode::getErrText($json['errcode']);
                return false;
            }
            return $json;
        }
        return false;
    }

    //二进制文件流上传永久图片
    public function uploadForeverIMG($data,$boundary,$type='image'){
        if (!$this->access_token && !$this->checkAuth()) return false;

        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_UPLOAD_URL.'access_token='.$this->access_token.'&type='.$type,$data,true,$boundary);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = ErrCode::getErrText($json['errcode']);
                return false;
            }
            return $json;
        }
        return false;
    }
    //二进制文件流上传文章图片
    public function uploadIMG($data,$boundary){
        if (!$this->access_token && !$this->checkAuth()) return false;

        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_IMG_UPLOAD.'access_token='.$this->access_token,$data,true,$boundary);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = ErrCode::getErrText($json['errcode']);
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 上传永久图文素材(认证后的订阅号可用)
     * 新增的永久素材也可以在公众平台官网素材管理模块中看到
     * @param array $data 消息结构{"articles":[{...}]}
     * @return boolean|array
     */
    public function uploadForeverArticles($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        foreach ($data as &$item){
            foreach ($item as $k=>$v){
                //将""转化为单引号，转化为html实体后编码
                if($k =='content'){
                    $item[$k] = urlencode(htmlspecialchars(str_replace("\"","'",$item[$k])));
                }else{
                    $item[$k] = urlencode($v);
                }
            }
        }

        //转化为json
        $datas='{"articles":[';
        foreach($data as $key => $value){
            $datas = $datas . '{';
            $datas = $datas . "\"thumb_media_id\":"."\"".$value['thumb_media_id']."\",";
            $datas = $datas . "\"author\":"."\"".$value['author']."\",";
            $datas = $datas . "\"title\":"."\"".$value['title']."\",";
            $datas = $datas . "\"content_source_url\":"."\"".$value['content_source_url']."\",";
            $datas = $datas . "\"content\":"."\"".$value['content']."\",";
            $datas = $datas . "\"digest\":"."\"".$value['digest']."\",";
            $datas = $datas . "\"show_cover_pic\":"."\"".$value['show_cover_pic']."\"";
            $datas = $datas . '},';
        }
        $datas = trim($datas,',');
        $datas = $datas . ']}';
        //上传之前对内容进行urldecode解码，将html实体转成html标签
        $datas= urldecode($datas);
        $datas= htmlspecialchars_decode($datas);

        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_NEWS_UPLOAD_URL.'access_token='.$this->access_token,$datas);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = ErrCode::getErrText($json['errcode']);
                return false;
            }
            return $json;
        }
        return false;
    }
    //单
    public function uploadForeverArticlesOne($item){
        if (!$this->access_token && !$this->checkAuth()) return false;

        foreach ($item as $k=>$v){
            //将""转化为单引号，转化为html实体后编码
            if($k =='content'){
                //外链图片替换，以流文件HTTP协议上传到永久素材
                preg_match_all('/<img.*?src=[\'|\"](\S*?(\.png|\.jpg|\.gif))[\'|\"].*?[\/]?>/i',$v,$match);
                $num = isset($match[1])?count($match[1]):0;
                if($num) {
                    $arr = array();
                    foreach ($match[1] as $vv) {
                        $boundary = "---------------------" . md5(mt_rand() . microtime()); //
                        $file_data = self::updateInternetImg($vv, $boundary); //获得http信息流
                        $res = self::uploadIMG($file_data,$boundary);  //上传得微信图片url
                        $arr[] = $res['url'];
                    }
                    $item[$k] = str_replace($match[1], $arr, $item[$k]);
                }
                $item[$k] = urlencode(htmlspecialchars(str_replace("\"","'",$item[$k])));
            }else{
                $item[$k] = urlencode($v);
            }
        }


        //转化为json
        $datas='{"articles":[';

        $datas .= '{';
        $datas .= "\"thumb_media_id\":"."\"".$item['thumb_media_id']."\",";
        $datas .= "\"author\":"."\"".$item['author']."\",";
        $datas .= "\"title\":"."\"".$item['title']."\",";
        $datas .= "\"content_source_url\":"."\"".$item['content_source_url']."\",";
        $datas .= "\"content\":"."\"".$item['content']."\",";
        $datas .= "\"digest\":"."\"".$item['digest']."\",";
        $datas .= "\"show_cover_pic\":"."\"".$item['show_cover_pic']."\"";
        $datas .= '}';

        $datas .= ']}';
        //上传之前对内容进行urldecode解码，将html实体转成html标签
        $datas= urldecode($datas);
        $datas= htmlspecialchars_decode($datas);

        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_NEWS_UPLOAD_URL.'access_token='.$this->access_token,$datas);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = ErrCode::getErrText($json['errcode']);
                return false;
            }
            return $json;
        }
        return false;
    }

    //远程图片转HTTP $k name参数
    function updateInternetImg($file,$boundary,$k='media') {

        // invalid characters for "name" and "filename"
        static $disallow = array("\0", "\"", "\r", "\n");

        // build file parameters
        $data = file_get_contents($file);
        $v = call_user_func("end", explode('/', $file));
        $type = call_user_func("end", explode('.', $v));
        $v = str_replace($disallow, "_", $v);
        $body[] = implode("\r\n", array(
            "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
            "Content-Type: image/".$type,
            "",
            $data,
        ));

        // add boundary for each parameters
        array_walk($body, function (&$part) use ($boundary) {
            $part = "--{$boundary}\r\n{$part}";
        });

        // add final boundary
        $body[] = "--{$boundary}--";
        $body[] = "";

        // set options
        return implode("\r\n", $body);
    }


    /**
     * 修改永久图文素材(认证后的订阅号可用)
     * 永久素材也可以在公众平台官网素材管理模块中看到
     * @param string $media_id 图文素材id
     * @param array $data 消息结构{"articles":[{...}]}
     * @param int $index 更新的文章在图文素材的位置，第一篇为0，仅多图文使用
     * @return boolean|array
     */
    public function updateForeverArticles($media_id,$data,$index=0){
        if (!$this->access_token && !$this->checkAuth()) return false;
        if (!isset($data['media_id'])) $data['media_id'] = $media_id;
        if (!isset($data['index'])) $data['index'] = $index;

        foreach ($data as $k=>&$v){
            //将""转化为单引号，转化为html实体后编码
            if($k =='content'){
                $data[$k] = urlencode(htmlspecialchars(str_replace("\"","'",$v)));
            }else{
                $data[$k] = urlencode($v);
            }
        }
        //转化为json
        $datas='{"articles":';
        $datas = $datas . '{';
        $datas = $datas . "\"thumb_media_id\":"."\"".$data['thumb_media_id']."\",";
        $datas = $datas . "\"author\":"."\"".$data['author']."\",";
        $datas = $datas . "\"title\":"."\"".$data['title']."\",";
        $datas = $datas . "\"content_source_url\":"."\"".$data['content_source_url']."\",";
        $datas = $datas . "\"content\":"."\"".$data['content']."\",";
        $datas = $datas . "\"digest\":"."\"".$data['digest']."\",";
        $datas = $datas . "\"show_cover_pic\":"."\"".$data['show_cover_pic']."\"";
        $datas = $datas . '},';
        $datas = $datas . '"media_id":"'.$data['media_id'].'",';
        $datas = $datas . '"index":"'.$data['index'].'"}';
        //上传之前对内容进行urldecode解码，将html实体转成html标签
        $datas= urldecode($datas);
        $datas= htmlspecialchars_decode($datas);
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_NEWS_UPDATE_URL.'access_token='.$this->access_token,$datas);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = ErrCode::getErrText($json['errcode']);
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取永久素材(认证后的订阅号可用)
     * 返回图文消息数组或二进制数据，失败返回false
     * @param string $media_id 媒体文件id
     * @param boolean $is_video 是否为视频文件，默认为否
     * @return boolean|array|raw data
     */
    public function getForeverMedia($media_id,$is_video=false){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array('media_id' => $media_id);
        //#TODO 暂不确定此接口是否需要让视频文件走http协议
        //如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        //$url_prefix = $is_video?str_replace('https','http',self::API_URL_PREFIX):self::API_URL_PREFIX;
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_GET_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            if (is_string($result)) {
                $json = json_decode($result,true);
                if (isset($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }
                return $json;
            }
            return $result;
        }
        return false;
    }

    /**
     * 删除永久素材(认证后的订阅号可用)
     * @param string $media_id 媒体文件id
     * @return boolean
     */
    public function delForeverMedia($media_id){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array('media_id' => $media_id);
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_DEL_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 获取永久素材列表(认证后的订阅号可用)
     * @param string $type 素材的类型,图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int $offset 全部素材的偏移位置，0表示从第一个素材
     * @param int $count 返回素材的数量，取值在1到20之间
     * @return boolean|array
     * 返回数组格式:
     * array(
     *  'total_count'=>0, //该类型的素材的总数
     *  'item_count'=>0,  //本次调用获取的素材的数量
     *  'item'=>array()   //素材列表数组，内容定义请参考官方文档
     * )
     */
    public function getForeverList($type,$offset,$count){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            'type' => $type,
            'offset' => $offset,
            'count' => $count,
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_BATCHGET_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取永久素材总数(认证后的订阅号可用)
     * @return boolean|array
     * 返回数组格式:
     * array(
     *  'voice_count'=>0, //语音总数量
     *  'video_count'=>0, //视频总数量
     *  'image_count'=>0, //图片总数量
     *  'news_count'=>0   //图文总数量
     * )
     */
    public function getForeverCount(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::MEDIA_FOREVER_COUNT_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 上传图文消息素材，用于群发(认证后的订阅号可用)
     * @param array $data 消息结构{"articles":[{...}]}
     * @return boolean|array
     */
    public function uploadArticles($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_UPLOADNEWS_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 上传视频素材(认证后的订阅号可用)
     * @param array $data 消息结构
     * {
     *     "media_id"=>"",     //通过上传媒体接口得到的MediaId
     *     "title"=>"TITLE",    //视频标题
     *     "description"=>"Description"        //视频描述
     * }
     * @return boolean|array
     * {
     *     "type":"video",
     *     "media_id":"mediaid",
     *     "created_at":1398848981
     *  }
     */
    public function uploadMpVideo($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::UPLOAD_MEDIA_URL.self::MEDIA_VIDEO_UPLOAD.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 根据OpenID列表群发图文消息(订阅号不可用)
     * 	注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * {
     *     "touser"=>array(
     *         "OPENID1",
     *         "OPENID2"
     *     ),
     *      "msgtype"=>"mpvideo",
     *      // 在下面5种类型中选择对应的参数内容
     *      // mpnews | voice | image | mpvideo => array( "media_id"=>"MediaId")
     *      // text => array ( "content" => "hello")
     * }
     * @return boolean|array
     */
    public function sendMassMessage($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MASS_SEND_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 根据群组id群发图文消息(认证后的订阅号可用)
     * 	注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * {
     *     "filter"=>array(
     *         "is_to_all"=>False,     //是否群发给所有用户.True不用分组id，False需填写分组id
     *         "group_id"=>"2"     //群发的分组id
     *     ),
     *      "msgtype"=>"mpvideo",
     *      // 在下面5种类型中选择对应的参数内容
     *      // mpnews | voice | image | mpvideo => array( "media_id"=>"MediaId")
     *      // text => array ( "content" => "hello")
     * }
     * @return boolean|array
     */
    public function sendGroupMassMessage($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MASS_SEND_GROUP_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 删除群发图文消息(认证后的订阅号可用)
     * @param int $msg_id 消息id
     * @return boolean|array
     */
    public function deleteMassMessage($msg_id){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MASS_DELETE_URL.'access_token='.$this->access_token,self::json_encode(array('msg_id'=>$msg_id)));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 高级群发消息, 预览群发消息(认证后的订阅号可用)
     * 	注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * {
     *     "touser"=>"OPENID",
     *      "msgtype"=>"mpvideo",
     *      // 在下面5种类型中选择对应的参数内容
     *      // mpnews | voice | image | mpvideo => array( "media_id"=>"MediaId")
     *      // text => array ( "content" => "hello")
     * }
     * @return boolean|array
     */
    public function previewMassMessage($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MASS_PREVIEW_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 查询群发消息发送状态(认证后的订阅号可用)
     * @param int $msg_id 消息id
     * @return boolean|array
     * {
     *     "msg_id":201053012,     //群发消息后返回的消息id
     *     "msg_status":"SEND_SUCCESS" //消息发送后的状态，SENDING表示正在发送 SEND_SUCCESS表示发送成功
     * }
     */
    public function queryMassMessage($msg_id){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MASS_QUERY_URL.'access_token='.$this->access_token,self::json_encode(array('msg_id'=>$msg_id)));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    //重置所有接口
    public function clear_all(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            'appid' => $this->appid,
        );
        $result = $this->http_post(self::API_URL_PREFIX.'/clear_quota?access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode']) && $json['errcode']==0 && $json['errmsg']=='ok') {
                return $json;
            }else{
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
        }
        return false;
    }

	/**
	 * 创建二维码ticket
	 * @param int|string $scene_id 自定义追踪id,临时二维码只能用数值型
	 * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)；2:永久二维码(此时expire参数无效)
	 * @param int $expire 临时二维码有效期，最大为1800秒
	 * @return array('ticket'=>'qrcode字串','expire_seconds'=>1800,'url'=>'二维码图片解析后的地址')
	 */
	public function getQRCode($scene_id,$type=0,$expire=1800){
		if (!$this->access_token && !$this->checkAuth()) return false;
		$type = ($type && is_string($scene_id))?2:$type;
		$data = array(
			'action_name'=>$type?($type == 2?"QR_LIMIT_STR_SCENE":"QR_LIMIT_SCENE"):"QR_SCENE",
			'expire_seconds'=>$expire,
			'action_info'=>array('scene'=>($type == 2?array('scene_str'=>$scene_id):array('scene_id'=>$scene_id)))
		);
		if ($type == 1) {
			unset($data['expire_seconds']);
		}
		$result = $this->http_post(self::API_URL_PREFIX.self::QRCODE_CREATE_URL.'access_token='.$this->access_token,self::json_encode($data));
		if ($result)
		{
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}

	/**
	 * 获取二维码图片
	 * @param string $ticket 传入由getQRCode方法生成的ticket参数
	 * @return string url 返回http地址
	 */
	public function getQRUrl($ticket) {
		return self::QRCODE_IMG_URL.urlencode($ticket);
	}
	/**
	 * 长链接转短链接接口
	 * @param string $long_url 传入要转换的长url
	 * @return boolean|string url 成功则返回转换后的短url
	 */
	public function getShortUrl($long_url){
	    if (!$this->access_token && !$this->checkAuth()) return false;
	    $data = array(
            'action'=>'long2short',
            'long_url'=>$long_url
	    );
	    $result = $this->http_post(self::API_URL_PREFIX.self::SHORT_URL.'access_token='.$this->access_token,self::json_encode($data));
	    if ($result)
	    {
	        $json = json_decode($result,true);
	        if (!$json || !empty($json['errcode'])) {
	            $this->errCode = $json['errcode'];
	            $this->errMsg = $json['errmsg'];
	            return false;
	        }
	        return $json['short_url'];
	    }
	    return false;
	}
	/**
	 * 获取关注者详细信息
	 * @param string $openid
	 * @return array {subscribe,openid,nickname,sex,city,province,country,language,headimgurl,subscribe_time,[unionid]}
	 * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
	 */
	public function getUserInfo($openid){
		if (!$this->access_token && !$this->checkAuth()) return false;
		$result = $this->http_get(self::API_URL_PREFIX.self::USER_INFO_URL.'access_token='.$this->access_token.'&openid='.$openid);
		if ($result)
		{
			$json = json_decode($result,true);
			if (isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
	/**
	 * oauth 授权跳转接口
	 * @param string $callback 回调URI
	 * @return string
	 */
	public function getOauthRedirect($callback,$state='',$scope='snsapi_base'){
		return self::OAUTH_PREFIX.self::OAUTH_AUTHORIZE_URL.'appid='.$this->appid.'&redirect_uri='.urlencode($callback).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
	}
	/**
	 * 通过code获取Access Token
	 * @return array {access_token,expires_in,refresh_token,openid,scope}
	 */
	public function getOauthAccessToken(){
		$code = isset($_GET['code'])?$_GET['code']:'';
		if (!$code) return false;
		$result = $this->http_get(self::API_BASE_URL_PREFIX.self::OAUTH_TOKEN_URL.'appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$code.'&grant_type=authorization_code');
		if ($result)
		{
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			$this->user_token = $json['access_token'];
			return $json;
		}
		return false;
	}
	/**
	 * 刷新access token并续期
	 * @param string $refresh_token
	 * @return boolean|mixed
	 */
	public function getOauthRefreshToken($refresh_token){
		$result = $this->http_get(self::API_BASE_URL_PREFIX.self::OAUTH_REFRESH_URL.'appid='.$this->appid.'&grant_type=refresh_token&refresh_token='.$refresh_token);
		if ($result)
		{
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			$this->user_token = $json['access_token'];
			return $json;
		}
		return false;
	}
	/**
	 * 获取授权后的用户资料
	 * @param string $access_token
	 * @param string $openid
	 * @return array {openid,nickname,sex,province,city,country,headimgurl,privilege,[unionid]}
	 * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
	 */
	public function getOauthUserinfo($access_token,$openid){
		$result = $this->http_get(self::API_BASE_URL_PREFIX.self::OAUTH_USERINFO_URL.'access_token='.$access_token.'&openid='.$openid);
		if ($result)
		{
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
	/**
	 * 检验授权凭证是否有效
	 * @param string $access_token
	 * @param string $openid
	 * @return boolean 是否有效
	 */
	public function getOauthAuth($access_token,$openid){
	    $result = $this->http_get(self::API_BASE_URL_PREFIX.self::OAUTH_AUTH_URL.'access_token='.$access_token.'&openid='.$openid);
	    if ($result)
	    {
	        $json = json_decode($result,true);
	        if (!$json || !empty($json['errcode'])) {
	            $this->errCode = $json['errcode'];
	            $this->errMsg = $json['errmsg'];
	            return false;
	        } else
	          if ($json['errcode']==0) return true;
	    }
	    return false;
	}
}