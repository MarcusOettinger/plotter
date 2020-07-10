<?php
/*
modules/helpers.php: 
Required by: graph.php, single.php

Copyright (C) 2015-2020 Marcus Oettinger,
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

// debug_writeline: write a line into debug.txt
function debug_writeline($instr){
	$file = 'debug.txt';
	$current = file_get_contents($file);
	$current .= "$instr\n"; 
	file_put_contents($file, $current);
}



// replace constant names by numbers
// $instr: a string that may contain constant names like 'pi','e', ...
//
function handleConstants($instr) {
	$result = 0;
	$instr=str_replace(",",".",$instr);
	
	$instr=str_replace('pi2',M_PI_2,$instr);
	$instr=str_replace('PI2',M_PI_2,$instr);
	$instr=str_replace('pi',M_PI,$instr);
	$instr=str_replace('PI',M_PI,$instr);
	$instr=str_replace('e',M_E,$instr);
	$instr=str_replace('sq2',M_SQRT2,$instr);
	$instr=str_replace('go','1.6180339887499',$instr);
	$instr=str_replace('d','4.669201609103',$instr);
		
	// sanitize input
	$instr= preg_replace("/[^0-9+\-.*\/()%]/","",$instr);
	
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
* @return number
*/
function evalmath($equation)
{
$result = 0;
 
// sanitize imput
$equation = preg_replace("/[^0-9+\-.*\/()%]/","",$equation);
 
// convert percentages to decimal
$equation = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$equation);
$equation = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$equation);
$equation = preg_replace("/([0-9]+)(%)/",".\$1",$equation);
 
if ( $equation != "" )
{
$result = @eval("return " . $equation . ";" );
}
 
if ($result == null)
{
throw new Exception("Unable to calculate equation");
}
 
return $result;
}
