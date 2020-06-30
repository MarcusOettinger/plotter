<?php
//
// Plotter: URL shortening via tinyurl.com
// M. Oettinger 06/2020
// License: GPL
//

$useShortening = true;
$useQR = false;

function getshortlink($longurl) {
	global $shorten;
	
	// Open phased url
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $shorten .$longurl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$retval= curl_exec($ch);
	curl_close($ch);

	return $retval;
}
?>
