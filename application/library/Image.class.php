<?php
/**
 * 图片操作类库
 */
class Image{
	/**
	 * 获取图片信息
	 * @param   string    $filename [文件名]
	 * @return  array  [文件信息,宽度，高度，文件名，扩展名]
	 */
	public static function getImageInfo($filename){
		if(@!$info = getimagesize($filename)){
			exit('文件不是图片');
		}
		$fileInfo['width'] = $info[0];
		$fileInfo['height'] = $info[1];
		$mime = image_type_to_mime_type($info[2]);
		// 创建画布资源、
		$createFun = str_replace('/', 'createfrom', $mime);
		$outFun = str_replace('/', '', $mime);
		// 创建的源文件名
		$fileInfo['createFun'] = $createFun;
		// 创建输出文件名
		$fileInfo['outFun'] = $outFun;
		// 获取文件名后缀 .jpg
		$fileInfo['ext'] = strtolower(image_type_to_extension($info[2]));
		return $fileInfo;
	}

	/**
	 * 缩略图
	 * @param   [string]    $filename  [文件名]
	 * @param   float     $scale     [默认缩放比例]
	 * @param   [type]    $dst_w     [最大宽度]
	 * @param   [type]    $dst_h     [最大高度]
	 * @param   string    $dest      [缩略图保存路径，默认保存在thumb目录下]
	 * @param   string    $prefix    [缩略图文件名前缀]
	 * @param   boolean   $delSource [是否删除源文件]
	 * @return  [type]               [最终保存路径及文件名]
	 */
	public static function thumbImage($filename, $scale = 0.5, $dst_w = null, $dst_h = null, $dest = 'thumb', $prefix = 'thumb_', $delSource = false){
		$fileInfo = self::getImageInfo($filename);
		$src_w = $fileInfo['width'];
		$src_h = $fileInfo['height'];
		// 如果指定最大宽度和高度，则按照等比例缩放进行处理
		if(is_numeric($dst_w) && is_numeric($dst_h)){
			// 等比例缩放算法
			$ratio_orig = $src_w / $src_h;
			if($dst_w / $dst_h > $ratio_orig){
				$dst_w = $dst_h * $ratio_orig;
			}else{
				$dst_h = $dst_w / $ratio_orig;
			}
		}else{
			// 默认缩放比例处理
			$dst_w = ceil($src_w * $scale);
			$dst_h = ceil($src_h * $scale);
		}
		$dst_image = imagecreatetruecolor($dst_w, $dst_h);
		$src_image = $fileInfo['createFun']($filename);
		imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
		
		$randNum = mt_rand(100000, 999999);
		// 检测目标目录是否存在，不存在则创建
		if($dest && !file_exists($dest)){
			mkdir($dest, 0777, true);
		}
		$dstName = "{$prefix}{$randNum}" . $fileInfo['ext'];
		$destnation = $dest ? $dest.'/'.$dstName : $dstName;
		$fileInfo['outFun']($dst_image, $destnation);
		imagedestroy($src_image);
		imagedestroy($dst_image);
		if($delSource){
			@unlink($filename); //删除源文件
		}
		return $destnation;
	}

	/**
	 * 文字水印
	 * @param              [type]    $filename [description]
	 * @param              [type]    $fontfile [description]
	 * @param              string    $text     [description]
	 * @param              string    $dest     [description]
	 * @param              string    $prefix   [description]
	 * @param              integer   $r        [description]
	 * @param              integer   $g        [description]
	 * @param              integer   $b        [description]
	 * @param              integer   $alpha    [description]
	 * @param              integer   $fontsize [description]
	 * @param              integer   $angle    [description]
	 * @param              integer   $x        [description]
	 * @param              integer   $y        [description]
	 * @param              bool      $delSource 是否删除源文件
	 * @return             [type]              [description]
	 */
	public static function waterText($filename, $fontfile = './verdana.ttf', $text = 'wwww.daowu.com', $dest = 'water_text', $prefix = 'water_text_', $r = 255, $g = 0, $b = 0, $alpha = 60, $fontsize = 30, $angle = 0, $x = 0, $y = 30, $delSource = false){
		$fileInfo = self::getImageInfo($filename);
		$image = $fileInfo['createFun']($filename);
		$red = imagecolorallocatealpha($image, $r, $g, $b, $alpha);
		
		imagettftext($image, $fontsize, $angle, $x, $y, $red, $fontfile, $text);
		
		if($dest && !file_exists($dest)){
			mkdir($dest, 0777, true);
		}
		$randNum = mt_rand(100000, 999999);
		$dstName = "{$prefix}{$randNum}" . $fileInfo['ext'];
		$destnation = $dest ? $dest.'/'.$dstName : $dstName;
		$fileInfo['outFun']($image, $destnation);
		imagedestroy($image);
		if($delSource){
			@unlink($filename); //删除源文件
		}
		return $destnation;
	}

	/**
	 * 图片水印
	 * @param              [type]    $dstName   [水印图片]
	 * @param              [type]    $srcName   [源图片]
	 * @param              integer   $position  [水印的位置]
	 * @param              integer   $pct       [透明度]
	 * @param              string    $dest      [保存文件的路径]
	 * @param              string    $prefix    [文件名前缀]
	 * @param              boolean   $delSource [是否删除源文件]
	 * @return             [type]               [文件路径]
	 */
	public static function waterPic($dstName, $srcName, $position = 0, $pct = 50, $dest = 'water_pic', $prefix = 'water_pic', $delSource = false){
		$dstInfo = self::getImageInfo($dstName);
		$srcInfo = self::getImageInfo($srcName);
		$dst_im = $dstInfo['createFun']($dstName);
		$src_im = $srcInfo['createFun']($srcInfo);
		$src_width = $src_im['width'];
		$src_height = $src_im['height'];
		$dst_width = $dst_im['width'];
		$dst_height = $dst_im['height'];
		switch ($position) {
			case 0:
				$x = 0;
				$y = 0;
				break;
			case 1:
				$x = ($dst_width-$src_width)/2;
				$y = 0;
				break;
			case 2:
				$x = $dst_width-$src_width;
				$y = 0;
				break;
			case 3:
				$x = 0;
				$y = ($dst_height-$src_height)/2;
				break;
			case 4:
				$x = ($dst_width-$src_width)/2;
				$y = ($dst_height-$src_height)/2;
				break;
			case 5:
				$x = $dst_width-$src_width;
				$y = ($dst_height-$src_height)/2;
				break;
			case 6:
				$x = 0;
				$y = $dst_height-$src_height;
				break;
			case 7:
				$x = ($dst_width-$src_width)/2;
				$y = $dst_height-$src_height;
				break;
			case 8: //右下角
				$x = $dst_width-$src_width;
				$y = $dst_height-$src_height;
				break;
			default:
				$x = 0;
				$y = 0;
				break;
		}
		// 合并图片
		imagecopymerge($dst_im, $src_im, $x, $y, 0, 0, $src_w, $src_h, $pct);
		if($dest && !file_exists($dest)){
			mkdir($dest, 0777, true);
		}
		$randNum = mt_rand(100000, 999999);
		$dstName = "{$prefix}{$randNum}" . $fileInfo['ext'];
		$destnation = $dest ? $dest.'/'.$dstName : $dstName;
		$fileInfo['outFun']($src_im, $destnation);
		imagedestroy($src_im);
		imagedestroy($dst_im);
		if($delSource){
			@unlink($filename); //删除源文件
		}
		return $destnation;
	}
}
// $filename = 'images/env6.jpg';
// Image::waterText($filename);
// var_dump($dstName);