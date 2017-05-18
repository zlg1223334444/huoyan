<?php

namespace services;

class Search{

    public function __construct(){
        include BASEDIR.'/vendor/autoload.php';
    }

    public function search($id){

        $hosts = ['122.112.13.200:9230'];

        $client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();
        $params = [
            'index' => 'ims_base',
            'type' => 'wemedia_weibo_user',
            'id' => $id,
        ];

        $res = $client->get($params);
        return $res;

    }
}