<?php
//
// index.php: a function plotter (mostly), based on
// openPlaG (http://rechneronline.de/openPlaG/) by
// Juergen Kummer (GPL)
// ---------------------------------------------------
// Marcus Oettinger (www.oettinger-physics.de)
// 09/2019
//  * removed inline styles/scripts
//  * polished UI (options in a jquery-ui dialog, colors)
//  * replaced some tables by divs to get the interface responsive
//  * replaced color setting mechanism
//  * restructured display (grouping elements by some sort of logic)
// Functional changes:
//  * added the possibility to plot up to 10 points (Maxima or such)
//  * added shortlink (a la tinyurl) for URL with the bloated query string
// License: GPL
//
//

function ErrMsg($header, $text) {
        echo '<html><head><title>plotter error</title>
        <link rel="stylesheet" href="include/ploterror.css" type="text/css">';
	echo "<body><h1 class='errheader'>$header</h1>";
        echo "$text";
	echo "<p>(see <a href='INSTALL.txt'>INSTALL.txt</a> or <a href='http://marcusoettinger.github.io/plotter/'>the github pages</a>)</p>";
        echo "</body></html>";
        exit ($header);
}

if (!file_exists("config.inc")) {
	ErrMsg("Error: unable to find config.inc", 
	"<p>To get your plotter up and running, copy the default file <b>config.inc.default</b> to <b>config.inc</b> and edit the new file according to your setup.</p>");
}

if (!is_readable("config.inc")) {
	ErrMsg("Unable to read config.inc", "<p>Probably there is a problem with file ownership or permissions.</p>");
}

include_once("config.inc");
require_once("modules/helpers.php");

if (!chkgd2()) {
	ErrMsg("Error: unable to find GD2",
	"<p>The PHP GD2 library  wasn't found - probably you need to install or enable it (see
	 <a href='https://www.libgd.org'>www.libgd.org</a>)</p>");
} 

// set silent true to suppress creation of short link and QRCode
// (no need if testing/Debugging)
$silent = false;
include("common.inc");
?>
