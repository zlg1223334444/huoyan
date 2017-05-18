<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Icike;
class Cikeservices{
     
    function __construct() {
        $this->config=  include_once 'config.php';
    }
    
    /**
     * 添加任务请求
     * @param array $param  请求内容
     * @return boolean/array    处理失败/返回值
     */
    function task_add($param){
        //var_dump($param);
        $param=$this->param_handle($param,"ADDTASK");
        if(!$param){
            return  false;
        }else{
            $re=  $this->_post($param,  $this->config["URL_ADDTASK"]);
            return json_decode($re,true);
        }
    }
    
    /**
     * 参数处理
     * @param array $param      要处理的参数数组
     * @param string $fname     对应的配置名称
     * @return boolean/array    失败/处理后的参数数组
     */
    public function param_handle($param,$fname){
        $fname=  trim($fname);
        if(empty($param)||empty($fname)){
            return false;
        }
        $str_configname= "PARAM_". strtoupper($fname);
        $config_param=  $this->config[$str_configname];
        $result=array();
        foreach($config_param as $k_param=>$v_param){
            if(isset($param[$k_param])){
                $result[$k_param]=$this->_conversion($param[$k_param],$v_param[0]);
            }else{
                if($v_param[1]==0){
                    return false;
                }
            }
        }
        return $result;
    }
    
  
    /**
     * 返回值处理
     * @param int $status
     * @param array $data
     * @return string   json_encode(array)
     */
    public function return_handle($status,$data=""){
        $re=array();
        if($status==200){
            $re["code"]=0;
            $re["msg"]="录入成功";
            $re["data"]=$data;
        }elseif(array_key_exists($status, $this->config["STATUS_CONTENT"])){
            $re["code"]=1;
            $re["msg"]="录入失败";
            $re["data"]=array(
                "error_code"=>$status,
                "error_msg"=>$this->config["STATUS_CONTENT"][$status]
            );
        }else{
            $re["code"]=1;
            $re["msg"]="录入失败";
            $re["data"]=array(
                "error_code"=>999,
                "error_msg"=>"未知错误"
            );
        }
        return json_encode($re);
    }
    
    /**
     * 参数检测
     * @param string $content   要检测的内容
     * @param string $type      既定的类型     
     * @return string           强制转换类型后的内容
     */
    private function _conversion($content,$type=""){
        if(empty($type)){
            $re=$content;
        }elseif($type=="int"){
            $re=  intval(round($content));
        }elseif($type=="string"){
            $re= (string)$content;
        }else{
            $re=$content;
        }
        return $re;
    }

    /**
     * curl post方式请求
     * @param array $param  发送参数   
     * @param string $url   请求链接
     * @param int $timeout  超时时间
     * @return type
     */
    private function _post($param,$url ,$timeout){
        $timeout =empty($timeout)?$this->config['TIME_OUT']:$timeout;
        $strParam =$this->_param2str($param);

        $ch = curl_init();

        curl_setopt_array($ch, array(CURLOPT_HTTP_VERSION=>CURL_HTTP_VERSION_1_0,
                                    CURLOPT_RETURNTRANSFER=>TRUE,
                                    CURLOPT_HEADER=>FALSE,
                                    CURLOPT_POST=>TRUE,
                                    CURLOPT_POSTFIELDS=>$strParam,
                                    CURLOPT_URL=>$url,
                                    CURLOPT_CONNECTTIMEOUT=>20,
                                    CURLOPT_TIMEOUT=>$timeout,
                                   // CURLOPT_USERPWD=>empty($this->authUser)?'':("[{$this->authUser}:{$this->authPwd}]"),
        ));
        $response = curl_exec($ch);
        $this->HttpStatus = curl_getinfo($ch,CURLINFO_HTTP_CODE); //获取HTTPSTAT码
        curl_close ($ch);
        return $response;
    }
    
    /**
     * 拼接请求参数,供curl使用
     * @param array $param 数组参数
     * @return type         拼接后的请求参数
     */
    private function _param2str($param){
        $arr = array();
        foreach($param as $key=>$val){
            $arr [] = $key."=".$val;
        }
        return implode("&", $arr);
    }
}
