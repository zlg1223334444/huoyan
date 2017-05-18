<?php
/**
 * desc : 图片上传接口
 * @author : mahongru
 * @date 2016/09/08
 */
namespace Weiq;
class Upyunpic
{
    private $upyun;
    private $default_file_config;
    /**
     * 默认上传配置
     * @var array
     */
    private $upyunconfig = array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
        'exts' => '', //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'Thumbnailwidth' => '0', // 宽和高都是0时不缩放，宽！=0 是限定宽度高度自适应；高！=0是限定高度宽自适应
        'Thumbnailheight' => '0', //
    );

    // 错误信息
    private $error = '';
    // 上传成功的文件信息
    private $uploadFileInfo;

    function __construct()
    {
        vendor("Upyun.upyunconfig");
        vendor("Upyun.upyun#class");
        $bucketname = UPYUN_BUCKET;
        $operator_name = UPYUN_USERNAME;
        $operator_pwd = UPYUN_PWD;
        $this->upyun = new \UpYun("$bucketname", "$operator_name", "$operator_pwd", \UpYun::ED_AUTO, 600);
        $default_config_class = new UploadConfig();
        $this->default_file_config = C('IMG_DEAL_CONFIG') ? array_merge($default_config_class->get_default_file_config()['IMG_DEAL_CONFIG'], C('IMG_DEAL_CONFIG')) : $default_config_class->get_default_file_config()['IMG_DEAL_CONFIG'];
    }

    function initparam($config)
    {
        /* 获取配置 */
        $this->upyunconfig = array_merge($this->upyunconfig, $config);
    }

    function _empty()
    {
    }

    /**
     * 转换上传文件数组变量为正确的方式
     * @access private
     * @param array $files 上传的文件变量
     * @return array
     */
    private function dealFiles($files)
    {
        $fileArray = array();
        $n = 0;
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                $keys = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    $fileArray[$n]['key'] = $key;
                    foreach ($keys as $_key) {
                        $fileArray[$n][$_key] = $file[$_key][$i];
                    }
                    $n++;
                }
            } else {
                $fileArray = $files;
                break;
            }
        }
        return $fileArray;
    }

    /**
     * 检查上传的文件
     * @param array $file 文件信息
     * @param int $is_crop_upload 标识是否是处理完图片后自动再次调用上传方法上传到新浪云时检查文件是否为post方式提交，0为post，1为非post
     */
    private function check($file, $is_crop_upload = 0)
    {
        /* 文件上传失败，捕获错误代码 */
        if ($file['error']) {
            $this->errormsg($file['error']);
            return false;
        }

        /* 无效上传 */
        if (empty($file['name'])) {
            $this->error = '未知上传错误！';
        }

        /* 检查是否合法上传 */
        if ($is_crop_upload == 0) {
            if (!is_uploaded_file($file['tmp_name'])) {
                $this->error = '非法上传文件！';
                return false;
            }
        }

        /* 检查文件大小 */
        if (!$this->checkSize($file['size'])) {
            $this->error = '上传文件大小不符！';
            return false;
        }

        /* 检查文件Mime类型 */
        //TODO:FLASH上传的文件获取到的mime类型都为application/octet-stream
        if (!$this->checkMime($file['type'])) {
            $this->error = '上传文件MIME类型不允许！';
            return false;
        }

        /* 检查文件后缀 */
        if (!$this->checkExt($file['ext'])) {
            $this->error = '上传文件后缀不允许';
            return false;
        }

        /* 通过检测 */
        return true;
    }

    /**
     * 获取错误代码信息
     * @param string $errorNo 错误号
     */
    private function errormsg($errorNo)
    {
        switch ($errorNo) {
            case 1:
                $this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值！';
                break;
            case 2:
                $this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值！';
                break;
            case 3:
                $this->error = '文件只有部分被上传！';
                break;
            case 4:
                $this->error = '没有文件被上传！';
                break;
            case 6:
                $this->error = '找不到临时文件夹！';
                break;
            case 7:
                $this->error = '文件写入失败！';
                break;
            default:
                $this->error = '未知上传错误！';
        }
    }

    /**
     * 检查文件大小是否合法
     * @param integer $size 数据
     */
    private function checkSize($size)
    {
        return !($size > $this->upyunconfig['maxSize']) || (0 == $this->upyunconfig['maxSize']);
    }

    /**
     * 检查上传的文件MIME类型是否合法
     * @param string $mime 数据
     */
    private function checkMime($mime)
    {
        return empty($this->upyunconfig['mimes']) ? true : in_array(strtolower($mime), $this->upyunconfig['mimes']);
    }

    /**
     * 检查上传的文件后缀是否合法
     * @param string $ext 后缀
     */
    private function checkExt($ext)
    {
        return empty($this->upyunconfig['exts']) ? true : in_array(strtolower($ext), $this->upyunconfig['exts']);
    }

    /**
     * 根据上传文件命名规则取得保存文件名
     * @param string $file 文件信息
     */
    private function getSaveName($file)
    {
        $rule = $this->upyunconfig['saveName'];
        if (empty($rule)) { //保持文件名不变
            /* 解决pathinfo中文文件名BUG */
            $filename = substr(pathinfo("_{$file['name']}", PATHINFO_FILENAME), 1);
            $savename = $filename;
        } else {
            $savename = $this->getName($rule, $file['name']);
            if (empty($savename)) {
                $this->error = '文件命名规则错误！';
                return false;
            }
        }

        /* 文件保存后缀，支持强制更改文件后缀 */
        $ext = empty($this->upyunconfig['saveExt']) ? $file['ext'] : $this->upyunconfig['saveExt'];

        return $savename . '.' . $ext;
    }

    /**
     * 根据指定的规则获取文件或目录名称
     * @param  array $rule 规则
     * @param  string $filename 原文件名
     * @return string           文件或目录名称
     */
    private function getName($rule, $filename)
    {
        $name = '';
        if (is_array($rule)) { //数组规则
            $func = $rule[0];
            $param = (array)$rule[1];
            foreach ($param as &$value) {
                $value = str_replace('__FILE__', $filename, $value);
            }
            $name = call_user_func_array($func, $param);
        } elseif (is_string($rule)) { //字符串规则
            if (function_exists($rule)) {
                $name = call_user_func($rule) . rand();
            } else {
                $name = $rule;
            }
        }
        return $name;
    }

    /*
    *计算缩略尺寸 
    */
    private function createThumbnail($filename)
    {
        $opts = array(//\UpYun::CONTENT_SECRET => 'th'
        );
        if ($this->upyunconfig['Thumbnailwidth'] > 0 && $this->upyunconfig['Thumbnailheight'] > 0) {
            $data = getimagesize($picfile);
            //计算缩略图的宽高
            $srcW = $data[0];
            $srcH = $data[1];
            $toWH = $this->upyunconfig['Thumbnailwidth'] / $this->upyunconfig['Thumbnailheight'];
            $srcWH = $srcW / $srcH;
            if ($toWH <= $srcWH) {
                $ftoW = $this->upyunconfig['Thumbnailwidth'];
                $ftoH = 0;
            } else {
                $ftoH = $this->upyunconfig['Thumbnailheight'];
                $ftoW = 0;
            }
            if ($ftoW) {
                $opts[\UpYun::X_GMKERL_TYPE] = 'fix_width';
                $opts[\UpYun::X_GMKERL_VALUE] = $ftoW;
            } else {
                $opts[\UpYun::X_GMKERL_TYPE] = 'fix_height';
                $opts[\UpYun::X_GMKERL_VALUE] = $ftoH;
            }
        } else if ($this->upyunconfig['Thumbnailwidth'] > 0 && $this->upyunconfig['Thumbnailheight'] == 0) {
            //计算缩略图的宽高
            $opts[\UpYun::X_GMKERL_TYPE] = 'fix_width';
            $opts[\UpYun::X_GMKERL_VALUE] = $this->upyunconfig['Thumbnailwidth'];
        } else if ($this->upyunconfig['Thumbnailwidth'] == 0 && $this->upyunconfig['Thumbnailheight'] > 0) {
            $opts[\UpYun::X_GMKERL_TYPE] = 'fix_height';
            $opts[\UpYun::X_GMKERL_VALUE] = $this->upyunconfig['Thumbnailheight'];
        }
        return $opts;
    }

    /*
     * 上传图片
     * @param int $is_crop_upload 标识是否是处理完图片后自动再次调用上传方法上传到新浪云时检查文件是否为post方式提交，0为post，1为非post
     */
    function uploadPic($picfile, $is_crop_upload = 0)
    {
        /* 逐个检测并上传文件 */
        $info = array();
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
        }
        // 对上传文件数组信息处理
        $file = $this->dealFiles($picfile);

        $file['name'] = strip_tags($file['name']);

        if (!empty($file['name'])) {
            //if(!isset($file['key']))   $file['key']    =   $key;
            /* 通过扩展获取文件类型，可解决FLASH上传$FILES数组返回文件类型错误的问题 */
            if (isset($finfo)) {
                $file['type'] = finfo_file($finfo, $file['tmp_name']);
            }
            /* 获取上传文件后缀，允许上传无后缀文件 */
            $file['ext'] = pathinfo($file['name'], PATHINFO_EXTENSION);

            /* 文件上传检测 */
            if (!$this->check($file, $is_crop_upload)) {
                return false;
                exit;
            }

            /* 生成保存文件名 */
//            $savename = $this->getSaveName($file);
            /*修改代码*/
            $savename = $file['name'];
            /*修改代码end*/
            if (false == $savename) {
                return false;
                exit;
            }
            try {
                $picfile = $file['tmp_name'];

                $picUpfile = $this->upyunconfig['savePath'] . $savename;

                $opts = $this->createThumbnail($picfile);
                $fh = fopen("$picfile", 'r');

                if (empty($opts)) {
                    $this->upyun->writeFile("$picUpfile", $fh, true);
                } else {
                    $this->upyun->writeFile("$picUpfile", $fh, true);
                    $picUpfileone = $this->upyunconfig['savePath'] . 'thumbnail/' . $savename;
                    $this->upyun->writeFile("$picUpfileone", $fh, true, $opts);
                }
                fclose($fh);
                $this->uploadFileInfo = $picUpfile;
                return true;
            } catch (Exception $e) {
                //echo $e->getCode();     // 错误代码
                //echo $e->getMessage();  // 具体错误信息
                $this->error = $e->getCode() . "--" . $e->getMessage();
                return false;
            }
        }
    }

    /**
     * 上传所有文件
     * @access public
     * @param string $savePath 上传文件保存路径
     * @return string
     */
    public function upload()
    {
        $fileInfo = array();
        $isUpload = false;
        // 获取上传的文件信息
        // 对$_FILES数组信息处理
        $files = $this->dealFiles($_FILES);
        //write_log(json_encode($files),'pic');
        //exit;
        foreach ($files as $key => $file) {
            $file['name'] = strip_tags($file['name']);
            //过滤无效的上传
            if (!empty($file['name'])) {
                if (!isset($file['key'])) $file['key'] = $key;
                /* 通过扩展获取文件类型，可解决FLASH上传$FILES数组返回文件类型错误的问题 */
                if (isset($finfo)) {
                    $file['type'] = finfo_file($finfo, $file['tmp_name']);
                }
                /* 获取上传文件后缀，允许上传无后缀文件 */
                $file['ext'] = pathinfo($file['name'], PATHINFO_EXTENSION);
                /* 文件上传检测 */
                if (!$this->check($file)) {
                    return false;
                    exit;
                }
                /* 生成保存文件名 */
//                $savename = $this->getSaveName($file);
                /* 修改代码begin*/
                $savename = md5($file['name']) . time() . '.' . $file['ext'];
                $this->upyunconfig['savePath'] = $this->upyunconfig['savePath'] . substr($savename, 0, C('IMG_DEAL_CONFIG')['IMG_DIR_LENGTH'] ? C('IMG_DEAL_CONFIG')['IMG_DIR_LENGTH'] : $this->default_file_config['IMG_DIR_LENGTH']) . '/';
                /* 修改代码end*/
                if (false == $savename) {
                    return false;
                    exit;
                }
                try {
                    $picfile = $file['tmp_name'];
                    $picUpfile = $this->upyunconfig['savePath'] . $savename;
                    $opts = $this->createThumbnail($picfile);
                    $fh = fopen("$picfile", 'r');
                    if (empty($opts)) {
                        $this->upyun->writeFile("$picUpfile", $fh, true);
                    } else {
                        $this->upyun->writeFile("$picUpfile", $fh, true);
                        $picUpfileone = $this->upyunconfig['savePath'] . 'thumbnail/' . $savename;
                        $this->upyun->writeFile("$picUpfileone", $fh, true, $opts);
                    }
                    fclose($fh);
                    //上传成功后保存文件信息，供其他地方调用
                    unset($file['tmp_name'], $file['error']);
                    $fileInfo[] = $picUpfile;
                    $isUpload = true;
                } catch (Exception $e) {
                    //echo $e->getCode();     // 错误代码
                    //echo $e->getMessage();  // 具体错误信息
                    $this->error = $e->getCode() . "--" . $e->getMessage();
                    return false;
                }
            }
        }
        if ($isUpload) {
            $this->uploadFileInfo = $fileInfo;
            //write_log(json_encode($fileInfo),'pic');
            //write_log($fileInfo,'pic');
            return $fileInfo;
            //return true;
        } else {
            $this->error = '没有选择上传文件';
            return false;
        }
    }

    /*
    * 上传图片：根据图片地址上传图片
    */
    public function uploadPicurl($picurl = '')
    {
        $photoimg = '';
        if ($picurl) {
            try {
                $temp_photo = time() . rand();
                $info = array();
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                }
                if (isset($finfo)) {
                    $mime = finfo_file($finfo, $picurl);
                    //检查文件Mime类型 
                    if (!$this->checkMime($mime)) {
                        $this->error = '上传文件MIME类型不允许！';
                        return false;
                    }
                }
                //获取上传文件后缀，允许上传无后缀文件 
                $ext = strrchr($picurl, '=');
                $ext = str_replace('=', '', $ext);
                //检查文件后缀 
                if (!$this->checkExt($ext)) {
                    $this->error = '上传文件后缀不允许';
                    return false;
                }
                $filesize = filesize($picurl);
                //检查文件大小 
                if (!$this->checkSize($filesize)) {
                    $this->error = '上传文件大小不符！';
                    return false;
                }
                $ext = '.' . $ext;
                $picUpfile = $this->upyunconfig['savePath'] . $temp_photo . $ext;
                $opts = $this->createThumbnail($picurl);
                //$fh = fopen($picurl,'r');
                $fh = file_get_contents($picurl);
                if (empty($opts)) {
                    $this->upyun->writeFile("$picUpfile", $fh, true);
                } else {
                    $this->upyun->writeFile("$picUpfile", $fh, true);
                    $picUpfileone = $this->upyunconfig['savePath'] . 'thumbnail/' . $temp_photo . $ext;
                    $this->upyun->writeFile("$picUpfileone", $fh, true, $opts);
                }
                return $picUpfile;
            } catch (Exception $e) {
                $this->error = $e->getCode() . "--" . $e->getMessage();
                return $photoimg;
            }
        }
        return $photoimg;
    }

    /*
    * 上传图片：根据图片地址上传图片
    */
    public function uploadwechatPicurl($picurl = '')
    {
        $photoimg = '';
        if ($picurl) {
            try {
                $temp_photo = time() . rand();
                $info = array();
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                }
                if (isset($finfo)) {
                    $mime = finfo_file($finfo, $picurl);
                    //检查文件Mime类型 
                    if (!$this->checkMime($mime)) {
                        $this->error = '上传文件MIME类型不允许！';
                        return false;
                    }
                }
                //获取上传文件后缀，允许上传无后缀文件 
                $ext = strrchr($picurl, '=');
                $ext = str_replace('=', '', $ext);
                //检查文件后缀 
                if (!$this->checkExt($ext)) {
                    $this->error = '上传文件后缀不允许';
                    throw new \Think\Exception('上传文件后缀不允许');
                    return false;
                }
                $filesize = filesize($picurl);
                //检查文件大小 
                if (!$this->checkSize($filesize)) {
                    $this->error = '上传文件大小不符！';
                    return false;
                }
                if (empty($ext)) {
                    $ext = 'jpg';
                }
                $ext = '.' . $ext;
                $picUpfile = $this->upyunconfig['savePath'] . $temp_photo . $ext;
                $opts = $this->createThumbnail($picurl);
                //$fh = fopen($picurl,'r');
                $fh = file_get_contents($picurl);
                if (empty($opts)) {
                    $this->upyun->writeFile("$picUpfile", $fh, true);
                } else {
                    $this->upyun->writeFile("$picUpfile", $fh, true);
                    $picUpfileone = $this->upyunconfig['savePath'] . 'thumbnail/' . $temp_photo . $ext;
                    $this->upyun->writeFile("$picUpfileone", $fh, true, $opts);
                }
                return $picUpfile;
            } catch (\Exception $e) {
                //这里的\Exception不加斜杠的话回使用think的Exception类    
                write_log($e->getCode(), "abnormal_error");
                write_log($e->getMessage(), "abnormal_error");
                write_log($e->getTrace(), "abnormal_error");

                $this->error = $e->getCode() . "--" . $e->getMessage();
                return $photoimg;
            }
        }
        return $photoimg;
    }

    /*
    * 上传图片：根据图片地址上传图片(本地图片)
    */
    public function uploadlocalPicurl($picurl = '')
    {
        $photoimg = '';
        if ($picurl) {
            if (file_exists($picurl)) {
                try {
                    $temp_photo = time() . rand();
                    $info = array();
                    if (function_exists('finfo_open')) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    }
                    if (isset($finfo)) {
                        $mime = finfo_file($finfo, $picurl);
                        //检查文件Mime类型 
                        if (!$this->checkMime($mime)) {
                            $this->error = '上传文件MIME类型不允许！';
                            return false;
                        }
                    }
                    //获取上传文件后缀，允许上传无后缀文件 
                    $ext = pathinfo($picurl, PATHINFO_EXTENSION);
                    $ext = strrchr($picurl, '=');
                    //检查文件后缀 
                    if (!$this->checkExt($ext)) {
                        $this->error = '上传文件后缀不允许';
                        return false;
                    }
                    $filesize = filesize($picurl);
                    //检查文件大小 
                    if (!$this->checkSize($filesize)) {
                        $this->error = '上传文件大小不符！';
                        return false;
                    }
                    $picUpfile = $this->upyunconfig['savePath'] . $temp_photo . '.' . $ext;
                    $opts = $this->createThumbnail($picurl);
                    $fh = fopen($picurl, 'r');
                    if (empty($opts)) {
                        $this->upyun->writeFile("$picUpfile", $fh, true);
                    } else {
                        $this->upyun->writeFile("$picUpfile", $fh, true);
                        $picUpfileone = $this->upyunconfig['savePath'] . 'thumbnail/' . $temp_photo . '.' . $ext;
                        $this->upyun->writeFile("$picUpfileone", $fh, true, $opts);
                    }
                    return $picUpfile;
                } catch (Exception $e) {
                    $this->error = $e->getCode() . "--" . $e->getMessage();
                    return $photoimg;
                }
            }
        }
        return $photoimg;
    }

    /**
     * 取得上传文件的信息
     * @access public
     * @return array
     */
    public function getUploadFileInfo()
    {
        return $this->uploadFileInfo;
    }

    /**
     * 取得最后一次错误信息
     * @access public
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->error;
    }

    //读取文件
    function readPic($picpath)
    {
        try {
            $fh = fopen('/tmp/demo.png', 'w');
            $this->upyun->readFile('/temp/upload_demo.png', $fh);
            fclose($fh);
        } catch (Exception $e) {
            echo $e->getCode();     // 错误代码
            echo $e->getMessage();  // 具体错误信息
        }
        /*
        $data = $upyun->readFile('/temp/upload_demo.png');
        2.使用文件流模式下载:

        $fh = fopen('/tmp/demo.png', 'w');
        $upyun->readFile('/temp/upload_demo.png', $fh);
        fclose($fh);
        直接获取文件时，返回文件内容，使用数据流形式获取时，成功返回true。 如果获取文件失败，则抛出异常。
        */
    }

    //创建目录
    function makeDir($param = '')
    {
        try {
            $res = $this->upyun->makeDir($param);
            /*
            $upyun->makeDir('/demo/');
            */
            print_r($res);
        } catch (Exception $e) {
            echo $e->getCode();     // 错误代码
            echo $e->getMessage();  // 具体错误信息
        }
    }

    //获取目录使用情况
    function dirlist($param = '/')
    {
        try {
            echo $this->operator_pwd;
            exit;
            $res = $this->upyun->getList("/wechatphoto/");
            /*
            $file = $res[0];
            echo $file['name']; // 文件名
            echo $file['type']; // 类型（目录: folder; 文件: file）
            echo $file['size']; // 尺寸
            echo $file['time']; // 创建时间
           */
            print_r($res);
        } catch (Exception $e) {
            echo $e->getCode();     // 错误代码
            echo $e->getMessage();  // 具体错误信息
        }
    }

    //获取文件详情
    function fileinfo($param = '')
    {
        try {
            $res = $this->upyun->getFileInfo($param);
            /*
            $result = $upyun->getFileInfo('/demo/demo.png');
            echo $result['x-upyun-file-type']; // 文件类型
            echo $result['x-upyun-file-size']; // 文件大小
            echo $result['x-upyun-file-date']; // 创建日期
            */
            print_r($res);
        } catch (Exception $e) {
            echo $e->getCode();     // 错误代码
            echo $e->getMessage();  // 具体错误信息
        }
    }

    //获取空间使用情况
    function usage()
    {
        try {
            $res = $this->upyun->getBucketUsage();
            print_r($res);
        } catch (Exception $e) {
            echo $e->getCode();     // 错误代码
            echo $e->getMessage();  // 具体错误信息
        }
    }
}

?>