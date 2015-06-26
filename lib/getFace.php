<?php
include ('getminecraftprofile.php' )?>
<?php 
$profile = ProfileUtils::getProfile($_GET['username']);
$result = $profile->getFace();
header('Content-type: image/png');
echo $result;
