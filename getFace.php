<?php
include ($_SERVER["DOCUMENT_ROOT"] . '/test/src/getminecraftprofile.php' )?>
<?php 
$profile = ProfileUtils::getProfile($_GET['username']);
$result = $profile->getFace();
header('Content-type: image/png');
echo $result;
