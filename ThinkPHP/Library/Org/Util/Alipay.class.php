<?php
namespace Org\Util;
class Alipay {

	var $gateway = "https://www.alipay.com/cooperate/gateway.do?";         //支付接口
	var $parameter;       //全部需要传递的参数
	var $security_code;   //安全校验码
	var $mysign;          //签名

	//构造支付宝外部服务接口控制
	function __construct($parameter,$security_code,$sign_type = "MD5",$transport= "https") {
		$this->parameter      = $this->para_filter($parameter);
		$this->security_code  = $security_code;
		$this->sign_type      = $sign_type;
		$this->mysign         = '';
		$this->transport      = $transport;
		
		if($this->transport == "https") {
			$this->gateway = "https://www.alipay.com/cooperate/gateway.do?";
		} else $this->gateway = "http://www.alipay.com/cooperate/gateway.do?";
		$sort_array  = array();
		$arg         = "";
		$sort_array  = $this->arg_sort($this->parameter);
		foreach($sort_array as $k => $v){
			$arg .= $k . '=' . $v . '&';
		}

		//echo $arg;exit;
		$prestr = substr($arg,0,-1);  //去掉最后一个&
		$this->mysign = md5($prestr.$this->security_code);
	}

	//若使用GET方式传递，请使用create_url函数获得完整URL链接
	function create_url() {
		$url         = $this->gateway;
		$sort_array  = array();
		$arg         = "";
		$sort_array  = $this->arg_sort($this->parameter);
		foreach($sort_array as $k => $v){
			$arg .= $k . '=' . urlencode($v) . '&';
		}
		$url.= $arg."sign=" .$this->mysign ."&sign_type=".$this->sign_type;
		return $url;
	}
	
	//若使用POST方式传递，请使用Get_Sign函数获得加密结果字符串
	function Get_Sign() {
		return $this->mysign;
	}

	function arg_sort($array) {
		ksort($array);
		reset($array);
		return $array;
	}


	function para_filter($parameter) { //除去数组中的空值和签名模式
	
		foreach($parameter as $k => $v){
			if($key == "sign" || $key == "sign_type" || $val == ""){
				unset($parameter[$key]);
			}
		}
		return $parameter;
	}

}
?>