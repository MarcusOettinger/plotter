<?php
//
// index.php: a function plotter (mostly), based on
// openPlaG (http://rechneronline.de/openPlaG/) by
// Juergen Kummer (GPL)
// ---------------------------------------------------
// Marcus Oettinger (www.oettinger-physisc.de)
// 06/2015
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
include_once("config.inc");
require_once("helpers.php");

// set silent true to suppress creation of short link and QRCode
// (no need if testing/Debugging)
$silent = false;

include("common.inc");
?>
