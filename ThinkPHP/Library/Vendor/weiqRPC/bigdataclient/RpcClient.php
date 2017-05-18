<?php

/**
 * RpcClient Rpc客户端
 */
class RpcClient {

    //发送数据和接收数据的超时时间 单位S
    const TIME_OUT = 5;

    //swoole客户端设置
    protected $swooleClientSets = array(
        'open_eof_check' => true,
        'package_eof' => "\r\n"
    );

    //服务端地址
    protected static $addressArray = array();

    //异步调用实例
    protected static $asyncInstances = array();

    //同步调用实例
    protected static $instances = array();

    //到服务端的socket连接
    protected $connection = null;

    //到服务端的swoole client连接
    protected $swooleClient = null;

    //实例的服务名
    protected $serviceName = '';

    //使用swoole方式
    protected static $useSwoole = false;

    /*
     * 设置/获取服务端地址
     * @param array $addressArray
     * @return array
     */
    public static function config($addressArray = array()){
        if (! empty($addressArray)) {
            self::$addressArray = $addressArray;
        }
        return self::$addressArray;
    }

    /*
     * 获取一个实例
     * @param string $serviceName
     * @return instance of RpcClient
     */
    public static function instance($serviceName){
        if (! isset(self::$instances[$serviceName])) {
            self::$instances[$serviceName] = new self($serviceName);
        }
        return self::$instances[$serviceName];
    }

    /*
     * 构造函数
     * @param string $serviceName
     */
    protected function __construct($serviceName){
        $this->serviceName = $serviceName;
    }

    public static function setSwooleClient(){
        self::$useSwoole = true;
    }

    /*
     * 调用
     * @param string $method
     * @param array $arguments
     * @throws Exception
     * @return
     *
     */
    public function __call($method, $arguments){
        // 同步发送接收
        $this->sendData($method, $arguments);
        return $this->recvData();
    }

    /*
     * 发送数据给服务端
     * @param string $method
     * @param array $arguments
     * @return bool
     */
    public function sendData($method, $arguments){
        $bin_data = \protocols\Json::encode(array(
                'class' => $this->serviceName,
                'method' => $method,
                'paramArray' => $arguments,
                'user' => 'weiq',
                'password' => 'ims-weiq'
            ))."\r\n";
        $this->openConnection();
        if (self::$useSwoole) {
            return $this->swooleClient->send($bin_data);
        } else {
            return fwrite($this->connection, $bin_data) == strlen($bin_data);
        }
    }

    /*
     * 从服务端接收数据
     * @throws Exception
     */
    public function recvData(){
        if (self::$useSwoole) {
            $res = $this->swooleClient->recv();
            $this->swooleClient->close();
        } else {
            $res = fgets($this->connection);
            $this->closeConnection();
        }
        if (! $res) {
            throw new Exception("recvData empty");
        }
        return \protocols\Json::decode($res);
    }

    /*
     * 打开到服务端的连接
     * @throws Exception
     * @return void
     */
    protected function openConnection(){
        $address = self::$addressArray[array_rand(self::$addressArray)];
        if (self::$useSwoole) {
            $address = explode(':', $address);
            $this->swooleClient = new swoole_client(SWOOLE_SOCK_TCP);
            $this->swooleClient->set($this->swooleClientSets);
            if (!$this->swooleClient->connect($address[0], $address[1], self::TIME_OUT)) {
                exit("connect failed. Error: {$this->swooleClient->errCode}\n");
            }
        } else {
            $this->connection = stream_socket_client($address, $err_no, $err_msg);
            if (! $this->connection) {
                throw new Exception("can not connect to $address , $err_no:$err_msg");
            }
            stream_set_blocking($this->connection, true);
            stream_set_timeout($this->connection, self::TIME_OUT);
        }
    }

    /*
     * 关闭到服务端的连接
     * @return void
     */
    protected function closeConnection(){
        fclose($this->connection);
        $this->connection = null;
    }
}