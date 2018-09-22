<?php
//
// YAPlot: URL shortening via yourls
// M. Oettinger 08/2015
// License: GPL
//

$useShortening = true;

function getshortlink($longurl) {
	global $secret, $shorten;
	
	// Open phased url
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $shorten . $longurl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$yourlsoutput = curl_exec($ch);
	curl_close($ch);

	// Converts xml to php array
	$xml = simplexml_load_string($yourlsoutput);
	if ($xml->statusCode == 200) {
		$retval = $xml->shorturl;
	} else {
		$retval = "ERR";
	}
	return $retval;
//print_r($xml);
}
?>