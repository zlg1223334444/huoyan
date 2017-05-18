<?php
namespace Weiq;
class UploadConfig
{
    private $default_file_config = array(
        //图片处理参数
        'IMG_DEAL_CONFIG' => [
            'IMG_DEAL' => [
                'crop_size' => ['width' => 640, 'height' => 640],
                'little_size' => ['width' => 100, 'height' => 100]
            ],
            'IMG_URL' => 'http://weiq-pic.b0.upaiyun.com',    //图片服务器
            'IMG_DIR' => 'Uploads', //一级存储目录名称
            'DIR_LENGTH' => '9', //一级存储目录名称字符数加2
            'IMG_DIR_LENGTH' => '6', //二级存储目录截取长度
            'IMG_NAME_STR_LENGTH' => '16', //一级目录名称字符数+二级存储目录截取长度+3
            'IMG_URL_LENGTH' => '30', //IMG_URL字符数
            'LOGO_DIR' => 'logo',
            'LOGO' => 'logo.png',//水印图名称
        ],
    );
    private $default_yun_config = array(
        'U_PAI_YUN' => [
            'mimes' => array(), //允许上传的文件MiMe类型
            'maxSize' => 3145728, //上传的文件大小限制 (0-不做限制)
            'exts' => array('jpg', 'gif', 'png', 'jpeg', 'bmp'), //允许上传的文件后缀
            'savePath' => '', //保存路径
            'Thumbnailwidth' => 100, // 宽和高都是0时不缩放，宽！=0 是限定宽度高度自适应；高！=0是限定高度宽自适应
            'Thumbnailheight' => 0 //
        ]
    );

    public function get_default_file_config()
    {
        return $this->default_file_config;
    }

    public function get_default_yun_config()
    {
        return $this->default_yun_config;
    }
}