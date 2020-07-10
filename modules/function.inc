<?php
//
// function.inc: handle query Options and call graph.php.
// (included by function.php and function_silent.php)
//
// Marcus Oettinger 7/2020:
// - added option transparency
// - fixed querystring for named points
// - converted query string used to load plot into the main page
//     to an URL (usable for Hyperlinks).
// - reworked code for smoother color handling
// - added short link via oeshort.de (oettinger-physics special) - only in function.php!
// - added a QRCode containing URL to plot Image (via oeshort.de/TCPDF) (oettinger-physics special) - only in function.php!
//
// 12/2015 new option to use custom variable names (h1)
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

// get all variables from query string into separate variables - that's
// quite heavy but makes checking those variables easier.
//
$c = $_POST['c'];
$formula1 = $_POST['formula1'];	// fn1: f(x)
$formula2 = $_POST['formula2'];	// fn2: g(x)
$formula3 = $_POST['formula3'];	// fn3: h(x)
$term1 = $_POST['term0'];	// option: legend (show function term)
$term2 = $_POST['term1'];	// option: legend (show function term)
$term3 = $_POST['term2'];	// option: legend (show function term)
$width = $_POST['width'];
$height = $_POST['height'];
$rulex1 = $_POST['rulex1'];	// x-axis from
$rulex2 = $_POST['rulex2'];	// x-axis to
$ruley1 = $_POST['ruley1'];	// y-axis from
$ruley2 = $_POST['ruley2'];	// y-axis to
$intervalsx = $_POST['intervalsx'];
$intervalsy = $_POST['intervalsy'];
$linex = $_POST['linex'];
$liney = $_POST['liney'];
$deci = $_POST['deci'];
$mid = $_POST['mid'];
$lines = $_POST['lines'];
$numbers = $_POST['numbers'];
$dashes = $_POST['dashes'];
$frame = $_POST['frame'];
$errors = $_POST['errors'];
$sint1 = $_POST['sint1'];
$sint2 = $_POST['sint2'];
$sint3 = $_POST['sint3'];
$grid = $_POST['grid'];
$gridx = $_POST['gridx'];
$gridy = $_POST['gridy'];
$logsk = $_POST['logsk'];	// radiobuttons for logarithmic base
$logski = $_POST['logski'];	// text input to set a logarithmic base (see $logsk)
$ta1 = urlencode($_POST['ta1']);
$ta2 = urlencode($_POST['ta2']);
$tb1 = urlencode($_POST['tb1']);
$tb2 = urlencode($_POST['tb2']);
$tc1 = urlencode($_POST['tc1']);
$tc2 = urlencode($_POST['tc2']);
$cint1 = $_POST['cint1'];
$cint2 = $_POST['cint2'];
$cint3 = $_POST['cint3'];
$qq = $_POST['qq'];
$con0 = $_POST['con0'];
$con1 = $_POST['con1'];
$con2 = $_POST['con2'];
$anti = $_POST['anti'];
$gamma = $_POST['gamma'];
$bri = $_POST['bri'];
$cont = $_POST['cont'];
$emb = $_POST['emb'];
$blur = $_POST['blur'];
$neg = $_POST['neg'];
$gray = $_POST['gray'];
$mean = $_POST['mean'];
$edge = $_POST['edge'];
$bf = $_POST['bf'];
$pol = $_POST['pol'];
$rotate = $_POST['rotate'];
$filetype = $_POST['filetype'];
$logskx = $_POST['logskx'];	// radiobuttons for logarithmic base
$logskix = $_POST['logskix'];	// text input to set a logarithmic base (see $logskx)
$Y = $_POST['Y'];
for ($i=0; $i<8; $i++) $selfcol[$i] = $_POST["selfcol$i"];
$thick = $_POST['thick'];	// line thickness
$varname = $_POST['varname'];	// allow for custom variable names
$transp = $_POST['transp'];

// Handle named points in plot,
// maximum number of points is 10
$pcount = 0;
for ($i = 0;$i<10;$i++) {
	$dummy = $i+1;
	if (isset($_POST["PName$dummy"])) {
		$pcount = $i+1;
		$pn[$i] = $_POST["PName$dummy"];
		$px[$i] = $_POST["PX$dummy"];
		$py[$i] = $_POST["PY$dummy"];
	}
}

// alter the submitted values to fit into constraints
// and do some plausibility checks
//
$formula1 = urlencode($formula1);
$formula2 = urlencode($formula2);
$formula3 = urlencode($formula3);
$qq = urlencode($qq);
$Y = urlencode($Y);
$width = abs(intval($width));
$height = abs(intval($height));
if (!$width || $width>500) $width = 500;
if (!$height || $height>500) $height = 500;
if ($width<200) $width = 200;
if ($height<200) $height = 200;

// x- and y-axis min and max in [-10000; 10000]
// 
$rulex1 = urlencode($rulex1);
$rulex2 = urlencode($rulex2);
$ruley1 = urlencode($ruley1);
$ruley2 = urlencode($ruley2);
if ($rulex1>100000 || $rulex1 < -100000) $rulex1 = -100000;
if ($rulex2>100000 || $rulex2 < -100000) $rulex2 = 100000;
if ($ruley1>100000 || $ruley1 < -100000) $ruley1 = -100000;
if ($ruley2>100000 || $ruley2 < -100000) $ruley2 = 100000;

$gamma = str_replace(",", ".", $gamma);

$intervalsx = abs(intval($intervalsx));
$intervalsy = abs(intval($intervalsy));
if($intervalsx>250) $intervalsx = 250;
if($intervalsy>250) $intervalsy = 250;
if($intervalsx>$width/2) $intervalsx = round($width/2);
if($intervalsy>$height/2) $intervalsy = round($height/2);

$gridx = abs(intval($gridx));
$gridy = abs(intval($gridy));
if($gridx>250) $gridx = 250;
if($gridy>250) $gridy = 250;
if($gridx>$width/2) $gridx = round($width/2);
if($gridy>$height/2) $gridy = round($height/2);

$linex = abs(intval($linex));
$liney = abs(intval($liney));
if($linex>500) $linex = 500;
if($liney>500) $liney = 500;

$deci = abs(intval($deci));
$mid = abs(intval($mid));
if($deci>12) $deci = 12;
if($mid>500) $mid = 500;

if($logski) $logsk = $logski;
$logsk = str_replace(",", ".", $logsk);
$bri = str_replace(",", ".", $bri);
$rotate = str_replace(",", "." ,$rotate);

if($logskix) $logskx = $logskix;
$logskx = str_replace(",",".",$logskx);
// a base of 1 means no logarithm at all
if($logskx == 1) $logskx = 0;
if($logsk == 1) $logsk = 0;
// allow line thicknesses from 1 to 200 px
if( $thick > 200 ) $thick = 200;
if( $thick < 1 ) $thick = 1;

if (ctype_alpha($varname) ==  false) { $varname = "x"; }

// Catenate all the options into a query string to append to graph.php:
// * use 2-digit variable names to keep the query string short (well, sort of...)
// * &z marks the end of the query string (currently unused)
//
$query="a0=".$c."&a1=".$formula1."&a2=".$formula2."&a3=".$formula3."&a7=".$term1."&a8=".$term2."&a9=".$term3
     ."&b0=".$width."&b1=".$height."&b2=".$rulex1."&b3=".$rulex2."&b4=".$ruley1."&b5=".$ruley2."&b6=".$intervalsx."&b7=".$intervalsy."&b8=".$linex."&b9=".$liney
     ."&c0=".$deci."&c1=".$mid."&c2=".$lines."&c3=".$numbers."&c4=".$dashes."&c5=".$frame."&c6=".$errors."&c7=".$sint1."&c8=".$sint2."&c9=".$sint3
     ."&d0=".$grid."&d1=".$gridx."&d2=".$gridy."&d3=".$logsk."&d4=".$ta1."&d5=".$ta2."&d6=".$tb1."&d7=".$tb2."&d8=".$tc1."&d9=".$tc2
     ."&e0=".$cint1."&e1=".$cint2."&e2=".$cint3."&e3=".$qq."&e4=".$selfcol[3]."&e5=".$selfcol[6]."&e6=".$selfcol[4]."&e7=".$selfcol[5]."&e8=".$con0."&e9=".$con1
     ."&f0=".$con2."&f1=".$anti."&f2=".$gamma."&f3=".$bri."&f4=".$cont."&f5=".$emb."&f6=".$blur."&f7=".$neg."&f8=".$gray."&f9=".$mean
     ."&g0=".$edge."&g1=".$bf."&g2=".$pol."&g3=".$rotate."&g4=".$filetype."&g5=".$logskx."&g6=".$Y."&g7=".$selfcol[0]."&g8=".$selfcol[1]."&g9=".$selfcol[2]
     ."&h0=".$thick."&h1=".$varname."&h2=".$transp;
if (strpos($query, "&pc = ")  ===  FALSE) {
	if ($pcount > 0) {
		$query .=  "&p=".$selfcol[7];
		for ($i = 0;$i<$pcount;$i++) {
			$query .=  "&p".$i."=".rawurlencode($pn[$i])."&x".$i."=".$px[$i]."&y".$i."=".$py[$i];
		}
	}
}
// append &z to mark the end
$query .= "&z";

// Finally, encode everything in UTF8 to avoid Problems in the URL
// (e.g. if using # or ^ in functions):
$query = utf8_encode($query);
?>
