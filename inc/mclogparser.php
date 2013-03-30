<?php

// mclogparse
// Parses Minecraft log file and returns HTML
function mclogparse($str) {
	$str = htmlspecialchars($str);
	
	$str = preg_replace("/\\[3([0-9a-f])\;(1m|22m|39m)/",'<span class="c${1}">',$str);
	$str = strtr($str,array(
		'[INFO]' => '[<span class="c7">INFO</span>]',
		'[WARNING]' => '[<span class="c6">WARNING</span>]',
		'[SEVERE]' => '[<span class="cc">SEVERE</span>]'
	));
	$str = str_replace('[0;39m','</span></span></span></span></span>',$str);
	$str = str_replace('[m','</span></span></span></span></span></span></span></span></span></span>',$str);	// I have no idea what I'm doing with this :P
	
	return $str;
}

?>