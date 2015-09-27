<?php
/* 
 * graph.php - called by function.php -
 *     draws the function plot and displays it as png, gif or jpeg image.
 *
 * Modified by Marcus Oettinger for plot.oettinger-physics.de 
 * 06/2015:
 *  - use TTF font set in config.inc for text output
 *  - added the ability to draw up to 10 additional points on the plot
 *  - reworked the code to allow for smoother color handling
 *  - added a workaround for gdlib packages w/o imageantialias (debian, ubuntu
 *    maybe others?)
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
//if($HTTP_REFERER!="http://rechneronline.de/openPlaG/v030/function.php" && $HTTP_REFERER!="http://www.rechneronline.de/openPlaG/v030/function.php")
//	die();

include_once("config.inc");
include_once("helpers.php");

$c=$_GET['a0'];
// set initial values
if (!$c) {
	$formula1='x^2'; // default value for formula 1 (f(x))
	$col1=1;		//color 1
	$term1=1;	//show term 1
	$term2="";	//don't show term 2
	$term3="";	//don't show term 3
	$sint1=0;	//integral, derivative or f(x) of function 1
	$width=500;	// image width and height
	$height=500;
	$rulex1=-5;	//x left
	$ruley1=-5;	//y bottom
	$rulex2=5;	//x right
	$ruley2=5;	//y top
	$intervalsx=10;	//number of x-intervals
	$intervalsy=10;	//number of y-intervals
	$gridx=20;	//number of grid lines
	$gridy=20;	
	$linex=5;	// length x dashes
	$liney=5;	// length y dashes
	$mid=0;		// gap at origin
	$deci=3;		// decimal places
	$lines=1;	// axis lines yes
	$grid=1;		// grid lines on
	$numbers=1;	// caption yes
	$dashes=1;	// dashes yes
	$frame=0;	// no Frame around the plot
	$errors=1;//show errors yes
	$logsk=0;//logarithmic scale y no
	$logskx=0;//logarithmic scale x no
	$bg=14;//white background
	$gapc=14;//white gap
	$capt=13;//black caption
	$linec=12;//grey reticule lines
	$con0=0;//connect dots
	$con1=0;//connect dots
	$con2=0;//connect dots
	$anti=1;//antialiasing yes
	$gamma=1;//no gamma correction
	$bri=0;//normal brightness
	$cont=0;//normal contrast
	$bf=1;//lines in background
	$pol=1;//find poles
	$rotate=0;//no rotation
	$filetype=0;	//output as png image
	$Y="Y";		// plot function values - no hull function
	$selfcol0="ffffff";//self-defined colors
	$selfcol1="a0b0c0";
	$selfcol2="6080a0";
	$thick=1;	//line thickness
} else { // read the query string and give the variables their old name 
	$formula1=$_GET['a1'];
	$formula2=$_GET['a2'];
	$formula3=$_GET['a3'];
	$term1=$_GET['a7'];
	$term2=$_GET['a8'];
	$term3=$_GET['a9'];
	$width=$_GET['b0'];
	$height=$_GET['b1'];
	$rulex1=$_GET['b2'];
	$rulex2=$_GET['b3'];	
	$ruley1=$_GET['b4'];
	$ruley2=$_GET['b5'];
	$intervalsx=$_GET['b6'];
	$intervalsy=$_GET['b7'];
	$linex=$_GET['b8'];
	$liney=$_GET['b9'];
	$deci=$_GET['c0'];
	$mid=$_GET['c1'];
	$lines=$_GET['c2'];
	$numbers=$_GET['c3'];
	$dashes=$_GET['c4'];
	$frame=$_GET['c5'];
	$errors=$_GET['c6'];
	$sint1=$_GET['c7'];
	$sint2=$_GET['c8'];
	$sint3=$_GET['c9'];
	$grid=$_GET['d0'];
	$gridx=$_GET['d1'];
	$gridy=$_GET['d2'];
	$logsk=$_GET['d3'];
	$ta1=$_GET['d4'];
	$ta2=$_GET['d5'];
	$tb1=$_GET['d6'];
	$tb2=$_GET['d7'];
	$tc1=$_GET['d8'];
	$tc2=$_GET['d9'];
	$cint1=$_GET['e0'];
	$cint2=$_GET['e1'];
	$cint3=$_GET['e2'];
	$qq=$_GET['e3'];
	$bg=$_GET['e4'];
	$gapc=$_GET['e5'];
	$capt=$_GET['e6'];
	$linec=$_GET['e7'];
	$con0=$_GET['e8'];
	$con1=$_GET['e9'];
	$con2=$_GET['f0'];
	$anti=$_GET['f1'];
	$gamma=$_GET['f2'];
	$bri=$_GET['f3'];
	$cont=$_GET['f4'];
	$emb=$_GET['f5'];
	$blur=$_GET['f6'];
	$neg=$_GET['f7'];
	$gray=$_GET['f8'];
	$mean=$_GET['f9'];
	$edge=$_GET['g0'];
	$bf=$_GET['g1'];
	$pol=$_GET['g2'];
	$rotate=$_GET['g3'];
	$filetype=$_GET['g4'];
	$logskx=$_GET['g5'];
	$Y=$_GET['g6'];
	$selfcol0=$_GET['g7'];
	$selfcol1=$_GET['g8'];
	$selfcol2=$_GET['g9'];
	$thick=$_GET['h0'];
	if (isset($_GET["pc"])) $pointc = $_GET["pc"];
	for ($i=0;$i<10;$i++) {
		if (isset($_GET["p$i"])) {
			$pcount = $i+1;
			$pn[$i] = rawurldecode($_GET["p$i"]);	// Name (P,Q,...)
			$newval = handleConstants($_GET["x$i"]);
			if (is_numeric($newval)) $px[$i] = $newval;	// x
			$newval = handleConstants($_GET["y$i"]);
			if (is_numeric($newval)) $py[$i] = $newval;	// y
		}
	}
}

//echo urldecode($_SERVER['QUERY_STRING']);


// html header: set file type
if($filetype==1) 	Header ("Content-type: image/gif");
else if($filetype==2) 	Header ("Content-type: image/jpeg");
else 			Header ("Content-type: image/png");

// use constants as overall range
$rulex1=handleConstants($rulex1);
$ruley1=handleConstants($ruley1);
$rulex2=handleConstants($rulex2);
$ruley2=handleConstants($ruley2);

// global variables initiation
$sum=0; //addition variable for phi
$sums=0; //addition variable for integral in function
$sums2=0; //addition variable for second integral in function
$sums3=0; //addition variable for third integral in function
$iter=''; //last value for iteration
$iter2=''; //one before last value for iteration
$istep=0; //counter for iteration steps
$average=0; //memory for arithmetic mean
$overflow=0; //counter for overflows
$asyval[0]='';//values for asymptotes
$asyval[1]='';
$asyval[2]='';
$derval=NULL;//start value for the derivative within a function, e.g. D(x)
$derval2=NULL;//start value for the second derivative within a function, e.g. D2(x)
$derval21=NULL;//start value for the first derivative within the second derivative within a function
$derval3=NULL;//start value for the third derivative within a function, e.g. D3(x)
$derval31=NULL;//start value for the first derivative within the third derivative within a function
$derval32=NULL;//start value for the second derivative within the third derivative within a function
$derval0=NULL;//start value for the derivative within a function, alternative form, e.g. D0(x)
$derval02=NULL;//start value for the second derivative within a function, alternative form, e.g. D02(x)
$derval021=NULL;//start value for the first derivative within the second derivative within a function, alternative form
$derval03=NULL;//start value for the third derivative within a function, alternative form, e.g. D03(x)
$derval031=NULL;//start value for the first derivative within the third derivative within a function, alternative form
$derval032=NULL;//start value for the second derivative within the third derivative within a function, alternative form

// read values into arrays
$form[0]=$formula1;//formula term 1 for display
$form[1]=$formula2;//formula term 2 for display
$form[2]=$formula3;//formula term 2 for display
$sint[0]=$sint1;//integral, derivative or f(x) of func 1
$sint[1]=$sint2;//integral, derivative or g(x) of func 2
$sint[2]=$sint3;//integral, derivative or h(x) of func 3
 $col[0]=0; // $col1;//color 1
 $col[1]=1; //$col2;//color 2
 $col[2]=2; //$col3;//color 3
$term[0]=$term1;//show term 1
$term[1]=$term2;//show term 2
$term[2]=$term3;//show term 3
$con[0]=$con0;//connection term 1
$con[1]=$con1;//connection term 1
$con[2]=$con2;//connection term 1

// handle Substitution of Q and Y in expressions:
for($i=0;$i<3;$i++) {
	$form[$i]=str_replace('Q','('.$qq.')',$form[$i]);
	if($Y && $Y!="Y" && $form[$i])
		$form[$i]=str_replace('Y','('.$form[$i].')',$Y);
}

// definition and result range
// and +C for integrals
if(strlen(strval($ta1))==0) $ta1=-999999;
if(strlen(strval($ta2))==0) $ta2=999999;
if(strlen(strval($tb1))==0) $tb1=-999999;
if(strlen(strval($tb2))==0) $tb2=999999;
if(strlen(strval($tc1))==0) $tc1=-999999;
if(strlen(strval($tc2))==0) $tc2=999999;
$tdef1[0]=$ta1;
$tdef1[1]=$tb1;
$tdef1[2]=$tc1;
$tdef2[0]=$ta2;
$tdef2[1]=$tb2;
$tdef2[2]=$tc2;
$cint[0]=$cint1;
$cint[1]=$cint2;
$cint[2]=$cint3;
for($i=0;$i<3;$i++) { //constants as from-to range and +C
	$tdef1[$i]=handleConstants($tdef1[$i]);
	$tdef2[$i]=handleConstants($tdef2[$i]);
	$cint[$i]=handleConstants($cint[$i]);
}

// Everything initialized -  
// create an empty image and define colors
//
$img=imagecreatetruecolor($width,$height);
if (function_exists(imageantialias)) imageantialias($img,$anti);

// color[0 - 3] - function 1 - 3
$color[0]=imagecolorallocate($img,hexdec(substr($selfcol0,0,2)),hexdec(substr($selfcol0,2,2)),hexdec(substr($selfcol0,4,2)));//self-defined color 1
$color[1]=imagecolorallocate($img,hexdec(substr($selfcol1,0,2)),hexdec(substr($selfcol1,2,2)),hexdec(substr($selfcol1,4,2)));//self-defined color 2
$color[2]=imagecolorallocate($img,hexdec(substr($selfcol2,0,2)),hexdec(substr($selfcol2,2,2)),hexdec(substr($selfcol2,4,2)));//self-defined color 3
// color[4-6] - background, axis/captions, grid lines, gap color
$color[3]=imagecolorallocate($img,hexdec(substr($bg,0,2)),hexdec(substr($bg,2,2)),hexdec(substr($bg,4,2)));//self-defined color 4
$color[4]=imagecolorallocate($img,hexdec(substr($capt,0,2)),hexdec(substr($capt,2,2)),hexdec(substr($capt,4,2)));//self-defined color 5
$color[5]=imagecolorallocate($img,hexdec(substr($linec,0,2)),hexdec(substr($linec,2,2)),hexdec(substr($linec,4,2)));//self-defined color 6
$color[6]=imagecolorallocate($img,hexdec(substr($gapc,0,2)),hexdec(substr($gapc,2,2)),hexdec(substr($gapc,4,2)));//self-defined color 7
// color[7] - additional points
$color[7]=imagecolorallocate($img,hexdec(substr($pointcc,0,2)),hexdec(substr($pointc,2,2)),hexdec(substr($pointc,4,2)));//Point color

// fill Image with bg color
imagefill ($img,0,0,$color[3]);

$single=0; //we don't want to compute a single value, but the whole graph
include 'init.php';

// start values for lines and caption
$rulex=$rulex2-$rulex1;
$ruley=$ruley2-$ruley1;
$startx=round($height/$ruley*$ruley2);
$starty=round($width/$rulex*(-$rulex1));
if ($startx<0) $startx=0;
if ($startx>=$height) $startx=$height-1;
if ($starty<0) $starty=0;
if ($starty>=$width) $starty=$width-1;

//

//
// the Font to use in the plot - if the TTF set in config.inc is found, use it:
$nice = file_exists( $defaultTTFont);

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



// grid and axis lines, caption and dashes
function drawlines() {
	global $width, $height, $gridx, $gridy, $startx, $starty, $color, $intervalsx, $intervalsy, $rulex, $rulex1, $ruley, $ruley2, $linex, $liney, $grid, $dashes, $deci, $numbers, $logsk, $logskx, $linec, $capt, $gapc, $lines, $img, $nice;

	// draw reticule lines
	if($grid) {
		$stepx=floor($width/$gridx+.5);
		for($i=0;$i<=$stepx*$gridx ;$i+=$stepx) {
			if($i>0) { //reticule lines x
				if ($i==$starty+1 || $i==$starty-1)
					$i=$starty;
				imageline ($img,$i,0,$i,$height-1,$color[5]);
			}
		}
		$stepy=floor($height/$gridy+.5);
		for($i=0;$i<=$stepy*$gridy ;$i+=$stepy) {
			if ($i>0) { //reticule lines y
				if ($i==$startx+1 || $i==$startx-1)
					$i=$startx;
				imageline ($img,0,$i,$width-1,$i,$color[5]);
			}
		}
	}
	
	// caption x-axis
	$count=-1;
	$stepx=round($width/$intervalsx);
	for($i=0;$i<=$stepx*$intervalsx ;$i+=$stepx) {
		$gap=0;
		++$count;
		if($dashes && $i>0) { //dashes x
			if ($i==$starty+1 || $i==$starty-1)
				$i=$starty;
			imageline ($img,$i,min($startx,$height)+$linex+1,$i,min($startx,$height)-$linex,$color[4]);
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
		plotText($img,4,$width-15,min($startx,$height)+$align-$linex,"x",$color[4],$nice);
	
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
if($bf==1)
	drawlines();

// draw gap at origin
if ($mid>0) {
	$middlex=-$rulex1/$rulex*$width;
	$middley=$ruley2/$ruley*$height;
	imagerectangle ($img,$middlex-$mid,$middley-$mid,$middlex+$mid,$middley+$mid,$color[4]);
	--$mid;
	if ($mid>0)
		imagefilledrectangle ($img,$middlex-$mid,$middley-$mid,$middlex+$mid,$middley+$mid,$color[6]);
}

// draw the graph
$derivative=''; //remember old value for derivative
$valyold=''; //remember old value for derivative
for ($j=0;$j<3;$j++) {
	$count=0; //count results
	$average=0; //reset value for mean
	$isteps=0; //reset value for iteration steps
	$thick1=floor($thick/3); //line thickness left
	$thick2=ceil($thick/3)-1; //line thickness right
	if($asyval[$j]) { //draw asymptotes
		if($thick<2)
			imageline ($img,$asyval[$j],0,$asyval[$j],$height-1,$color[$col[$j]]);
		else if($con[$j]==1) //make asymptote thicker if dots are chosen
			imagefilledrectangle ($img,$asyval[$j]-floor($thick/2),0,$asyval[$j]+ceil($thick/2),$height-1,$color[$col[$j]]);
		else
			imagefilledrectangle ($img,$asyval[$j]-$thick1,0,$asyval[$j]+$thick2+1,$height-1,$color[$col[$j]]);
	}
	else if ($formula[$j]!='') { //don't draw graph when function is empty
		$iter='';
		$iter2='';
		$istep=0;
		if(!$sint[$j] || !$logsk) { //no integral or derivative at a log scale
			$sum=0;
			$sums=0;
			$sums2=0;
			$sums3=0;
			$integral=0;
			$intplus=0; //+C at integral
			unset($x1);
			unset($y1);
			$startcalc=0;
			if($formula[$j]!=str_replace("D","",$formula[$j]))
				$startcalc=-10; //start at -10 to avoid artefacts at the left side of a graph with D, D2, D3, ...
			for ($i=$startcalc;$i<=$width;$i++) {
				$valx=$rulex1+$i*$rulex/$width;
				if($valx>=$tdef1[$j] && $valx<=$tdef2[$j]) { //draw over definition range
					$valy=graph($valx,$formula[$j]); //calculate value at reached position
					if($sint[$j]==2 && is_numeric($valy)) { //integrate
						$integral+=$valy/$width*$rulex;
						$valy=$integral;
						if($cint[$j])
							$intplus=$cint[$j]*$width/$rulex;
					}
					else if($sint[$j]==1 && is_numeric($valy)) { //derivate
						$valyold=$valy;
						if(is_numeric($derivative)) {
							$valy-=$derivative;
							$valy*=$width/$rulex;
						}
						if(!is_numeric($derivative))
							$valy=NULL;
						$derivative=$valyold;
					}
					if ($valy==999999 && $i>=0) {
						++$overflow;
						++$count;
						if($overflow>9) {
							if($errors) {
								imagefilledrectangle ($img,$height/2-85,80+$j*40,$height/2-85+9*strlen($text2)+15,95+$j*40,$color[3]);
								plotText($img,5,$height/2-80,80+$j*40,$text2.($j+1),$color[$col[$j]],$nice);

							}
							next;
						}
					}
					$set1=$height-1-floor(($valy-$ruley1)/$ruley*$height-.5);
					$set2=$height-1-floor(($valy-$ruley1)/$ruley*$height+.5);
					$set=round(($set1+$set2)/2);
					if (strlen($valy)) {
						if (!isset($x1)) $x1=$i;
						if (!isset($y1)) $y1=$set;
						$from=$y1-$intplus; //start coordinate y-axis
						$to=$set-$intplus; //target coordinate y-axis
						if($from<-200) //don't draw too far outside the image
							$from=-200;
						else if($from>$height+200)
							$from=$height+200;
						if($to<-200)
							$to=-200;
						else if($to>$height+200)
							$to=$height+200;
						if(!(($y1>$height && $set<0) || ($y1<0 && $set>$height)) && $i>0 && ($from>-$thick1 || $to>-$thick2)) {
							if($con[$j]==0 && (abs($y1-$set)<$height/3 || !$pol)) { //connect two dots, but don't connect poles (a pole is defined here as when two dots have a further vertical distance than 1/3 of the image height. Of course this is a bit problematic, but I didn't find a better way so far.)
								if($thick<2)
									imageline($img,$x1,$from,$i,$to,$color[$col[$j]]);
								else { //lines with thickness
									if($from==$to)
										imagefilledrectangle($img,$x1,$to-$thick1,$i,$to+$thick2+1,$color[$col[$j]]);
									else { //all of these four are necessary to make a smooth line in every case
										imagefilledrectangle($img,$x1-$thick1,$from+$thick1,$i-$thick2,$to+$thick2,$color[$col[$j]]);
										imagefilledrectangle($img,$x1+$thick1,$from-$thick1,$i+$thick2,$to-$thick2,$color[$col[$j]]);
										imagefilledrectangle($img,$x1-$thick1,$from-$thick1,$i+$thick2,$to+$thick2,$color[$col[$j]]);
										imagefilledrectangle($img,$x1+$thick1,$from+$thick1,$i-$thick2,$to-$thick2,$color[$col[$j]]);
									}
								}
							}
							else if($con[$j]==1) { //only draw dots
								if($thick<2)
									imagesetpixel($img,$x1,$from,$color[$col[$j]]);
								else 
									imagefilledellipse($img,$x1,$from,$thick,$thick,$color[$col[$j]]);
							}
							else if($con[$j]==2) //fill graph inside
								imageline($img,$x1,$startx,$x1,$to,$color[$col[$j]]);
							else if($con[$j]==3) { //fill graph outside
								imageline($img,$x1,0,$x1,min($startx,$to),$color[$col[$j]]);
								imageline($img,$x1,$height,$x1,max($startx,$to),$color[$col[$j]]);
							}
						}
						$x1=$i;
						$y1=$set;
						++$count;
					} else { //don't connect definition gaps
						unset($x1);
						unset($y1);
					}
				}
			}
		}
		unset($sumres);
	} else if($i>0)
		++$count;
	// error message at result overflows or when no result found
	if(!$count && $errors && !$asyval[$j] && !$bracketerror[$j]){
		imagefilledrectangle ($img,$height/2-85,80+$j*40,$height/2-85+9*max(strlen($text3),strlen($text4))+10,115+$j*40,$color[3]);
		plotText($img,5,$height/2-80,80+$j*40,$text3,$color[$col[$j]],$nice);
		plotText($img,5,$height/2-80,100+$j*40,$text4.($j+1),$color[$col[$j]],$nice);
	}
	// bracket errors
	if($bracketerror[$j] && $errors) {
		imagefilledrectangle ($img,$height/2-85,80+$j*40,$height/2-85+9*strlen($text5)+15,95+$j*40,$color[3]);
		plotText($img,5,$height/2-80,80+$j*40,$text5.($j+1),$color[$col[$j]],$nice);
	}
}

// draw lines in foreground
if($bf==2)
	drawlines();

// draw function terms
for($i=0;$i<3;$i++) {
	if($form[$i]!='' && $term[$i]){
		if($i==0)
			$term1="f";		
		else if($i==1)
			$term1="g";
		else
			$term1="h";
		if ($sint[$i]==2) {
			$form[$i]=strtoupper($term1).'(x)=S['.$form[$i].']';
			if($cint[$i]>0)
				$form[$i].='+';
			if ($cint[$i])
				$form[$i].=$cint[$i];
		}
		else if ($sint[$i]==1)
			$form[$i]=$term1."'(x)=[".$form[$i]."]'";
		else
			$form[$i]=$term1.'(x)='.$form[$i];
		if(strlen($form[$i])<$width/9) {
			imagefilledrectangle ($img,0,20*($i+1)-10,9*strlen($form[$i])+5,20*($i+1)+5,$color[3]);
			plotText($img,5,5,20*($i+1)-10,$form[$i],$color[$col[$i]],$nice);
		} else {//reduce font size if term is too long
			imagefilledrectangle ($img,0,20*($i+1)-10,5*strlen($form[$i])+5,20*($i+1)+5,$color[3]);
			plotText($img,1,5,20*($i+1)-6,$form[$i],$color[$col[$i]],$nice);

		}
	}
}

// draw frame
if ($frame)
	imagerectangle ($img,0,0,$width-1,$height-1,$color[4]);
	
// finally, draw additional points
for ($i=0; $i<$pcount; $i++) {
	// calculate x/y:
	$valx=($px[$i] - $rulex1)/$rulex*$width;
	$valy=($ruley2 - $py[$i])/$ruley*$height;
	// color??
	imagefilledellipse ($img , $valx, $valy , 6 , 6 , $color[7] );
	plotText($img, 5, $valx-15, $valy-18, $pn[$i], $color[7], $nice);
}

// apply filters and/or rotate image if selected
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

imagecolortransparent($img,$color[38]);

// finally, we're done, stream the result in selected Format
if($filetype==1)
	imagegif($img);
else if($filetype==2)
	imagejpeg($img,NULL,90);
else
	imagepng($img,NULL,1);
?>
