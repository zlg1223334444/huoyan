<?php
/**
 * desc : uplode(上传的相关处理)
 * @author : mahongru
 * @date 2016/09/08
 */
namespace Weiq;
class Upload
{
    private $error_msg;
    private $default_config = array('maxSize' => 3145728, 'exts' => array('jpg', 'gif', 'png', 'jpeg'),);
    private $default_file_config;//默认系统上传参数
    private $default_yun_config;//默认系统新浪云上传参数

    public function __construct($config = array())
    {
        $default_config_class = new UploadConfig();
        $this->default_file_config = C('IMG_DEAL_CONFIG') ? array_merge($default_config_class->get_default_file_config()['IMG_DEAL_CONFIG'], C('IMG_DEAL_CONFIG')) : $default_config_class->get_default_file_config()['IMG_DEAL_CONFIG'];
        $this->default_yun_config = $config ? array_merge($default_config_class->get_default_yun_config()['U_PAI_YUN'], $config) : $default_config_class->get_default_yun_config()['U_PAI_YUN'];
        $this->default_config = array_merge($this->default_config, $config);
    }

    //上传文件
    private function file_upload()
    {
        $upload = new \Think\Upload($this->default_config); // 实例化上传类
        // 上传文件 
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->set_error_msg($upload->getError());
            return false;
        } else {// 上传成功
            return $info;
        }
    }

    //设置错误信息
    private function set_error_msg($msg)
    {
        $this->error_msg = $msg;
    }

    //返回参数设置错误信息
    public function get_error_msg()
    {
        return $this->error_msg;
    }

    /*
     * 处理图片缩略图名称和路径
     * @param string $img_path //数据库中的图片路径
     */
    public function deal_img_url($img_path)
    {
        $file_config = $this->default_file_config;//获取文件配置信息
        $flag = strstr($img_path, $file_config['IMG_URL']);//标识是否是新浪云的上传
        $img_path = $flag ? substr($img_path, $file_config['IMG_URL_LENGTH']) : $img_path;
        $img_name = substr($img_path, $file_config['IMG_NAME_STR_LENGTH']); //源图名称
        $dir_name = substr($img_path, $file_config['DIR_LENGTH'], $file_config['IMG_DIR_LENGTH']); //目录名称
        $file_info = $this->get_file_info($img_name);
        $img_thumb_name = $file_info['img_pre_name'] . '_thumb' . $file_info['file_ext']; //水印图名称
        $img_thumb_little_name = $file_info['img_pre_name'] . '_thumb_little' . $file_info['file_ext']; //非水印小图名称
        $thumb_img_url = $flag ? $file_config['IMG_URL'] . '/' . $file_config['IMG_DIR'] . '/' . $dir_name . '/' . $img_thumb_name : '/' . $file_config['IMG_DIR'] . '/' . $dir_name . '/' . $img_thumb_name;
        $thumb_little_img_url = $flag ? $file_config['IMG_URL'] . '/' . $file_config['IMG_DIR'] . '/' . $dir_name . '/' . $img_thumb_little_name : '/' . $file_config['IMG_DIR'] . '/' . $dir_name . '/' . $img_thumb_little_name;

        $img_url = array();
        $img_url['thumb_img_url'] = $thumb_img_url;
        $img_url['thumb_little_img_url'] = $thumb_little_img_url;
        $img_url['dir_name'] = $dir_name;
        $img_url['img_path'] = $flag ? $file_config['IMG_URL'] . $img_path : '.' . $img_path;
        $img_url['logo'] = $file_config['LOGO'];
        $img_url['logo_dir'] = $file_config['LOGO_DIR'];
        $img_url['save_path'] = '/' . $file_config['IMG_DIR'] . '/' . $dir_name . '/';
        $img_url['img_dir'] = $file_config['IMG_DIR'];
        $img_url['flag'] = $flag;
        $img_url['img_thumb_name'] = $img_thumb_name;
        $img_url['img_thumb_little_name'] = $img_thumb_little_name;
        $img_url['img_thumb_path'] = './' . $file_config['IMG_DIR'] . '/' . $dir_name . '/' . $img_thumb_name;//水印图保存地址
        $img_url['img_thumb_little_path'] = './' . $file_config['IMG_DIR'] . '/' . $dir_name . '/' . $img_thumb_little_name;//非水印小图保存地址

        return $img_url;
    }

    /*
     * 获取文件信息，后缀名和后缀名之前的字符串
     * @param string $img_name //文件全名
     */
    private function get_file_info($img_name)
    {
        $file_arr = array();
        $file_arr['file_ext'] = substr($img_name, strrpos($img_name, '.'));//文件后缀名
        $file_arr['img_pre_name'] = substr($img_name, 0, strrpos($img_name, '.'));//文件后缀名之前的字符串
        return $file_arr;
    }

    /*
     *本地上传
     */
    function upload_local()
    {
        $info = $this->file_upload();
        if (!$info) {// 上传错误提示错误信息
            return false;
        } else {
            foreach ($info as $k_img => $v_img) {
                $img_url = '/' . $v_img['savepath'] . $v_img['savename'];
                print_r($img_url);
            }
        }
    }

    /*
     * 云上传
     */
    public function upload_yun()
    {
        $upyun_config = array(
            'savePath' => '/' . $this->default_file_config['IMG_DIR'] . '/', //保存路径
        );
        $this->default_yun_config = array_merge($this->default_yun_config, $upyun_config);
        $img = new \Weiq\Upyunpic();
        $img->initparam($this->default_yun_config);
        $imgpath = $img->upload();
        $url = $this->default_file_config['IMG_URL'] . $imgpath[0];
        exit(json_encode($url));
//        print_r($url);
    }

    //删除需要上传的图片
    public function del($src)
    {
//        if (file_exists($src)) {
//            unlink($src);
//        }
    }

    //建立文件夹
    private function create_dir($is_dir)
    {
        if (!is_dir($is_dir)) {
            mkdir($is_dir);
        }
    }

    /*
     * 处理图片
     * @param string $img_path 源图片地址
     * @param string $logo 水印图名称
     * @param int $is_water 1代表水印，0代表不水印
     */
    public function image_deal($img_path, $logo, $is_water = 0)
    {
        $deal_info = $this->deal_img_url($img_path);
        $image = new \Think\Image();
        foreach ($this->default_file_config['IMG_DEAL'] as $k_img => $v_img) {
            $this->create_dir('.' . $deal_info['save_path']);
            $this->check_file_exsit($deal_info['img_path']);
            $image->open($deal_info['img_path']);
            $width = $image->width();
            $height = $image->height();
            if ($k_img == 'crop_size') {
                $this->img_crop($logo, $image, $v_img, $deal_info, $deal_info['img_thumb_path'], $deal_info['img_thumb_name'], $width, $height, $is_water);
            }
            if ($k_img == 'little_size') {
                $this->img_crop($logo, $image, $v_img, $deal_info, $deal_info['img_thumb_little_path'], $deal_info['img_thumb_little_name'], $width, $height);
            }
        }
    }

    /*
     * 检查文件是否存在
     * @param string $img_path //源图片地址
     */
    private function check_file_exsit($img_path)
    {
        $info = getimagesize($img_path);
        if (!is_file($img_path) && !$info) {
            echo "<script>alert('图片不存在，请重新上传！');history.go(-1);</script>";
            die();
        }
    }

    /*
     * 处理图片
     * @param string $logo 水印图名称
     * @param object $image image实例
     * @param array $v_img 图片被裁剪之后的宽和高
     * @param array $deal_info 图片处理信息数组
     * @param string $path 裁剪图的保存路径
     * @param string $name 要上传的图片名称
     * @param int $width 源图片的宽
     * @param int $height 源图片的高
     * @param int $is_water 1代表水印，0代表不水印
     */
    private function img_crop($logo, $image, $v_img, $deal_info, $path, $name, $width, $height, $is_water = 0)
    {
        if ($is_water == 1) {
            $logo = $logo ? $logo : $deal_info['logo'];
            $logo_name = './' . $deal_info['img_dir'] . '/' . $deal_info['logo_dir'] . '/' . $logo;
            $image->thumb($v_img['width'], $v_img['height'] / $width * $height, \Think\Image::IMAGE_THUMB_FIXED)->water($logo_name, \Think\Image::IMAGE_WATER_WEST, 50)->save($path);
            //处理完之后的图片再次上传到新浪云
            if ($deal_info['flag']) {
                $this->crop_up($deal_info['img_dir'], $deal_info['dir_name'], $name, $deal_info['save_path']);
            }
        } else {
            $image->thumb($v_img['width'], $v_img['height'] / $width * $height, \Think\Image::IMAGE_THUMB_FIXED)->save($path);
            //处理完之后的图片再次上传到新浪云
            if ($deal_info['flag']) {
                $this->crop_up($deal_info['img_dir'], $deal_info['dir_name'], $name, $deal_info['save_path']);
            }
        }

    }

    /*
     * 上传处理后的图片到新浪云
     * @param string $dir 一级上传目录名称
     * @param string $dir_name 上传文件的二级目录名称
     * @param string $name 要保存的文件名称
     * @param string $save_path 新浪云保存路径
     */
    private function crop_up($dir, $dir_name, $name, $save_path)
    {
        $tmp_name = getcwd() . '\\' . $dir . '\\' . $dir_name . '\\' . $name;
        $_FILES['crop_pic'] = array('tmp_name' => $tmp_name, 'name' => $name, 'type' => 'image/jpeg', 'error' => 0, 'size' => getimagesize($tmp_name)['bits']);
        $this->upload_crop_Img($_FILES, $save_path);
    }

    /*
     * 新浪云上传处理后的本地图片,属于自动调用
     * @param array $file 需要上传的图片信息
     * @param string 新浪云的的保存路径
     */
    private function upload_crop_Img($file, $path)
    {
        $upyun_config = array(
            'maxSize' => 3145728, //上传的文件大小限制 (0-不做限制)
            'exts' => array('jpg', 'gif', 'png', 'jpeg', 'bmp'), //允许上传的文件后缀
            'savePath' => $path, //保存路径
        );
        $this->default_yun_config = array_merge($this->default_yun_config, $upyun_config);
        $img = new \Weiq\Upyunpic();
        $img->initparam($this->default_yun_config);
        $img->uploadPic($file['crop_pic'], 1);
        $picurl_url = $img->getUploadFileInfo();
        return $picurl_url;
    }
}