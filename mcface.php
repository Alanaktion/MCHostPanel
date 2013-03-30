<?php // Get Minecraft face from skin

$expires = 3600*24*2; // 2 days
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: '.gmdate('D, d M Y H:i:s',time()+$expires).' GMT');
header('Content-type: image/png');

error_reporting(0);

$size = $_GET['size'] ? $_GET['size'] : 40;

$file = file_get_contents('http://minecraft.net/skin/'.$_GET['user'].'.png');
$skin = imagecreatefromstring($file);
if(!$skin) {
	// Unable to load image
	$err = imagecreatetruecolor(1,1);
	$blk = imagecolorallocate($err,0,0,0);
	imagefill($err,0,0,$blk);
	imagecolortransparent($err,$blk);
	imagepng($err);
	imagedestroy($err);
}
$face = imagecreatetruecolor($size,$size);
imagecopyresized($face,$skin,0,0,8,8,$size,$size,8,8);

imagepng($face);
imagedestroy($face);

?>