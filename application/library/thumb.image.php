<?php
/**
 * 缩略图效果
 */
/*$filename = 'images/env6.jpg';
$fileInfo = getimagesize($filename);
if($fileInfo){
	list($src_w, $src_h) = $fileInfo;
}else{
	die("not image");
}
$src_image = imagecreatefromjpeg($filename);
// 50 * 50
$dst_image_50 = imagecreatetruecolor(50, 50);
// 270 * 270
$dst_image_270 = imagecreatetruecolor(270, 270);
imagecopyresampled($dst_image_50, $src_image, 0, 0, 0, 0, 50, 50, $src_w, $src_h);
imagecopyresampled($dst_image_270, $src_image, 0, 0, 0, 0, 270, 270, $src_w, $src_h);
// 保存图片
imagepng($dst_image_50, 'images/thumb_50x50.jpg');
imagepng($dst_image_270, 'images/thumb_270x270.jpg');
imagedestroy($dst_image_50);
imagedestroy($dst_image_270);
imagedestroy($src_image);*/

/**
 * 等比例缩放
 */
$filename = 'images/env6.jpg';
if($fileInfo=getimagesize($filename)){
	list($src_w, $src_h) = $fileInfo;
	$mime = $fileInfo['mime'];
}else{
	die("不是图片");
}
$createFun = str_replace('/', 'createfrom', $mime);
$outFun = str_replace('/', null, $mime);
// echo $createFun;exit;
$dst_w = 300;
$dst_h = 600;
// 等比例算法
$ratio_orig = $src_w / $src_h;
if($dst_w / $dst_h > $ratio_orig){
	$dst_w = $dst_h * $ratio_orig;
}else{
	$dst_h = $dst_w / $ratio_orig;
}
// 创建原画布资源和目标画布资源
// $src_image = imagecreatefromjpeg($filename);
// 动态创建
$src_image = $createFun($filename);
$dst_image = imagecreatetruecolor($dst_w, $dst_h);
imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
//保存图片
// imagejpeg($dst_image, 'images/test_thumb.jpg');
$outFun($dst_image, 'images/test_thumb1.jpg')
imagedestroy($src_image);
imagedestroy($dst_image);