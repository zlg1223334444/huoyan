<?php
/**
 * PHP SDK for weibo.com (using OAuth2) 
 * 
 * @author Elmer Zhang <freeboy6716@gmail.com>
 */

/**
 * @ignore
 */
class OAuthException extends Exception {
	// pass
}


/**
 * 新浪微博 OAuth 认证类(OAuth2)
 *
 * 授权机制说明请大家参考微博开放平台文档：{@link http://open.weibo.com/wiki/Oauth2}
 *
 * @package sae
 * @author Elmer Zhang
 * @version 1.0
 */
class SaeTOAuthV2 {
	/**
	 * @ignore
	 */
	public $client_id;
	/**
	 * @ignore
	 */
	public $client_secret;
	/**
	 * @ignore
	 */
	public $access_token;
	/**
	 * @ignore
	 */
	public $refresh_token;
	/**
	 * Contains the last HTTP status code returned. 
	 *
	 * @ignore
	 */
	public $http_code;
	/**
	 * Contains the last API call.
	 *
	 * @ignore
	 */
	public $url;
	/**
	 * Set up the API root URL.
	 *
	 * @ignore
	 */
	public $host = "https://api.weibo.com/2/";
    /**
     * 商业接口的host
     * @var String
     */
    public $cHost = 'https://c.api.weibo.com/2/';
	/**
	 * Set timeout default.
	 *
	 * @ignore
	 */
	public $timeout = 30;
	/**
	 * Set connect timeout.
	 *
	 * @ignore
	 */
	public $connecttimeout = 30;
	/**
	 * Verify SSL Cert.
	 *
	 * @ignore
	 */
	public $ssl_verifypeer = FALSE;
	/**
	 * Respons format.
	 *
	 * @ignore
	 */
	public $format = 'json';
	/**
	 * Decode returned json data.
	 *
	 * @ignore
	 */
	public $decode_json = TRUE;
	/**
	 * Contains the last HTTP headers returned.
	 *
	 * @ignore
	 */
	public $http_info;
	/**
	 * Set the useragnet.
	 *
	 * @ignore
	 */
	public $useragent = 'Sae T OAuth2 v0.1';

	/**
	 * print the debug info
	 *
	 * @ignore
	 */
	public $debug = FALSE;

	/**
	 * boundary of multipart
	 * @ignore
	 */
	public static $boundary = '';

	/**
	 * Set API URLS
	 */
	/**
	 * @ignore
	 */
	function accessTokenURL()  { return 'https://api.weibo.com/oauth2/access_token'; }
	/**
	 * @ignore
	 */
	function authorizeURL()    { return 'https://api.weibo.com/oauth2/authorize'; }

	/**
	 * construct WeiboOAuth object
	 */
	function __construct($client_id, $client_secret, $access_token = NULL, $refresh_token = NULL) {
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->access_token = $access_token;
		$this->refresh_token = $refresh_token;
	}

	/**
	 * authorize接口
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/Oauth2/authorize Oauth2/authorize}
	 *
	 * @param string $url 授权后的回调地址,站外应用需与回调地址一致,站内应用需要填写canvas page的地址
	 * @param string $response_type 支持的值包括 code 和token 默认值为code
	 * @param string $state 用于保持请求和回调的状态。在回调时,会在Query Parameter中回传该参数
	 * @param string $display 授权页面类型 可选范围: 
	 *  - default		默认授权页面		
	 *  - mobile		支持html5的手机		
	 *  - popup			弹窗授权页		
	 *  - wap1.2		wap1.2页面		
	 *  - wap2.0		wap2.0页面		
	 *  - js			js-sdk 专用 授权页面是弹窗，返回结果为js-sdk回掉函数		
	 *  - apponweibo	站内应用专用,站内应用不传display参数,并且response_type为token时,默认使用改display.授权后不会返回access_token，只是输出js刷新站内应用父框架
	 * @return array
	 */
	function getAuthorizeURL( $url, $response_type = 'code', $state = NULL, $display = NULL ) {
		$params = array();
		$params['client_id'] = $this->client_id;
		$params['redirect_uri'] = $url;
		$params['response_type'] = $response_type;
		$params['state'] = $state;
		$params['display'] = $display;
		return $this->authorizeURL() . "?" . http_build_query($params);
	}

	/**
	 * access_token接口
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/OAuth2/access_token OAuth2/access_token}
	 *
	 * @param string $type 请求的类型,可以为:code, password, token
	 * @param array $keys 其他参数：
	 *  - 当$type为code时： array('code'=>..., 'redirect_uri'=>...)
	 *  - 当$type为password时： array('username'=>..., 'password'=>...)
	 *  - 当$type为token时： array('refresh_token'=>...)
	 * @return array
	 */
	function getAccessToken( $type = 'code', $keys ) {
		$params = array();
		$params['client_id'] = $this->client_id;
		$params['client_secret'] = $this->client_secret;
		if ( $type === 'token' ) {
			$params['grant_type'] = 'refresh_token';
			$params['refresh_token'] = $keys['refresh_token'];
		} elseif ( $type === 'code' ) {
			$params['grant_type'] = 'authorization_code';
			$params['code'] = $keys['code'];
			$params['redirect_uri'] = $keys['redirect_uri'];
		} elseif ( $type === 'password' ) {
			$params['grant_type'] = 'password';
			$params['username'] = $keys['username'];
			$params['password'] = $keys['password'];
		} else {
			throw new OAuthException("wrong auth type");
		}

		$response = $this->oAuthRequest($this->accessTokenURL(), 'POST', $params);
		$token = json_decode($response, true);
		if ( is_array($token) && !isset($token['error']) ) {
			$this->access_token = $token['access_token'];
			$this->refresh_token = $token['refresh_token'];
		} else {
			//throw new OAuthException("get access token failed." . $token['error']);
			redirect(U('index.php/media/sina/sinalist'));
		}
		return $token;
	}

	/**
	 * 解析 signed_request
	 *
	 * @param string $signed_request 应用框架在加载iframe时会通过向Canvas URL post的参数signed_request
	 *
	 * @return array
	 */
	function parseSignedRequest($signed_request) {
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
		$sig = self::base64decode($encoded_sig) ;
		$data = json_decode(self::base64decode($payload), true);
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') return '-1';
		$expected_sig = hash_hmac('sha256', $payload, $this->client_secret, true);
		return ($sig !== $expected_sig)? '-2':$data;
	}

	/**
	 * @ignore
	 */
	function base64decode($str) {
		return base64_decode(strtr($str.str_repeat('=', (4 - strlen($str) % 4)), '-_', '+/'));
	}

	/**
	 * 读取jssdk授权信息，用于和jssdk的同步登录
	 *
	 * @return array 成功返回array('access_token'=>'value', 'refresh_token'=>'value'); 失败返回false
	 */
	function getTokenFromJSSDK() {
		$key = "weibojs_" . $this->client_id;
		if ( isset($_COOKIE[$key]) && $cookie = $_COOKIE[$key] ) {
			parse_str($cookie, $token);
			if ( isset($token['access_token']) && isset($token['refresh_token']) ) {
				$this->access_token = $token['access_token'];
				$this->refresh_token = $token['refresh_token'];
				return $token;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * 从数组中读取access_token和refresh_token
	 * 常用于从Session或Cookie中读取token，或通过Session/Cookie中是否存有token判断登录状态。
	 *
	 * @param array $arr 存有access_token和secret_token的数组
	 * @return array 成功返回array('access_token'=>'value', 'refresh_token'=>'value'); 失败返回false
	 */
	function getTokenFromArray( $arr ) {
		if (isset($arr['access_token']) && $arr['access_token']) {
			$token = array();
			$this->access_token = $token['access_token'] = $arr['access_token'];
			if (isset($arr['refresh_token']) && $arr['refresh_token']) {
				$this->refresh_token = $token['refresh_token'] = $arr['refresh_token'];
			}

			return $token;
		} else {
			return false;
		}
	}
    /**
     * 将参数拼为字符串在调用接口，可以支持同名参数
     * @param type $url 
     * @param type $paramstr p1=v1&p2=v2&p2=v21...
     * @return type
     */
    function get2($url,$paramstr=""){
        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
			$url = "{$this->host}{$url}.{$this->format}";
		}
        $paramArr = explode("&", $paramstr);
        $hasSource = FALSE;
        foreach($paramArr as $fv){
            $vv = explode("=", $fv);
            if($vv['0'] == "soure"){
                $hasSource = TRUE;
                break;
            }
        }
        if(!$hasSource){
            if($paramstr){
                $paramstr .= '&source='.$this->client_id;
            }else{
                $paramstr = 'source='.$this->client_id;
            }
        }
        $url .= "?" . $paramstr;
        $response = $this->http($url, 'GET');
        
        if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
    }
	/**
	 * GET wrappwer for oAuthRequest.
	 *
	 * @return mixed
	 */
	function get($url, $parameters = array()) {
		$response = $this->oAuthRequest($url, 'GET', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	/**
	 * POST wreapper for oAuthRequest.
	 *
	 * @return mixed
	 */
	function post($url, $parameters = array(), $multi = false) {
		$response = $this->oAuthRequest($url, 'POST', $parameters, $multi );
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	/**
	 * DELTE wrapper for oAuthReqeust.
	 *
	 * @return mixed
	 */
	function delete($url, $parameters = array()) {
		$response = $this->oAuthRequest($url, 'DELETE', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}
    /**
	 * GET wrappwer for oAuthRequest.调试时使用
	 *
	 * @return mixed
	 */
	function getbug($url, $parameters = array()) {
		$response = $this->oAuthRequestbug($url, 'GET', $parameters);
        print_r($response);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}
    /**
	 * Format and sign an OAuth / API request 调试时使用
	 *
	 * @return string
	 * @ignore
	 */
	function oAuthRequestbug($url, $method, $parameters, $multi = false) {

		if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
			$url = "{$this->host}{$url}.{$this->format}";
		}
	
		switch ($method) {
			case 'GET':
				$url = $url . '?' . http_build_query($parameters);
				$url .= '&source='.$this->client_id;
				return $this->http($url, 'GET');
			default:
				$headers = array();
				if (!$multi && (is_array($parameters) || is_object($parameters)) ) {
					$body = http_build_query($parameters);
				} else {
					$body = self::build_http_query_multi($parameters);
					$headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
				}
				return $this->http($url, $method, $body, $headers);
		}
	}
	/**
	 * Format and sign an OAuth / API request
	 *
	 * @return string
	 * @ignore
	 */
	function oAuthRequest($url, $method, $parameters, $multi = false) {

		if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
			$url = "{$this->host}{$url}.{$this->format}";
		}
	
		switch ($method) {
			case 'GET':
				$url = $url . '?' . http_build_query($parameters);
				$url .= '&source='.$this->client_id;
				return $this->http($url, 'GET');
			default:
				$headers = array();
				if (!$multi && (is_array($parameters) || is_object($parameters)) ) {
					$body = http_build_query($parameters);
				} else {
					$body = self::build_http_query_multi($parameters);
					$headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
				}
				return $this->http($url, $method, $body, $headers);
		}
	}

	/**
	 * Make an HTTP request
	 *
	 * @return string API results
	 * @ignore
	 */
	function http($url, $method, $postfields = NULL, $headers = array()) {
		$this->http_info = array();
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_ENCODING, "");
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->postdata = $postfields;
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}

		if ( isset($this->access_token) && $this->access_token )
			$headers[] = "Authorization: OAuth2 ".$this->access_token;

		$headers[] = "API-RemoteIP: " . (isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:' ');
		curl_setopt($ci, CURLOPT_URL, $url );
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		$this->url = $url;

		if ($this->debug) {
			echo "=====post data======\r\n";
			var_dump($postfields);

			echo '=====info====='."\r\n";
			print_r( curl_getinfo($ci) );

			echo '=====$response====='."\r\n";
			print_r( $response );
		}
		curl_close ($ci);
		return $response;
	}

	/**
	 * Get the header info to store.
	 *
	 * @return int
	 * @ignore
	 */
	function getHeader($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}

	/**
	 * @ignore
	 */
	public static function build_http_query_multi($params) {
		if (!$params) return '';

		uksort($params, 'strcmp');

		$pairs = array();

		self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

		foreach ($params as $parameter => $value) {

			if( in_array($parameter, array('pic', 'image')) && $value{0} == '@' ) {
				$url = ltrim( $value, '@' );
				$content = file_get_contents( $url );
				$array = explode( '?', basename( $url ) );
				$filename = $array[0];

				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= "Content-Type: image/unknown\r\n\r\n";
				$multipartbody .= $content. "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
			}

		}

		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}
}


/**
 * 新浪微博操作类V2
 *
 * 使用前需要先手工调用saetv2.ex.class.php <br />
 *
 * @package sae
 * @author Easy Chen, Elmer Zhang,Lazypeople
 * @version 1.0
 */
class SaeTClientV2
{
	/**
	 * 构造函数
	 * 
	 * @access public
	 * @param mixed $akey 微博开放平台应用APP KEY
	 * @param mixed $skey 微博开放平台应用APP SECRET
	 * @param mixed $access_token OAuth认证返回的token
	 * @param mixed $refresh_token OAuth认证返回的token secret
	 * @return void
	 */
	function __construct( $akey, $skey, $access_token, $refresh_token = NULL)
	{
		$this->oauth = new SaeTOAuthV2( $akey, $skey, $access_token, $refresh_token );
	}
	/**
	 * 发送通知接口
	 * @param 接收通知的ID $uid
	 * @param 通知内容 $text
	 * @param 通知模版ID $tempId
	 * @param 链接地址 $action_url
	 * @return json
	 */
	function notificationSend( $source = 0, $access_token='', $uid = 0, $text = array(), $tempId = 0, $action_url = '')
	{
		$params = array();
		$params['source'] = $source;
		$params['access_token'] = $access_token;
		$params['uids'] = intval($uid);
		$params['tpl_id'] = intval($tempId);
		for ($i=1;$i<=count($text);$i++){
			$params['objects'.$i] = $text[($i-1)];
		}
		$params['action_url'] = $action_url;
		print_r($params); 
		return $this->oauth->post('notification/send', $params);
	}
	/**
     * 查询用户access_token的授权相关信息
     * @param 用户的access_token $access_token
     */
    function getTokeninfo( $access_token=''){
        $params = array();
        $params['access_token'] = $access_token;
        return $this->oauth->post('https://api.weibo.com/oauth2/get_token_info', $params);
    }
	/**
	 * 获取最新的公共微博消息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/public_timeline statuses/public_timeline}
	 *
	 * @access public
	 * @param int $count 单页返回的记录条数，默认为50。
	 * @param int $page 返回结果的页码，默认为1。
	 * @param int $base_app 是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * @return array
	 */
	function public_timeline( $page = 1, $count = 50, $base_app = 0 )
	{
		$params = array();
		$params['count'] = intval($count);
		$params['page'] = intval($page);
		$params['base_app'] = intval($base_app);
		return $this->oauth->get('statuses/public_timeline', $params);//可能是接口的bug不能补全
	}

	/**
	 * 获取当前登录用户及其所关注用户的最新微博消息。
	 *
	 * 获取当前登录用户及其所关注用户的最新微博消息。和用户登录 http://weibo.com 后在“我的首页”中看到的内容相同。同friends_timeline()
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/home_timeline statuses/home_timeline}
	 * 
	 * @access public
	 * @param int $page 指定返回结果的页码。根据当前登录用户所关注的用户数及这些被关注用户发表的微博数，翻页功能最多能查看的总记录数会有所不同，通常最多能查看1000条左右。默认值1。可选。
	 * @param int $count 每次返回的记录数。缺省值50，最大值200。可选。
	 * @param int $since_id 若指定此参数，则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。可选。
	 * @param int $max_id 若指定此参数，则返回ID小于或等于max_id的微博消息。可选。
	 * @param int $base_app 是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * @param int $feature 过滤类型ID，0：全部、1：原创、2：图片、3：视频、4：音乐，默认为0。
	 * @return array
	 */
	function home_timeline( $page = 1, $count = 50, $since_id = 0, $max_id = 0, $base_app = 0, $feature = 0 )
	{
		$params = array();
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
		$params['count'] = intval($count);
		$params['page'] = intval($page);
		$params['base_app'] = intval($base_app);
		$params['feature'] = intval($feature);

		return $this->oauth->get('statuses/home_timeline', $params);
	}

	/**
	 * 获取当前登录用户及其所关注用户的最新微博消息。
	 *
	 * 获取当前登录用户及其所关注用户的最新微博消息。和用户登录 http://weibo.com 后在“我的首页”中看到的内容相同。同home_timeline()
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/friends_timeline statuses/friends_timeline}
	 * 
	 * @access public
	 * @param int $page 指定返回结果的页码。根据当前登录用户所关注的用户数及这些被关注用户发表的微博数，翻页功能最多能查看的总记录数会有所不同，通常最多能查看1000条左右。默认值1。可选。
	 * @param int $count 每次返回的记录数。缺省值50，最大值200。可选。
	 * @param int $since_id 若指定此参数，则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。可选。
	 * @param int $max_id 若指定此参数，则返回ID小于或等于max_id的微博消息。可选。
	 * @param int $base_app 是否基于当前应用来获取数据。1为限制本应用微博，0为不做限制。默认为0。可选。
	 * @param int $feature 微博类型，0全部，1原创，2图片，3视频，4音乐. 返回指定类型的微博信息内容。转为为0。可选。
	 * @return array
	 */
	function friends_timeline( $page = 1, $count = 50, $since_id = 0, $max_id = 0, $base_app = 0, $feature = 0 )
	{
		return $this->home_timeline( $since_id, $max_id, $count, $page, $base_app, $feature);
	}

	/**
	 * 获取用户发布的微博信息列表
	 *
	 * 返回用户的发布的最近n条信息，和用户微博页面返回内容是一致的。此接口也可以请求其他用户的最新发表微博。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/user_timeline statuses/user_timeline}
	 * 
	 * @access public
	 * @param int $page 页码
	 * @param int $count 每次返回的最大记录数，最多返回200条，默认50。
	 * @param mixed $uid 指定用户UID或微博昵称
	 * @param int $since_id 若指定此参数，则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。可选。
	 * @param int $max_id 若指定此参数，则返回ID小于或等于max_id的提到当前登录用户微博消息。可选。
	 * @param int $base_app 是否基于当前应用来获取数据。1为限制本应用微博，0为不做限制。默认为0。
	 * @param int $feature 过滤类型ID，0：全部、1：原创、2：图片、3：视频、4：音乐，默认为0。
	 * @param int $trim_user 返回值中user信息开关，0：返回完整的user信息、1：user字段仅返回uid，默认为0。
	 * @return array
	 */
	function user_timeline_by_id( $uid = NULL , $page = 1 , $count = 50 , $since_id = 0, $max_id = 0, $feature = 0, $trim_user = 0, $base_app = 0)
	{
		$params = array();
		$params['uid']=$uid;
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
		$params['base_app'] = intval($base_app);
		$params['feature'] = intval($feature);
		$params['count'] = intval($count);
		$params['page'] = intval($page);
		$params['trim_user'] = intval($trim_user);

		return $this->oauth->get( 'http://i2.api.weibo.com/2/statuses/user_timeline.json', $params );	//statuses/user_timeline
	}
	
	
	/**
	 * 获取用户发布的微博信息列表
	 *
	 * 返回用户的发布的最近n条信息，和用户微博页面返回内容是一致的。此接口也可以请求其他用户的最新发表微博。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/user_timeline statuses/user_timeline}
	 * 
	 * @access public
	 * @param string $screen_name 微博昵称，主要是用来区分用户UID跟微博昵称，当二者一样而产生歧义的时候，建议使用该参数 
	 * @param int $page 页码
	 * @param int $count 每次返回的最大记录数，最多返回200条，默认50。
	 * @param int $since_id 若指定此参数，则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。可选。
	 * @param int $max_id 若指定此参数，则返回ID小于或等于max_id的提到当前登录用户微博消息。可选。
	 * @param int $feature 过滤类型ID，0：全部、1：原创、2：图片、3：视频、4：音乐，默认为0。
	 * @param int $trim_user 返回值中user信息开关，0：返回完整的user信息、1：user字段仅返回uid，默认为0。
	 * @param int $base_app 是否基于当前应用来获取数据。1为限制本应用微博，0为不做限制。默认为0。
	 * @return array
	 */
	function user_timeline_by_name( $screen_name = NULL , $page = 1 , $count = 50 , $since_id = 0, $max_id = 0, $feature = 0, $trim_user = 0, $base_app = 0 )
	{
		$params = array();
		$params['screen_name'] = $screen_name;
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
		$params['base_app'] = intval($base_app);
		$params['feature'] = intval($feature);
		$params['count'] = intval($count);
		$params['page'] = intval($page);
		$params['trim_user'] = intval($trim_user);

		return $this->oauth->get( 'statuses/user_timeline', $params );
	}
	/**
     * 获取用户timeline的ID
     * @param type $uid
     * @param type $screen_name
     * @param type $page
     * @param type $count 最大100
     * @param type $since_id
     * @param type $max_id
     * @param type $feature
     * @param type $trim_user
     * @param type $base_app
     * @return type
     */
	function user_timelineIds ( $uid = NULL ,$screen_name = "" , $page = 1 , $count = 50 , $since_id = 0, $max_id = 0, $feature = 0, $trim_user = 0, $base_app = 0 )
	{
		$params = array();
		$params['uid']=$uid;
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
        if($screen_name){
            $params['screen_name'] = $screen_name;
        }
		$params['base_app'] = intval($base_app);
		$params['feature'] = intval($feature);
		$params['count'] = intval($count);
		$params['page'] = intval($page);
		$params['trim_user'] = intval($trim_user);

		return $this->oauth->get( 'http://i2.api.weibo.com/2/statuses/user_timeline/ids.json', $params );	//statuses/user_timeline
	}
	
	/**
	 * 批量获取指定的一批用户的timeline
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/timeline_batch statuses/timeline_batch}
	 *
	 * @param string $screen_name  需要查询的用户昵称，用半角逗号分隔，一次最多20个
	 * @param int    $count        单页返回的记录条数，默认为50。
	 * @param int    $page  返回结果的页码，默认为1。 
	 * @param int    $base_app  是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * @param int    $feature   过滤类型ID，0：全部、1：原创、2：图片、3：视频、4：音乐，默认为0。
	 * @return array
	 */
	function timeline_batch_by_name( $screen_name, $page = 1, $count = 50, $feature = 0, $base_app = 0)
	{
		$params = array();
		if (is_array($screen_name) && !empty($screen_name)) {
			$params['screen_name'] = join(',', $screen_name);
		} else {
			$params['screen_name'] = $screen_name;
		}
		$params['count'] = intval($count);
		$params['page'] = intval($page); 
		$params['base_app'] = intval($base_app);
		$params['feature'] = intval($feature);
		return $this->oauth->get('statuses/timeline_batch', $params);
	}
	/**
	 * 批量获取一批微博的转评数
	 * @param string $ids 用半角逗号分隔，一次最多100个
	 * @return type
	 */
	function count_batch_by_id($ids){
		$params = array();
		$params['source'] = $this->oauth->client_id;
		$params['ids'] = $ids;
		return $this->oauth->get('http://i2.api.weibo.com/2/statuses/count.json', $params);
	}
	/**
	 * 批量获取一批微博的转评数及阅读数
	 * @param string $ids 用半角逗号分隔，一次最多20个
	 * @return type
	 */
	function count_sp_batch_by_id($ids){
		$params = array();
		$params['source'] = $this->oauth->client_id;
		$params['ids'] = $ids;
		return $this->oauth->get('http://i2.api.weibo.com/2/statuses/count_sp.json', $params);
	}
	
	/**
	 * 获取用户微博总数
	 * @param int $uid 用户uid
	 * @return type
	 */
	function user_timeline_count($uid){
		$params = array();
		$params['uids']=$uid;
		return $this->oauth->get('https://api.weibo.com/2/users/counts.json', $params);
	}

	/**
	 * 批量获取指定的一批用户的timeline
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/timeline_batch statuses/timeline_batch}
	 *
	 * @param string $uids  需要查询的用户ID，用半角逗号分隔，一次最多20个。
	 * @param int    $count        单页返回的记录条数，默认为50。
	 * @param int    $page  返回结果的页码，默认为1。 
	 * @param int    $base_app  是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * @param int    $feature   过滤类型ID，0：全部、1：原创、2：图片、3：视频、4：音乐，默认为0。
	 * @return array
	 */
	function timeline_batch_by_id( $uids, $page = 1, $count = 50, $feature = 0, $base_app = 0)
	{
		$params = array();
		if (is_array($uids) && !empty($uids)) {
			foreach($uids as $k => $v) {
				$this->id_format($uids[$k]);
			}
			$params['uids'] = join(',', $uids);
		} else {
			$params['uids'] = $uids;
		}
		$params['count'] = intval($count);
		$params['page'] = intval($page); 
		$params['base_app'] = intval($base_app);
		$params['feature'] = intval($feature);
		return $this->oauth->get('statuses/timeline_batch', $params);
	}


	/**
	 * 返回一条原创微博消息的最新n条转发微博消息。本接口无法对非原创微博进行查询。 
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/repost_timeline statuses/repost_timeline}
	 * 
	 * @access public
	 * @param int $sid 要获取转发微博列表的原创微博ID。
	 * @param int $page 返回结果的页码。 
	 * @param int $count 单页返回的最大记录数，最多返回200条，默认50。可选。
	 * @param int $since_id 若指定此参数，则只返回ID比since_id大的记录（比since_id发表时间晚）。可选。
	 * @param int $max_id 若指定此参数，则返回ID小于或等于max_id的记录。可选。
	 * @param int $filter_by_author 作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
	 * @return array
	 */
	function repost_timeline( $sid, $page = 1, $count = 50, $since_id = 0, $max_id = 0, $filter_by_author = 0 )
	{
		$this->id_format($sid);

		$params = array();
		$params['id'] = $sid;
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
		$params['filter_by_author'] = intval($filter_by_author);

		return $this->request_with_pager( 'statuses/repost_timeline', $page, $count, $params );
	}

	/**
	 * 获取当前用户最新转发的n条微博消息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/repost_by_me statuses/repost_by_me}
	 * 
	 * @access public
	 * @param int $page 返回结果的页码。 
	 * @param int $count  每次返回的最大记录数，最多返回200条，默认50。可选。
	 * @param int $since_id 若指定此参数，则只返回ID比since_id大的记录（比since_id发表时间晚）。可选。
	 * @param int $max_id  若指定此参数，则返回ID小于或等于max_id的记录。可选。
	 * @return array
	 */
	function repost_by_me( $page = 1, $count = 50, $since_id = 0, $max_id = 0 )
	{
		$params = array();
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}

		return $this->request_with_pager('statuses/repost_by_me', $page, $count, $params );
	}

	/**
	 * 获取@当前用户的微博列表
	 *
	 * 返回最新n条提到登录用户的微博消息（即包含@username的微博消息）
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/mentions statuses/mentions}
	 * 
	 * @access public
	 * @param int $page 返回结果的页序号。
	 * @param int $count 每次返回的最大记录数（即页面大小），不大于200，默认为50。
	 * @param int $since_id 若指定此参数，则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。可选。
	 * @param int $max_id 若指定此参数，则返回ID小于或等于max_id的提到当前登录用户微博消息。可选。
	 * @param int $filter_by_author 作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
	 * @param int $filter_by_source 来源筛选类型，0：全部、1：来自微博、2：来自微群，默认为0。
	 * @param int $filter_by_type 原创筛选类型，0：全部微博、1：原创的微博，默认为0。
	 * @return array
	 */
	function mentions( $page = 1, $count = 50, $since_id = 0, $max_id = 0, $filter_by_author = 0, $filter_by_source = 0, $filter_by_type = 0 )
	{
		$params = array();
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
		$params['filter_by_author'] = $filter_by_author;
		$params['filter_by_source'] = $filter_by_source;
		$params['filter_by_type'] = $filter_by_type;

		return $this->request_with_pager( 'statuses/mentions', $page, $count, $params );
	}


	/**
	 * 根据ID获取单条微博信息内容
	 *
	 * 获取单条ID的微博信息，作者信息将同时返回。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/show statuses/show}
	 * 
	 * @access public
	 * @param int $id 要获取已发表的微博ID, 如ID不存在返回空
	 * @return array
	 */
	function show_status( $id )
	{
		$this->id_format($id);
		$params = array();
		$params['id'] = $id;
		return $this->oauth->get('http://i2.api.weibo.com/2/statuses/show.json', $params);	//statuses/show
	}

	/**
	 * 根据微博id号获取微博的信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/show_batch statuses/show_batch}
	 *
	 * @param string $ids 需要查询的微博ID，用半角逗号分隔，最多不超过50个。
	 * @return array
	 */
    function show_batch( $ids )
	{
		$params=array();
		if (is_array($ids) && !empty($ids)) {
			foreach($ids as $k => $v) {
				$this->id_format($ids[$k]);
			}
			$params['ids'] = join(',', $ids);
		} else {
			$params['ids'] = $ids;
		}
		return $this->oauth->get('statuses/show_batch', $params);
	}

	/**
	 * 通过微博（评论、私信）ID获取其MID
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/querymid statuses/querymid}
	 *
	 * @param int|string $id  需要查询的微博（评论、私信）ID，批量模式下，用半角逗号分隔，最多不超过20个。
	 * @param int $type  获取类型，1：微博、2：评论、3：私信，默认为1。
	 * @param int $is_batch 是否使用批量模式，0：否、1：是，默认为0。
	 * @return array
	 */
	function querymid( $id, $type = 1, $is_batch = 0 )
	{
		$params = array();
		$params['id'] = $id;
		$params['type'] = intval($type);
		$params['is_batch'] = intval($is_batch);
		return $this->oauth->get( 'statuses/querymid',  $params);
	}

	/**
	 * 通过微博（评论、私信）MID获取其ID
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/queryid statuses/queryid}
	 *
	 * @param int|string $mid  需要查询的微博（评论、私信）MID，批量模式下，用半角逗号分隔，最多不超过20个。
	 * @param int $type  获取类型，1：微博、2：评论、3：私信，默认为1。
	 * @param int $is_batch 是否使用批量模式，0：否、1：是，默认为0。
	 * @param int $inbox  仅对私信有效，当MID类型为私信时用此参数，0：发件箱、1：收件箱，默认为0 。
	 * @param int $isBase62 MID是否是base62编码，0：否、1：是，默认为0。
	 * @return array
	 */
    function queryid( $mid, $type = 1, $is_batch = 0, $inbox = 0, $isBase62 = 0)
    {
        $params = array();
        $params['mid'] = $mid;
        $params['type'] = intval($type);
        $params['is_batch'] = intval($is_batch);
        $params['inbox'] = intval($inbox);
        $params['isBase62'] = intval($isBase62);
        //http://i2.api.weibo.com/2/statuses/queryid.json
        return $this->oauth->get('https://api.weibo.com/2/statuses/queryid.json', $params);    //statuses/queryid
    }

	/**
	 * 按天返回热门微博转发榜的微博列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/hot/repost_daily statuses/hot/repost_daily}
	 *
	 * @param int $count 返回的记录条数，最大不超过50，默认为20。
	 * @param int $base_app 是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * @return array
	 */
	function repost_daily( $count = 20, $base_app = 0)
	{
		$params = array();
		$params['count'] = intval($count);
		$params['base_app'] = intval($base_app);
		return $this->oauth->get('statuses/hot/repost_daily',  $params);
	}

	/**
	 * 按周返回热门微博转发榜的微博列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/hot/repost_weekly statuses/hot/repost_weekly}
	 *
	 * @param int $count 返回的记录条数，最大不超过50，默认为20。
	 * @param int $base_app 是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * @return array
	 */
	function repost_weekly( $count = 20,  $base_app = 0)
	{
		$params = array();
		$params['count'] = intval($count);
		$params['base_app'] = intval($base_app);
		return $this->oauth->get( 'statuses/hot/repost_weekly',  $params);
	}

	/**
	 * 按天返回热门微博评论榜的微博列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/hot/comments_daily statuses/hot/comments_daily}
	 *
	 * @param int $count 返回的记录条数，最大不超过50，默认为20。
	 * @param int $base_app 是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * @return array
	 */
	function comments_daily( $count = 20,  $base_app = 0)
	{
		$params =  array();
		$params['count'] = intval($count);
		$params['base_app'] = intval($base_app);
		return $this->oauth->get( 'statuses/hot/comments_daily',  $params);
	}

	/**
	 * 按周返回热门微博评论榜的微博列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/hot/comments_weekly statuses/hot/comments_weekly}
	 *
	 * @param int $count 返回的记录条数，最大不超过50，默认为20。
	 * @param int $base_app 是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * @return array
	 */
	function comments_weekly( $count = 20, $base_app = 0)
	{
		$params =  array();
		$params['count'] = intval($count);
		$params['base_app'] = intval($base_app);
		return $this->oauth->get( 'statuses/hot/comments_weekly', $params);
	}


	/**
	 * 转发一条微博信息。
	 *
	 * 可加评论。为防止重复，发布的信息与最新信息一样话，将会被忽略。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/repost statuses/repost}
	 * 
	 * @access public
	 * @param int $sid 转发的微博ID
	 * @param string $text 添加的评论信息。可选。
	 * @param int $is_comment 是否在转发的同时发表评论，0：否、1：评论给当前微博、2：评论给原微博、3：都评论，默认为0。
	 * @return array
	 */
	function repost( $sid, $text = NULL, $is_comment = 0,$annotations=Null, $client_appkey='3260142752' )
	{
		$this->id_format($sid);

		$params = array();
		$params['id'] = $sid;
		$params['is_comment'] = $is_comment;
		if( $text ) $params['status'] = $text;
		if (is_string($annotations)) {
			$params['annotations'] = $annotations;
		} elseif (is_array($annotations)) {
			$params['annotations'] = json_encode($annotations);
		}
		if($client_appkey){
			$params['client_appkey'] = $client_appkey;		//3260142752设置该值使微博显示来源为新浪微博
		}
		return $this->oauth->post( 'statuses/repost', $params  );
		//return $this->oauth->post( 'http://i2.api.weibo.com/2/statuses/repost.json', $params  );
	}

	/**
	 * 删除一条微博
	 * 
	 * 根据ID删除微博消息。注意：只能删除自己发布的信息。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/destroy statuses/destroy}
	 * 
	 * @access public
	 * @param int $id 要删除的微博ID
	 * @return array
	 */
	function delete( $id )
	{
		return $this->destroy( $id );
	}

	/**
	 * 删除一条微博
	 *
	 * 删除微博。注意：只能删除自己发布的信息。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/destroy statuses/destroy}
	 * 
	 * @access public
	 * @param int $id 要删除的微博ID
	 * @return array
	 */
	function destroy( $id )
	{
		$this->id_format($id);
		$params = array();
		$params['id'] = $id;
		return $this->oauth->post( 'statuses/destroy',  $params );
	}

	
	/**
	 * 发表微博
	 *
	 * 发布一条微博信息。
	 * <br />注意：lat和long参数需配合使用，用于标记发表微博消息时所在的地理位置，只有用户设置中geo_enabled=true时候地理位置信息才有效。
	 * <br />注意：为防止重复提交，当用户发布的微博消息与上次成功发布的微博消息内容一样时，将返回400错误，给出错误提示：“40025:Error: repeated weibo text!“。 
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/update statuses/update}
	 * 
	 * @access public
	 * @param string $status 要更新的微博信息。信息内容不超过140个汉字, 为空返回400错误。
	 * @param float $lat 纬度，发表当前微博所在的地理位置，有效范围 -90.0到+90.0, +表示北纬。可选。
	 * @param float $long 经度。有效范围-180.0到+180.0, +表示东经。可选。
	 * @param mixed $annotations 可选参数。元数据，主要是为了方便第三方应用记录一些适合于自己使用的信息。每条微博可以包含一个或者多个元数据。请以json字串的形式提交，字串长度不超过512个字符，或者数组方式，要求json_encode后字串长度不超过512个字符。具体内容可以自定。例如：'[{"type2":123}, {"a":"b", "c":"d"}]'或array(array("type2"=>123), array("a"=>"b", "c"=>"d"))。
	 * @return array
	 */
	function update( $status, $lat = NULL, $long = NULL, $annotations = NULL , $client_appkey='3260142752' )
	{    
		$params = array();
		$params['status'] = $status;
		if ($lat) {
			$params['lat'] = floatval($lat);
		}
		if ($long) {
			$params['long'] = floatval($long);
		}
		if (is_string($annotations)) {
			$params['annotations'] = $annotations;
		} elseif (is_array($annotations)) {
			$params['annotations'] = json_encode($annotations);
		}
		if($client_appkey){
			$params['client_appkey'] = $client_appkey;		//设置该值使微博显示来源为新浪微博
		}
     	return $this->oauth->post( 'statuses/update', $params );
		//return $this->oauth->post( 'http://i2.api.weibo.com/2/statuses/update.json' , $params );	//改为内部接口
	}

	/**
	 * 发表图片微博
	 *
	 * 发表图片微博消息。目前上传图片大小限制为<5M。 
	 * <br />注意：lat和long参数需配合使用，用于标记发表微博消息时所在的地理位置，只有用户设置中geo_enabled=true时候地理位置信息才有效。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/upload statuses/upload}
	 * 
	 * @access public
	 * @param string $status 要更新的微博信息。信息内容不超过140个汉字, 为空返回400错误。
	 * @param string $pic_path 要发布的图片路径, 支持url。[只支持png/jpg/gif三种格式, 增加格式请修改get_image_mime方法]
	 * @param float $lat 纬度，发表当前微博所在的地理位置，有效范围 -90.0到+90.0, +表示北纬。可选。
	 * @param float $long 可选参数，经度。有效范围-180.0到+180.0, +表示东经。可选。
	 * @return array
	 */
	function upload( $status, $pic_path, $lat = NULL, $long = NULL )
	{
		$params = array();
		$params['status'] = $status;
		$params['pic'] = '@'.$pic_path;
		if ($lat) {
			$params['lat'] = floatval($lat);
		}
		if ($long) {
			$params['long'] = floatval($long);
		}

		return $this->oauth->post( 'statuses/upload', $params, true );
		//return $this->oauth->post( 'http://i2.api.weibo.com/2/statuses/upload.json' , $params ,true );
	}


	/**
	 * 指定一个图片URL地址抓取后上传并同时发布一条新微博
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/upload_url_text statuses/upload_url_text}
	 *
	 * @param string $status  要发布的微博文本内容，内容不超过140个汉字。
	 * @param string $url    图片的URL地址，必须以http开头。
	 * @return array
	 */
	function upload_url_text( $status,  $url )
	{
		$params = array();
		$params['status'] = $status;
		$params['url'] = $url;
//		return $this->oauth->post( 'statuses/upload', $params, true );
		return $this->oauth->post( 'http://i2.api.weibo.com/2/statuses/upload.json' , $params ,true );
	}
	
	/**
	 * 上传图片
	 */
	function upload_pic_2( $pic_path ,$print_mark = 0)
	{
		$params = array();
		$params['pic'] = '@'.$pic_path;
        $params['print_mark'] = 0;
        if($print_mark){
            $params['print_mark'] = 1;
        }
		//https://api.t.sina.com.cn/statuses/upload_pic.json
		return $this->oauth->post( 'http://i2.api.weibo.com/2/statuses/upload_pic.json' , $params ,true );
	}
	/**
	 * 发布一条微博
	 */
	function update2( $status,$base_app=0, $lat = NULL, $long = NULL, $annotations = NULL )
	{
		$params = array();
		$params['status'] = $status;
// 		if($visible){
// 			$params['visible'] = $visible;
// 		}
		//$params['source'] = WB_AKEY;
		if($base_app){
			$params['base_app'] = $base_app;
		}
		if ($lat) {
			$params['lat'] = floatval($lat);
		}
		if ($long) {
			$params['long'] = floatval($long);
		}
		if (is_string($annotations)) {
			$params['annotations'] = $annotations;
		} elseif (is_array($annotations)) {
			$params['annotations'] = json_encode($annotations);
		}
	
		//return $this->oauth->post( 'statuses/update', $params );
		//https://api.t.sina.com.cn/statuses/update.json
		return $this->oauth->post( 'http://i2.api.weibo.com/2/statuses/update.json' , $params );
	}
	
	/**
	 * 发布一条微博，同时指定已经上传的图片picid或internet上的图片url
	 */
	function upload_url_text_2( $status, $pic_id = NULL, $url = NULL, $annotations=Null, $base_app=0, $client_appkey='3260142752')
	{
		$params = array();
		$params['status'] = $status;
	
		if ($pic_id) {
			$params['pic_id'] = $pic_id;
		}
		if ($url) {
			$params['url'] = $url;
		}
        if (is_string($annotations)) {
			$params['annotations'] = $annotations;
		} elseif (is_array($annotations)) {
			$params['annotations'] = json_encode($annotations);
		}
// 		if ($visible) {
// 			$params['visible'] = $visible;
// 		}
		if ($base_app) {
			$params['base_app'] = $base_app;
		}
		if($client_appkey){
			$params['client_appkey'] = $client_appkey;		//设置该值使微博显示来源为新浪微博
		}
		//https://api.t.sina.com.cn/statuses/upload_url_text.json
        return $this->oauth->post( 'statuses/upload_url_text' , $params );
		//return $this->oauth->post( 'http://i2.api.weibo.com/2/statuses/upload_url_text.json' , $params );
	}
	/**
	 * 获取用户的状态
	 * @param unknown_type $uid
	 * @return Ambigous <mixed, string>
	 */
	function getUserState($uid=0){
		//返回值type，用户类型，扩展信息，需要时才返回，0：初级用户，1：普通用户，3：高级用户，4：敏感用户，
		//5：绿色用户，6：危险用户，7：封杀用户，8：冻结用户，9：只读用户，10：未激活用户，140：保护组，
		//149：平台测试； 
		$params=array();
		$params['uid'] = $uid;
		$params['source'] = $this->oauth->client_id;
		return $this->oauth->get( 'http://i2.api.weibo.com/2/users/state.json' , $params );
	}
	/**
	 * 获取用户状态是否异常，取代getUserState方法
	 * @param type $uids 多个用逗号隔开，最多20个
	 * @return type
	 */
	function isNatural($uids){
		//返回值{"result":{"2463625662":false,"1656953035":true}}
		$params=array();
		$params['uids'] = $uids;
		$params['source'] = $this->oauth->client_id;
		return $this->oauth->get( 'http://i.api.weibo.com/proxy/admin/content/isnatural.json' , $params );
	}
	/**
	 * 获取表情列表
	 *
	 * 返回新浪微博官方所有表情、魔法表情的相关信息。包括短语、表情类型、表情分类，是否热门等。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/emotions emotions}
	 * 
	 * @access public
	 * @param string $type 表情类别。"face":普通表情，"ani"：魔法表情，"cartoon"：动漫表情。默认为"face"。可选。
	 * @param string $language 语言类别，"cnname"简体，"twname"繁体。默认为"cnname"。可选
	 * @return array
	 */
	function emotions( $type = "face", $language = "cnname" )
	{
		$params = array();
		$params['type'] = $type;
		$params['language'] = $language;
		return $this->oauth->get( 'emotions', $params );
	}


	/**
	 * 根据微博ID返回某条微博的评论列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/comments/show comments/show}
	 *
	 * @param int $sid 需要查询的微博ID。
	 * @param int $page 返回结果的页码，默认为1。
	 * @param int $count 单页返回的记录条数，默认为50。
	 * @param int $since_id 若指定此参数，则返回ID比since_id大的评论（即比since_id时间晚的评论），默认为0。
	 * @param int $max_id  若指定此参数，则返回ID小于或等于max_id的评论，默认为0。
	 * @param int $filter_by_author 作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
	 * @return array
	 */
	function get_comments_by_sid( $sid, $page = 1, $count = 50, $since_id = 0, $max_id = 0, $filter_by_author = 0 )
	{
		$params = array();
		$this->id_format($sid);
		$params['id'] = $sid;
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
		$params['count'] = $count;
		$params['page'] = $page;
		$params['filter_by_author'] = $filter_by_author;
		return $this->oauth->get( 'comments/show',  $params );
	}


	/**
	 * 获取当前登录用户所发出的评论列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/comments/by_me comments/by_me}
	 *
	 * @param int $since_id 若指定此参数，则返回ID比since_id大的评论（即比since_id时间晚的评论），默认为0。
	 * @param int $max_id 若指定此参数，则返回ID小于或等于max_id的评论，默认为0。
	 * @param int $count  单页返回的记录条数，默认为50。
	 * @param int $page 返回结果的页码，默认为1。
	 * @param int $filter_by_source 来源筛选类型，0：全部、1：来自微博的评论、2：来自微群的评论，默认为0。
	 * @return array
	 */
	function comments_by_me( $page = 1 , $count = 50, $since_id = 0, $max_id = 0,  $filter_by_source = 0 )
	{
		$params = array();
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
		$params['count'] = $count;
		$params['page'] = $page;
		$params['filter_by_source'] = $filter_by_source;
		return $this->oauth->get( 'comments/by_me', $params );
	}

	/**
	 * 获取当前登录用户所接收到的评论列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/comments/to_me comments/to_me}
	 *
	 * @param int $since_id 若指定此参数，则返回ID比since_id大的评论（即比since_id时间晚的评论），默认为0。
	 * @param int $max_id  若指定此参数，则返回ID小于或等于max_id的评论，默认为0。
	 * @param int $count 单页返回的记录条数，默认为50。
	 * @param int $page 返回结果的页码，默认为1。
	 * @param int $filter_by_author 作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
	 * @param int $filter_by_source 来源筛选类型，0：全部、1：来自微博的评论、2：来自微群的评论，默认为0。
	 * @return array
	 */ 
	function comments_to_me( $page = 1 , $count = 50, $since_id = 0, $max_id = 0, $filter_by_author = 0, $filter_by_source = 0)
	{
		$params = array();
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
		$params['count'] = $count;
		$params['page'] = $page;
		$params['filter_by_author'] = $filter_by_author;
		$params['filter_by_source'] = $filter_by_source;
		return $this->oauth->get( 'comments/to_me', $params );
	}

	/**
	 * 最新评论(按时间)
	 *
	 * 返回最新n条发送及收到的评论。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/comments/timeline comments/timeline}
	 * 
	 * @access public
	 * @param int $page 页码
	 * @param int $count 每次返回的最大记录数，最多返回200条，默认50。
	 * @param int $since_id 若指定此参数，则只返回ID比since_id大的评论（比since_id发表时间晚）。可选。
	 * @param int $max_id 若指定此参数，则返回ID小于或等于max_id的评论。可选。
	 * @return array
	 */
	function comments_timeline( $page = 1, $count = 50, $since_id = 0, $max_id = 0 )
	{
		$params = array();
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}

		return $this->request_with_pager( 'comments/timeline', $page, $count, $params );
	}


	/**
	 * 获取最新的提到当前登录用户的评论，即@我的评论
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/comments/mentions comments/mentions}
	 *
	 * @param int $since_id 若指定此参数，则返回ID比since_id大的评论（即比since_id时间晚的评论），默认为0。
	 * @param int $max_id  若指定此参数，则返回ID小于或等于max_id的评论，默认为0。
	 * @param int $count 单页返回的记录条数，默认为50。
	 * @param int $page 返回结果的页码，默认为1。
	 * @param int $filter_by_author  作者筛选类型，0：全部、1：我关注的人、2：陌生人，默认为0。
	 * @param int $filter_by_source 来源筛选类型，0：全部、1：来自微博的评论、2：来自微群的评论，默认为0。
	 * @return array
	 */ 
	function comments_mentions( $page = 1, $count = 50, $since_id = 0, $max_id = 0, $filter_by_author = 0, $filter_by_source = 0)
	{
		$params = array();
		$params['since_id'] = $since_id;
		$params['max_id'] = $max_id;
		$params['count'] = $count;
		$params['page'] = $page;
		$params['filter_by_author'] = $filter_by_author;
		$params['filter_by_source'] = $filter_by_source;
		return $this->oauth->get( 'comments/mentions', $params );
	}


	/**
	 * 根据评论ID批量返回评论信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/comments/show_batch comments/show_batch}
	 *
	 * @param string $cids 需要查询的批量评论ID，用半角逗号分隔，最大50
	 * @return array
	 */
	function comments_show_batch( $cids )
	{
		$params = array();
		if (is_array( $cids) && !empty( $cids)) {
			foreach($cids as $k => $v) {
				$this->id_format($cids[$k]);
			}
			$params['cids'] = join(',', $cids);
		} else {
			$params['cids'] = $cids;
		}
		return $this->oauth->get( 'comments/show_batch', $params );
	}


	/**
	 * 对一条微博进行评论
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/comments/create comments/create}
	 *
	 * @param string $comment 评论内容，内容不超过140个汉字。
	 * @param int $id 需要评论的微博ID。
	 * @param int $comment_ori 当评论转发微博时，是否评论给原微博，0：否、1：是，默认为0。
	 * @return array
	 */
	function send_comment( $id , $comment , $comment_ori = 0)
	{
		$params = array();
		$params['comment'] = $comment;
		$this->id_format($id);
		$params['id'] = $id;
		$params['comment_ori'] = $comment_ori;
		return $this->oauth->post( 'comments/create', $params );
	}

	/**
	 * 删除当前用户的微博评论信息。
	 *
	 * 注意：只能删除自己发布的评论，发部微博的用户不可以删除其他人的评论。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/comment_destroy statuses/comment_destroy}
	 * 
	 * @access public
	 * @param int $cid 要删除的评论id
	 * @return array
	 */
	function comment_destroy( $cid )
	{
		$params = array();
		$params['cid'] = $cid;
		return $this->oauth->post( 'comments/destroy', $params);
	}


	/**
	 * 根据评论ID批量删除评论
	 *
	 * 注意：只能删除自己发布的评论，发部微博的用户不可以删除其他人的评论。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/comments/destroy_batch comments/destroy_batch}
	 *
	 * @access public
	 * @param string $ids 需要删除的评论ID，用半角逗号隔开，最多20个。
	 * @return array
	 */
	function comment_destroy_batch( $ids )
	{
		$params = array();
		if (is_array($ids) && !empty($ids)) {
			foreach($ids as $k => $v) {
				$this->id_format($ids[$k]);
			}
			$params['cids'] = join(',', $ids);
		} else {
			$params['cids'] = $ids;
		}
		return $this->oauth->post( 'comments/destroy_batch', $params);
	}


	/**
	 * 回复一条评论
	 *
	 * 为防止重复，发布的信息与最后一条评论/回复信息一样话，将会被忽略。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/comments/reply comments/reply}
	 * 
	 * @access public
	 * @param int $sid 微博id
	 * @param string $text 评论内容。
	 * @param int $cid 评论id
	 * @param int $without_mention 1：回复中不自动加入“回复@用户名”，0：回复中自动加入“回复@用户名”.默认为0.
     * @param int $comment_ori	  当评论转发微博时，是否评论给原微博，0：否、1：是，默认为0。
	 * @return array
	 */
	function reply( $sid, $text, $cid, $without_mention = 0, $comment_ori = 0 )
	{
		$this->id_format( $sid );
		$this->id_format( $cid );
		$params = array();
		$params['id'] = $sid;
		$params['comment'] = $text;
		$params['cid'] = $cid;
		$params['without_mention'] = $without_mention;
		$params['comment_ori'] = $comment_ori;

		return $this->oauth->post( 'comments/reply', $params );

	}

	/**
	 * 根据用户UID或昵称获取用户资料
	 *
	 * 按用户UID或昵称返回用户资料，同时也将返回用户的最新发布的微博。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/users/show users/show}
	 * 
	 * @access public
	 * @param int  $uid 用户UID。
	 * @return array
	 */
	function show_user_by_id( $uid )
	{
		$params=array();
		if ( $uid !== NULL ) {
			$this->id_format($uid);
			$params['uid'] = $uid;
		}

		return $this->oauth->get('http://i2.api.weibo.com/2/users/show.json', $params );		//users/show
	}
	/**
     * 默认的  根据用户UID或昵称获取用户资料
     */
    function show_user_by_id_old( $uid )
    {
        $params=array();
        if ( $uid !== NULL ) {
            $this->id_format($uid);
            $params['uid'] = $uid;
        }

        return $this->oauth->get('users/show', $params );
    }
	/**
	 * 根据用户UID或昵称获取用户资料
	 *
	 * 按用户UID或昵称返回用户资料，同时也将返回用户的最新发布的微博。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/users/show users/show}
	 * 
	 * @access public
	 * @param string  $screen_name 用户UID。
	 * @return array
	 */
	function show_user_by_name( $screen_name )
	{
		$params = array();
		$params['screen_name'] = $screen_name;

		return $this->oauth->get( 'users/show', $params );
	}

	/**
	 * 通过个性化域名获取用户资料以及用户最新的一条微博
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/users/domain_show users/domain_show}
	 * 
	 * @access public
	 * @param mixed $domain 用户个性域名。例如：lazypeople，而不是http://weibo.com/lazypeople
	 * @return array
	 */
	function domain_show( $domain )
	{
		$params = array();
		$params['domain'] = $domain;
		return $this->oauth->get( 'users/domain_show', $params );
	}

	 /**
	 * 批量获取用户信息按uids
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/users/show_batch users/show_batch}
	 *
	 * @param string $uids 需要查询的用户ID，用半角逗号分隔，一次最多20个。
	 * @return array
	 */
	function users_show_batch_by_id( $uids )
	{
		$params = array();
		if (is_array( $uids ) && !empty( $uids )) {
			foreach( $uids as $k => $v ) {
				$this->id_format( $uids[$k] );
			}
			$params['uids'] = join(',', $uids);
		} else {
			$params['uids'] = $uids;
		}
		return $this->oauth->get( 'users/show_batch', $params );
	}
	
	/**
	 * 批量获取用户信息按screen_name
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/users/show_batch users/show_batch}
	 *
	 * @param string  $screen_name 需要查询的用户昵称，用半角逗号分隔，一次最多20个。
	 * @return array
	 */
	function users_show_batch_by_name( $screen_name )
	{
		$params = array();
		if (is_array( $screen_name ) && !empty( $screen_name )) {
			$params['screen_name'] = join(',', $screen_name);
		} else {
			$params['screen_name'] = $screen_name;
		}
		return $this->oauth->get( 'users/show_batch', $params );
	}


	/**
	 * 获取用户的关注列表
	 *
	 * 如果没有提供cursor参数，将只返回最前面的5000个关注id
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/friends friendships/friends}
	 * 
	 * @access public
	 * @param int $cursor 返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
	 * @param int $count 单页返回的记录条数，默认为50，最大不超过200。
	 * @param int $uid  要获取的用户的ID。
	 * @return array
	 */
	function friends_by_id( $uid, $cursor = 0, $count = 50 )
	{
		$params = array();
		$params['cursor'] = $cursor;
		$params['count'] = $count;
		$params['uid'] = $uid;

		return $this->oauth->get( 'friendships/friends', $params );
	}
	
	
	/**
	 * 获取用户的关注列表
	 *
	 * 如果没有提供cursor参数，将只返回最前面的5000个关注id
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/friends friendships/friends}
	 * 
	 * @access public
	 * @param int $cursor 返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
	 * @param int $count 单页返回的记录条数，默认为50，最大不超过200。
	 * @param string $screen_name  要获取的用户的 screen_name
	 * @return array
	 */
	function friends_by_name( $screen_name, $cursor = 0, $count = 50 )
	{
		$params = array();
		$params['cursor'] = $cursor;
		$params['count'] = $count;
		$params['screen_name'] = $screen_name;
		return $this->oauth->get( 'friendships/friends', $params );
	}


	/**
	 * 获取两个用户之间的共同关注人列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/friends/in_common friendships/friends/in_common}
	 *
	 * @param int $uid  需要获取共同关注关系的用户UID
	 * @param int $suid  需要获取共同关注关系的用户UID，默认为当前登录用户。
	 * @param int $count  单页返回的记录条数，默认为50。
	 * @param int $page  返回结果的页码，默认为1。
	 * @return array
	 */
	function friends_in_common( $uid, $suid = NULL, $page = 1, $count = 50 )
	{
		$params = array();
		$params['uid'] = $uid;
		$params['suid'] = $suid;
		$params['count'] = $count;
		$params['page'] = $page;
		return $this->oauth->get( 'friendships/friends/in_common', $params  );
	}

	/**
	 * 获取用户的双向关注列表，即互粉列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/friends/bilateral friendships/friends/bilateral}
	 *
	 * @param int $uid  需要获取双向关注列表的用户UID。
	 * @param int $count  单页返回的记录条数，默认为50。
	 * @param int $page  返回结果的页码，默认为1。
	 * @param int $sort  排序类型，0：按关注时间最近排序，默认为0。
	 * @return array
	 **/
	function bilateral( $uid, $page = 1, $count = 50, $sort = 0 )
	{
		$params = array();
		$params['uid'] = $uid;
		$params['count'] = $count;
		$params['page'] = $page;
		$params['sort'] = $sort;
		return $this->oauth->get( 'friendships/friends/bilateral', $params  );
	}

	/**
	 * 获取用户的双向关注uid列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/friends/bilateral/ids friendships/friends/bilateral/ids}
	 *
	 * @param int $uid  需要获取双向关注列表的用户UID。
	 * @param int $count 单页返回的记录条数，默认为50。
	 * @param int $page  返回结果的页码，默认为1。
	 * @param int $sort  排序类型，0：按关注时间最近排序，默认为0。
	 * @return array
	 **/
	function bilateral_ids( $uid, $page = 1, $count = 50, $sort = 0)
	{
		$params = array();
		$params['uid'] = $uid;
		$params['count'] = $count;
		$params['page'] = $page;
		$params['sort'] = $sort;
		return $this->oauth->get( 'friendships/friends/bilateral/ids',  $params  );
	}

	/**
	 * 获取用户的关注列表uid
	 *
	 * 如果没有提供cursor参数，将只返回最前面的5000个关注id
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/friends/ids friendships/friends/ids}
	 * 
	 * @access public
	 * @param int $cursor 返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
	 * @param int $count 每次返回的最大记录数（即页面大小），不大于5000, 默认返回500。
	 * @param int $uid 要获取的用户 UID，默认为当前用户
	 * @return array
	 */
	function friends_ids_by_id( $uid, $cursor = 0, $count = 500 )
	{
		$params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		$params['cursor'] = $cursor;
		$params['count'] = $count;
		return $this->oauth->get( 'friendships/friends/ids', $params );
	}
	
	/**
	 * 获取用户的关注列表uid
	 *
	 * 如果没有提供cursor参数，将只返回最前面的5000个关注id
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/friends/ids friendships/friends/ids}
	 * 
	 * @access public
	 * @param int $cursor 返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
	 * @param int $count 每次返回的最大记录数（即页面大小），不大于5000, 默认返回500。
	 * @param string $screen_name 要获取的用户的 screen_name，默认为当前用户
	 * @return array
	 */
	function friends_ids_by_name( $screen_name, $cursor = 0, $count = 500 )
	{
		$params = array();
		$params['cursor'] = $cursor;
		$params['count'] = $count;
		$params['screen_name'] = $screen_name;
		return $this->oauth->get( 'friendships/friends/ids', $params );
	}


	/**
	 * 批量获取当前登录用户的关注人的备注信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/friends/remark_batch friendships/friends/remark_batch}
	 *
	 * @param string $uids  需要获取备注的用户UID，用半角逗号分隔，最多不超过50个。
	 * @return array
	 **/
	function friends_remark_batch( $uids )
	{
		$params = array();
		if (is_array( $uids ) && !empty( $uids )) {
			foreach( $uids as $k => $v) {
				$this->id_format( $uids[$k] );
			}
			$params['uids'] = join(',', $uids);
		} else {
			$params['uids'] = $uids;
		}
		return $this->oauth->get( 'friendships/friends/remark_batch', $params  );
	}

	/**
	 * 获取用户的粉丝列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/followers friendships/followers}
	 *
	 * @param int $uid  需要查询的用户UID
	 * @param int $count 单页返回的记录条数，默认为50，最大不超过200。
	 * @param int $cursor false 返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
	 * @return array
	 **/
	function followers_by_id( $uid , $cursor = 0 , $count = 50)
	{
		$params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		$params['count'] = $count;
		$params['cursor'] = $cursor;
		return $this->oauth->get( 'friendships/followers', $params  );
	}
	
	/**
	 * 获取用户的粉丝列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/followers friendships/followers}
	 *
	 * @param string $screen_name  需要查询的用户的昵称
	 * @param int  $count 单页返回的记录条数，默认为50，最大不超过200。
	 * @param int  $cursor false 返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
	 * @return array
	 **/
	function followers_by_name( $screen_name, $cursor = 0 , $count = 50 )
	{
		$params = array();
		$params['screen_name'] = $screen_name;
		$params['count'] = $count;
		$params['cursor'] = $cursor;
		return $this->oauth->get( 'friendships/followers', $params  );
	}

	/**
	 * 获取用户的粉丝列表uid
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/followers friendships/followers}
	 *
	 * @param int $uid 需要查询的用户UID
	 * @param int $count 单页返回的记录条数，默认为50，最大不超过200。
	 * @param int $cursor 返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
	 * @return array
	 **/
	function followers_ids_by_id( $uid, $cursor = 0 , $count = 50 )
	{
		$params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		$params['count'] = $count;
		$params['cursor'] = $cursor;
		return $this->oauth->get( 'friendships/followers/ids', $params  );
	}
	
	/**
	 * 获取用户的粉丝列表uid
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/followers friendships/followers}
	 *
	 * @param string $screen_name 需要查询的用户screen_name
	 * @param int $count 单页返回的记录条数，默认为50，最大不超过200。
	 * @param int $cursor 返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
	 * @return array
	 **/
	function followers_ids_by_name( $screen_name, $cursor = 0 , $count = 50 )
	{
		$params = array();
		$params['screen_name'] = $screen_name;
		$params['count'] = $count;
		$params['cursor'] = $cursor;
		return $this->oauth->get( 'friendships/followers/ids', $params  );
	}

	/**
	 * 获取优质粉丝
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/followers/active friendships/followers/active}
	 *
	 * @param int $uid 需要查询的用户UID。
	 * @param int $count 返回的记录条数，默认为20，最大不超过200。
     * @return array
	 **/
	function followers_active( $uid,  $count = 20)
	{
		$param = array();
		$this->id_format($uid);
		$param['uid'] = $uid;
		$param['count'] = $count;
		return $this->oauth->get( 'friendships/followers/active', $param);
	}


	/**
	 * 获取当前登录用户的关注人中又关注了指定用户的用户列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/friends_chain/followers friendships/friends_chain/followers}
	 *
	 * @param int $uid 指定的关注目标用户UID。
	 * @param int $count 单页返回的记录条数，默认为50。
	 * @param int $page 返回结果的页码，默认为1。
	 * @return array
	 **/
	function friends_chain_followers( $uid, $page = 1, $count = 50 )
	{
		$params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		$params['count'] = $count;
		$params['page'] = $page;
		return $this->oauth->get( 'friendships/friends_chain/followers',  $params );
	}

	/**
	 * 返回两个用户关系的详细情况
	 *
	 * 如果源用户或目的用户不存在，将返回http的400错误
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/show friendships/show}
	 * 
	 * @access public
	 * @param mixed $target_id 目标用户UID
	 * @param mixed $source_id 源用户UID，可选，默认为当前的用户
	 * @return array
	 */
	function is_followed_by_id( $target_id, $source_id = NULL )
	{
		$params = array();
		$this->id_format($target_id);
		$params['target_id'] = $target_id;

		if ( $source_id != NULL ) {
			$this->id_format($source_id);
			$params['source_id'] = $source_id;
		}

		return $this->oauth->get( 'friendships/show', $params );
	}

	/**
	 * 返回两个用户关系的详细情况
	 *
	 * 如果源用户或目的用户不存在，将返回http的400错误
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/show friendships/show}
	 * 
	 * @access public
	 * @param mixed $target_name 目标用户的微博昵称
	 * @param mixed $source_name 源用户的微博昵称，可选，默认为当前的用户
	 * @return array
	 */
	function is_followed_by_name( $target_name, $source_name = NULL )
	{
		$params = array();
		$params['target_screen_name'] = $target_name;

		if ( $source_name != NULL ) {
			$params['source_screen_name'] = $source_name;
		}

		return $this->oauth->get( 'friendships/show', $params );
	}

	/**
	 * 关注一个用户。
	 *
	 * 成功则返回关注人的资料，目前最多关注2000人，失败则返回一条字符串的说明。如果已经关注了此人，则返回http 403的状态。关注不存在的ID将返回400。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/create friendships/create}
	 * 
	 * @access public
	 * @param int $uid 要关注的用户UID
	 * @return array
	 */
	function follow_by_id( $uid )
	{
		$params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		return $this->oauth->post( 'friendships/create', $params );
	}
	
	/**
	 * 关注一个用户。
	 *
	 * 成功则返回关注人的资料，目前的最多关注2000人，失败则返回一条字符串的说明。如果已经关注了此人，则返回http 403的状态。关注不存在的ID将返回400。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/create friendships/create}
	 * 
	 * @access public
	 * @param string $screen_name 要关注的用户昵称
	 * @return array
	 */
	function follow_by_name( $screen_name )
	{
		$params = array();
		$params['screen_name'] = $screen_name;
		return $this->oauth->post( 'friendships/create', $params);
	}


	/**
	 * 根据用户UID批量关注用户
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/friendships/create_batch friendships/create_batch}
	 *
	 * @param string $uids 要关注的用户UID，用半角逗号分隔，最多不超过20个。
	 * @return array
	 */
	function follow_create_batch( $uids )
	{
		$params = array();
		if (is_array($uids) && !empty($uids)) {
			foreach($uids as $k => $v) {
				$this->id_format($uids[$k]);
			}
			$params['uids'] = join(',', $uids);
		} else {
			$params['uids'] = $uids;
		}
		return $this->oauth->post( 'friendships/create_batch', $params);
	}

	/**
	 * 取消关注某用户
	 *
	 * 取消关注某用户。成功则返回被取消关注人的资料，失败则返回一条字符串的说明。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/destroy friendships/destroy}
	 * 
	 * @access public
	 * @param int $uid 要取消关注的用户UID
	 * @return array
	 */
	function unfollow_by_id( $uid )
	{
		$params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		return $this->oauth->post( 'friendships/destroy', $params);
	}
	
	/**
	 * 取消关注某用户
	 *
	 * 取消关注某用户。成功则返回被取消关注人的资料，失败则返回一条字符串的说明。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/destroy friendships/destroy}
	 * 
	 * @access public
	 * @param string $screen_name 要取消关注的用户昵称
	 * @return array
	 */
	function unfollow_by_name( $screen_name )
	{
		$params = array();
		$params['screen_name'] = $screen_name;
		return $this->oauth->post( 'friendships/destroy', $params);
	}

	/**
	 * 更新当前登录用户所关注的某个好友的备注信息
	 *
	 * 只能修改当前登录用户所关注的用户的备注信息。否则将给出400错误。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/friendships/remark/update friendships/remark/update}
	 * 
	 * @access public
	 * @param int $uid 需要修改备注信息的用户ID。
	 * @param string $remark 备注信息。
	 * @return array
	 */
	function update_remark( $uid, $remark )
	{
		$params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		$params['remark'] = $remark;
		return $this->oauth->post( 'friendships/remark/update', $params);
	}

	/**
	 * 获取当前用户最新私信列表
	 *
	 * 返回用户的最新n条私信，并包含发送者和接受者的详细资料。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/direct_messages direct_messages}
	 * 
	 * @access public
	 * @param int $page 页码
	 * @param int $count 每次返回的最大记录数，最多返回200条，默认50。
	 * @param int64 $since_id 返回ID比数值since_id大（比since_id时间晚的）的私信。可选。
	 * @param int64 $max_id 返回ID不大于max_id(时间不晚于max_id)的私信。可选。
	 * @return array
	 */
	function list_dm( $page = 1, $count = 50, $since_id = 0, $max_id = 0 )
	{
		$params = array();
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}

		return $this->request_with_pager( 'direct_messages', $page, $count, $params );
	}

	/**
	 * 获取当前用户发送的最新私信列表
	 *
	 * 返回登录用户已发送最新50条私信。包括发送者和接受者的详细资料。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/direct_messages/sent direct_messages/sent}
	 * 
	 * @access public
	 * @param int $page 页码
	 * @param int $count 每次返回的最大记录数，最多返回200条，默认50。
	 * @param int64 $since_id 返回ID比数值since_id大（比since_id时间晚的）的私信。可选。
	 * @param int64 $max_id 返回ID不大于max_id(时间不晚于max_id)的私信。可选。
	 * @return array
	 */
	function list_dm_sent( $page = 1, $count = 50, $since_id = 0, $max_id = 0 )
	{
		$params = array();
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}

		return $this->request_with_pager( 'direct_messages/sent', $page, $count, $params );
	}


	/**
	 * 获取与当前登录用户有私信往来的用户列表，与该用户往来的最新私信
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/direct_messages/user_list direct_messages/user_list}
	 *
	 * @param int $count  单页返回的记录条数，默认为20。
	 * @param int $cursor 返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
	 * @return array
	 */
	function dm_user_list( $count = 20, $cursor = 0)
	{
		$params = array();
		$params['count'] = $count;
		$params['cursor'] = $cursor;
		return $this->oauth->get( 'direct_messages/user_list', $params );
	} 

	/**
	 * 获取与指定用户的往来私信列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/direct_messages/conversation direct_messages/conversation}
	 *
	 * @param int $uid 需要查询的用户的UID。
	 * @param int $since_id 若指定此参数，则返回ID比since_id大的私信（即比since_id时间晚的私信），默认为0。
	 * @param int $max_id  若指定此参数，则返回ID小于或等于max_id的私信，默认为0。
	 * @param int $count 单页返回的记录条数，默认为50。
	 * @param int $page  返回结果的页码，默认为1。
	 * @return array
	 */
	function dm_conversation( $uid, $page = 1, $count = 50, $since_id = 0, $max_id = 0)
	{
		$params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}
		$params['count'] = $count;
		$params['page'] = $page;
		return $this->oauth->get( 'direct_messages/conversation', $params );
	}

	/**
	 * 根据私信ID批量获取私信内容
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/direct_messages/show_batch direct_messages/show_batch}
	 *
	 * @param string  $dmids 需要查询的私信ID，用半角逗号分隔，一次最多50个
	 * @return array
	 */
	function dm_show_batch( $dmids )
	{
		$params = array();
		if (is_array($dmids) && !empty($dmids)) {
			foreach($dmids as $k => $v) {
				$this->id_format($dmids[$k]);
			}
			$params['dmids'] = join(',', $dmids);
		} else {
			$params['dmids'] = $dmids;
		}
		return $this->oauth->get( 'direct_messages/show_batch',  $params );
	}

	/**
	 * 发送私信
	 *
	 * 发送一条私信。成功将返回完整的发送消息。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/direct_messages/new direct_messages/new}
	 * 
	 * @access public
	 * @param int $uid 用户UID
	 * @param string $text 要发生的消息内容，文本大小必须小于300个汉字。
	 * @param int $id 需要发送的微博ID。
	 * @return array
	 */
	function send_dm_by_id( $uid, $text, $id = NULL )
	{
		$params = array();
		$this->id_format( $uid );
		$params['text'] = $text;
		$params['uid'] = $uid;
		if ($id) {
			$this->id_format( $id );
			$params['id'] = $id;
		}
		return $this->oauth->post( 'direct_messages/new', $params );
	}
	
	/**
	 * 发送私信
	 *
	 * 发送一条私信。成功将返回完整的发送消息。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/direct_messages/new direct_messages/new}
	 * 
	 * @access public
	 * @param string $screen_name 用户昵称
	 * @param string $text 要发生的消息内容，文本大小必须小于300个汉字。
	 * @param int $id 需要发送的微博ID。
	 * @return array
	 */
	function send_dm_by_name( $screen_name, $text, $id = NULL )
	{
		$params = array();
		$params['text'] = $text;
		$params['screen_name'] = $screen_name;
		if ($id) {
			$this->id_format( $id );
			$params['id'] = $id;
		}
		return $this->oauth->post( 'direct_messages/new', $params);
	}

	/**
	 * 删除一条私信
	 *
	 * 按ID删除私信。操作用户必须为私信的接收人。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/direct_messages/destroy direct_messages/destroy}
	 * 
	 * @access public
	 * @param int $did 要删除的私信主键ID
	 * @return array
	 */
	function delete_dm( $did )
	{
		$this->id_format($did);
		$params = array();
		$params['id'] = $did;
		return $this->oauth->post('direct_messages/destroy', $params);
	}

	/**
	 * 批量删除私信
	 *
	 * 批量删除当前登录用户的私信。出现异常时，返回400错误。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/direct_messages/destroy_batch direct_messages/destroy_batch}
	 * 
	 * @access public
	 * @param mixed $dids 欲删除的一组私信ID，用半角逗号隔开，或者由一组评论ID组成的数组。最多20个。例如："4976494627, 4976262053"或array(4976494627,4976262053);
	 * @return array
	 */
	function delete_dms( $dids )
	{
		$params = array();
		if (is_array($dids) && !empty($dids)) {
			foreach($dids as $k => $v) {
				$this->id_format($dids[$k]);
			}
			$params['ids'] = join(',', $dids);
		} else {
			$params['ids'] = $dids;
		}

		return $this->oauth->post( 'direct_messages/destroy_batch', $params);
	}
	


	/**
	 * 获取用户基本信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/basic account/profile/basic}
	 *
	 * @param int $uid  需要获取基本信息的用户UID，默认为当前登录用户。
	 * @return array
	 */
	function account_profile_basic( $uid = NULL  )
	{
		$params = array();
		if ($uid) {
			$this->id_format($uid);
			$params['uid'] = $uid;
		}
		return $this->oauth->get( 'account/profile/basic', $params );
	}
	
	/**
	 * 获取用户基本信息（内部使用）
	 *
	 * 对应API：{@link http://wiki.intra.weibo.com/1/account/profile/basic account/profile/basic}
	 *
	 * @param int $uid  需要获取基本信息的用户UID，默认为当前登录用户。
	 * @return array
	 */
	function account_profile_basic_intra( $uid=NULL )
	{
		$params = array();
		$params['uid'] = $uid;
		if ($uid) {
			$this->id_format($uid);
			$params['uid'] = $uid;
		}
		return $this->oauth->get( 'http://i2.api.weibo.com/2/account/profile/basic.json', $params );
	}

	/**
	 * 获取用户的教育信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/education account/profile/education}
	 *
	 * @param int $uid  需要获取教育信息的用户UID，默认为当前登录用户。
	 * @return array
	 */
	function account_education( $uid = NULL )
	{
		$params = array();
		if ($uid) {
			$this->id_format($uid);
			$params['uid'] = $uid;
		}
		return $this->oauth->get( 'account/profile/education', $params );
	}

	/**
	 * 批量获取用户的教育信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/education_batch account/profile/education_batch}
	 *
	 * @param string $uids 需要获取教育信息的用户UID，用半角逗号分隔，最多不超过20。
	 * @return array
	 */
	function account_education_batch( $uids  )
	{
		$params = array();
		if (is_array($uids) && !empty($uids)) {
			foreach($uids as $k => $v) {
				$this->id_format($uids[$k]);
			}
			$params['uids'] = join(',', $uids);
		} else {
			$params['uids'] = $uids;
		}

		return $this->oauth->get( 'account/profile/education_batch', $params );
	}


	/**
	 * 获取用户的职业信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/career account/profile/career}
	 *
	 * @param int $uid  需要获取教育信息的用户UID，默认为当前登录用户。
	 * @return array
	 */
	function account_career( $uid = NULL )
	{
		$params = array();
		if ($uid) {
			$this->id_format($uid);
			$params['uid'] = $uid;
		}
		return $this->oauth->get( 'account/profile/career', $params );
	}

	/**
	 * 批量获取用户的职业信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/career_batch account/profile/career_batch}
	 *
	 * @param string $uids 需要获取教育信息的用户UID，用半角逗号分隔，最多不超过20。
	 * @return array
	 */
	function account_career_batch( $uids )
	{
		$params = array();
		if (is_array($uids) && !empty($uids)) {
			foreach($uids as $k => $v) {
				$this->id_format($uids[$k]);
			}
			$params['uids'] = join(',', $uids);
		} else {
			$params['uids'] = $uids;
		}

		return $this->oauth->get( 'account/profile/career_batch', $params );
	}

	/**
	 * 获取隐私信息设置情况
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/get_privacy account/get_privacy}
	 * 
	 * @access public
	 * @return array
	 */
	function get_privacy()
	{
		return $this->oauth->get('account/get_privacy');
	}

	/**
	 * 获取所有的学校列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/school_list account/profile/school_list}
	 *
	 * @param array $query 搜索选项。格式：array('key0'=>'value0', 'key1'=>'value1', ....)。支持的key:
	 *  - province	int		省份范围，省份ID。
	 *  - city		int		城市范围，城市ID。
	 *  - area		int		区域范围，区ID。
	 *  - type		int		学校类型，1：大学、2：高中、3：中专技校、4：初中、5：小学，默认为1。
	 *  - capital	string	学校首字母，默认为A。
	 *  - keyword	string	学校名称关键字。
	 *  - count		int		返回的记录条数，默认为10。
	 * 参数keyword与capital二者必选其一，且只能选其一。按首字母capital查询时，必须提供province参数。
	 * @access public
	 * @return array
	 */
	function school_list( $query )
	{
		$params = $query;

		return $this->oauth->get( 'account/profile/school_list', $params );
	}

	/**
	 * 获取当前登录用户的API访问频率限制情况
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/rate_limit_status account/rate_limit_status}
	 * 
	 * @access public
	 * @return array
	 */
	function rate_limit_status()
	{
		return $this->oauth->get( 'account/rate_limit_status' );
	}

	/**
	 * OAuth授权之后，获取授权用户的UID
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/get_uid account/get_uid}
	 * 
	 * @access public
	 * @return array
	 */
	function get_uid()
	{
		return $this->oauth->get( 'account/get_uid' );
	}


	/**
	 * 更改用户资料
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/basic_update account/profile/basic_update}
	 * 
	 * @access public
	 * @param array $profile 要修改的资料。格式：array('key1'=>'value1', 'key2'=>'value2', .....)。
	 * 支持修改的项：
	 *  - screen_name		string	用户昵称，不可为空。
	 *  - gender	i		string	用户性别，m：男、f：女，不可为空。
	 *  - real_name			string	用户真实姓名。
	 *  - real_name_visible	int		真实姓名可见范围，0：自己可见、1：关注人可见、2：所有人可见。
	 *  - province	true	int		省份代码ID，不可为空。
	 *  - city	true		int		城市代码ID，不可为空。
	 *  - birthday			string	用户生日，格式：yyyy-mm-dd。
	 *  - birthday_visible	int		生日可见范围，0：保密、1：只显示月日、2：只显示星座、3：所有人可见。
	 *  - qq				string	用户QQ号码。
	 *  - qq_visible		int		用户QQ可见范围，0：自己可见、1：关注人可见、2：所有人可见。
	 *  - msn				string	用户MSN。
	 *  - msn_visible		int		用户MSN可见范围，0：自己可见、1：关注人可见、2：所有人可见。
	 *  - url				string	用户博客地址。
	 *  - url_visible		int		用户博客地址可见范围，0：自己可见、1：关注人可见、2：所有人可见。
	 *  - credentials_type	int		证件类型，1：身份证、2：学生证、3：军官证、4：护照。
	 *  - credentials_num	string	证件号码。
	 *  - email				string	用户常用邮箱地址。
	 *  - email_visible		int		用户常用邮箱地址可见范围，0：自己可见、1：关注人可见、2：所有人可见。
	 *  - lang				string	语言版本，zh_cn：简体中文、zh_tw：繁体中文。
	 *  - description		string	用户描述，最长不超过70个汉字。
	 * 填写birthday参数时，做如下约定：
	 *  - 只填年份时，采用1986-00-00格式；
	 *  - 只填月份时，采用0000-08-00格式；
	 *  - 只填某日时，采用0000-00-28格式。
	 * @return array
	 */
	function update_profile( $profile )
	{
		return $this->oauth->post( 'account/profile/basic_update',  $profile);
	}


	/**
	 * 设置教育信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/edu_update account/profile/edu_update}
	 * 
	 * @access public
	 * @param array $edu_update 要修改的学校信息。格式：array('key1'=>'value1', 'key2'=>'value2', .....)。
	 * 支持设置的项：
	 *  - type			int		学校类型，1：大学、2：高中、3：中专技校、4：初中、5：小学，默认为1。必填参数
	 *  - school_id	`	int		学校代码，必填参数
	 *  - id			string	需要修改的教育信息ID，不传则为新建，传则为更新。
	 *  - year			int		入学年份，最小为1900，最大不超过当前年份
	 *  - department	string	院系或者班别。
	 *  - visible		int		开放等级，0：仅自己可见、1：关注的人可见、2：所有人可见。
	 * @return array
	 */
	function edu_update( $edu_update )
	{
		return $this->oauth->post( 'account/profile/edu_update',  $edu_update);
	}

	/**
	 * 根据学校ID删除用户的教育信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/edu_destroy account/profile/edu_destroy}
	 * 
	 * @param int $id 教育信息里的学校ID。
	 * @return array
	 */
	function edu_destroy( $id )
	{
		$this->id_format( $id );
		$params = array();
		$params['id'] = $id;
		return $this->oauth->post( 'account/profile/edu_destroy', $params);
	}

	/**
	 * 设置职业信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/car_update account/profile/car_update}
	 * 
	 * @param array $car_update 要修改的职业信息。格式：array('key1'=>'value1', 'key2'=>'value2', .....)。
	 * 支持设置的项：
	 *  - id			string	需要更新的职业信息ID。
	 *  - start			int		进入公司年份，最小为1900，最大为当年年份。
	 *  - end			int		离开公司年份，至今填0。
	 *  - department	string	工作部门。
	 *  - visible		int		可见范围，0：自己可见、1：关注人可见、2：所有人可见。
	 *  - province		int		省份代码ID，不可为空值。
	 *  - city			int		城市代码ID，不可为空值。
	 *  - company		string	公司名称，不可为空值。
	 * 参数province与city二者必选其一<br />
	 * 参数id为空，则为新建职业信息，参数company变为必填项，参数id非空，则为更新，参数company可选
	 * @return array
	 */
	function car_update( $car_update )
	{
		return $this->oauth->post( 'account/profile/car_update', $car_update);
	}

	/**
	 * 根据公司ID删除用户的职业信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/profile/car_destroy account/profile/car_destroy}
	 * 
	 * @access public
	 * @param int $id  职业信息里的公司ID
	 * @return array
	 */
	function car_destroy( $id )
	{
		$this->id_format($id);
		$params = array();
		$params['id'] = $id;
		return $this->oauth->post( 'account/profile/car_destroy', $params);
	}

	/**
	 * 更改头像
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/avatar/upload account/avatar/upload}
	 *
	 * @param string $image_path 要上传的头像路径, 支持url。[只支持png/jpg/gif三种格式, 增加格式请修改get_image_mime方法] 必须为小于700K的有效的GIF, JPG图片. 如果图片大于500像素将按比例缩放。
	 * @return array
	 */
	function update_profile_image( $image_path )
	{
		$params = array();
		$params['image'] = "@{$image_path}";

		return $this->oauth->post('account/avatar/upload', $params);
	}

	/**
	 * 设置隐私信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/account/update_privacy account/update_privacy}
	 * 
	 * @param array $privacy_settings 要修改的隐私设置。格式：array('key1'=>'value1', 'key2'=>'value2', .....)。
	 * 支持设置的项：
	 *  - comment	int	是否可以评论我的微博，0：所有人、1：关注的人，默认为0。
	 *  - geo		int	是否开启地理信息，0：不开启、1：开启，默认为1。
	 *  - message	int	是否可以给我发私信，0：所有人、1：关注的人，默认为0。
	 *  - realname	int	是否可以通过真名搜索到我，0：不可以、1：可以，默认为0。
	 *  - badge		int	勋章是否可见，0：不可见、1：可见，默认为1。
	 *  - mobile	int	是否可以通过手机号码搜索到我，0：不可以、1：可以，默认为0。
	 * 以上参数全部选填
	 * @return array
	 */
	function update_privacy( $privacy_settings )
	{
		return $this->oauth->post( 'account/update_privacy', $privacy_settings);
	}


	/**
	 * 获取当前用户的收藏列表
	 *
	 * 返回用户的发布的最近20条收藏信息，和用户收藏页面返回内容是一致的。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/favorites favorites}
	 * 
	 * @access public
	 * @param  int $page 返回结果的页码，默认为1。
	 * @param  int $count 单页返回的记录条数，默认为50。
	 * @return array
	 */
	function get_favorites( $page = 1, $count = 50 )
	{
		$params = array();
		$params['page'] = intval($page);
		$params['count'] = intval($count);

		return $this->oauth->get( 'favorites', $params );
	}


	/**
	 * 根据收藏ID获取指定的收藏信息
	 *
	 * 根据收藏ID获取指定的收藏信息。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/favorites/show favorites/show}
	 * 
	 * @access public
	 * @param int $id 需要查询的收藏ID。
	 * @return array
	 */
	function favorites_show( $id )
	{
		$params = array();
		$this->id_format($id);
		$params['id'] = $id;
		return $this->oauth->get( 'favorites/show', $params );
	}


	/**
	 * 根据标签获取当前登录用户该标签下的收藏列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/favorites/by_tags favorites/by_tags}
	 *
	 * 
	 * @param int $tid  需要查询的标签ID。'
	 * @param int $count 单页返回的记录条数，默认为50。
	 * @param int $page 返回结果的页码，默认为1。
	 * @return array
	 */
	function favorites_by_tags( $tid, $page = 1, $count = 50)
	{
		$params = array();
		$params['tid'] = $tid;
		$params['count'] = $count;
		$params['page'] = $page;
		return $this->oauth->get( 'favorites/by_tags', $params );
	}


	/**
	 * 获取当前登录用户的收藏标签列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/favorites/tags favorites/tags}
	 * 
	 * @access public
	 * @param int $count 单页返回的记录条数，默认为50。
	 * @param int $page 返回结果的页码，默认为1。
	 * @return array
	 */
	function favorites_tags( $page = 1, $count = 50)
	{
		$params = array();
		$params['count'] = $count;
		$params['page'] = $page;
		return $this->oauth->get( 'favorites/tags', $params );
	}


	/**
	 * 收藏一条微博信息
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/favorites/create favorites/create}
	 * 
	 * @access public
	 * @param int $sid 收藏的微博id
	 * @return array
	 */
	function add_to_favorites( $sid )
	{
		$this->id_format($sid);
		$params = array();
		$params['id'] = $sid;

		return $this->oauth->post( 'favorites/create', $params );
	}

	/**
	 * 删除微博收藏。
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/favorites/destroy favorites/destroy}
	 * 
	 * @access public
	 * @param int $id 要删除的收藏微博信息ID.
	 * @return array
	 */
	function remove_from_favorites( $id )
	{
		$this->id_format($id);
		$params = array();
		$params['id'] = $id;
		return $this->oauth->post( 'favorites/destroy', $params);
	}


	/**
	 * 批量删除微博收藏。
	 *
	 * 批量删除当前登录用户的收藏。出现异常时，返回HTTP400错误。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/favorites/destroy_batch favorites/destroy_batch}
	 * 
	 * @access public
	 * @param mixed $fids 欲删除的一组私信ID，用半角逗号隔开，或者由一组评论ID组成的数组。最多20个。例如："231101027525486630,201100826122315375"或array(231101027525486630,201100826122315375);
	 * @return array
	 */
	function remove_from_favorites_batch( $fids )
	{
		$params = array();
		if (is_array($fids) && !empty($fids)) {
			foreach ($fids as $k => $v) {
				$this->id_format($fids[$k]);
			}
			$params['ids'] = join(',', $fids);
		} else {
			$params['ids'] = $fids;
		}

		return $this->oauth->post( 'favorites/destroy_batch', $params);
	}


	/**
	 * 更新一条收藏的收藏标签
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/favorites/tags/update favorites/tags/update}
	 * 
	 * @access public
	 * @param int $id 需要更新的收藏ID。
	 * @param string $tags 需要更新的标签内容，用半角逗号分隔，最多不超过2条。
	 * @return array
	 */
	function favorites_tags_update( $id,  $tags )
	{
		$params = array();
		$params['id'] = $id;
		if (is_array($tags) && !empty($tags)) {
			foreach ($tags as $k => $v) {
				$this->id_format($tags[$k]);
			}
			$params['tags'] = join(',', $tags);
		} else {
			$params['tags'] = $tags;
		}
		return $this->oauth->post( 'favorites/tags/update', $params );
	}

	/**
	 * 更新当前登录用户所有收藏下的指定标签
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/favorites/tags/update_batch favorites/tags/update_batch}
	 *
	 * @param int $tid  需要更新的标签ID。必填
	 * @param string $tag  需要更新的标签内容。必填
	 * @return array
	 */
	function favorites_update_batch( $tid, $tag )
	{
		$params = array();
		$params['tid'] = $tid;
		$params['tag'] = $tag;
		return $this->oauth->post( 'favorites/tags/update_batch', $params);
	}

	/**
	 * 删除当前登录用户所有收藏下的指定标签
	 *
	 * 删除标签后，该用户所有收藏中，添加了该标签的收藏均解除与该标签的关联关系
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/favorites/tags/destroy_batch favorites/tags/destroy_batch}
	 *
	 * @param int $tid  需要更新的标签ID。必填
	 * @return array
	 */
	function favorites_tags_destroy_batch( $tid )
	{
		$params = array();
		$params['tid'] = $tid;
		return $this->oauth->post( 'favorites/tags/destroy_batch', $params);
	}

	/**
	 * 获取某用户的话题
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/trends trends}
	 * 
	 * @param int $uid 查询用户的ID。默认为当前用户。可选。
	 * @param int $page 指定返回结果的页码。可选。
	 * @param int $count 单页大小。缺省值10。可选。
	 * @return array
	 */
	function get_trends( $uid = NULL, $page = 1, $count = 10 )
	{
		$params = array();
		if ($uid) {
			$params['uid'] = $uid;
		} else {
			$user_info = $this->get_uid();
			$params['uid'] = $user_info['uid'];
		}
		$this->id_format( $params['uid'] );
		$params['page'] = $page;
		$params['count'] = $count;
		return $this->oauth->get( 'trends', $params );
	}


	/**
	 * 判断当前用户是否关注某话题
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/trends/is_follow trends/is_follow}
	 * 
	 * @access public
	 * @param string $trend_name 话题关键字。
	 * @return array
	 */
	function trends_is_follow( $trend_name )
	{
		$params = array();
		$params['trend_name'] = $trend_name;
		return $this->oauth->get( 'trends/is_follow', $params );
	}

	/**
	 * 返回最近一小时内的热门话题
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/trends/hourly trends/hourly}
	 * 
	 * @param  int $base_app 是否基于当前应用来获取数据。1表示基于当前应用来获取数据，默认为0。可选。
	 * @return array
	 */
	function hourly_trends( $base_app = 0 )
	{
		$params = array();
		$params['base_app'] = $base_app;

		return $this->oauth->get( 'trends/hourly', $params );
	}

	/**
	 * 返回最近一天内的热门话题
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/trends/daily trends/daily}
	 * 
	 * @param int $base_app 是否基于当前应用来获取数据。1表示基于当前应用来获取数据，默认为0。可选。
	 * @return array
	 */
	function daily_trends( $base_app = 0 )
	{
		$params = array();
		$params['base_app'] = $base_app;

		return $this->oauth->get( 'trends/daily', $params );
	}

	/**
	 * 返回最近一周内的热门话题
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/trends/weekly trends/weekly}
	 * 
	 * @access public
	 * @param int $base_app 是否基于当前应用来获取数据。1表示基于当前应用来获取数据，默认为0。可选。
	 * @return array
	 */
	function weekly_trends( $base_app = 0 )
	{
		$params = array();
		$params['base_app'] = $base_app;

		return $this->oauth->get( 'trends/weekly', $params );
	}

	/**
	 * 关注某话题
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/trends/follow trends/follow}
	 * 
	 * @access public
	 * @param string $trend_name 要关注的话题关键词。
	 * @return array
	 */
	function follow_trends( $trend_name )
	{
		$params = array();
		$params['trend_name'] = $trend_name;
		return $this->oauth->post( 'trends/follow', $params );
	}

	/**
	 * 取消对某话题的关注
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/trends/destroy trends/destroy}
	 * 
	 * @access public
	 * @param int $tid 要取消关注的话题ID。
	 * @return array
	 */
	function unfollow_trends( $tid )
	{
		$this->id_format($tid);

		$params = array();
		$params['trend_id'] = $tid;

		return $this->oauth->post( 'trends/destroy', $params );
	}

	/**
	 * 返回指定用户的标签列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/tags tags}
	 * 
	 * @param int $uid 查询用户的ID。默认为当前用户。可选。
	 * @param int $page 指定返回结果的页码。可选。
	 * @param int $count 单页大小。缺省值20，最大值200。可选。
	 * @return array
	 */
	function get_tags( $uid = NULL, $page = 1, $count = 20 )
	{
		$params = array();
		if ( $uid ) {
			$params['uid'] = $uid;
		} else {
			$user_info = $this->get_uid();
			$params['uid'] = $user_info['uid'];
		}
		$this->id_format( $params['uid'] );
		$params['page'] = $page;
		$params['count'] = $count;
		return $this->oauth->get( 'tags', $params );
	}

	/**
	 * 批量获取用户的标签列表
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/tags/tags_batch tags/tags_batch}
	 * 
	 * @param  string $uids 要获取标签的用户ID。最大20，逗号分隔。必填
	 * @return array
	 */
	function get_tags_batch( $uids )
	{
		$params = array();
		if (is_array( $uids ) && !empty( $uids )) {
			foreach ($uids as $k => $v) {
				$this->id_format( $uids[$k] );
			}
			$params['uids'] = join(',', $uids);
		} else {
			$params['uids'] = $uids;
		}
		return $this->oauth->get( 'tags/tags_batch', $params );
	}

	/**
	 * 返回用户感兴趣的标签
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/tags/suggestions tags/suggestions}
	 * 
	 * @access public
	 * @param int $count 单页大小。缺省值10，最大值10。可选。
	 * @return array
	 */
	function get_suggest_tags( $count = 10)
	{
		$params = array();
		$params['count'] = intval($count);
		return $this->oauth->get( 'tags/suggestions', $params );
	}

	/**
	 * 为当前登录用户添加新的用户标签
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/tags/create tags/create}
	 * 
	 * @access public
	 * @param mixed $tags 要创建的一组标签，每个标签的长度不可超过7个汉字，14个半角字符。多个标签之间用逗号间隔，或由多个标签构成的数组。如："abc,drf,efgh,tt"或array("abc", "drf", "efgh", "tt")
	 * @return array
	 */
	function add_tags( $tags )
	{
		$params = array();
		if (is_array($tags) && !empty($tags)) {
			$params['tags'] = join(',', $tags);
		} else {
			$params['tags'] = $tags;
		}
		return $this->oauth->post( 'tags/create', $params);
	}

	/**
	 * 删除标签
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/tags/destroy tags/destroy}
	 * 
	 * @access public
	 * @param int $tag_id 标签ID，必填参数
	 * @return array
	 */
	function delete_tag( $tag_id )
	{
		$params = array();
		$params['tag_id'] = $tag_id;
		return $this->oauth->post( 'tags/destroy', $params );
	}

	/**
	 * 批量删除标签
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/tags/destroy_batch tags/destroy_batch}
	 * 
	 * @access public
	 * @param mixed $ids 必选参数，要删除的tag id，多个id用半角逗号分割，最多10个。或由多个tag id构成的数组。如：“553,554,555"或array(553, 554, 555)
	 * @return array
	 */
	function delete_tags( $ids )
	{
		$params = array();
		if (is_array($ids) && !empty($ids)) {
			$params['ids'] = join(',', $ids);
		} else {
			$params['ids'] = $ids;
		}
		return $this->oauth->post( 'tags/destroy_batch', $params );
	}


	/**
	 * 验证昵称是否可用，并给予建议昵称
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/register/verify_nickname register/verify_nickname}
	 *
	 * @param string $nickname 需要验证的昵称。4-20个字符，支持中英文、数字、"_"或减号。必填
	 * @return array
	 */
	function verify_nickname( $nickname )
	{
		$params = array();
		$params['nickname'] = $nickname;
		return $this->oauth->get( 'register/verify_nickname', $params );
	}



	/**
	 * 搜索用户时的联想搜索建议
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/search/suggestions/users search/suggestions/users}
	 *
	 * @param string $q 搜索的关键字，必须做URLencoding。必填,中间最好不要出现空格
	 * @param int $count 返回的记录条数，默认为10。
	 * @return array
	 */
	function search_users( $q,  $count = 10 )
	{
		$params = array();
		$params['q'] = $q;
		$params['count'] = $count;
		return $this->oauth->get( 'search/suggestions/users',  $params );
	}


	/**
	 * 搜索微博时的联想搜索建议
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/search/suggestions/statuses search/suggestions/statuses}
	 *
	 * @param string $q 搜索的关键字，必须做URLencoding。必填
	 * @param int $count 返回的记录条数，默认为10。
	 * @return array
	 */
	function search_statuses( $q,  $count = 10)
	{
		$params = array();
		$params['q'] = $q;
		$params['count'] = $count;
		return $this->oauth->get( 'search/suggestions/statuses', $params );
	}


	/**
	 * 搜索学校时的联想搜索建议
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/search/suggestions/schools search/suggestions/schools}
	 *
	 * @param string $q 搜索的关键字，必须做URLencoding。必填
	 * @param int $count 返回的记录条数，默认为10。
	 * @param int type 学校类型，0：全部、1：大学、2：高中、3：中专技校、4：初中、5：小学，默认为0。选填
	 * @return array
	 */
	function search_schools( $q,  $count = 10,  $type = 1)
	{
		$params = array();
		$params['q'] = $q;
		$params['count'] = $count;
		$params['type'] = $type;
		return $this->oauth->get( 'search/suggestions/schools', $params );
	}

	/**
	 * 搜索公司时的联想搜索建议
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/search/suggestions/companies search/suggestions/companies}
	 *
	 * @param string $q 搜索的关键字，必须做URLencoding。必填
	 * @param int $count 返回的记录条数，默认为10。
	 * @return array
	 */
	function search_companies( $q, $count = 10)
	{
		$params = array();
		$params['q'] = $q;
		$params['count'] = $count;
		return $this->oauth->get( 'search/suggestions/companies', $params );
	}


	/**
	 * ＠用户时的联想建议
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/search/suggestions/at_users search/suggestions/at_users}
	 *
	 * @param string $q 搜索的关键字，必须做URLencoding。必填
	 * @param int $count 返回的记录条数，默认为10。
	 * @param int $type 联想类型，0：关注、1：粉丝。必填
	 * @param int $range 联想范围，0：只联想关注人、1：只联想关注人的备注、2：全部，默认为2。选填
	 * @return array
	 */
	function search_at_users( $q, $count = 10, $type=0, $range = 2)
	{
		$params = array();
		$params['q'] = $q;
		$params['count'] = $count;
		$params['type'] = $type;
		$params['range'] = $range;
		return $this->oauth->get( 'search/suggestions/at_users', $params );
	}
	
	/**
	 * 返回与关键字相匹配的微博
	 *
	 * 对应API：{@link http://wiki.intra.weibo.com/1/search/statuses}
	 *
	 *
	 */
	function search_statuses_intra($params=array())
	{
		$params_arr = array(
						'source'=>$this->oauth->client_id,
						'sid'=>'qa_test',
						'q'=>'',
						'uid'=>'',
						'atten'=>'',
						'gid'=>'',
						'atme'=>'',
						'ids'=>'',
						'sort'=>'',
						'province'=>'',
						'city'=>'',
						'starttime'=>'',
						'endtime'=>'',
						'hasori'=>'',
						'hasret'=>'',
						'hastext'=>'',
						'haspic'=>'',
						'hasvideo'=>'',
						'hasmusic'=>'',
						'haslink'=>'',
						'hasat'=>'',
						'hasv'=>'',
						'istag'=>'',
						'onlynum'=>'',
						'dup'=>'',
						'antispam'=>'',
						'page'=>'',
						'count'=>'',
						'base_app'=>'',
					  );
		foreach($params as $key=>$val)
		{
			if(!isset($params_arr[$key]) || $key == 'source' || $key == 'sid')
				unset($params[$key]);			
		}
		$params = array_merge(array('sid'=>$params_arr['sid']),$params);
		
		return $this->oauth->get( 'http://i.api.weibo.com/2/search/statuses.json', $params );
	}


	


	/**
	 * 搜索与指定的一个或多个条件相匹配的微博
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/search/statuses search/statuses}
	 *
	 * @param array $query 搜索选项。格式：array('key0'=>'value0', 'key1'=>'value1', ....)。支持的key:
	 *  - q				string	搜索的关键字，必须进行URLencode。
	 *  - filter_ori	int		过滤器，是否为原创，0：全部、1：原创、2：转发，默认为0。
	 *  - filter_pic	int		过滤器。是否包含图片，0：全部、1：包含、2：不包含，默认为0。
	 *  - fuid			int		搜索的微博作者的用户UID。
	 *  - province		int		搜索的省份范围，省份ID。
	 *  - city			int		搜索的城市范围，城市ID。
	 *  - starttime		int		开始时间，Unix时间戳。
	 *  - endtime		int		结束时间，Unix时间戳。
	 *  - count			int		单页返回的记录条数，默认为10。
	 *  - page			int		返回结果的页码，默认为1。
	 *  - needcount		boolean	返回结果中是否包含返回记录数，true：返回、false：不返回，默认为false。
	 *  - base_app		int		是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * needcount参数不同，会导致相应的返回值结构不同
	 * 以上参数全部选填
	 * @return array
	 */
	function search_statuses_high( $query )
	{
		return $this->oauth->get( 'search/statuses', $query );
	}
    /**
     * 搜索微博
     * 文档地址http://open.weibo.com/wiki/C/2/search/statuses/limited
     * 
     * @param string $q 搜索关键词，必须进行url_encode
     * @param type $page    页码
     * @param type $count   条数
     * @param type $sort    排序方式，time：时间倒序、hot：热门度、fwnum：按转发数倒序、cmtnum：评论数倒序，默认为time。
     * @param type $starttime 搜索范围起始时间，取值为时间戳。
     * @param type $endtime 搜索范围结束时间，取值为时间戳。
     * @param type $hasori 是否包含原创，0：不包含原创、1：只包含原创，默认为空（全部）。
     * @param type $hasret 是否包含转发，0：不包含转发、1：只包含转发，默认为空（全部）。
     * @param type $hastext 是否包含纯文本，0：不包含纯文本、1：只包含纯文本，默认为空（全部）。
     * @param type $haspic 是否包含图片，0：不包含图片、1：只包含图片，默认为空（全部）。
     * @param type $hasvideo 是否包含视频，0：不包含视频、1：只包含视频，默认为空（全部）。
     * @param type $hasmusic 是否包含音乐，0：不包含音乐、1：只包含音乐，默认为空（全部）。
     * @param type $haslink 是否包含链接，0：不包含链接、1：只包含链接，默认为空（全部）。
     * @param type $hasat 是否包含@，0：不包含@、1：只包含@，默认为空（全部）。
     * @param type $hasv 是否为v用户发言，0：否、1：是，默认为空（全部）。
     * @param type $istag 是否严格为搜##内的话题，0：否、1：##内模糊匹配、2：##内精确匹配，默认为0。
     * @param type $onlynum 是否只返回总数，0：否、1：是，默认为0。
     * @param type $dup 是否排重（不显示相似数据），0：否、1：是，默认为1。
     * @param type $antispam 是否反垃圾（不显示低质量数据），0：否、1：是，默认为1。
     * @param type $base_app 是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
     * @return array
     */
    function search_statuses_limited($q,$page=1,$count=10,$sort=FALSE,$starttime=FALSE,$endtime=FALSE,$hasori=NULL,$hasret=NULL,
            $hastext=NULL,$haspic=NULL,$hasvideo=NULL,$hasmusic=NULL,$haslink=NULL,$hasat=NULL,$hasv=NULL,
            $istag=0,$onlynum=0,$dup=FALSE,$antispam=FALSE,$base_app=0){
        $params = array();
		$params['q'] = $q;
        $params['page'] = $page;
		$params['count'] = $count;
        if($sort && ($sort == "time" || $sort == "hot" || $sort == "fwnum" || $sort == "cmtnum")){
            $params['sort'] = $sort;
        }
        if($starttime){
            $params['starttime'] = $starttime;
        }
        if($endtime){
            $params['endtime'] = $endtime;
        }
        if($hasori !== NULL){
            $params['hasori'] = $hasori;
        }
        if($hasret !== NULL){
            $params['hasret'] = $hasret;
        }
        if($hastext !== NULL){
            $params['hastext'] = $hastext;
        }
        if($haspic !== NULL){
            $params['haspic'] = $haspic;
        }
        if($hasvideo !== NULL){
            $params['hasvideo'] = $hasvideo;
        }
        if($hasmusic !== NULL){
            $params['hasmusic'] = $hasmusic;
        }
        if($haslink !== NULL){
            $params['haslink'] = $haslink;
        }
        if($hasat !== NULL){
            $params['hasat'] = $hasat;
        }
        if($hasv !== NULL){
            $params['hasv'] = $hasv;
        }
        if($istag){
            $params['istag'] = $istag;
        }
        if($onlynum){
            $params['onlynum'] = $onlynum;
        }
        if($dup || $dup === 0){
            $params['dup'] = $dup;
        }
        if($antispam || $antispam === 0){
            $params['antispam'] = $antispam;
        }
        if($base_app){
            $params['base_app'] = $base_app;
        }
        
		return $this->oauth->get( 'https://c.api.weibo.com/2/search/statuses/limited.json', $params );
    }

	/**
	 * 通过关键词搜索用户
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/search/users search/users}
	 *
	 * @param array $query 搜索选项。格式：array('key0'=>'value0', 'key1'=>'value1', ....)。支持的key:
	 *  - q			string	搜索的关键字，必须进行URLencode。
	 *  - snick		int		搜索范围是否包含昵称，0：不包含、1：包含。
	 *  - sdomain	int		搜索范围是否包含个性域名，0：不包含、1：包含。
	 *  - sintro	int		搜索范围是否包含简介，0：不包含、1：包含。
	 *  - stag		int		搜索范围是否包含标签，0：不包含、1：包含。
	 *  - province	int		搜索的省份范围，省份ID。
	 *  - city		int		搜索的城市范围，城市ID。
	 *  - gender	string	搜索的性别范围，m：男、f：女。
	 *  - comorsch	string	搜索的公司学校名称。
	 *  - sort		int		排序方式，1：按更新时间、2：按粉丝数，默认为1。
	 *  - count		int		单页返回的记录条数，默认为10。
	 *  - page		int		返回结果的页码，默认为1。
	 *  - base_app	int		是否只获取当前应用的数据。0为否（所有数据），1为是（仅当前应用），默认为0。
	 * 以上所有参数全部选填
	 * @return array
	 */
	function search_users_keywords( $query )
	{
		return $this->oauth->get( 'search/users', $query );
	}



	/**
	 * 获取系统推荐用户
	 *
	 * 返回系统推荐的用户列表。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/suggestions/users/hot suggestions/users/hot}
	 * 
	 * @access public
	 * @param string $category 分类，可选参数，返回某一类别的推荐用户，默认为 default。如果不在以下分类中，返回空列表：<br />
	 *  - default:人气关注
	 *  - ent:影视名星
	 *  - hk_famous:港台名人
	 *  - model:模特
	 *  - cooking:美食&健康
	 *  - sport:体育名人
	 *  - finance:商界名人
	 *  - tech:IT互联网
	 *  - singer:歌手
	 *  - writer：作家
	 *  - moderator:主持人
	 *  - medium:媒体总编
	 *  - stockplayer:炒股高手
	 * @return array
	 */
	function hot_users( $category = "default" )
	{
		$params = array();
		$params['category'] = $category;

		return $this->oauth->get( 'suggestions/users/hot', $params );
	}

	/**
	 * 获取用户可能感兴趣的人
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/suggestions/users/may_interested suggestions/users/may_interested}
	 * 
	 * @access public
	 * @param int $page 返回结果的页码，默认为1。
	 * @param int $count 单页返回的记录条数，默认为10。
	 * @return array
	 * @ignore
	 */
	function suggestions_may_interested( $page = 1, $count = 10 )
	{   
		$params = array();
		$params['page'] = $page;
		$params['count'] = $count;
		return $this->oauth->get( 'suggestions/users/may_interested', $params);
	}

	/**
	 * 根据一段微博正文推荐相关微博用户。 
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/suggestions/users/by_status suggestions/users/by_status}
	 * 
	 * @access public
	 * @param string $content 微博正文内容。
	 * @param int $num 返回结果数目，默认为10。
	 * @return array
	 */
	function suggestions_users_by_status( $content, $num = 10 )
	{
		$params = array();
		$params['content'] = $content;
		$params['num'] = $num;
		return $this->oauth->get( 'suggestions/users/by_status', $params);
	}

	/**
	 * 热门收藏
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/suggestions/favorites/hot suggestions/favorites/hot}
	 *
	 * @param int $count 每页返回结果数，默认20。选填
	 * @param int $page 返回页码，默认1。选填
	 * @return array
	 */
	function hot_favorites( $page = 1, $count = 20 )
	{
		$params = array();
		$params['count'] = $count;
		$params['page'] = $page;
		return $this->oauth->get( 'suggestions/favorites/hot', $params);
	}

	/**
	 * 把某人标识为不感兴趣的人
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/suggestions/users/not_interested suggestions/users/not_interested}
	 *
	 * @param int $uid 不感兴趣的用户的UID。
	 * @return array
	 */
	function put_users_not_interested( $uid )
	{
		$params = array();
		$params['uid'] = $uid;
		return $this->oauth->post( 'suggestions/users/not_interested', $params);
	}



	// =========================================

	/**
	 * @ignore
	 */
	protected function request_with_pager( $url, $page = false, $count = false, $params = array() )
	{
		if( $page ) $params['page'] = $page;
		if( $count ) $params['count'] = $count;

		return $this->oauth->get($url, $params );
	}

	/**
	 * @ignore
	 */
	protected function request_with_uid( $url, $uid_or_name, $page = false, $count = false, $cursor = false, $post = false, $params = array())
	{
		if( $page ) $params['page'] = $page;
		if( $count ) $params['count'] = $count;
		if( $cursor )$params['cursor'] =  $cursor;

		if( $post ) $method = 'post';
		else $method = 'get';

		if ( $uid_or_name !== NULL ) {
			$this->id_format($uid_or_name);
			$params['id'] = $uid_or_name;
		}

		return $this->oauth->$method($url, $params );

	}

	/**
	 * @ignore
	 */
	protected function id_format(&$id) {
		if ( is_float($id) ) {
			$id = number_format($id, 0, '', '');
		} elseif ( is_string($id) ) {
			$id = trim($id);
		}
	}

	/* ===========================缓存接口=================================== */
	//mc公共缓存
	function mc()
	{
		return mem_cache_share();
	}
	
	//本地缓存
	function mc_local()
	{
		return mem_cache_local();
	}
	/**
	 * 根据ID获取单条微博信息内容
	 *
	 * 获取单条ID的微博信息，作者信息将同时返回。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/show statuses/show}
	 *
	 * @access public
	 * @param int $id 要获取已发表的微博ID, 如ID不存在返回空
	 * @return array
	 */
	function show_status_mc( $id )
	{
		$mc = $this->mc_local();
		$mc_key = 'mc_api_statuses_show_'.$id;
		$data = $mc->get($mc_key);
		if ( empty($data) )
		{
			$this->id_format($id);
			$params = array();
			$params['id'] = $id;
			$data = $this->oauth->get('statuses/show', $params);
			if ( !$data['error'] )
			$mc->set($mc_key,$data,3600*24);
		}
		return $data;
	}
	/**
	 * 根据微博地址获取单条微博信息内容
	 *
	 * 获取单条ID的微博信息，作者信息将同时返回。
	 *
	 * @access public
	 * @param string $url 要获取已发表的微博URL, 如URL不存在返回空
	 * @return array
	 */
	function show_status_url( $url='' )
	{
		if ( $url == '' )
			return '';

		$pattern = '/^(.*?)weibo\.com\/(.*?)\/(.*?)$/i';
		preg_match_all($pattern,$url, $matches);

		$mid = $matches[3][0];
		$res = $this->queryid( $mid, 1, 0, 0, 1);

		if ( $res['error'] )
		{
			return $res;
		}
		//print_r($res);
		$id = $res['id'];
		return $this->show_status_mc($id);
	}
	/**
	 * 获取表情列表
	 *
	 * 返回新浪微博官方所有表情、魔法表情的相关信息。包括短语、表情类型、表情分类，是否热门等。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/emotions emotions}
	 *
	 * @access public
	 * @param string $type 表情类别。"face":普通表情，"ani"：魔法表情，"cartoon"：动漫表情。默认为"face"。可选。
	 * @param string $language 语言类别，"cnname"简体，"twname"繁体。默认为"cnname"。可选
	 * @return array
	 */
	function emotions_mc( $type = "face", $language = "cnname" )
	{
		$mc = $this->mc_local();
		$mc_key = 'mc_api_emotions_'.$type.'_'.$language;
		$data = $mc->get($mc_key);
		if ( empty($data) )
		{
			$data = $this->emotions( $type, $language);

			if ( !$data['error'] )
				$mc->set($mc_key,$data,3600*24);
		}
		return $data;
	}
	/**
	 * 获取省份列表
	 *
	 * 对应API：{@link https://api.weibo.com/2/common/get_province.json common/get_province.json}
	 *
	 * @access public
	 * @param string $country 国家的国家代码。 
	 * @param string $capital 省份的首字母，a-z，可为空代表返回全部，默认为全部。 
	 * @param string $language 返回的语言版本，zh-cn：简体中文、zh-tw：繁体中文、english：英文，默认为zh-cn。 
	 * @return array
	 * @ignore
	 */
	function get_province_mc( $country , $capital='', $language='zh-cn')
	{
		$mc = $this->mc_local();
		$mc_key = 'mc_api_get_province_'.$country.'_'.$capital.'_'.$language;
		$data = $mc->get($mc_key);
		if ( empty($data) )
		{
			$params = array();
			$params['country'] = $country;
			$params['capital'] = $capital;
			$params['language'] = $language;
			$data = $this->oauth->get( 'common/get_province', $params);
			if ( !$data['error'] )
				$mc->set($mc_key,$data,3600*24);
		}
		return $data;
	}
	/**
	 * 获取城市列表
	 *
	 * 对应API：{@link https://api.weibo.com/2/common/get_city.json common/get_city.json}
	 *
	 * @access public
	 * @param string $province 省份代码。
	 * @param string $capital 省份的首字母，a-z，可为空代表返回全部，默认为全部。
	 * @param string $language 返回的语言版本，zh-cn：简体中文、zh-tw：繁体中文、english：英文，默认为zh-cn。
	 * @return array
	 * @ignore
	 */
	function get_city_mc( $province , $capital='', $language='zh-cn')
	{
		$mc = $this->mc_local();
		$mc_key = 'mc_api_get_city_'.$province.'_'.$capital.'_'.$language;
		$data = $mc->get($mc_key);
		if ( empty($data) )
		{
			$params = array();
			$params['province'] = $province;
			$params['capital'] = $capital;
			$params['language'] = $language;
			$data = $this->oauth->get( 'common/get_city', $params);
			if ( !$data['error'] )
				$mc->set($mc_key,$data,3600*24);
		}
		return $data;
	}
	/**
	 * 将一个或多个长链接转换成短链接 
	 *
	 * 对应API：{@link https://api.weibo.com/2/short_url/shorten.json}
	 *  $url_long可传入字符串或者数组
	 * @param string $url_long 需要转换的长链接，需要URLencoded，最多不超过20个。
	 * @return array
	 */
	function short_url_shorten( $url_long )
	{
		$params = array();
        if(is_array($url_long)){
            $paramStr = "source={$this->oauth->client_id}";
            foreach($url_long as $val){
                $paramStr .= "&url_long={$val}";
            }
            return $this->oauth->get2('short_url/shorten', $paramStr);
        }else{
            $params['source'] = $this->oauth->client_id;
            $params['url_long'] = $url_long;
            return $this->oauth->get( 'short_url/shorten', $params);
        }
	}
	/**
	 * 将一个或多个短链接转换成长链接
	 *
	 * 对应API：{@link https://api.weibo.com/2/short_url/expand.json}
	 *
	 * @param string $url_short 需要转换的短链接，需要URLencoded，最多不超过20个。
	 * @return array
	 */
	function short_url_expand( $url_short )
	{
		$params = array();
		$params['source'] = $this->oauth->client_id;
		$params['url_short'] = $url_short;
		//print_r($params);
		return $this->oauth->get( 'short_url/expand', $params);
	}
	//转换url成短url
	function shortUrl($url, $is_short = true, $is_batch = false, $useType = true)
	{
		$result = $this->oauth->oAuthRequest(sprintf('%sshortUrl.%s', 'https://api.t.sina.com.cn/', 'json'), 'get', array('url' => $url, 'is_short' => $is_short, 'is_batch' => $is_batch), $useType, false, true);
		return $result;
	}
	/**
	* 将获取短链接的总点击数 
	* 对应API：{@link https://api.weibo.com/2/short_url/clicks.json}
	* @param string $url_short短链接
	* @return array
	*/
	function short_url_clicks($short_url){
		$params = array();
 		$params['source'] = $this->oauth->client_id;
		$params['url_short'] = $short_url;
		//print_r($params);
		return $this->oauth->get( 'short_url/clicks', $params);
	}
	/**
	 * 获取短链接的总点击数
	 * 对应API：{@link https://api.weibo.com/2/short_url/clicks.json}
	 * @param string $url_short 短链接
	 * @return array
	 */
	function short_url_locations($short_url){
		$params = array();
		$params['source'] = $this->oauth->client_id;
		$params['url_short'] = $short_url;
		//print_r($params);
		return $this->oauth->get('short_url/locations', $params);
	}
	/**
	 * 获取一个短链接点击的referer来源和数量
	 * 对应API：{@link https://api.weibo.com/2/short_url/referers.json}
	 * @param string $url_short短链接
	 * @return array
	 */
	function short_url_referers($short_url){
		$params = array();
		$params['source'] = $this->oauth->client_id;
		$params['url_short'] = $short_url;
		//print_r($params);
		return $this->oauth->get('short_url/referers', $params);
	} 
	/**
	 * 获取短链接在微博上的微博分享数 
	 * 对应API：{@link https://api.weibo.com/2/short_url/share/counts.json}
	 * @param string $url_short短链接
	 * @return array
	 */
	function short_url_sharecounts($short_url) {
		$params = array();
		$params['source'] = $this->oauth->client_id;
		$params['url_short'] = $short_url;
		//print_r($params);
		return $this->oauth->get('short_url/share/counts', $params);
	}
	/**
	 * 获取短链接在微博上的微博评论数 
	 * 对应API：{@link https://api.weibo.com/2/short_url/comment/counts.json}
	 * @param string $url_short短链接
	 * @return array
	 */
	function short_url_commentcounts($short_url) {
		$params = array();
		$params['source'] = $this->oauth->client_id;
		$params['url_short'] = $short_url;
		//print_r($params);
		return $this->oauth->get('short_url/comment/counts', $params);
	}
	/**
	 * 获取用户最近30天带短链的微博的短链的点击数
	 * @param type $userid
	 * @param type $page
	 * @param type $count
	 * @return type
	 */
	function short_url_weibo_clicks($userid,$page=1,$count=20){
		//
		$params = array();
		$params['source'] = $this->oauth->client_id;
		$params['uid'] = $userid;
		$params['page'] = $page;
		$params['count'] = $count;
		//print_r($params);
		return $this->oauth->get('https://api.weibo.com/2/proxy/cdata/enterprisev2/get_shorturl_analysis.json', $params);
	}
	function exposure( $id ){
		$params = array();
		$params['id'] = $id;
		return $this->oauth->get('https://c.api.weibo.com/2/statuses/exposure.json', $params);
	}
    /**
     * 获取用户30天的曝光数 
     * http://api.weibo.com/2/proxy/sdata/enterprisev2/get_weibo_exposure.json
     * @param type $uid
     * @param type $page
     * @param type $count
     * @return type
     */
    function userExposure($id,$page=1,$count=20){
        $params = array();
        //$params['source'] = $this->oauth->client_id;
		$params['uid'] = $id;
        $params['page'] = $page;
        $params['count'] = $count;
		return $this->oauth->get($this->oauth->host. 'proxy/sdata/enterprisev2/get_weibo_exposure.json', $params);
    }
    /**
     * 获取用户30天的点击
     * https://api.weibo.com/2/proxy/cdata/enterprisev2/get_shorturl_analysis.json
     * @param type $id
     * @param type $page
     * @param type $count
     * @return type
     */
    function userClick($id,$page=1,$count=20){
        $params = array();
		$params['uid'] = $id;
        $params['page'] = $page;
        $params['count'] = $count;
		return $this->oauth->get($this->oauth->host. 'proxy/cdata/enterprisev2/get_shorturl_analysis.json', $params);
    }
	/**
	 * 通过微博（评论、私信）ID获取其MID 存入MC
	 *
	 * 对应API：{@link http://open.weibo.com/wiki/2/statuses/querymid statuses/querymid}
	 *
	 * @param int|string $id  需要查询的微博（评论、私信）ID，批量模式下，用半角逗号分隔，最多不超过20个。
	 * @param int $type  获取类型，1：微博、2：评论、3：私信，默认为1。
	 * @param int $is_batch 是否使用批量模式，0：否、1：是，默认为0。
	 * @return array
	 */
	function querymid_mc( $id, $type = 1, $is_batch = 0 )
	{
		$mc = $this->mc_local();
		$mc_key = 'mc_api_querymid_'.$id.'_'.$type.'_'.$is_batch;
		$data = $mc->get($mc_key);
		if ( empty($data) )
		{
			$data = $this->querymid($id,$type,$is_batch);
			if ( !$data['error'] )
				$mc->set($mc_key,$data,3600*24);
		}
		return $data;
	}
    
    /**
     * 获取当前用户粉丝的性别细分数据
     * {
     *       "uid": 10438,
     *       "result": {
     *              "male": "63",  //男粉丝数
     *              "female": "58",  //女粉丝数
     *              "vip": "58",  //vip粉丝数
     *              "daren": "58"  //达人粉丝数
     *          }
     *  }
     * 文档： http://wiki.intra.weibo.com/2/friendships/followers/gender_count
     * @param type $uid
     * @return type
     */
    function fans_gender_count($uid)
    {
        $params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		return $this->oauth->get( $this->oauth->cHost .'friendships/followers/gender_count.json', $params );
    }
    
    /**
     * 获取当前用户粉丝的年龄段细分数据
     * {
     *      "uid": 10438,
     *      "result":
     *          {
     *              "total_count": "16",
     *              "ages": [
     *                  {"age": "0-17","count": "2"},
     *                  {"age": "18-24","count": "2"},
     *                  {"age": "25-29","count": "2"},
     *                  {"age": "30-34","count": "2"},
     *                  {"age": "35-39","count": "2"},
     *                  {"age": "40-49","count": "2"},
     *                  {"age": "50-59","count": "2"},
     *                  {"age": "60-",//大于等于60岁 "count": "2"}
     *              ]
     *          }
     *  }
     * 文档： http://wiki.intra.weibo.com/2/friendships/followers/age_group_count
     * @param type $uid
     * @return type
     */
    function fans_age_count($uid)
    {
        $params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		return $this->oauth->get( $this->oauth->cHost .'friendships/followers/age_group_count.json', $params );
    }
    
    /**
     * 获取当前用户粉丝的性别细分数据
     * 文档： http://wiki.intra.weibo.com/2/friendships/followers/location_count
     * {
     *      "uid": 10438,
     *      "result":
     *          {
     *              "total_count": "121",
     *              "locations": [
     *                  {
     *                      "count": "56",
     *                      "province": "13"  //省级地区编码
     *                  },
     *                  ...
     *              ]
     *          }
     *  }
     * @param type $uid
     * @return type
     */
    function fans_location_count($uid)
    {
        $params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		return $this->oauth->get( $this->oauth->cHost .'friendships/followers/location_count.json', $params );
    }
    
    /**
     * 获取用户的全部粉丝的前几个微博语义标签
     * {
     *      "uid":124353,
     *       "result": [
     *           "计算机", //前几个标签列表
     *           "微博"， ...
     *       ]
     *   }
     * 文档： http://wiki.intra.weibo.com/2/friendships/followers/tags
     * @param type $uid
     * @return array
     */
    function fans_tags($uid)
    {
        $params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		return $this->oauth->get( $this->oauth->cHost .'friendships/followers/tags.json', $params );
    }
    
    /**
     * 获取当前用户活跃粉丝的性别、年龄、地域细分数据
     * {
     *       "gender": 
     *          {
     *              "male": "63",  //男粉丝数
     *              "female": "58"  //女粉丝数
     *          },
     *       "age":{"0-17":"305","18-24":"867","25-29":"546",.......},
     *       "area":{"35":"101","36":"26","15":"7","33":"171","34":"42","13":"83"....... }
     *  }
     * http://i2.api.weibo.com/2/proxy/sdata/enterprisev2/get_active_fans_properties.json 
     * @param type $uid
     * @return type
     */
    function active_fans_properties($uid)
    {
        $params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		return $this->oauth->get('http://i2.api.weibo.com/2/proxy/sdata/enterprisev2/get_active_fans_properties.json', $params );
    }
    
    /**
     * 获取用户的全部粉丝的前几个微博语义标签
     * {"旅游":"474","美女":"421","美食":"400","搞笑幽默":"322","穿衣美容":"265","娱乐":"248","星座命理":"236","时尚":"196","名人明星":"184"}
     * http://i2.api.weibo.com/2/proxy/sdata/enterprisev2/get_active_fans_tag_top.json
     * @param type $uid
     * @return array
     */
    function active_fans_tags($uid)
    {
        $params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		return $this->oauth->get( 'http://i2.api.weibo.com/2/proxy/sdata/enterprisev2/get_active_fans_tag_top.json', $params );
    }
    /**
     * 获取当前用户互动粉丝的性别、年龄、地域细分数据
     * {
     *       "gender": 
     *          {
     *              "male": "63",  //男粉丝数
     *              "female": "58"  //女粉丝数
     *          },
     *       "age":{"0-17":"305","18-24":"867","25-29":"546",.......},
     *       "area":{"35":"101","36":"26","15":"7","33":"171","34":"42","13":"83"....... }
     *  }
     * http://i2.api.weibo.com/2/proxy/sdata/enterprisev2/get_interact_fans_properties.json 
     * @param type $uid
     * @return type
     */
    function interact_fans_properties($uid)
    {
        $params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
		return $this->oauth->get('http://i2.api.weibo.com/2/proxy/sdata/enterprisev2/get_interact_fans_properties.json', $params );
    }
    /**
     * 当前登录用户向特定用户发私信 
     * 文档地址：http://wiki.intra.weibo.com/2/direct_messages/post 
     * @param type $text 要发送的消息内容。需要做URLEncode，文本大小必须小于300个汉字。 
     * @param type $uid 指定接收用户的ID。
     * @param type $screen_name 私信接收方的微博昵称。在用户ID与微博昵称容易混淆的时候，使用该参数。
     * @param type $fids 需要发送的附件ID。多个ID时以逗号分隔。上限为10个。  
     * @param type $id 需要发送的微博ID。 
     * @param type $fids 需要发送的附件ID。多个ID时以逗号分隔。上限为10个。 
     * @param type $lat  纬度。有效范围：-90.0到+90.0，+表示北纬。默认为0.0。 
     * @param type $long 经度。有效范围：-180.0到+180.0，+表示东经。默认为0.0。  
     * @param type $annotations  500字节以内,供无线部门使用的扩展字段。 
     * @param type $is_encoded  返回结果是否转义。0：不转义，1：转义。默认1。需要转义的符号及转义规则见相关约束。
     * @param type $node_id  操作身份的专页node_id
     * @param type $rip  开发者上报的操作用户真实IP，形如：211.156.0.1
     * @return type
     */
    function post_message($text, $uid, $screen_name='', $fids=NULL,$id=NULL,$lat=NULL,$long=NULL,$annotations=NULL,$is_encoded=1,$node_id=NULL,$rip=NULL){
        $params = array();
		$this->id_format($uid);
        $params['text'] = $text;
		$params['uid'] = $uid; 
        $params['screen_name'] = $screen_name;
        if($fids){
            if(is_array($fids))
                $params['fids'] = implode (",", $fids);
            else
                $params['fids'] = $fids;
        }
		return $this->oauth->post('http://i2.api.weibo.com/2/direct_messages/new.json', $params );
    }
    
    /**
     * 获取当前用户收到的最新私信列表。返回私信按最新时间排序 
     * 文档地址：http://wiki.intra.weibo.com/1/direct_messages
     * @param type $since_id 返回ID比since_id大的私信（即比since_id时间晚的私信）
     * @param type $max_id 返回ID小于或等于max_id的私信
     * @param type $count 返回结果的条数数量，最大不超过200。 
     * @param type $page 返回结果的页码
     * @param type $is_encoded 返回结果是否转义
     */
    function getmessage($since_id=0, $max_id=0, $count=20, $page=1,$is_encoded=1){
        $params = array();
		if($since_id)
            $params['since_id'] = $since_id;
        if($max_id)
            $params['max_id'] = $max_id;
        if($count)
            $params['count'] = $count;
        if($page)
            $params['page'] = $page;
        if($is_encoded)
            $params['is_encoded'] = $is_encoded;
		return $this->oauth->get('http://i2.api.weibo.com/2/direct_messages.json', $params );
    }
    
    /**
     * 获取当前登录用户的留言箱的消息接收列表 。返回私信按最新时间排序 
     * 文档地址：http://i2.api.weibo.com/2/direct_messages/public/messages.json
     * @param type $since_id 返回ID比since_id大的私信（即比since_id时间晚的私信）
     * @param type $max_id 返回ID小于或等于max_id的私信
     * @param type $count 返回结果的条数数量，最大不超过200。 
     * @param type $page 返回结果的页码
     * @param type $is_encoded 返回结果是否转义
     */
    function getpublicmessage($since_id=0, $max_id=0, $count=20, $page=1,$is_encoded=1){
        $params = array();
		if($since_id)
            $params['since_id'] = $since_id;
        if($max_id)
            $params['max_id'] = $max_id;
        if($count)
            $params['count'] = $count;
        if($page)
            $params['page'] = $page;
        if($is_encoded)
            $params['is_encoded'] = $is_encoded;
		return $this->oauth->get('http://i2.api.weibo.com/2/direct_messages/public/messages.json', $params );
    }
    
    /**
     * 评论微博。
     * 文档：http://wiki.intra.weibo.com/2/comments/biz_create
     * @param type $id 微博ID。
     * @param type $comment 评论内容。必须做URLEncode,信息内容不超过140个汉字。
     * @param type $comment_ori 当回复一条转发微博的评论时，是否评论给原微博。0：不评论给原微博，1：评论给原微博。默认0。
     * @return type
     */
    function biz_create($id , $comment , $comment_ori=0	){
        $params = array();
		$params['comment'] = $comment;
		$this->id_format($id);
		$params['id'] = $id;
		$params['comment_ori'] = $comment_ori;
		return $this->oauth->post( 'https://c.api.weibo.com/2/comments/biz_create.json', $params );
    }
    /**
     * 获取短链的被点击数、被转发数、被评论数等数据的TOP50短链列表。
     * 文档：http://wiki.intra.weibo.com/2/short_url/active_list
     * @return type{
            "uid": "10438",
            "result": [            //被点击数、被转发数、被评论数等数据的TOP50短链列表
                        {
                            "short_url":"h4DwT1",   //短链以 http://t.cn/ 开头
                            "mid": "8343854354345",   //含有此短链的微博mid
                            "clicks": "343",          //被点击数
                            "reposted_count":"234",   //被转发数
                            "commented_count":"3432",  //被评论数
                            "created_time":"2012-04-05 00：00：00"

                        },
                        ....
                    ]
                }
     */
    function active_list(){
		return $this->oauth->get( 'https://c.api.weibo.com/2/short_url/active_list.json' );
    } 
    /**
     * 发送一条私信。
     * 文档：http://wiki.intra.weibo.com/2/direct_messages/send
     * @param type $text 要发送的消息内容。需要做URLEncode，文本大小必须小于300个汉字。
     * @param type $uid 私信接收方的用户ID。
     * @param type $screen_name 私信接收方的微博昵称。在用户ID与微博昵称容易混淆的时候，使用该参数。
     * @return type
     */
    function send_message($text, $uid, $screen_name){
        $params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
        $params['text'] = $text;
        if($screen_name)
            $params['screen_name'] = $screen_name;
        
		return $this->oauth->post('https://c.api.weibo.com/2/direct_messages/send.json', $params );
    }
    /**
     * 获取其他用户的全量粉丝ID列表（不受5000条限制）
     * 文档：http://wiki.intra.weibo.com/2/friendships/followers/other_all/ids
     * @param type $uid 指定其他用户ID，获取其粉丝，若不指定，则获取当前用户粉丝。
     * @param type $gender 指定接收用户的性别范围，m：男、f：女。
     * @param type $province 指定接收用户的省份范围，省份ID。
     * @param type $city 指定接收用户的城市范围，城市ID。
     * @param type $age 指定接收用户的年龄范围，取值内容为：6-12、13-18、19-22、23-25、26-29、30-39、40-59、60-80、其他。
     * @param type $type 指定粉丝的认证类型。0：所有用户；1：普通用户；2：黄V；4：蓝v（包括政府，企业，媒体，网站，校园，应用，机构/团体，公益组织）；8：达人；支持任意数字叠加值，如，6：黄V+蓝V，默认为所有用户。
     * @param type $flag 指定粉丝的属性条件。0：所有粉丝；1：活跃粉丝；2：互动粉丝；支持任意数字叠加值，如，3：活跃粉丝+互动粉丝，默认为0。
     * @param type $bucket 筛选粉丝的个数范围，默认为1000，最大不超过10000。
     * @param type $max_time 返回结果的时间戳游标，若指定此参数，则返回关注时间小于或等于max_time的粉丝，默认从当前时间开始算。返回结果中会得到next_cursor字段，表示下一页的max_time。next_cursor为0表示已经到记录末尾。
     * @param type $cursor_uid 排重ID，传上次获取时最后一个用户ID。
     * @return type {
                        "ids": [
                            2903271035,
                            2813252435,
                            ...
                        ],
                        "next_cursor": 1234567,       // 下次max_time值
                        "previous_cursor": 12345678,  // 上次max_time值
                        "total_number": 14947058
                    }
     */
    function getAllFollowers($uid=0, $gender = '', $province=0 , $city=0, $age='', $type=0, $flag=0, $bucket=1000, $max_time=0, $cursor_uid=0){
        $params = array();
		$this->id_format($uid);
		$params['uid'] = $uid;
        if($gender){
            if($gender == 'm')
                $param['gender'] = 'm';
            if($gender == 'f')
                $param['gender'] = 'f';
        }
        if($province)
            $param['province'] = $province;
        if($city)
            $param['city'] = $city;
        if($age)
            $params['age'] = $age;
        if($type)
            $params['type'] = $type;
        if($flag)
            $params['flag'] = $flag;
        if($bucket)
            $params['bucket'] = $bucket;
        if($max_time)
            $params['max_time'] = $max_time;
        if($cursor_uid)
            $params['cursor_uid'] = $cursor_uid;
        
		return $this->oauth->get('https://c.api.weibo.com/2/friendships/followers/other_all/ids.json', $params );
    }
    
    /**
     * 获取当前用户的全量粉丝ID列表
     * 文档：http://wiki.intra.weibo.com/2/friendships/followers/all/ids
     * @param type $gender 指定接收用户的性别范围，m：男、f：女。
     * @param type $province 指定接收用户的省份范围，省份ID。
     * @param type $city 指定接收用户的城市范围，城市ID。
     * @param type $age 指定接收用户的年龄范围，取值内容为：6-12、13-18、19-22、23-25、26-29、30-39、40-59、60-80、其他。
     * @param type $type 指定粉丝的认证类型。0：所有用户；1：普通用户；2：黄V；4：蓝v（包括政府，企业，媒体，网站，校园，应用，机构/团体，公益组织）；8：达人；支持任意数字叠加值，如，6：黄V+蓝V，默认为所有用户。
     * @param type $flag 指定粉丝的属性条件。0：所有粉丝；1：活跃粉丝；2：互动粉丝；支持任意数字叠加值，如，3：活跃粉丝+互动粉丝，默认为0。
     * @param type $bucket 筛选粉丝的个数范围，默认为1000，最大不超过10000。
     * @param type $max_time 返回结果的时间戳游标，若指定此参数，则返回关注时间小于或等于max_time的粉丝，默认从当前时间开始算。返回结果中会得到next_cursor字段，表示下一页的max_time。next_cursor为0表示已经到记录末尾。
     * @param type $cursor_uid 排重ID，传上次获取时最后一个用户ID。
     * @return type {
     "ids": [
     2903271035,
     2813252435,
     ...
     ],
     "next_cursor": 1234567,       // 下次max_time值
     "previous_cursor": 12345678,  // 上次max_time值
     "total_number": 14947058
     }
     */
    function followers_all_ids($gender='',$province=0,$city=0,$age='',$type=0,$flag=0,$bucket=0,$max_time='',$cursor_uid=0)
    {
    	$params = array();
    	if(in_array($gender, array('m','f'))){
    		$param['gender'] = $gender;
    	}
    	if($province)
    		$param['province'] = $province;
    	if($city)
    		$param['city'] = $city;
    	if($age)
    		$params['age'] = $age;
    	if($type)
    		$params['type'] = $type;
    	if($flag)
    		$params['flag'] = $flag;
    	if($bucket)
    		$params['bucket'] = $bucket;
    	if($max_time)
    		$params['max_time'] = $max_time;
    	if($cursor_uid)
    		$params['cursor_uid'] = $cursor_uid;
    	
    	return $this->oauth->get( 'https://c.api.weibo.com/2/friendships/followers/all/ids.json', $params );
    }
    
    /**
     * 搜索指定几个关键字的用户（只能搜索绑定关键字的用户） 
     * 
     * 对应API：{@link http://wiki.intra.weibo.com/2/search/users/limited}
     * 
     * @return Array
     */
    function search_users_limited($q,$gender='',$isv=0,$ip='',$sbirth=0,$ebirth=0,$sort=0,$page=0,$count=0,$sid='')
    {
    	$params = array();
    	$param['q'] = urlencode($q);
    	if(in_array($gender, array('m','f'))){
    		$param['gender'] = $gender;
    	}
    	if($isv)
    		$param['isv'] = $isv;
    	if($ip)
    		$param['ip'] = $ip;
    	if($sbirth)
    		$params['sbirth'] = $sbirth;
    	if($ebirth)
    		$params['ebirth'] = $ebirth;
    	if($sort)
    		$params['sort'] = $sort;
    	if($page)
    		$params['page'] = $page;
    	if($count)
    		$params['count'] = $count;
    	if($sid)
    		$params['sid'] = $sid;
    	
    	return $this->oauth->get( 'https://c.api.weibo.com/2/search/users/limited.json', $params );
    }
}
