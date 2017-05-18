<?php

namespace rpc\server;

class RpcServer {

    private $host = "0.0.0.0";
    private $port = 9501;
    private $model = SWOOLE_PROCESS;
    private $sockType = SWOOLE_SOCK_TCP;

    //MasterPid命令时格式化输出
    protected $_maxMasterPidLength = 12;
    protected $_maxManagerPidLength = 12;
    protected $_maxWorkerIdLength = 12;
    protected $_maxWorkerPidLength = 12;

    //服务类
    protected $serverices = [];

    public function __construct($serverConnect = []){
        if(is_array($serverConnect) && !empty($serverConnect)){
            foreach($serverConnect as $key=>$val){
                if(!empty($val)){
                    $this->$key = $serverConnect[$key];
                }
            }
        }
    }

    public function run($serverConfig){

        $server = new \swoole_server($this->host,$this->port,$this->model,$this->sockType);

        //设置server配置
        $server->set($serverConfig);

        //回调事件
        $server->on('Start', [$this, 'onStart']);
        $server->on('Connect', [$this, 'onConnect']);
        $server->on('Receive', [$this, 'onReceive']);
        $server->on('Close', [$this, 'onClose']);
        $server->on('Shutdown', [$this, 'onShutdown']);
        $server->on('WorkerStart', [$this, 'onWorkerStart']);
        $server->on('WorkerStop', [$this, 'onWorkerStop']);
        $server->on('Task', [$this, 'onTask']);
        $server->on('Finish', [$this, 'onFinish']);
        $server->on('WorkerError', [$this, 'onWorkerError']);
        $server->on('ManagerStart', [$this, 'onManagerStart']);
        $server->on('ManagerStop', [$this, 'onManagerStop']);
        $server->on('PipeMessage', [$this, 'onPipeMessage']);

        $server->start();
    }

    /*
     * @desc Server启动在主进程的主线程回调此函数
     */
    public function onStart(\swoole_server $server){
        global $argv;
        swoole_set_process_name("php {$argv[0]}: master");      //修改进程名

        echo "\033[1A\n\033[K-----------------------\033[47;30m INFO \033[0m-----------------------------\n\033[0m";
        echo 'swoole version:' . swoole_version() . "          PHP version:".PHP_VERSION."\n";
        echo "------------------------\033[47;30m WORKERS \033[0m---------------------------\n";
        echo "\033[47;30mMasterPid\033[0m", str_pad('', $this->_maxMasterPidLength + 2 - strlen('MasterPid')), "\033[47;30mManagerPid\033[0m", str_pad('', $this->_maxManagerPidLength + 2 - strlen('ManagerPid')), "\033[47;30mWorkerId\033[0m", str_pad('', $this->_maxWorkerIdLength + 2 - strlen('WorkerId')),  "\033[47;30mWorkerPid\033[0m\n";
    }

    /*
     * @desc 有新的连接进入时，在worker进程中回调
     */
    public function onConnect(\swoole_server $server, $fd, $from_id){
        echo "Worker#{$server->worker_pid} Client[$fd@$from_id]: Connect.\n";
    }

    /*
     * @desc 接收到数据时回调此函数，发生在worker进程中
     */
    public function onReceive(\swoole_server $server, $fd, $from_id, $data){

        //使用json协议 TODO 修改动态配置协议
        $protocol = new \rpc\server\protocols\Json();
        $data = $protocol->decode($data);

        //判断数据是否正确
        if(empty($data['class']) || empty($data['method']) || !isset($data['paramArray'])) {
            // 发送数据给客户端，请求包错误
            return $server->send($fd,$protocol->encode(['code'=>1001, 'msg'=>'empty in rpc data', 'data'=>'']));
        }

        // 获得要调用的类、方法、及参数
        $class = $data['class'];
        $method = $data['method'];
        $paramArray = $data['paramArray'];

        // 判断类对应文件是否载入
        if (!isset($this->services[$class]) || empty($this->services[$class])) {
            $class = '\rpc\server\services\\'.$class; // 加载 server端 文件 todo 加载tp本身的services层文件？
            if(!class_exists($class)){
                return $server->send($fd,$protocol->encode(['code' => 1002, 'msg' => "class $class not found", 'data' => '']));
            }
            $this->services[$class] = new $class();
        }

        // 调用类的方法
        if (method_exists($this->services[$class], $method)) {
            $ret = call_user_func_array([$this->services[$class], $method], $paramArray);
            //发送数据给客户端，调用成功，data下标对应的元素即为调用结果
            return $server->send($fd,$protocol->encode(['code' => 0, 'msg' => 'success', 'data' => $ret]));
        } else {
            return $server->send($fd,$protocol->encode(['code' => 1003, 'msg' => "method $method not found", 'data' => '']));
        }

    }

    /*
     * @desc TCP客户端连接关闭后，在worker进程中回调此函数
     */
    public function onClose($server, $fd, $from_id){
        $this->log("Worker#{$server->worker_pid} Client[$fd@$from_id]: fd=$fd is closed");
    }

    /*
     * @desc 在Server结束时发生
     */
    public function onShutdown($server){
        echo "Server: onShutdown\n";
    }

    /*
     * @desc 在worker进程/task进程启动时发生
     */
    public function onWorkerStart($server, $worker_id){

        //格式化输出
        global $argv;
        $worker_num = isset($server->setting['worker_num']) ? $server->setting['worker_num'] : 1;

        if ($worker_id >= $worker_num) {
            swoole_set_process_name("php {$argv[0]}: task");
        } else {
            swoole_set_process_name("php {$argv[0]}: worker");
        }
        echo str_pad($server->master_pid, $this->_maxMasterPidLength+2),
        str_pad($server->manager_pid, $this->_maxManagerPidLength+2),
        str_pad($server->worker_id, $this->_maxWorkerIdLength+2),
        str_pad($server->worker_pid, $this->_maxWorkerIdLength), "\n";
    }

    /*
     * @desc 在worker进程终止时发生
     */
    public function onWorkerStop($server, $worker_id){
        echo "WorkerStop[$worker_id]|pid=" . $server->worker_pid . ".\n";
    }

    /*
     * @desc 在task_worker进程内被调用
     */
    public function onTask(\swoole_server $server, $task_id, $from_id, $data){
        //这里是task任务的回调函数
        //一些处理时间比较长的流程可以放在这里执行
        echo "this is onTask\n";
        var_dump($data);
    }

    /*
     * @desc 当worker进程投递的任务在task_worker中完成时
     */
    public function onFinish(\swoole_server $server, $task_id, $data){
        //ontask执行完毕自动调用onFinish
        echo "taskid={$task_id} is over\n";
    }

    /*
     * @desc 当worker/task_worker进程发生异常后会在Manager进程内回调此函数
     */
    public function onWorkerError(\swoole_server $server, $worker_id, $worker_pid, $exit_code){
        echo "worker abnormal exit. WorkerId=$worker_id|Pid=$worker_pid|ExitCode=$exit_code\n";
    }

    /*
     * @desc 当管理进程启动时调用它
     */
    public function onManagerStart(\swoole_server $server){
        global $argv;
        swoole_set_process_name("php {$argv[0]}: manager");
    }

    /*
     * @desc 当管理进程启动时调用它
     */
    public function onManagerStop(\swoole_server $server){
        global $argv;
        swoole_set_process_name("php {$argv[0]}: manager");
    }

    /*
     * @desc 当工作进程收到由sendMessage发送的管道消息时
     */
    public function onPipeMessage(){

    }





    public function setTimerInWorker(\swoole_server $server, $worker_id)
    {
        if ($worker_id == 0) {
            echo "Start: " . microtime(true) . "\n";
            $server->addtimer(3000);
        }
    }


    public function log($msg)
    {
        echo "#" . $msg . PHP_EOL;
    }

}