<?php
require 'getminecraftprofile.php';

$profile = ProfileUtils::getProfile($_GET['username']);
if($profile === null) {
	exit('Failed to find profile ' . $_GET['username']);
}
$result = $profile->getFace();

header('Content-type: image/png');
echo $result;
