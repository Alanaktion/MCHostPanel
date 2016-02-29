<?php

$username = $_GET['username'];
if(empty($username)) exit('No username specified.');

$filename = 'skintmp/' . md5($username) . '.png';
if(file_exists($filename) && time()-filemtime($filename) < 3600*24*3 && filesize($filename)) { // cached file exists, is newer than 3 days
	$raw = file_get_contents($filename);
} else {
	$url = 'https://api.mojang.com/profiles/minecraft';
	$options = array('http' => array('header' => "Content-type: application/json\r\n", 'method' => 'POST', 'content' => json_encode(array($username))));
	$context = stream_context_create($options);
	$profile = json_decode(file_get_contents($url, false, $context));
	
	//If the profile doesn't exist, use the Steve template so we don't have broken images
	if(empty($profile[0]->id)) {
		$raw = file_get_contents("https://minecraft.net/images/steve.png");
	} else {
		$data = json_decode(file_get_contents('https://sessionserver.mojang.com/session/minecraft/profile/' . $profile[0]->id));
		$properties = json_decode(base64_decode($data->properties[0]->value));
		$raw = file_get_contents($properties->textures->SKIN->url);
	}
	
	if(!is_dir('mktmp')) mkdir('skintmp');
	file_put_contents($filename, $raw);
}

$size = isset($_GET['size']) ? max(8, min(250, $_GET['size'])) : 48;

$im = imagecreatefromstring($raw);
$av = imagecreatetruecolor($size, $size);
imagecopyresized($av, $im, 0, 0, 8, 8, $size, $size, 8, 8); // Face
imagecolortransparent($im, imagecolorat($im, 63, 0)); // Black Hat Issue
imagecopyresized($av, $im, 0, 0, 40, 8, $size, $size, 8, 8); // Accessories

header('Content-type: image/png');
echo imagepng($av);

imagedestroy($im);
imagedestroy($av);
