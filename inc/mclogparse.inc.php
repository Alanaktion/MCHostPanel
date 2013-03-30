<?php

// Parses text with control codes and returns HTML
function mclogparse($str) {

	// Prevent HTML interpretation
	$str = '<span id="mclogparse">'.htmlspecialchars($str);

	// Colors
	$fgColors = array(
		30 => 'black',
		31 => 'red',
		32 => 'green',
		33 => 'yellow',
		34 => '#0055ff', // blue
		35 => 'magenta',
		36 => 'cyan',
		37 => 'white'
	);

	// Replace color codes
	foreach(array_keys($fgColors) as $color)
		$str = preg_replace("/\x1B\[".$color.';(1m|22m)/','</span><span style="color: '.$fgColors[$color].';">',$str);
	
	// Replace "default" codes with closing span
	$str = preg_replace("/\x1B\[(0;39|0;49)?m/",'</span>', $str);
	
	// Color message types
	$str = strtr($str,array(
		'[INFO]' => '[<span style="color: #77ccff;">INFO</span>]',
		'[WARNING]' => '[<span style="color: yellow;">WARNING</span>]',
		'[SEVERE]' => '[<span style="color: red;">SEVERE</span>]'
	));
	
	return $str;
	
}

?>