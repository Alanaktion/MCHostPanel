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

// Parses text with control codes and returns what may be usable HTML.
// Original author: http://theperfectbeast.blogspot.com.es/2013/10/minecraft-server-log-web-interface.html
// Modified heavily to work better
function mclogparse2($str) {

	$lines = explode("\n", $str);

	foreach($lines as &$line) {
		// Remap problem characters
		$line = preg_replace("/</", "&lt;", $line);
		$line = preg_replace("/>/", "</span>&gt;", $line);

		// Remove unrequired formatting codes
		$line = str_replace(array("[m", "[21m", "[3m"), "", $line);

		// Split log line into sections to using first formatting code style
		$segarray = preg_split('/(\[0(?![0-9])|\[m)/', $line); // such lookaround :D - Alanaktion
		for ($i = 1; $i < count($segarray); ++$i) {
			// Do replace to add styled spans
			if (preg_match('/;\d{2};\d+m/', $segarray[$i])) {
				$segarray[$i] = preg_replace("/;30/", "<span class='black", $segarray[$i]);
				$segarray[$i] = preg_replace("/;31/", "<span class='red", $segarray[$i]);
				$segarray[$i] = preg_replace("/;32/", "<span class='green", $segarray[$i]);
				$segarray[$i] = preg_replace("/;33/", "<span class='gold", $segarray[$i]);
				$segarray[$i] = preg_replace("/;34/", "<span class='blue", $segarray[$i]);
				$segarray[$i] = preg_replace("/;35/", "<span class='purple", $segarray[$i]);
				$segarray[$i] = preg_replace("/;36/", "<span class='aqua", $segarray[$i]);
				$segarray[$i] = preg_replace("/;37/", "<span class='gray", $segarray[$i]);
				$segarray[$i] = preg_replace("/;22m/", "'>", $segarray[$i]);
				$segarray[$i] = preg_replace("/;1m/", " bold'>", $segarray[$i]);
				$segarray[$i] .= "</span>";
			}
		}

		// Rejoin then split log line using second formatting code style
		$line = implode("", $segarray);
		$segarray = preg_split('/§/', $line);

		for ($i = 1; $i < count($segarray); ++$i) {
			// Do replace to add styled spans
			$segarray[$i] = preg_replace("/^0/", "<span class='black'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^1/", "<span class='blue'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^2/", "<span class='green'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^3/", "<span class='aqua'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^4/", "<span class='red'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^5/", "<span class='purple'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^6/", "<span class='gold'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^7/", "<span class='gray'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^8/", "<span class='gray'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^9/", "<span class='blue'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^a/", "<span class='green'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^b/", "<span class='aqua'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^c/", "<span class='red'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^d/", "<span class='purple'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^e/", "<span class='gold'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^f/", "<span class='black'>", $segarray[$i]);
			$segarray[$i] = preg_replace("/^r/", "<span class='black'>", $segarray[$i]);
			$segarray[$i] .= "</span>";
		}

		$line = implode("", $segarray);

		// Format prefixes nicely
		$line = preg_replace("/^(\[[0-9\:]+\] \[(Server thread|[A-Za-z0-9# -])\/(ERROR|WARN|INFO)\]:)/", "<span class='gray'>$1</span>", $line);
		$line = preg_replace(array("/(\[(Server thread|[A-Za-z0-9# -])\/WARN\])/", "/(\[(Server thread|[A-Za-z0-9# -])\/ERROR\])/"), array("<span class='gold'>$1</span>", "<span class='red'>$1</span>"), $line);

		// Remove excessive "Server thread" prefix
		$line = str_replace("[Server thread/", "[", $line);

		// Remove remaining control bytes
		$line = str_replace("\x1B", "", $line);
	}

	return implode("\n", $lines);

}

// Strips control codes from log
function mclogclean($str) {
	$str = preg_replace("/\x1B\[([0-9]+;?([0-9]+;)?(1|22)?)?m/", "", $str);
	return htmlspecialchars($str);
}
