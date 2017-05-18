<?php

class fetch
{
    //模拟登录
    public function login_post($fetch_url)
    {
        header('Content-type:text/html;charset=utf-8');

        $loginUrl = 'https://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.4.15)&_=1403138799543';

        $cookie_file1 = dirname(__FILE__).'/cookie/cookie1.txt';
        $cookie_file2 = dirname(__FILE__).'/cookie/cookie2.txt';
        $cookie_file3 = dirname(__FILE__).'/cookie/cookie3.txt';

        //登录新浪通行证
        $loginData['entry'] = 'sso';
        $loginData['gateway'] = '1';
        $loginData['from'] = 'null';
        $loginData['savestate'] = '30';
        $loginData['useticket'] = '0';
        $loginData['pagerefer'] = '';
        $loginData['vsnf'] = '1';
        $loginData['su'] = base64_encode('13051390038');//用户名
        $loginData['service'] = 'sso';
        $loginData['sp'] = 'liuxiaomeng,1';//密码
        $loginData['sr'] = '1920*1080';
        $loginData['encoding'] = 'UTF-8';
        $loginData['cdult'] = '3';
        $loginData['domain'] = 'sina.com.cn';
        $loginData['prelt'] = '0';
        $loginData['returntype'] = 'TEXT';

        $login = json_decode($this->loginPost($loginUrl, $loginData), true);

        //获取微博cookie
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $login['crossDomainUrlList'][0]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file2);
        $return = curl_exec($ch);
        curl_close($ch);

        //通过获取的cookie 登录微博， 自动跳转
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fetch_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file2);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file3);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }

    function loginPost($url, $data)
    {
        global $cookie_file1;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file1);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
}

?>