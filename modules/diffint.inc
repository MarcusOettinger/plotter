<?php
/*
diffint.inc: calculus for any of Plotters functions.
M. Oettinger 06/2020
--------------------------------------------------------------------------------

OpenPLAG: Copyright (C) 2011 Juergen Kummer

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

/*
Differential and integral functions have been swapped from init.php in
this extra file from version 3.0 on, to make the init-file easier to read.
Also the way this is programmed isn't very satisfactory. Any approvement
would be appreciated.
*/


// simple numerical Integration (using the fundamental theorem
// of calculus - approximation by rectangles)
//
function S($a) {
	if (!is_numeric($a)) return NULL;
	
	global $sums, $width, $rulex;
	$sums += ($a/$width*$rulex);
	return $sums;
}

// second integral
//
function S2($a) {
	if (!is_numeric($a)) return NULL;
	
	$a=S($a);
	global $sums2, $width, $rulex;
	$sums2+=($a/$width*$rulex);
	return $sums2;
}

// third integral
//
function S3($a) {
	if (!is_numeric($a)) return NULL;
	
	$a=S2($a);
	global $sums3, $width, $rulex;
	$sums3 += ($a/$width*$rulex);
	return $sums3;
}

// derivative in function
function D($a) {
	if (!is_numeric($a)) return NULL;
	
	global $derval;
	if (!is_numeric($derval)){
		$derval=$a;
		return NULL;
	}

	global $width;
	global $rulex;
	$y=$a-$derval;
	$derval=$a;
	return $y*$width/$rulex;
}

// second derivative in function
function D2($a) {
	if (!is_numeric($a)) return NULL;
	
	//first d
	global $derval21;
	if (!is_numeric($derval21)){
		$derval21=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval21;
	$derval21=$a;
	$a=$y*$width/$rulex;
	//second d
	global $derval2;
	if (!is_numeric($derval2)){
		$derval2=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval2;
	$derval2=$a;
	return $y*$width/$rulex;
}

// third derivative in function
function D3($a) {
	if (!is_numeric($a))
		return NULL;
	//first d
	global $derval31;
	if (!is_numeric($derval31)){
		$derval31=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval31;
	$derval31=$a;
	$a=$y*$width/$rulex;
	//second d
	global $derval32;
	if (!is_numeric($derval32)){
		$derval32=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval32;
	$derval32=$a;
	$a=$y*$width/$rulex;
	//third d
	global $derval3;
	if (!is_numeric($derval3)){
		$derval3=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval3;
	$derval3=$a;
	return $y*$width/$rulex;
}

// Same again for the double use of D
// derivative in function II
function D0($a) {
	if (!is_numeric($a))
		return NULL;
	global $derval0;
	if (!is_numeric($derval0)){
		$derval0=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval0;
	$derval0=$a;
	return $y*$width/$rulex;
}

// second derivative in function II
function D02($a) {
	if (!is_numeric($a))
		return NULL;
	//first d
	global $derval021;
	if (!is_numeric($derval021)){
		$derval021=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval021;
	$derval021=$a;
	$a=$y*$width/$rulex;
	//second d
	global $derval02;
	if (!is_numeric($derval02)){
		$derval02=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval02;
	$derval02=$a;
	return $y*$width/$rulex;
}

// third derivative in function II
function D03($a) {
	if (!is_numeric($a))
		return NULL;
	//first d
	global $derval031;
	if (!is_numeric($derval031)){
		$derval031=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval031;
	$derval031=$a;
	$a=$y*$width/$rulex;
	//second d
	global $derval032;
	if (!is_numeric($derval032)){
		$derval032=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval032;
	$derval032=$a;
	$a=$y*$width/$rulex;
	//third d
	global $derval03;
	if (!is_numeric($derval03)){
		$derval03=$a;
		return NULL;
	}
	global $width;
	global $rulex;
	$y=$a-$derval03;
	$derval03=$a;
	return $y*$width/$rulex;
}
?>
