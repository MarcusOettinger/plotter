<?php
//
// function.php: handle query Options and call
// graph.php.
// (This file is the content of the iframe, where the image
// of the graph is displayed. It is called by the main page
// hosting the iframe and only contains the image plus some
// js code to interoperate with the calling page)
//
// Marcus Oettinger 06/2020
// - conversion to SSL-encrypted pages: QR-code inline to avoid
//   mixed content
// 06/2015:
// - converted query string used to load plot into the main page
//     to an URL (usable for Hyperlinks).
// - reworked code for smoother color handling
// - added short link via oeshort.de (oettinger-physics special)
// - added a QRCode containing URL to plot Image (via oeshort.de/TCPDF) (oettinger-physics special)
//
// --------------------------------------------------------------------
/*
Original source: http://rechneronline.de/function-graphs/ (GPL)
Copyright (C) 2011 Juergen Kummer

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

include_once("config.inc");
include("modules/function.inc");

?>
<!DOCTYPE html><html>
<head><title></title>
<meta http-equiv="expires" content="0" />
<meta http-equiv="cache-control" content="no-cache">
<meta name="robots" content="noindex,nofollow" />
<link rel="stylesheet" href="include/plotstyle.min.css" type="text/css" />
</head><body>
<table border="0" cellspacing="0" cellpadding="0">
<tr><td id="back">
<img src="graph.php?<?= $query ?>" width="<?= $width ?>" height="<?= $height ?>" alt="Graph" />
</td></tr></table>
<script type="text/javascript" nonce="<?php echo $GLOBALS['plotternonce'];?>">
    parent.document.getElementById("path").value="<?php
	// ... echo the original (ugly) URL and ...
	$longurl = $srv . $query;
	echo $longurl;
    ?>";
    if ( parent.document.getElementById("shortpath") !== null) {
      parent.document.getElementById("shortpath").value="<?php
	//
	// $srv . $query is the URL of the graph,
	// use URL shortening service to squeeze it, ...
	//
	if (isset($useShortening) && $useShortening ) {
		$longurl = $srv . utf8_encode($query);
		$shorturl = getshortlink(rawurlencode($longurl));
		echo $shorturl;
	}
        ?>";
      }
      if ( parent.document.getElementById("QRcode") !== null) {
        parent.document.getElementById("QRcode").src="<?php
	//
	// ... display QRcode of  thew shortened link
	//
	// echo $shorturl . ".qr";
	// the page is served SSL-encrypted, to avoid trouble the
	// QR-code is embedded as a data-URI:
	//
	$imgurl = $shorturl . ".qr" ;
	$imagedata = file_get_contents($imgurl);
	$base64 = base64_encode($imagedata);
	echo "data:image/png;base64," . $base64;
        ?>";
      }
    </script>
</body></html>
