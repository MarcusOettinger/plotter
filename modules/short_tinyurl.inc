<?php
//
// YAPlot: URL shortening via tinyurl.com
// M. Oettinger 08/2015
// License: GPL
//

$useShortening = true;

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