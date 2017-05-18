<?php
namespace Vendor\Authcode\lcode;
session_start();

class Wadmincode {

    var $code = '';
    var $size = 26;
    var $width = 120;
    var $height = 40;
    var $length = 4;
    var $image;
    var $isck = 1;
    var $key = 'code';
    var $TPadden = 0.8;
    var $Txbase = 5;
    var $Tybase = 5;
    var $distortion_im;
    var $bcolor;
    var $tcolor;
    var $font;
    var $isBgImg = 0;
    var $isPixel = 1;
    var $isLine1 = 0;
    var $isLine2 = 0;
    var $isBgNoisy = 0;
    var $fontz = 0;
    var $authcode;

    function __construct($length = 6, $width = 120, $height = 40, $isck = true, $key = 'code', $fontz = 0) {
        $this->size = 28;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->isck = $isck;
        $this->key = $key;
        $this->fontz = $fontz;
        if (!isset($_COOKIE['wadmincode'])) {
            setcookie('wadmincode', session_id(), time() + 3600 * 12, '/');
            $this->authcode = session_id();
        } else {
            $this->authcode = $_COOKIE['wadmincode'];
        }
    }

    function init() {
        $fontinfo = $this->getFontArray();
        $this->font = $fontinfo['font'];
        $this->size = $fontinfo['size'];
        $this->isBgImg = $fontinfo['img'];
        $this->isPixel = $fontinfo['pixel'];
        $this->isLine1 = $fontinfo['line1'];
        $this->isLine2 = $fontinfo['line2'];
        $this->isBgNoisy = $fontinfo['bgnoisy'];
        $this->TPadden = $fontinfo['pd'];
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $this->tcolor = ImageColorAllocate($this->image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
        $this->tcolor = $this->getColor();
        if (!empty($this->isBgImg))
            $this->setBgImg();
        $this->bcolor = $this->getBgColor();
        imagefill($this->image, 16, 13, $this->bcolor);
        $this->distortion_im = imagecreatetruecolor($this->width, $this->height);
        imagefill($this->distortion_im, 16, 13, $this->bcolor);
    }

    function show() {
        $this->init();
        $text = $this->getStingArray();
        $length = $this->length;
        $im = $this->image;
        $size = $angle = $x = $y = $color = array();
        $font_size = $this->size;
        $font_file = $this->font;
        $info = array();
        $left = $this->Txbase;
        $TPadden = $this->TPadden;
        $color = $this->tcolor;
        for ($i = 0; $i < $length; $i++) {
            $angle = $this->getAngle();
            $box = $this->getTextBox($font_size, $angle, $font_file, $text[$i]);
            $v['size'] = $font_size;
            $v['angle'] = $angle;
            $v['x'] = $left;
            $v['y'] = $box['height'];
            $v['color'] = $color;
            $v['font'] = $font_file;
            $v['text'] = $text[$i];
            $left = $left + $box['width'] * $TPadden - $box['left'];
            $info[] = $v;
        }
        //print_r($info);
        $this->draw($info);
    }

    function draw($info) {
        $im = $this->image;
        if (!empty($this->isBgNoisy))
            $this->setBgNoisy();
        foreach ($info as $v) {
            ImageTTFText($im, $v['size'], $v['angle'], $v['x'], $v['y'], $v['color'], $v['font'], $v['text']);
        }
        $this->setImg();
        if (!empty($this->isPixel))
            $this->setPixel();
        if (!empty($this->isLine1))
            $this->setLine1();
        if (!empty($this->isLine2))
            $this->setLine2();
        $this->display();
    }

    function display() {
        $im = $this->image;
        $distortion_im = $this->distortion_im;
        Header("Content-type: image/JPEG");
        ImagePNG($distortion_im);
        ImageDestroy($distortion_im);
        ImageDestroy($im);
    }

    function setBgImg() {
        //图片文件必须是绝对路径
        $bgname = array(dirname(__FILE__) . '/bgpic1.jpg', dirname(__FILE__) . '/bgpic2.jpg', dirname(__FILE__) . '/bgpic3.jpg');
        $imgname = $bgname[mt_rand(0, 2)];
        $src_im = @imagecreatefromjpeg($imgname);
        $dst_im = $this->image;
        imagecopy($dst_im, $src_im, 0, 0, mt_rand(0, 50), 0, $this->width, $this->height);
    }

    function setImg() {
        $im = $this->image;
        $im_x = $this->width;
        $im_y = $this->height;
        $distortion_im = $this->distortion_im;
        for ($i = 0; $i < $im_x; $i++) {
            for ($j = 0; $j < $im_y; $j++) {
                $rgb = imagecolorat($im, $i, $j);
                imagesetpixel($distortion_im, $i, $j, $rgb);
            }
        }
    }

    function getStingArray() {
        $length = $this->length;
        $s[] = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'R', 'J', 'K', 'L', 'M', 'N', 'P', 'R', 'T', 'U', 'V', 'W', 'X', 'Y'); //22
        $s[] = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'r', 'j', 'k', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y'); //22
        $s[] = array('3', '4', '5', 'N', 'A', '8', 'K', '3', '4', '5', 'X', 'S', '8', 'Y', 'Z', '3', '4', '5', '6', 'm', '8', 'd'); //22
        $code = '';
        $text = array();
        for ($i = 0; $i < $length; $i++) {
            $str = $s[rand(0, 2)][rand(0, 21)];
            $code.=$str;
            $text[] = $str;
        }
        $this->code = strtolower($code);
        $this->save_yanma_mec($this->code, $this->authcode);
        /*
          if($this->isck){
          setcookie('authcode',md5($this->code.$this->key),time()+3600,'/',null,null,true);
          //setcookie('authcode',$this->code,time()+3600,'/',null,null,true);
          }else{
          if(!isset($_SESSION))session_start();
          $_SESSION['authcode']=$this->code;
          }
         */
        return $text;
    }

    function getCode() {
        if (empty($this->code)) {
            $this->getStingArray();
        }
        return $this->code;
    }

    function getFontArray() {
        $font = array('font' => dirname(__FILE__) . '/Just Another Courier.ttf', 'size' => 18, 'img' => 0, 'pixel' => 0, 'line1' => 1, 'line2' => 0, 'bgnoisy' => 0, 'pd' => 0.8); //*****  26
        return $font;
    }

    function getAngle() {
        $arr = array(-1, 1);
        $p = array_rand($arr);
        $p = $arr[$p];
        $angle = $p * mt_rand(1, 10);
        return $angle;
    }

    function setBgNoisy() {
        $im = $this->image;
        for ($i = 0; $i < 6; $i++) {
            $d = $this->getColor();
            imageline($im, rand(0, 20), mt_rand(0, 40), mt_rand(180, 250), mt_rand(0, 40), $d);
        }
    }

    function setLine1() {
        $im = $this->distortion_im;
        $color = $this->tcolor;
        $rand = mt_rand(5, 30);
        $rand1 = mt_rand(15, 25);
        $rand2 = mt_rand(5, 10);
        //for ($yy=$rand; $yy<=+$rand+2; $yy++){
        for ($yy = $rand; $yy <= +$rand; $yy++) {
            for ($px = -80; $px <= 80; $px = $px + 0.1) {
                $x = $px / $rand1;
                if ($x != 0)
                    $y = sin($x);
                $py = $y * $rand2;
                imagesetpixel($im, $px + 80, $py + $yy, $color);
            }
        }
    }

    function setLine2() {
        $color = $this->tcolor; //$this->bcolor;
        $im = $this->distortion_im;
        for ($i = 0; $i < 3; $i++) {
            imageline($im, 0, mt_rand(0, 40), $this->width, mt_rand(0, 40), $color);
        }
    }

    function setPixel() {
        $im = $this->distortion_im;
        for ($i = 0; $i < 50; $i++) {
            $d = $this->getColor();
            for ($j = 0; $j < 10; $j++)
                imagesetpixel($im, mt_rand(0, 250), mt_rand(0, 40), $d);
        }
    }

    function getColor() {
        $color = imagecolorallocate($this->image, rand(0, 150), rand(2, 150), rand(3, 150));
        return $color;
    }

    function getBgColor() {
        $color = ImageColorAllocate($this->image, rand(200, 255), rand(200, 255), rand(200, 255));
        return $color;
    }

    function getTextBox($size, $angle, $font, $text) {
        $box = imagettfbbox($size, $angle, $font, $text);
        $min_x = min(array($box[0], $box[2], $box[4], $box[6]));
        $max_x = max(array($box[0], $box[2], $box[4], $box[6]));
        $min_y = min(array($box[1], $box[3], $box[5], $box[7]));
        $max_y = max(array($box[1], $box[3], $box[5], $box[7]));
        $v = array(
            'left' => ($min_x >= -1) ? -abs($min_x + 1) : abs($min_x + 2),
            'top' => abs($min_y),
            'width' => $max_x - $min_x,
            'height' => $max_y - $min_y,
            'box' => $box
        );
        return $v;
    }

    function save_yanma_mec($yanma, $authcode) {
        //echo "yanma=".$yanma."<br/>";
        $key = md5("wadmincode" . $authcode);
        //setcookie('yanmakey',$key,60,'/');
        //write_log("authcode=$authcode;yanmakey=".$key,"login");
            if (S($key)) {
                S($key, null); 
                S($key, $yanma, 1800);
            } else {
                //echo "add"."<br/>";
                S($key, $yanma, 1800);
                //echo "after yanma=".$Cache->get($key);
            }
        //echo "after22 yanma=".$Cache->get($key);
        //exit;
    }

    //end class
}

/*
  $length=6,  //验证码字符数，默认6
  $width=120,  //图片宽度，默认120PX
  $height=40, //图片高度，默认40PX
  $isck=true,  //是否用COOKIE保存验证码，默认是
  $key='code'  //COOKIE 加密（MD5）时的干扰码，默认code
 */
//$fontz = $_GET['fontz'];
/* $img=new Authcode(4,80,35,true,'code',$fontz);
  $img->show(); */
?>