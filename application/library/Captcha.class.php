<?php
/**
 * 验证码类库
 * @Create             Sublime
 * @Author             MORTAL
 * @DateTime 		   2017-07-06
 * @version            [v1]
 */
class Captcha{
	/**
	 * 字体文件
	 * @var string
	 */
	private $_fontfile = '';
	/**
	 * 字体大小
	 * @var integer
	 */
	private $_fontsize = 20;
	/**
	 * 验证码宽度
	 * @var integer
	 */
	private $_width = 120;
	/**
	 * 验证码高度
	 * @var integer
	 */
	private $_height = 40;
	/**
	 * 验证码长度
	 * @var integer
	 */
	private $_length = 4;
	/**
	 * 画布资源
	 * @var null
	 */
	private $_image = null;
	/**
	 * 干扰元素雪花*
	 * @var integer
	 */
	private $_snow = 10;
	/**
	 * 像素个数
	 * @var integer
	 */
	private $_pixel = 0;
	/**
	 * 干扰元素线段个数
	 * @var integer
	 */
	private $_line = 0;

	public function __construct($config =[]){
		if(is_array($config) && count($config) > 0){
			// 检测字体文件是否存在并可读
			if(isset($config['fontfile']) && is_file($config['fontfile']) && is_readable($config['fontfile'])){
				$this->_fontfile = $config['fontfile'];				
			}else{
				return false;
			}
			// 检测是否设置字体大小
			if(isset($config['fontsize'] ) && $config['fontsize'] > 0){
				$this->_fontsize = $config['fontsize'];
			}
			// 检测是否设置验证码宽高
			if(isset($config['width'] ) && $config['width'] > 0){
				$this->_width = $config['width'];
			}
			if(isset($config['height'] ) && $config['height'] > 0){
				$this->_height = $config['height'];
			}
			if(isset($config['length'] ) && $config['length'] > 0){
				$this->_length = $config['length'];
			}
			// 配置干扰元素
			if(isset($config['snow']) && $config['snow'] > 0){
				$this->_snow = $config['snow'];
			}
			if(isset($config['pixel']) && $config['pixel'] > 0){
				$this->_pixel = $config['pixel'];
			}
			if(isset($config['line']) && $config['line'] > 0){
				$this->_line = $config['line'];
			}
			// 创建画布
			// $this->_image = imagecreatetruecolor($this->_width, $this->_height);
			$this->_image = imagecreatetruecolor($this->_width, $this->_height);
			// return $this->_image;
		}else{
			return false;
		}
	}

	/**
	 * 获取验证码
	 * @return  [string]    [验证码]
	 */
	public function getCaptcha(){
		
		$white = imagecolorallocate($this->_image, 255, 255, 255);
		
		// 填充矩阵
		imagefilledrectangle($this->_image, 0, 0, $this->_width, $this->_height, $white);
		// 生成验证码
		$str = $this->_generateStr($this->_length);
		if(false === $str){
			return false;
		}
		// 绘制验证码
		for ($i=0; $i < $this->_length; $i++) { 
			$size = $this->_fontsize;
			$angle = mt_rand(-30, 30);
			$x = ceil(($this->_width/$this->_length)*$i+mt_rand(5, 10));
			$y = ceil($this->_height/1.5);
			$color = $this->_getRandColor();
			$fontfile = $this->_fontfile;
			// $text = mb_substr($str, $i, 1, 'utf-8');
			$text = $str{$i};
			imagettftext($this->_image, $size, $angle, $x, $y, $color, $fontfile, $text);
		}
		// 像素和线段
		if($this->_snow){
			// 使用雪花干扰元素
			$this->_getSnow();
		}else{
			if($this->_pixel){
				$this->_getPixel();
			}
			if($this->_line){
				$this->_getLine();
			}
		}
		// 输出图像
		header('Content-type:image/png');
		imagepng($this->_image);
		imagedestroy($this->_image);
		return strtolower($str);
	}

	/**
	 * 产生验证码字符
	 * @param  integer   $length [验证码长度]
	 * @return  string  [验证码字符]
	 */
	private function _generateStr($length = 4){
		if($length < 1 || $length > 30){
			return false;
		}
		$chars = array(
			'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9'		);
		$str = join('', array_rand(array_flip($chars), $length));
		return $str;
	}

	/**
	 * 获取随机颜色
	 */
	private function _getRandColor(){
		return imagecolorallocate($this->_image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
	}

	/**
	 * 产生雪花干扰元素	 
	 *  @return string 
	 */
	private function _getSnow(){
		for ($i=0; $i < $this->_snow; $i++) { 
			imagestring($this->_image, mt_rand(1, 5), mt_rand(0, $this->_width), mt_rand(0, $this->_height), '*', $this->_getRandColor());
		}
	}
	/**
	 * 产生像素干扰元素	 
	 *  @return string 
	 */
	private function _getPixel(){
		for ($i=0; $i < $this->_pixel; $i++) { 
			imagesetpixel($this->_image, mt_rand(1, 5), mt_rand(0, $this->_width), mt_rand(0, $this->_height), '*', $this->_getRandColor());
		}
	}
	/**
	 * 产生线段干扰元素	 
	 *  @return string 
	 */
	private function _getLine(){
		for ($i=0; $i < $this->_line; $i++) { 
			imageline($this->_image, mt_rand(1, 5), mt_rand(0, $this->_width), mt_rand(0, $this->_height), '*', $this->_getRandColor());
		}
	}
}