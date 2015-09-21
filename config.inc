<?php
// familie-oettinger/oettinger-physics: function plotter configuration
//
// M. Oettinger 06/2015
// -------------------------------------------------------------------------
//
require_once("/homepages/34/d212654589/htdocs/lib/sitewide/config.inc");
$thisdir = $basedir;
$fqdn = "http://plot.oettinger-physics.de";

// This is the value prepended to the URL of the generated function plot
// (set this to the location of plot.php).
$srv = "$fqdn/graph.php?";

// the truetype font to use. To disable freetype font display, just leave
// this value empty (as a builtin GD font is used if the font file isn't
// found).
// 
$defaultTTFont = "FreeSans.ttf";

// --------------------------------------------------------------------------------
// enable URL shortening - use a Service like tinyurl to get a short
// link pointing to the fong URL with query string.
//
// URL shortening service to use, examples are 
//
// tinyurl: no authentication needed, the API will silently act on your link:
// $shorten = "http://tinyurl.com/api-create.php?url=";
// include_once("short_tinyurl.inc");
// $short_msg = "Alternatively, use the short link provided below";

// for your own shortening service via yourls (http://www.yourls.com),
// use authentication via secret signature token:
// $secret = "XXXX"
// $shorten = "http://example.com/yourls-api.php?action=shorturl&signature=".$secret."&url=";
// $short_msg = "Alternatively, use the short link provided below";


$secret = "444c0cefa8";
$shorten= "http://oeshort.de/yourls-api.php?action=shorturl&signature=".$secret."&url=";
$short_msg = "Because this is an ugly beast, I added my URL shortener - the link via oeshort is equivalent to the long one.";
include_once("short_oeshort.inc");

