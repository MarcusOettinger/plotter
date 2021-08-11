<?php
/*
modules/helpers.php: 
Required by: graph.php, single.php

Copyright (C) 2015-2021 Marcus Oettinger,
Original source: http://rechneronline.de/function-graphs/
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

// Paranoia mode: if not defined, guess at least 32 bit integers.
// This should be defined from PHP 4.4 on (?)
// 
if (!defined('PHP_INT_MAX'))
	define('PHP_INT_MAX', 2147483647);


// emulate PHP_VERSION_ID for older interpreters
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

// check if the GD extension is installed and if
// its a GD2+
function chkgd2(){
  $testGD = get_extension_funcs("gd"); // Grab function list
  if (!$testGD){ echo "GD not even installed."; exit; }
  if (!in_array ("imagegd2",$testGD)) $gd_version = "<2"; // Check
  if ($gd_version == "<2") return false; else return true;
}

// debug_writeline: 
// Set $debug to true in config.inc to write a line into debug.txt
function debug_writeline($instr){
	global $debug;
	if ($debug) {
		$file = 'debug.txt';
		$current = file_get_contents($file);
		$current .= "$instr\n"; 
		file_put_contents($file, $current);
	}
}

function replaceByConst($cst, $rpl, $instr) {
	$instr = preg_replace('/' . $cst. '(?=[x0-9*+-\/\)\(])/', $rpl, $instr);
	return $instr;
}


// 
// replaceConstants($instr):
// replace constant names by numbers
// $instr: a string that may contain constant names like 'pi','e', ...
//
function replaceConstants($instr) {
	$instr=str_replace('pi2',M_PI_2,$instr);
	$instr=str_replace('PI2',M_PI_2,$instr);
	$instr=str_replace('pi',M_PI,$instr);
	$instr=str_replace('PI',M_PI,$instr);
	$instr=str_replace('g_acc','9.81',$instr);	
	// use a negative look-ahead to avoid replacing 'e' in 'ceil' and similar
	$instr = preg_replace('/e(?!\(|i)/', M_E, $instr);
	$instr=str_replace('sq2',M_SQRT2,$instr);
	$instr=str_replace('go','1.6180339887499',$instr);
	// use a negative look-ahead to avoid replacing 'd' in 'round' and similar
	$instr = preg_replace('/d(?!\(|e)/', '4.669201609103', $instr);
	// Planck constant (https://www.bipm.org/utils/common/pdf/CGPM-2018/26th-CGPM-Resolutions.pdf)
	$instr = preg_replace('/h(?!\(|)/', '6.62607015E-34', $instr);
	$instr=str_replace('hbar', '1.054571817E-34', $instr);
	// speed of light (https://physics.nist.gov/cgi-bin/cuu/Value?c) 
	$instr = preg_replace('/c(?!\(|e|i)/', '299792458', $instr);
	// Avogadro number: 6.02214076E23
	$instr=str_replace('Na', '6.02214076E23', $instr);
	
	$instr=str_replace('--', '-', $instr);
	$instr=str_replace('++', '+', $instr);
	
	return $instr;
}


// 
// handleConstants($instr):
// replace constant names by numbers and check if $instr is a constant
// value.
// $instr: a string that may contain constant names like 'pi','e', ...
//
function handleConstants($instr) {
	$result = 0;
	$instr=str_replace(",",".",$instr);
	
	$instr = replaceConstants($instr);
	
	// sanitize input
	$instr= preg_replace("/[^0-9+\-.*\/()E%]/","",$instr);
	if ( $instr != "" ){
		$result = @eval("return " . $instr. ";" );
	}
	return $result;
}


function linetype($id){
	echo '<select id="' . $id .'" name="' . $id . '">
	<option value="0" selected="selected">Connect</option>
	<option value="1">Dots</option>
	<option value="2">Fill in</option>
	<option value="3">Fill out</option>
	</select>&nbsp;&nbsp;&nbsp;';
}


/**
* Evaluates a math equation and returns the result, sanitizing
* input for security purposes.  Note, this function does not
* support math functions (sin, cos, etc)
*
* @param string the math equation to evaluate (ex:  100 * 24)
* @return a number
*/
function evalmath($equation){
	$result = 0;
 
	// sanitize imput
	$equation = preg_replace("/[^0-9+\-.*\/()E%]/","",$equation);
 
	// convert percentages to decimal
	$equation = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$equation);
	$equation = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$equation);
	$equation = preg_replace("/([0-9]+)(%)/",".\$1",$equation);
 
	if ( $equation != "" )
		$result = @eval("return " . $equation . ";" );
	if ($result == null) 
		throw new Exception("Unable to calculate equation");
 
	return $result;
}

/*
 * streamImage: create an image in teh selected format.
 * @param $img:
 * @param $filetype int (0: jpeg, 1: gif, 2: png)
 */
function streamImage($img, $filetype) {
	if( 1 == $filetype )
		imagegif($img);
	else if( 2 == $filetype )
		imagepng($img, NULL, 1);
	else
		imagejpeg( $img, NULL, 90 );
}
