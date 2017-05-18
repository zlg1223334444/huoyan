<?php

namespace client;

class Search{

    protected static $searchClient;

    public function __construct(){
        self::$searchClient= \RpcClient::instance('Search');
    }

    /**
     * @desc 搜索
     * @param array $info
     *              platType                array     weibo/weixin
     *              weibo_name              array     搜索词
     *              pageInfo                array      分页信息
     *                      page                int         当前页码
     *                      size                int         每页条数（默认10）
     *              weiq_class               array     分类
     *              weiq_post_price_adver    array      价格区间［min,max］
     *              followers_count          array      粉丝数区间［min,max］
     *              star                     array        星级
     *              ability_tag              array     标签
     * @return mixed
     */
    public static function search($info){

        return self::$searchClient->search($info);
    }

    /**
     * @desc 搜索提示
     * @param array $info
     *              keyword      string  搜索词
     *              platType     array
     *                  val         string  weibo/weixin
     * @return mixed
     */
    public static function searchRecommend($info){

        return self::$searchClient->searchRecommend($info);
    }
}


