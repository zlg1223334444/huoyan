<?php

namespace services;
use lib\MysqlObj;
use lib\IRedis;

/**
 *  测试
 */
class User{

    public function getInfoByUid($uid){
        return array(
            'uid' => $uid,
            'name' => '小萝莉',
            'age' => 18,
            'sex' => '女'
        );
    }

    public function testRedis(){
        $redis = IRedis::instance();
        $res = $redis->get('test');
        return $res;
    }

    public function testMysql($uid){
        $db = MysqlObj::bi_init();
        $sql = "select * from wemedia_weibo_user where id=".$uid;
        return $db->rawQueryOne($sql);
    }
}
