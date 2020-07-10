<?php
/* 
 * graph.php - called by function.php or standalone -
 *     draws the function plot and displays it as png, gif or jpeg image.
 *
 * Modified by Marcus Oettinger for plot.oettinger-physics.de 
 * 07/2020:
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

// a0 signals a querystring (set to 2 in the form on the mainpage
// - this switches the language of error messages to english)
$c = $_GET['a0'];

if (!$c) {
	// set reasonable default values
	$func[0] = 'x^2'; // default value for formula 1 (f(x))
	for ($i=0; $i<3; $i++) {
		$term[$i] = 1;	// show term 1-3 in the legend
		$sint[$i] = 0;	// integral (2), derivative (1) or f(x) (0) 
				// of function 1-3
		$con[$i] = 0;	// dot, connect, fill in or out
	}
	$width = 500;	// image width and height
	$height = 500;
	$rulex1 = -5;	// x min
	$ruley1 = -5;	// y min
	$rulex2 = 5;	// x max
	$ruley2 = 5;	// y max
	$intervalsx=10;	// number of x-intervals
	$intervalsy=10;	// number of y-intervals
	$gridx = 20;	// number of grid lines
	$gridy = 20;	
	$linex = 5;	// length x dashes
	$liney = 5;	// length y dashes
	$mid = 0;	// gap at origin
	$deci = 3;	// decimal places
	$lines = 1;	// axis lines on
	$grid = 1;	// grid lines on
	$numbers = 1;	// captions on
	$dashes = 1;	// dashes on
	$frame = 0;	// no Frame around the plot
	$errors = 1;	// show errors in the 
	$logsk = 0;	// no logarithmic scale in y
	$logskx = 0;	// no logarithmic scale in x
	$bg = "ffffff";		// white background
	$gapc = "ffffff";	// white gap
	$capt = "141414";	// dark gray captions
	$linec = "f2f2f2";	// grey grid lines
	$anti = 1;	// use antialiasing if available
	$gamma = 1;	// no gamma correction
	$bri = 0;	// normal brightness
	$cont = 0;	// normal contrast
	$bf = 1;	// draw lines in the background
	$pol = 1;	// find poles
	$rotate = 0;	// do not rotate the plot
	$filetype=0;	// output as jpeg image
	$Y="Y";		// plot function values - no hull function
	$selfcol0 = "ff8000";	// colors for the curves
	$selfcol1 = "a0b0c0";
	$selfcol2 = "6080a0";
	$thick = 1;	// line thickness
	$varname = "x";	// variable name to display in legend
	$transp = 1;
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
	$rulex1 = $_GET['b2'];
	$rulex2 = $_GET['b3'];	
	$ruley1 = $_GET['b4'];
	$ruley2 = $_GET['b5'];
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
	$bg = $_GET['e4'];
	$gapc = $_GET['e5'];
	$capt = $_GET['e6'];
	$linec = $_GET['e7'];
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
	$selfcol0 = $_GET['g7'];
	$selfcol1 = $_GET['g8'];
	$selfcol2 = $_GET['g9'];
	$thick = $_GET['h0'];
	$varname = isset($_GET['h1']) ? $_GET['h1'] : "x";
	$transp = isset($_GET['h2']) ? $_GET['h2'] : 1;
	/*
	if (isset($_GET['h1'])) {
		$varname = $_GET['h1']; 
	} else {
		$varname = "x";
	}
	if (isset($_GET['h2'])) {
		$transp = $_GET['h2']; 
	} else {
		$transp = 1;
	}
	*/
	if (isset($_GET['p'])) $pointc = $_GET['p'];
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

//echo urldecode($_SERVER['QUERY_STRING']);


// html header: set file type
$fth = array ("image/gif", "image/png", "image/jpeg");
$filetype = ($filetype > 3 ? 3 : $filetype);
Header ("Content-type: " . $fth[$filetype-1]);

// replace named constants in ranges
$rulex1 = handleConstants($rulex1);
$ruley1 = handleConstants($ruley1);
$rulex2 = handleConstants($rulex2);
$ruley2 = handleConstants($ruley2);

// global variables initiation
$sum = 0;	//addition variable for phi
$sums = 0;	//addition variable for integral in function
$sums2 = 0;	//addition variable for second integral in function
$sums3 = 0;	//addition variable for third integral in function
$iter = '';	//current value for iteration
$iter2 = '';	//previous value for iteration
$istep = 0;	//counter for iteration steps
$average = 0;	//memory for arithmetic mean
$overflow = 0;	//counter for overflows
$asyval[0] = '';	//values for asymptotes
$asyval[1] = '';
$asyval[2] = '';
$derval=NULL;		//start value for the derivative within a function, e.g. D(x)
$derval2=NULL;		//start value for the second derivative within a function, e.g. D2(x)
$derval21=NULL;		//start value for the first derivative within the second derivative within a function
$derval3=NULL;		//start value for the third derivative within a function, e.g. D3(x)
$derval31=NULL;		//start value for the first derivative within the third derivative within a function
$derval32=NULL;		//start value for the second derivative within the third derivative within a function
$derval0=NULL;		//start value for the derivative within a function, alternative form, e.g. D0(x)
$derval02=NULL;		//start value for the second derivative within a function, alternative form, e.g. D02(x)
$derval021=NULL;	//start value for the first derivative within the second derivative within a function, alternative form
$derval03=NULL;		//start value for the third derivative within a function, alternative form, e.g. D03(x)
$derval031=NULL;	//start value for the first derivative within the third derivative within a function, alternative form
$derval032=NULL;	//start value for the second derivative within the third derivative within a function, alternative form

// read values into arrays and handle substitution of
// Q and Y in expressions:
for ($i=0; $i<3; $i++) {
	$form[$i]=$func[$i]; // terms to display in the legend
	$form[$i]=str_replace('Q','('.$qq.')',$form[$i]);
	if($Y && $Y!="Y" && $form[$i])
		$form[$i]=str_replace('Y','('.$form[$i].')',$Y);
}

// definition and result range
$tdef1[0] = strlen(strval($ta1)) == 0 ? -999999 : $ta1;
$tdef1[1] = strlen(strval($tb1)) == 0 ? -999999 : $tb1;
$tdef1[2] = strlen(strval($tc1)) == 0 ? -999999 : $tc1;
$tdef2[0] = strlen(strval($ta2)) == 0 ? 999999 : $ta2;
$tdef2[1] = strlen(strval($tb2)) == 0 ? 999999 : $tb2;
$tdef2[2] = strlen(strval($tc2)) == 0 ? 999999 : $tc2;

for($i=0; $i<3; $i++) { // handle named constants in ranges and integration constant
	$tdef1[$i] = handleConstants($tdef1[$i]);
	$tdef2[$i] = handleConstants($tdef2[$i]);
	$cint[$i] = handleConstants($cint[$i]);
}

// Everything should be set up by now -  
// create an empty image and define colors
//
$img = imagecreatetruecolor($width, $height);
// catch problems with missing antialias in PHP
if (function_exists('imageantialias')) imageantialias($img, $anti);

// color[0 - 2] - color for functions
$color[0] = imagecolorallocate($img,hexdec(substr($selfcol0,0,2)),hexdec(substr($selfcol0,2,2)),hexdec(substr($selfcol0,4,2)));//self-defined color 1
$color[1] = imagecolorallocate($img,hexdec(substr($selfcol1,0,2)),hexdec(substr($selfcol1,2,2)),hexdec(substr($selfcol1,4,2)));//self-defined color 2
$color[2] = imagecolorallocate($img,hexdec(substr($selfcol2,0,2)),hexdec(substr($selfcol2,2,2)),hexdec(substr($selfcol2,4,2)));//self-defined color 3
// color[3-6] - background, axis/captions, grid lines and gap color
$color[3] = imagecolorallocate($img,hexdec(substr($bg,0,2)),hexdec(substr($bg,2,2)),hexdec(substr($bg,4,2)));//self-defined color 4
$color[4] = imagecolorallocate($img,hexdec(substr($capt,0,2)),hexdec(substr($capt,2,2)),hexdec(substr($capt,4,2)));//self-defined color 5
$color[5] = imagecolorallocate($img,hexdec(substr($linec,0,2)),hexdec(substr($linec,2,2)),hexdec(substr($linec,4,2)));//self-defined color 6
$color[6] = imagecolorallocate($img,hexdec(substr($gapc,0,2)),hexdec(substr($gapc,2,2)),hexdec(substr($gapc,4,2)));//self-defined color 7
// color[7] - additional points
$color[7] = imagecolorallocate($img,hexdec(substr($pointc,0,2)),hexdec(substr($pointc,2,2)),hexdec(substr($pointc,4,2)));//Point color

// fill Image with background color
imagefill($img, 0, 0, $color[3]);

$single = 0; // we don't want to compute a single value, but the whole graph
include 'modules/init.php';

// start values for lines and captions
$rulex = $rulex2-$rulex1;
$ruley = $ruley2-$ruley1;
$startx = round($height/$ruley*$ruley2);
$starty = round($width/$rulex*(-$rulex1));
if ($startx<0) $startx = 0;
if ($startx >= $height) $startx  =$height-1;
if ($starty < 0) $starty = 0;
if ($starty >= $width) $starty = $width-1;

//

//
// the Font to use in the plot - if the TTF set in config.inc is found, use it:
$nice = file_exists( $defaultTTFont );

// plot text: draw string $text at position $x/$y in a given color.
//            Boolean $nice: use ttf Font defined in config.inc
function plotText($img, $size, $x, $y, $text, $color, $nice){
	global $defaultTTFont;
	if ($nice) {
		$ttfb = imagettfbbox ( $size*2+2 , 0 , $defaultTTFont, $text );
		imagettftext ($img , $size*2+2 , 0, $x, $y + abs($ttfb[5] - $ttfb[1]), $color , $defaultTTFont, $text);
	} else {
		imagestring ($img, $size, $x, $y, $text, $color);
	}
}



// draw grid and axis lines, captions and dashes
function drawlines() {
	// good god - that's ugly...
	global $width, $height, $gridx, $gridy,
	       $startx, $starty, $color, $intervalsx, $intervalsy,
	       $rulex, $rulex1, $ruley, $ruley2, $linex, $liney,
	       $grid, $dashes, $deci, $numbers, $logsk, $logskx,
	       $linec, $capt, $gapc, $lines, $img, $nice, $varname;

	// draw grid lines
	if($grid) {
		$stepx = floor($width/$gridx+.5);
		for($i=0; $i<=$stepx*$gridx; $i+=$stepx) {
			if($i>0) { // grid lines x
				if ($i == $starty+1 || $i == $starty-1)
					$i = $starty;
				imageline ($img, $i, 0, $i, $height-1, $color[5]);
			}
		}
		$stepy = floor($height/$gridy+.5);
		for($i=0; $i<=$stepy*$gridy; $i+=$stepy) {
			if ($i>0) { // grid lines y
				if ($i == $startx+1 || $i == $startx-1)
					$i = $startx;
				imageline ($img, 0, $i, $width-1, $i, $color[5]);
			}
		}
	}
	
	// captions x-axis
	$count = -1;
	$stepx = round($width/$intervalsx);
	for($i=0; $i<=$stepx*$intervalsx; $i+=$stepx) {
		$gap = 0;
		++$count;
		if($dashes && $i>0) { // dashes x
			if ($i == $starty+1 || $i == $starty-1)
				$i = $starty;
			imageline ($img, $i, min($startx,$height)+$linex+1, 
			           $i, min($startx,$height)-$linex, $color[4]);
		}
		$value=$rulex1+$count*$rulex/$intervalsx;
		$value=round($value,$deci);
		$gap+=strlen($value)*3;
		if ($value && $count && $i!=$stepx*$intervalsx) { //labels x
			if ($startx<$height/2)
				$align=15;
			else
				$align=-15;

			if(strval($logskx)=="M_E")
				$logskx=2.718281828459;
			if($logskx)
				$value=pow($logskx,$value);
			$value=round($value,$deci);
			$value=str_replace('E+','*10^',$value);
			if(substr($value,0,2)=="1*")
				$value=str_replace('1*','',$value);

			if ($numbers)
				plotText ($img,3,$i-$gap,min($startx,$height)+$align-$linex,$value,$color[4],$nice);
		}
	}
	if($numbers)
		plotText($img,4,$width-15,min($startx,$height)+$align-$linex,$varname,$color[4],$nice);
	
	// caption y-axis
	$count=0;
	$stepy=round($height/$intervalsy);
	for($i=$stepy;$i<=$stepy*$intervalsy ;$i+=$stepy) {
		++$count;
		if ($dashes && $i>0) { //dashes y
			if ($i==$startx+1 || $i==$startx-1)
				$i=$startx;
			imageline ($img,max($starty,0)+$liney+1,$i,max($starty,0)-$liney,$i,$color[4]);
		}
		$value=$ruley2-$count*$ruley/$intervalsy;
		$gap+=strlen($value)*3;
		if ($value && $count && $i!=$stepy*$intervalsy) { //labels y
			if ($starty<=$width/2)
				$align=4;
			else
				$align=-30-strlen($value)*4;
			if(strval($logsk)=="M_E")
				$logsk=2.718281828459;
			if($logsk)
				$value=pow($logsk,$value);
			$value=round($value,$deci);
			$value=str_replace('E+','*10^',$value);
			if(substr($value,0,2)=="1*")
				$value=str_replace('1*','',$value);
			if ($numbers)
				plotText($img,3,max($starty,0)+$align+$liney,$i-6,$value,$color[4],$nice);

		}
	}
	if($numbers)
		plotText($img,4,max($starty,0)+$align+$liney,5,"y",$color[4],$nice);
	
	// draw axis lines
	if ($lines) {
		imageline ($img,0,$startx,$width-1,$startx,$color[4]); //horizontally
		imageline ($img,$starty,0,$starty,$height-1,$color[4]); //vertically
	}
}

// draw lines in background
if($bf == 1) drawlines();

// draw a gap at origin
if ($mid>0) {
	$middlex = -$rulex1/$rulex*$width;
	$middley = $ruley2/$ruley*$height;
	imagerectangle($img, $middlex-$mid, $middley-$mid, $middlex+$mid, $middley+$mid, $color[4]);
	--$mid;
	if ($mid>0)
		imagefilledrectangle($img, $middlex-$mid, $middley-$mid, 
		                     $middlex+$mid, $middley+$mid, $color[6]);
}

// draw the graph
$derivative = '';			//remember value for derivative
$valyold = '';				//remember value for derivative
for ($j=0; $j<3; $j++) {
	$count = 0;			//count results
	$average = 0;			//reset value for mean
	$isteps = 0;			//reset value for iteration steps
	$thick1 = floor($thick/3);	//line thickness left
	$thick2 = ceil($thick/3)-1;	//line thickness right
	if($asyval[$j]) { 		//draw asymptotes
		if ($thick<2)
			imageline($img, $asyval[$j], 0, $asyval[$j], $height-1, $color[$j]);
		else if ($con[$j] == 1) //make asymptote thicker if dots are chosen
			imagefilledrectangle($img, $asyval[$j]-floor($thick/2), 0, 
			                     $asyval[$j]+ceil($thick/2), $height-1,$color[$j]);
		else
			imagefilledrectangle($img, $asyval[$j]-$thick1, 0,
			                     $asyval[$j]+$thick2+1, $height-1, $color[$j]);
	}
	else if ($formula[$j] != '') { // don't try to draw a curve if there's no function term
		$iter = '';
		$iter2 = '';
		$istep = 0;
		if(!$sint[$j] || !$logsk) { //no integral or derivative at a log scale
			$sum = 0;
			$sums = 0;
			$sums2 = 0;
			$sums3 = 0;
			$integral = 0;
			$intplus = 0;	//+C integration constant
			unset($x1);
			unset($y1);
			$startcalc = 0;
			if($formula[$j] != str_replace("D","",$formula[$j]))
				$startcalc = -2; 	// start calculation outside the plot
							// to avoid artefacts at the left side 
							// of a graph with D, D2, D3, ...
			for ($i=$startcalc; $i<=$width; $i++) {
				$valx = $rulex1 + $i*$rulex/$width;		// x value
				
				if($valx>=$tdef1[$j] && $valx<=$tdef2[$j]) { 	// don't draw outside definition range
					$valy = graph($valx, $formula[$j]); 	// calculate function value at $valx
					if($sint[$j]==2 && is_numeric($valy)) { // integrate
						$integral += $valy/$width*$rulex;
						$valy = $integral;
						if($cint[$j])
							$intplus = $cint[$j]*$width/$rulex;
					}
					else if($sint[$j]==1 && is_numeric($valy)) { // derivative
						$valyold = $valy;
						if(is_numeric($derivative)) {
							$valy -= $derivative;	// $valy now is Delta y
							$valy *= $width/$rulex; // $rulex/$width is Delta x
						}				// => $valy = Delta y/Delta x
						if(!is_numeric($derivative))
							$valy=NULL;
						$derivative=$valyold;
					}
					if ($valy==999999 && $i>=0) {		// 999999: overflow in graph()
						++$overflow;			// (see init.php)
						++$count;
						if($overflow>9) {
							if($errors) {
								imagefilledrectangle($img, $height/2-85, 80+$j*40,
								                    $height/2-85+9*strlen($text2)+15,
								                    95+$j*40,$color[3]);
								plotText($img, 5, $height/2-80, 80+$j*40, $text2.($j+1),
								                    $color[$j],$nice);

							}
							next;
						}
					}
					
					$set1 = $height-1-floor(($valy-$ruley1)/$ruley*$height-.5);
					$set2 = $height-1-floor(($valy-$ruley1)/$ruley*$height+.5);
					$set = round(($set1+$set2)/2);
					if(strlen($valy)) {
						if (!isset($x1)) $x1 = $i;
						if (!isset($y1)) $y1 = $set;
						$from = $y1 - $intplus;	//start coordinate y-axis
						$to = $set - $intplus;	//target coordinate y-axis
						if($from<-200) //don't draw too far outside the image
							$from = -200;
						else if($from > $height+200)
							$from = $height+200;
						if($to < -200)
							$to = -200;
						else if($to > $height+200)
							$to = $height+200;
						if (!(($y1>$height && $set<0) || ($y1<0 && $set>$height)) && $i>0 && ($from>-$thick1 || $to>-$thick2)) {
							// connect dots, but do not try to connect poles (a pole is defined
							// here as two dots with a vertical distance of more than 1/3 of the
							// image height.
							// Of course this is a bit problematic, but I didn't find a better way
							// so far.)
							if($con[$j]==0 && (abs($y1-$set)<$height/3 || !$pol)) { 
								if ($thick < 2)
									imageline($img, $x1, $from, $i, $to,
									          $color[$j]);
								else { //lines with thickness
									if ($from == $to)
										imagefilledrectangle($img, $x1, $to-$thick1, $i,
										                     $to+$thick2+1, $color[$j]);
									else { //all of these four are necessary to make a smooth line in every case
										imagefilledrectangle($img, $x1-$thick1, $from+$thick1,
										                     $i-$thick2, $to+$thick2, $color[$j]);
										imagefilledrectangle($img, $x1+$thick1, $from-$thick1,
										                     $i+$thick2,$to-$thick2,$color[$j]);
										imagefilledrectangle($img, $x1-$thick1, $from-$thick1,
										                     $i+$thick2, $to+$thick2, $color[$j]);
										imagefilledrectangle($img, $x1+$thick1, $from+$thick1,
										                     $i-$thick2,$to-$thick2,$color[$j]);
									}
								}
							}
							else if ($con[$j] == 1) { // draw dots, do not connect
								if($thick<2)
									imagesetpixel($img, $x1, $from, $color[$j]);
								else 
									imagefilledellipse($img, $x1, $from, $thick, $thick,
									                   $color[$j]);
							}
							else if($con[$j]==2) //fill graph inside
								imageline($img,$x1,$startx,$x1,$to,$color[$j]);
							else if($con[$j]==3) { //fill graph outside
								imageline($img,$x1,0,$x1,min($startx,$to),$color[$j]);
								imageline($img,$x1,$height,$x1,max($startx,$to),$color[$j]);
							}
						}
						$x1 = $i;
						$y1 = $set;
						++$count;
					} else { // don't connect definition gaps
						unset($x1);
						unset($y1);
					}
				}
			}
		}
		unset($sumres);
	} else if ($i>0)
		++$count;
		
	// error message at result overflows or when no result found
	if(!$count && $errors && !$asyval[$j] && !$bracketerror[$j]){
		imagefilledrectangle($img, $height/2-85, 80+$j*40, $height/2-85+9*max(strlen($text3),
		                     strlen($text4))+10,115+$j*40,$color[3]);
		plotText($img, 5, $height/2-80, 80+$j*40, $text3, $color[$j], $nice);
		plotText($img, 5, $height/2-80, 100+$j*40, $text4.($j+1), $color[$j], $nice);
	}
	// bracket errors
	if($bracketerror[$j] && $errors) {
		imagefilledrectangle($img, $height/2-85, 80+$j*40, $height/2-85+9*strlen($text5)+15,
		                     95+$j*40,$color[3]);
		plotText($img, 5, $height/2-80,80+$j*40, $text5.($j+1), $color[$j], $nice);
	}
}

// draw lines in foreground
if($bf == 2) drawlines();

// If $varname ist set, replace all occurences of 'x' by $varname
// and draw function terms into the image.
for($i=0;$i<3;$i++) {
	if ($form[$i]!='' && $term[$i]){
		$form[$i] = str_replace("x", $varname, $form[$i]);
		$fname = chr(102+$i); // f, g or h
		if ($sint[$i] == 2) { // integral
			$form[$i] = strtoupper($fname).'('.$varname.')=S['.$form[$i].']';
			if ($cint[$i] > 0)
				$form[$i].='+';
			if ($cint[$i])
				$form[$i].=$cint[$i];
		}
		else if($sint[$i] == 1) // derivative
			$form[$i] = $fname."'($varname)=[".$form[$i]."]'";
		else                    // function
			$form[$i]=$fname.'('.$varname.')='.$form[$i];
			
		if(strlen($form[$i])<$width/9) {
			imagefilledrectangle($img, 0, 20*($i+1)-10, 9*strlen($form[$i])+5, 20*($i+1)+5, $color[3]);
			plotText($img, 5, 5, 20*($i+1)-10, $form[$i], $color[$i], $nice);
		} else {//reduce font size if term is too long
			imagefilledrectangle($img, 0, 20*($i+1)-10, 5*strlen($form[$i])+5, 20*($i+1)+5, $color[3]);
			plotText($img, 1, 5, 20*($i+1)-6, $form[$i], $color[$i], $nice);

		}
	}
}

// draw a frame if desired
if ($frame)
	imagerectangle( $img, 0, 0, $width-1, $height-1, $color[4]);
	
// finally, draw additional named points
for ($i=0; $i<$pcount; $i++) {
	// calculate x/y:
	$valx = ($px[$i] - $rulex1)/$rulex*$width;
	$valy = ($ruley2 - $py[$i])/$ruley*$height;
	imagefilledellipse($img , $valx, $valy , 6 , 6 , $color[7]);
	plotText($img, 5, $valx-15, $valy-18, $pn[$i], $color[7], $nice);
}

// apply GD filters and/or rotate image if selected
//
if($gamma!=1) imagegammacorrect ($img,1,$gamma);
if($bri) imagefilter($img,IMG_FILTER_BRIGHTNESS,$bri);
if($cont) imagefilter($img,IMG_FILTER_CONTRAST,$cont);
if($emb) imagefilter($img,IMG_FILTER_EMBOSS);
if($blur) imagefilter($img,IMG_FILTER_GAUSSIAN_BLUR);
if($neg) imagefilter($img,IMG_FILTER_NEGATE);
if($gray) imagefilter($img,IMG_FILTER_GRAYSCALE);
if($mean) imagefilter($img,IMG_FILTER_MEAN_REMOVAL);
if($edge) imagefilter($img,IMG_FILTER_EDGEDETECT);
if($rotate) $img=imagerotate($img,-$rotate,$color[3]);

if ($transp == 1) imagecolortransparent( $img, $color[3] );

// That's it => stream the result in the desired format
if( $filetype == 1 )
	imagegif($img);
else if( $filetype == 2 )
	imagepng($img,NULL,1);
else
	imagejpeg( $img, NULL, 90 );
?>
