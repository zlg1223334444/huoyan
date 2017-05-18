<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
return array(
    "TIME_OUT"=>80,
   
    "URL_ADDTASK"=>"http://owner.cikevideo.com/adCollection/adInfoRecord",
    //"http://101.200.232.154:188/adCollection/adInfoRecord",
    
    "PARAM_ADDTASK" => array(
        "ad_name" => array("string",0),
        "ad_describe" => array("string",0),
        "show_type" => array("int",0),
        "money_limit" => array("int",0),
        "viewer_count_limit" => array("int",0),
        "duration_limit" => array("int",0),
        "preview_pic" => array("string",0),
        "user_id" => array("int",0),
        "task_id" => array("int",0),
    ),
    
    "PARAM_ADORDER"=>array(
        "taskid" => array("int",0),
        "id" => array("int",0),
        "nickname" => array("string",0),
        "platform" => array("string",0),
        "roomurl" => array("string",0),
        "thirdorderid" => array("int",0),
    ),
    "PARAM_ENDTASK"=>array(
        "taskid" => array("int",0),
    ),
    
    "STATUS_CONTENT"=>array(
        "101"=>"内部程序错误",
        "102"=>"内部数据错误",
        "103"=>"任务不存在",
        "104"=>"订单已存在",
        "501"=>"参数错误",
        "504"=>"任务重复",
    )
);

//潜艇 2016/9/27 17:43:24
//"exception_error" => 500,//异常错误
//        "params_error" => 501,//参数错误
//        "sign_error" => 502,//签名错误
//        "login_error" => 503,//获取用户广告平台错误
//        "task_id_error" => 504, //task_id 重复