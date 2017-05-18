<?php
vendor( 'Sina.config' );
vendor( 'Sina.saetv2#ex#class' );

class sina{
	
	private $_login_url;
	private $_sina_akey;
	private $_sina_skey;

	public function __construct() {
		$this->_sina_akey = WB_AKEY;
		$this->_sina_skey = WB_SKEY;
	}
	
	public function getUrl($call_back = null) {
	
		if ( empty($this->_sina_akey) || empty($this->_sina_skey) ) 
			return false;
			
		if (is_null($call_back)) {
			$call_back = WB_CALLBACK;
			
		}

		$o = new \SaeTOAuthV2( $this->_sina_akey , $this->_sina_skey );
		$this->_login_url = $o->getAuthorizeURL( $call_back );
	
		return $this->_login_url;
	}

	public function checkUser(){
		$o = new \SaeTOAuthV2( $this->_sina_akey , $this->_sina_skey );
		$keys = array();
		$keys['code'] = $_REQUEST['code'];
		$keys['redirect_uri'] = WB_CALLBACK;
		$token = $o->getAccessToken( 'code', $keys );
		$_SESSION["sina"]["token"] = $token;
		$_SESSION['open_platform_type'] = 'sina';
	}

	private function doClient(){
		$access_token = $_SESSION['sina']['token']['access_token'];
		return new \SaeTClientV2( $this->_sina_akey , $this->_sina_skey , $access_token );
	}

	//用户资料
	function userInfo(){
		$client = $this->doClient();
		$uid = $client->get_uid();
		$me = $client->show_user_by_id_old($uid['uid']);
        $user['plattype']  = 0;//新浪 0 qq 1
		$user['platid'] = $me['id'];
        $user['name'] = $me['name'];
        $user['nick'] = $me['screen_name'];
        $user['followers_count'] = $me['followers_count'];
        $user['avatar'] = $me['profile_image_url'];
        $user['accesstoken'] = $_SESSION['sina']['token']['access_token'];
        $user['expire'] = strtotime('+'.$_SESSION['sina']['token']['expires_in'].' seconds');
		$user['region'] = $me['location'];
		$user['friend_des'] = $me['description'];
		$user['gender'] = $me['gender'];
		$user['verified']=$me['verified_type'];
		return $user;
	}


}
?>