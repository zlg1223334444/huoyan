<?php

include 'client.php';
//
//$user = new client\WeiboUser();
//$res1 = $user->addUser(['aa'=>'bbb']);
//$res2 = $user->updateUser(['cc'=>'dd']);

//$user = new client\User();
//$res = $user->testRedis();

//$user = new client\User();
//$res = $user->testMysql(110);

$user = new client\WeiboUser();
$res = $user->getAnalysisData(1002468097);

//$user = new client\WeixinUser();
//$res = $user->getAnalysisData('lengtoo');

//
//$search = new client\Search();
//$params = [
//    'weibo_name'=>['val'=>'\美食&|'],
//    'platType'=>['val'=>'weibo','aggs'=>false],
//    'pageInfo'=>['page'=>1,'size'=>10],
//    //'weiq_class' => ['val'=>'网络服务','aggs'=>false],
//    //'weiq_post_price_adver' => ['min'=>10,'max'=>1000,'aggs'=>false],
//    'followers_count' => ['min'=>0,'max'=>10000,'aggs'=>true],
//    'weiq_star' => ['val'=>0,'aggs'=>true],
//];
//$res = $search->search($params);
//$res = $search->searchRecommend(['keyword'=>'美女','platType'=>['val'=>'weibo']]);

var_dump($res);