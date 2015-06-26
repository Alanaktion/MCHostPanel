<?php
/**
 * copywrite www.Jamesplanet.net 2014
     */
    class MinecraftProfile{
        private $username;
        private $uuid;
        private $properties;
        /**
     * @param string $username The player's username.
     * @param string $uuid The player's UUID.
     * @param array $properties The player's properties specified on their Mojang profile.
     */
        function __CONSTRUCT($username, $uuid, $properties = array()){
            $this->username = $username;
            $this->uuid = $uuid;
            $this->properties = $properties;
        }

        /**
     * @return string The player's username.
     */
        public function getUsername(){
            return $this->username;
        }

        /**
     * @return string The player's UUID.
     */
        public function getUUID(){
            return $this->uuid;
        }

        /**
     * @return array The player's properties listed on their mojang profile.
     */
        public function getProperties(){
            return $this->properties;
        }

        /**
     * @return array Returns an array with keys of 'properties, usernname and uuid'.
     */
        public function getProfileAsArray(){
            return array("username" => $this->username,"uuid" => $this->uuid,"properties" => $this->properties);
        }

        /**
     * @gets the skin of the profile and returns it as png data.
     */
        public function getSkin(){
            $properties = $this->properties;
            $encoded = json_encode($properties);
            $imgUrl = json_decode(base64_decode(json_decode($encoded, true) [0]['value']) , true) ['textures']['SKIN']['url'];
            if ($imgUrl == NULL)
            {
                //player doesnt exist
                return false;
            } 
            else            
            {
                //get the skin
            $img = file_get_contents($imgUrl);
            return $img;
            }
        }
        
        
       public function getFace()
       {
       		$properties = $this->properties;
            $encoded = json_encode($properties);
            $size = isset($_GET['size']) ? max(8, min(250, $_GET['size'])) : 48;
            $imgUrl = json_decode(base64_decode(json_decode($encoded, true) [0]['value']) , true) ['textures']['SKIN']['url'];
            if ($imgUrl == NULL)
            {
                //player doesnt exist
                return false;
            } 
            else            
            {
            	//get the face
            	$img = file_get_contents($imgUrl);
        		$im = imagecreatefromstring($img);
				$av = imagecreatetruecolor($size, $size);
				imagecopyresized($av, $im, 0, 0, 8, 8, $size, $size, 8, 8);    // Face
				imagecolortransparent($im, imagecolorat($im, 63, 0));    // Black Hat Issue
				imagecopyresized($av, $im, 0, 0, 40, 8, $size, $size, 8, 8);   // Accessories
				imagepng($av);
				imagedestroy($im);
				imagedestroy($av);
				return $im;
            }
      
       }

        /**
     * @gets the cape of the profile and returns it as png data.
     */
        public function getCape(){
            $properties = $this->properties;
            $encoded = json_encode($properties);
            $imgUrl = json_decode(base64_decode(json_decode($encoded, true) [0]['value']) , true) ['textures']['CAPE']['url'];
            if ($imgUrl == NULL)
            {
                //player has no capes
                return false;
            } 
            else            
            {
                //get the cape
            $img = file_get_contents($imgUrl);
            return $img;
            }
        }

          /**
     * @gets if the user is a mojang employee
     */
        public function isMojangEmployee(){
            $properties = $this->properties;
            $encoded = json_encode($properties);
            $imgUrl = json_decode(base64_decode(json_decode($encoded, true) [0]['value']) , true) ['textures']['CAPE']['url'];
            
            if ($imgUrl == NULL)
            {
                //player has no capes
                return false;
            } 
            else            
            {
                //compare the MD5 of the retreved image data against
                //the expected MD5 of the mojang employee cape.
                $mojangMD5 = 'a1a679415118ab3e93662fe41aaf0b34';
                $imgMD5 = md5_file($imgUrl);
                
                if($imgMD5 == $mojangMD5)
                {
                    return true;
                } 
                else                 
                {
                    return false;
                }
            }
        }


            /**
            * @gets if the user has bought minecraft
            */
            public function isPremium()
            {

            }

        }

    
    class ProfileUtils{
        /**
     * @param string $identifier Either the player's Username or UUID.
     * @param int $timeout The length in seconds of the http request timeout.
     * @return MinecraftProfile|null Returns null if fetching of profile failed. Else returns completed user profile.
     */


        public static function getProfile($identifier, $timeout = 5){
            if (strlen($identifier) <= 16) $identifier = ProfileUtils::getUUIDFromUsername($identifier, $timeout) ['uuid'];
            $url = "https://sessionserver.mojang.com/session/minecraft/profile/" . $identifier;

            $ctx = stream_context_create(array('http' => array('timeout' => $timeout)));
            $ret = file_get_contents($url, 0, $ctx);





            if (isset($ret) && $ret != null && $ret != false){
                $data = json_decode($ret, true);
                return new MinecraftProfile($data['name'], $data['id'], $data['properties']);

            } else {
                return null;
            }
            
       
        }

        /**
     * @param int $timeout http timeout in seconds
     * @param $username string Minecraft username.
     * @return array (Key => Value) "username" => Minecraft username (properly capitalized) "uuid" => Minecraft UUID
     */
    public static function getUUIDFromUsername($username, $timeout = 5){
            
            if (strlen($username) >= 16) return array("username" => "","uuid" => "");
            $url = 'https://api.mojang.com/profiles/page/1';
            $options = array('http' => array('header' => "Content-type: application/json\r\n",'method' => 'POST','content' => '{"name":"' . $username . '","agent":"minecraft"}','timeout' => $timeout) ,);
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $ress = json_decode($result, true);
            $ress = $ress["profiles"][0];
            $res = Array("username" => $ress['name'],"uuid" => $ress['id']);
            return $res;
        }

        /**
     * @param $uuid string UUID to format
     * @return string Properly formatted UUID (According to UUID v4 Standards xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx WHERE y = 8,9,A,or B and x = random digits.)
     */
        public static function formatUUID($uuid){
            $uid = "";
            $uid.= substr($uuid, 0, 8) . "-";
            $uid.= substr($uuid, 8, 4) . "-";
            $uid.= substr($uuid, 12, 4) . "-";
            $uid.= substr($uuid, 16, 4) . "-";
            $uid.= substr($uuid, 20);
            return $uid;
        }

    }
