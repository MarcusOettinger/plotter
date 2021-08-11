<?php
/* 
 * graph.php - called by function.php or standalone -
 *     draws the function plot and displays it as png, gif or jpeg image.
 *
 * Modified by Marcus Oettinger for plot.oettinger-physics.de 
 * 08/2021:
 *  - minor cleanups
 *  - use TTF font set in config.inc for text output
 *  - added the ability to draw up to 10 additional points on the plot
 *  - reworked the code to allow for smoother color handling
 *  - added a workaround for gdlib packages w/o imageantialias (debian, ubuntu
 *    maybe others?)
 *
 * 12/2015 new option to set custom variable names 
 *
 * ---------------------------------------------------------------------------------
/* (openPlaG: Original source: http://rechneronline.de/function-graphs/
    Copyright (C) 2011 Juergen Kummer)
*/
/*
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
// deny access on the generated image from external urls
// you do not need to do that, but I recommend it
// please change the path name to the one desired
//if($HTTP_REFERER!="http://XXX" && $HTTP_REFERER!="XXX")
//	die();

include_once("config.inc");
include_once("modules/helpers.php");

/*
 * a0 signals a querystring (set to 2 in the form on the mainpage
 * - this switches the language of error messages to english)
 */
$c = $_GET['a0'];

if (!$c) {
	/* set reasonable default values */
	$func[0] = 'x^2'; // default value for formula 1 (f(x))
	for ($i=0; $i<3; $i++) {
		$term[$i] = 1;	// show term 1-3 in the legend
		$sint[$i] = 0;	// integral (2), derivative (1) or f(x) (0) 
				// of function 1-3
		$con[$i] = 0;	// dot, connect, fill in or out
	}
	$width = $height = 500;	// image width and height
	$xlimit1 = $ylimit1 = -5;	// x/y min
	$xlimit2 = $ylimit2 = 5;	// x/y max
	$intervalsx = $intervalsy = 10;	// number of x/y-intervals
	$gridx = $gridy = 20;	// number of grid lines
	$linex = $liney = 5;	// length of dashes
	$mid = 0;	// gap at origin
	$deci = 3;	// decimal places
	$lines = $grid = $numbers = $dashes = $errors = 1;	// axis lines, grid, captions, dashes and error display on
	$frame = $logsk = $logskx = 0;	// no rame around the plot, no logscale
	$colRGB[0] = "ff8000";	// colors for the curves
	$colRGB[1] = "a0b0c0";
	$colRGB[2] = "6080a0";
	$colRGB[3] = "ffffff";	// white background
	$colRGB[6] = "ffffff";	// white gap
	$colRGB[4] = "141414";	// dark gray captions
	$colRGB[5] = "f2f2f2";	// grey grid lines
	$anti = $gamma = 1;	// use antialiasing if available, no gamma correction
	$bri = 0;	// normal brightness
	$cont = 0;	// normal contrast
	$bf = 1;	// draw lines in the background
	$pol = 1;	// find poles
	$rotate = 0;	// do not rotate the plot
	$filetype=0;	// output as jpeg image
	$Y="Y";		// plot function values - no hull function
	$thick = 1;	// line thickness
	$varname = "x";	// variable name to display in legend
	$transp = 1;
	$prettyprint = 1;
} 
else	// Loading a graph: read values from querystring and
	// store them in variables
{ 
	for ($i=0; $i<3; $i++) {
		$n = $i+1;	// function term $i is stored in a($i+1)!
		$func[$i] = $_GET["a$n"];
		$cint[$i] = $_GET["e$i"];
		$n = $i+7;
		$term[$i] = $_GET["a$n"];	// legend display is stored in a7-a9
		$sint[$i] = $_GET["c$n"];	// diff/int/func is stored in c7-c9
	}
	$width = $_GET['b0'];
	$height = $_GET['b1'];
	// replace named constants in ranges
	$xlimit1 = isset($_GET['b2']) ? handleConstants($_GET['b2']) : -5;
	$xlimit2 = isset($_GET['b3']) ? handleConstants($_GET['b3']) : 5;	
	$ylimit1 = isset($_GET['b4']) ? handleConstants($_GET['b4']) : -5;
	$ylimit2 = isset($_GET['b5']) ? handleConstants($_GET['b5']) : 5;
	$intervalsx = $_GET['b6'];
	$intervalsy = $_GET['b7'];
	$linex = $_GET['b8'];
	$liney = $_GET['b9'];
	$deci = $_GET['c0'];
	$mid = $_GET['c1'];
	$lines = $_GET['c2'];
	$numbers = $_GET['c3'];
	$dashes = $_GET['c4'];
	$frame = $_GET['c5'];
	$errors = $_GET['c6'];
	$grid = $_GET['d0'];
	$gridx = $_GET['d1'];
	$gridy = $_GET['d2'];
	$logsk = $_GET['d3'];
	$ta1 = $_GET['d4'];
	$ta2 = $_GET['d5'];
	$tb1 = $_GET['d6'];
	$tb2 = $_GET['d7'];
	$tc1 = $_GET['d8'];
	$tc2 = $_GET['d9'];
	$qq = $_GET['e3'];
	$colRGB[3] = $_GET['e4'];
	$colRGB[6] = $_GET['e5'];
	$colRGB[4] = $_GET['e6'];
	$colRGB[5] = $_GET['e7'];
	$con[0] = $_GET['e8'];
	$con[1] = $_GET['e9'];
	$con[2] = $_GET['f0'];
	$anti = $_GET['f1'];
	$gamma = $_GET['f2'];
	$bri = $_GET['f3'];
	$cont = $_GET['f4'];
	$emb = $_GET['f5'];
	$blur = $_GET['f6'];
	$neg = $_GET['f7'];
	$gray = $_GET['f8'];
	$mean = $_GET['f9'];
	$edge = $_GET['g0'];
	$bf = $_GET['g1'];
	$pol = $_GET['g2'];
	$rotate = $_GET['g3'];
	$filetype = $_GET['g4'];
	$logskx = $_GET['g5'];
	$Y = $_GET['g6'];
	$colRGB[0] = $_GET['g7'];
	$colRGB[1] = $_GET['g8'];
	$colRGB[2] = $_GET['g9'];
	$thick = $_GET['h0'];
	$varname = isset($_GET['h1']) ? $_GET['h1'] : "x";
	$transp = isset($_GET['h2']) ? $_GET['h2'] : 1;
	$transp = isset($_GET['h2']) ? $_GET['h2'] : 1;
	$prettyprint = isset($_GET['h3']) ? $_GET['h3'] : 1;
	if (isset($_GET['p'])) $colRGB[7] = $_GET['p'];
	for ($i=0; $i<10; $i++) {
		if (isset($_GET["p$i"])) {
			$pcount = $i+1;
			$pn[$i] = urldecode($_GET["p$i"]);		// Name of the point (P,Q,...)
			$newval = handleConstants($_GET["x$i"]);
			if (is_numeric($newval)) $px[$i] = $newval;	// x
			$newval = handleConstants($_GET["y$i"]);
			if (is_numeric($newval)) $py[$i] = $newval;	// y
		}
	}
}

// http header: set file type
$fth = array ("image/gif", "image/png", "image/jpeg");
$filetype = ($filetype > 3 ? 3 : $filetype);
Header ("Content-type: " . $fth[$filetype-1]);

// global variables
$overflow = 0;	//counter for overflow problems
for ($i=0; $i<3; $i++) $asyval[$i] = '';	//asymptote values

// substitution: replace Q and Y in expressions:
for ($i=0; $i<3; $i++) {
	$form[$i]=$func[$i]; // terms to display in the legend
	$form[$i]=str_replace('Q','('.$qq.')',$form[$i]);
	if($Y && $Y!="Y" && $form[$i])
		$form[$i]=str_replace('Y','('.$form[$i].')',$Y);
}

// definition and result range
$tdef1[0] = strlen(strval($ta1)) == 0 ? -PHP_INT_MAX : $ta1;
$tdef1[1] = strlen(strval($tb1)) == 0 ? -PHP_INT_MAX : $tb1;
$tdef1[2] = strlen(strval($tc1)) == 0 ? -PHP_INT_MAX : $tc1;
$tdef2[0] = strlen(strval($ta2)) == 0 ? PHP_INT_MAX : $ta2;
$tdef2[1] = strlen(strval($tb2)) == 0 ? PHP_INT_MAX : $tb2;
$tdef2[2] = strlen(strval($tc2)) == 0 ? PHP_INT_MAX : $tc2;

for($i=0; $i<3; $i++) { // handle named constants in ranges and integration constants
	$tdef1[$i] = handleConstants($tdef1[$i]);
	$tdef2[$i] = handleConstants($tdef2[$i]);
	$cint[$i] = handleConstants($cint[$i]);
}

// create an empty image and define colors
//
$img = imagecreatetruecolor($width, $height);
// catch problems with missing antialias in PHP
if (function_exists('imageantialias')) imageantialias($img, $anti);
// color[0 - 2] - color for functions
// color[3-6] - background, axis/captions, grid lines and gap color
// color[7] - used for points
for ($i=0; $i<8; $i++)
	$color[$i] = imagecolorallocate($img,hexdec(substr($colRGB[$i],0,2)),
	                                     hexdec(substr($colRGB[$i],2,2)),
	                                     hexdec(substr($colRGB[$i],4,2)));


// fill Image with background color
imagefill($img, 0, 0, $color[3]);

$single = 0; // compute the whole graph
include 'modules/init.php';

// start values for lines and captions

$xrange = $xlimit2 - $xlimit1;
$yrange = $ylimit2 - $ylimit1;
$startx = round($height/$yrange*$ylimit2);
$starty = round($width/$xrange*(-$xlimit1));
if ($startx<0) $startx = 0;
if ($startx >= $height) $startx  =$height-1;
if ($starty < 0) $starty = 0;
if ($starty >= $width) $starty = $width-1;

//
// the Font to use in the plot - if the TTF set
// in config.inc is found, use it:
//
$Font = file_exists( $defaultTTFont );

// plot text: draw string $text at position $x/$y in a given color.
//            Boolean $Font: use ttf Font defined in config.inc
function plotText($img, $size, $x, $y, $text, $color, $Font){
	global $defaultTTFont;
	if ($Font) {
		$ttfb = imagettfbbox ( $size*2+2 , 0 , $defaultTTFont, $text );
		imagettftext ($img , $size*2+2 , 0, $x, $y + abs($ttfb[5] - $ttfb[1]), $color , $defaultTTFont, $text);
	} else {
		imagestring ($img, $size, $x, $y, $text, $color);
	}
}

// plotValue: plot a caption value at $x/$y.
// if $prettyprint == 1 try to automagically format numbers into
// exponential notation if they are getting too long in decimal
function plotValue($img, $size, $x, $y, $value, $color, $Font){
	global $defaultTTFont, $deci, $prettyprint;
	
	if ($Font) {
		if (1 == $prettyprint) {
			$prettyprint = 0;
			if (abs($value) < 1/1000) $prettyprint = 1;
			if (abs($value) >= 1E4) $prettyprint = 1;
		}
		if (1 == $prettyprint) {
			$exp = floor(log10(abs($value)));
			$mantissa = round($value/pow(10, $exp), $deci);
			$ttfb = imagettfbbox ( $size*2+2 , 0 , $defaultTTFont, $mantissa . chr(183) . "10" );
			plotText($img, $size, $x, $y, $mantissa . chr(183) . "10", $color, $defaultTTFont);
			plotText($img , $size, $x+$ttfb[4]+2, $y - $size -2, $exp, $color , $defaultTTFont);
		} else 
			plotText($img, $size, $x, $y, $value, $color, $defaultTTFont);
	} else {
		imagestring ($img, $size, $x, $y, $value, $color);
	}
}



// draw grid and axis lines, captions and dashes
function drawsystem() {
	// good god - that's ugly...
	global $width, $height, $gridx, $gridy,
	       $startx, $starty, $color, $intervalsx, $intervalsy,
	       $xrange, $xlimit1, $yrange, $ylimit2, $linex, $liney,
	       $grid, $dashes, $deci, $numbers, $logsk, $logskx,
	       $colRGB, $lines, $img, $Font, $varname;

	// draw grid lines
	if( $grid ) {
		$stepx = floor( $width/$gridx+.5 );
		for($i=0; $i<=$stepx*$gridx; $i+=$stepx) {
			if($i>0)  // grid lines x
				imageline ($img, $i, 0, $i, $height-1, $color[5]);
		}
		$stepy = floor($height/$gridy+.5);
		for($i=0; $i<=$stepy*$gridy; $i+=$stepy) {
			if ($i>0)  // grid lines y
				imageline ($img, 0, $i, $width-1, $i, $color[5]);
		}
	}
	
	// captions x-axis
	$count = -1;
	$stepx = round($width/$intervalsx);
	for($i=0; $i<=$stepx*$intervalsx; $i+=$stepx) {
		$gap = 0;
		++$count;
		if($dashes && $i>0) { // dashes x-axis
			imageline ($img, $i, min($startx,$height)+$linex+1, 
			           $i, min($startx,$height)-$linex, $color[4]);
		}
		$value = $xlimit1+$count*$xrange/$intervalsx;
		$gap += abs($value) > 0.01 ? strlen(round($value, $deci))*3 : strlen($value)*3;
		if ($value && $count && $i != $stepx*$intervalsx) { //labels x
			$xalign = $startx<$height/2 ? 15 : -15;
			if(strval($logskx) == "M_E") $logskx = 2.718281828459;	
			if($logskx) $value = pow($logskx, $value);
			if (abs($value) > 0.01) $value = round($value, $deci);
			if ($numbers)
				plotValue ($img, 3, $i-$gap, min($startx,$height)+$xalign-$linex,
					$value, $color[4], $Font);
		}
	}
	
	
	// captions y-axis
	$count = 0;
	$stepy = round($height/$intervalsy);
	for($i=$stepy; $i<=$stepy*$intervalsy; $i+=$stepy) {
		++$count;
		if ($dashes && $i>0) { //dashes y
			/* if ($i==$startx+1 || $i==$startx-1)
				$i=$startx; */
			imageline ($img, max($starty,0)+$liney+1, $i, max($starty,0)-$liney, $i, $color[4]);
		}
		$value = $ylimit2-$count*$yrange/$intervalsy;
		$gap += abs($value) > 0.01 ? strlen(round($value, $deci))*3 : strlen($value)*3;
		if ($value && $count && $i!=$stepy*$intervalsy) { //labels y
			$yalign = $starty<=$width/2 ? 4 : -30-strlen($value)*4;
			if(strval($logsk)=="M_E") $logsk=2.718281828459;
			if($logsk) $value=pow($logsk,$value);
			if (abs($value) > 0.01)$value = round($value, $deci);
			if ($numbers)
				plotValue($img, 3, max($starty,0)+$yalign+$liney, $i-6, $value, $color[4], $Font);

		}
	}
	if($numbers) {
		plotText($img, 4 ,max($starty,0)+$yalign+$liney,5, "y", $color[4], $Font);
		plotText($img, 4, $width-15 ,min($startx,$height)+$xalign-$linex, $varname, $color[4], $Font);	
	}
	
	// draw axis lines
	if ($lines) {
		imageline ($img,0,$startx,$width-1,$startx,$color[4]); //horizontally
		imageline ($img,$starty,0,$starty,$height-1,$color[4]); //vertically
	}
}


// draw a line with thickness $thick onto the plot
function plotline($img, $x0, $y0, $x1, $y1, $thickdiff, $color) {
	if ($y1 == $y0) 
		imagefilledrectangle($img, $x1, $y1-$thickdiff, $x1,
			$y1+$thickdiff+1, $color);
	else { 
		//all of these four are necessary to make a smooth line in every case
		imagefilledrectangle($img, $x0-$thickdiff, $y0+$thickdiff,
			$x1-$thickdiff, $y1+$thickdiff, $color);
		imagefilledrectangle($img, $x0+$thickdiff, $y0-$thickdiff,
			$x1+$thickdiff,$y1-$thickdiff, $color);
		imagefilledrectangle($img, $x0-$thickdiff, $y0-$thickdiff,
			$x1+$thickdiff, $y1+$thickdiff, $color);
		imagefilledrectangle($img, $x0+$thickdiff, $y0+$thickdiff,
			$x1-$thickdiff,$y1-$thickdiff, $color);
	}
}

// draw lines in background
if($bf == 1) drawsystem();

// draw a gap around the origin
if ($mid>0) {
	$centerx = -$xlimit1/$xrange*$width;
	$centery = $ylimit2/$yrange*$height;
	imagerectangle($img, $centerx-$mid, $centery-$mid, $centerx+$mid, $centery+$mid, $color[4]);
	if ($mid>1) {
		--$mid;
		imagefilledrectangle($img, $centerx-$mid, $centery-$mid, 
		                     $centerx+$mid, $centery+$mid, $color[6]);
	}
}

// draw curves
$derivative = $valyold = '';		//remember values for derivative
for ($j=0; $j<3; $j++) {
	$count = 0;			//count results
	$average = 0;			//reset value for arithmetic mean
	$isteps = 0;			//reset value for iteration steps
	$thickdiff = floor($thick/3);	//used for line thickness

	if($asyval[$j]) { 		//draw asymptotes
		if ($thick<2)
			imageline($img, $asyval[$j], 0, $asyval[$j], $height-1, $color[$j]);
		else
			imagefilledrectangle($img, $asyval[$j]-$thickdiff, 0,
			                     $asyval[$j]+$thickdiff+1, $height-1, $color[$j]);
	}
	else if ($formula[$j] != '') { // don't try to draw a curve if there's no function term
		$iter = '';
		$iter2 = '';
		$istep = 0;
		if(!$sint[$j] || !$logsk) { //no integral or derivative at a log scale
			$sum = $sums = $sums2 = $sums3 = 0;
			$integral = $intplus = $startcalc = 0;
			unset($xprev);
			unset($yprev);
//			if($formula[$j] != str_replace("D","",$formula[$j]))
			if (strpos($formula[$j], "D") !== false)
				$startcalc = -2; 	// start calculation outside the plot
							// to avoid artefacts at the left side 
							// of a graph with D, D2, D3, ...
			for ($i=$startcalc; $i<=$width; $i++) {
				$valx = $xlimit1 + $i*$xrange/$width;		// x value
				
				if($valx>=$tdef1[$j] && $valx<=$tdef2[$j]) { 	// don't draw outside definition range
					$valy = graph($valx, $formula[$j]); 	// calculate function value at $valx
					
					if($sint[$j]==2 && is_numeric($valy)) { // integrate
						$integral += $valy/$width*$xrange;
						$valy = $integral;
						if($cint[$j])
							$intplus = $cint[$j]*$width/$xrange;
					}
					else if($sint[$j]==1 && is_numeric($valy)) { // derivative
						$valyold = $valy;
						if(is_numeric($derivative)) {
							$valy -= $derivative;	// $valy now is Delta y
							$valy *= $width/$xrange; // $xrange/$width is Delta x
						}				// => $valy = Delta y/Delta x
						if(!is_numeric($derivative))
							$valy=NULL;
						$derivative=$valyold;
					}
					if ($valy == PHP_INT_MAX && $i>=0) {		// PHP_INT_MAX: overflow in graph()
						++$overflow;			// (see init.php)
						++$count;
						if( $overflow > 9) {
							if($errors) {
								imagefilledrectangle($img, $height/2-85, 80+$j*40,
								                    $height/2-85+9*strlen($text2)+15,
								                    95+$j*40,$color[3]);
								plotText($img, 5, $height/2-80, 80+$j*40, $text2.($j+1),
								                    $color[$j],$Font);
							}
//							next;
						}
					}
					
					$set1 = $height-1-floor(($valy-$ylimit1)/$yrange*$height-.5);
					$set2 = $height-1-floor(($valy-$ylimit1)/$yrange*$height+.5);
					$set = round(($set1+$set2)/2);
					
					if(strlen($valy)) {
						if (!isset($xprev)) $xprev = $i;
						if (!isset($yprev)) $yprev = $set;
						$from = $yprev - $intplus;	//start coordinate y-axis
						$to = $set - $intplus;	//target coordinate y-axis
						// do not try to draw too far outside the visible image
						// but allow for an approximately correct slope when connecting
						// points
						$from = $from < -200 ? -200 : $from;
						$from = $from > $height+200 ? $height+200 : $from;
						$to = $to < -200 ? -200 : $to;
						$to = $to > $height+200 ? $height+200 : $to;
						
						if (!(($yprev>$height && $set<0) || ($yprev<0 && $set>$height)) && $i>0 && ($from>-$thickdiff || $to>-$thickdiff)) {
							// connect dots, but do not try to connect poles (a pole is defined
							// as two dots with a vertical distance of more than 1/3 of the image
							// height.
							// Of course this is a bit simple, but works for my needs
							if($con[$j]==0 && (abs($yprev-$set)<$height/3 || !$pol)) { 
								if ($thick < 2)
									imageline($img, $xprev, $from, $i, $to,
									          $color[$j]);
								else  // lines with a real thickness
									plotline($img, $xprev, $from, $i, $to, $thickdiff, $color[$j]);
							}
							else if ($con[$j] == 1) { // draw dots, do not connect
								if($thick<2)
									imagesetpixel($img, $i, $to, $color[$j]);
								else 
									imagefilledellipse($img, $i, $to, $thick, $thick,
									                   $color[$j]);
							}
							else if($con[$j]==2) //fill graph inside
								imageline($img, $xprev, $startx, $xprev, $to, $color[$j]);
							else if($con[$j]==3) { //fill graph outside
								imageline($img, $xprev, 0, $xprev, min($startx,$to), $color[$j]);
								imageline($img, $xprev, $height, $xprev, max($startx,$to), $color[$j]);
							}
						}
						$xprev = $i;
						$yprev = $set;
						++$count;
					} else { // don't connect inside definition gaps
						unset($xprev);
						unset($yprev);
					}
				}
			}
		}
		unset($sumres);
	} else if ($i>0)
		++$count;
		
	// error message at result overflows or when no result were found
	if(!$count && $errors && !$asyval[$j] && !$bracketerror[$j]){
		imagefilledrectangle($img, $height/2-85, 80+$j*40, $height/2-85+9*max(strlen($text3),
		                     strlen($text4))+10,115+$j*40,$color[3]);
		plotText($img, 5, $height/2-80, 80+$j*40, $text3, $color[$j], $Font);
		plotText($img, 5, $height/2-80, 100+$j*40, $text4.($j+1), $color[$j], $Font);
	}
	// bracket error(s) in one of the functions
	if($bracketerror[$j] && $errors) {
		imagefilledrectangle($img, $height/2-85, 80+$j*40, $height/2-85+9*strlen($text5)+15,
		                     95+$j*40,$color[3]);
		plotText($img, 5, $height/2-80,80+$j*40, $text5.($j+1), $color[$j], $Font);
	}
}

// draw lines in foreground
if($bf == 2) drawsystem();

// If $varname ist set, replace all occurences of 'x' by $varname
// and draw terms into the image.
for($i=0;$i<3;$i++) {
	if ($form[$i]!='' && $term[$i]){
		/* do not replace the 'x' in exp() and max() */
		$form[$i] = str_replace('exp', 'eyp', $form[$i]);
		$form[$i] = str_replace('max', 'may', $form[$i]);
		$form[$i] = str_replace("x", $varname, $form[$i]);
		$form[$i] = str_replace('eyp', 'exp', $form[$i]);
		$form[$i] = str_replace('may', 'max', $form[$i]);
		
		$fname = chr(102+$i); // f, g or h
		if ($sint[$i] == 2) { // integral
			$form[$i] = strtoupper($fname).'('.$varname.')=S['.$form[$i].']';
			if ($cint[$i] > 0) $form[$i].='+';
			if ($cint[$i]) $form[$i].=$cint[$i];
		}
		else if($sint[$i] == 1) // derivative
			$form[$i] = $fname."'($varname)=[".$form[$i]."]'";
		else                    // function
			$form[$i]=$fname.'('.$varname.')='.$form[$i];
			
		if(strlen($form[$i]) < $width/9) {
			imagefilledrectangle($img, 0, 20*($i+1)-10, 9*strlen($form[$i])+5, 20*($i+1)+5, $color[3]);
			plotText($img, 5, 5, 20*($i+1)-10, $form[$i], $color[$i], $Font);
		} else {//reduce font size if term is too long
			imagefilledrectangle($img, 0, 20*($i+1)-10, 5*strlen($form[$i])+5, 20*($i+1)+5, $color[3]);
			plotText($img, 1, 5, 20*($i+1)-6, $form[$i], $color[$i], $Font);
		}
	}
}

// draw frame
if ($frame) imagerectangle( $img, 0, 0, $width-1, $height-1, $color[4]);
	
// finally, draw additional named points
for ($i=0; $i<$pcount; $i++) {
	// calculate x/y:
	$valx = ($px[$i] - $xlimit1)/$xrange*$width;
	$valy = ($ylimit2 - $py[$i])/$yrange*$height;
	imagefilledellipse($img , $valx, $valy , 6 , 6 , $color[7]);
	plotText($img, 5, $valx-15, $valy-18, $pn[$i], $color[7], $Font);
}

// apply GD filters and/or rotate image if selected
//
if($gamma != 1) imagegammacorrect ($img, 1, $gamma);
if($bri) imagefilter($img, IMG_FILTER_BRIGHTNESS, $bri);
if($cont) imagefilter($img, IMG_FILTER_CONTRAST, $cont);
if($emb) imagefilter($img, IMG_FILTER_EMBOSS);
if($blur) imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
if($neg) imagefilter($img, IMG_FILTER_NEGATE);
if($gray) imagefilter($img, IMG_FILTER_GRAYSCALE);
if($mean) imagefilter($img, IMG_FILTER_MEAN_REMOVAL);
if($edge) imagefilter($img, IMG_FILTER_EDGEDETECT);
if($rotate) $img=imagerotate($img, -$rotate, $color[3]);

if (1 == $transp) imagecolortransparent( $img, $color[3] );

// That's it => stream the result in the desired format
streamImage($img, $filetype);
/*
if( 1 == $filetype ) {
	header('Content-type: image/gif');
	imagegif($img);
}
else if( 2 == $filetype ) {
	header('Content-type: image/png');
	imagepng($img, NULL, 1);
	}
else {
	header('Content-type: image/jpeg');
	imagejpeg( $img, NULL, 90 );
}
imagedestroy($img);
*/
?>
