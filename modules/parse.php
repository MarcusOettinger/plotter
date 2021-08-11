<?php
/*
modules/parse.php: sanity check function term, transform
  expressions into an evaluable form and calculate function
  values.
Required by: init.php, calc_results.php

Copyright (C) 2015-2021 Marcus Oettinger,
based on openPlaG (http://rechneronline.de/function-graphs/)
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
	$fn= multiop($fn);

	// insert substitution terms for Y and Q,
	if($Y && $fn)
		$fn= str_replace('Y','('.$fn.')',$Y);
	$fn= str_replace('Q','('.$qq.')' ,$fn);

	/*
	 * transform some redundant symbols and replace standard 
	 * trigonometric and hyperbolic functions by those used 
	 * internally.
	 * See modules/defs.php for $trfrom, $trto.
	 */
	$fn= str_replace($trfrom, $trto, $fn);

	// catch 0 as a function
	if($fn=='0') $fn='0*1';

	// The ** operator was added in PHP 5.6
	// - try to be backward compatible!
	if (PHP_VERSION_ID < 50600) {
		include("modules/caretPow.php");
		if($fn!= str_replace("^", "", $fn)) {
		$t = new term($fn);
		$fn= $t->ToString();
	}
	} else {
		// convert ^ to **
		$fn= str_replace("^","**" ,$fn);
	}

	// scan for bracket errors
	$bracketerror[$fnum] = 0;
	if (substr_count($fn, "(") != substr_count($fn, ")")) {
		if( !$single) {
			imagefill ($img,0,0,$color[3]);
			imagestring ($img, 5, $height/2-80, 100, $text5.($fnum+1), $color[$col[$fnum]]);
			streamImage($img, $filetype);
		} else if( $single) {
			echo $text5;
			die();
		}
	}

	// stop if more than one D, D2, D3, D0, D02, D03 or more than any one S in formula
	if((strlen($fn)-strlen(str_replace('D(','' ,$fn))>2 || strlen($fn)-strlen(str_replace('D2(','' ,$fn))>3 || strlen($fn)-strlen(str_replace('D3(','' ,$fn))>3 || strlen($fn)-strlen(str_replace('D0(','' ,$fn))>3 || strlen($fn)-strlen(str_replace('D02(','' ,$fn))>4 || strlen($fn)-strlen(str_replace('D03(','' ,$fn))>4 || strlen($fn)-strlen(str_replace('S','' ,$fn))>1) && !$single) {
		if($errors) {
			imagefill ($img,0,0,$color[3]);
			imagestring ($img, 5, $height/2-80, 100, $text6.($fnum+1), $color[$col[$fnum]]);
		}
		streamImage($img, $filetype);
		die();
	}

	/* 
	 * Sanity check of function terms:
	 * calculations are performed by eval(), so possible malicious code has to 
	 * be stripped. To sanitize the expressions, all allowed substrings will be
	 * removed from a test string $test and if anything remains in the test
	 * string, calculation is cancelled.
	 * $tok (see modules/defs.php) contains known tokens to remove from test
	 * string (digits are to be removed by preg_replace later on)
	 */
	$test = $fn;
	for ($t=0; $t<count($tok); $t++)
		$test = str_replace($tok[$t],'',$test);
	/* remove digits from test string */
	$test=preg_replace('/[0-9]/','',$test);

	/* stop if anything remains in the test string and write an error message
	 * to the image
	 */
	if(strlen($test) && !$single) {
		if($errors) {
			imagefill ($img, 0, 0, $color[3]);
			imagestring ($img, 5, $height/2-80, 100, $text1.($fnum+1), $color[$col[$fnum]]);
			imagestring ($img, 5, $height/2-80, 130, "(unable to parse near'".$test."')", $color[$col[$fnum]]);
		}
		streamImage($img, $filetype);
		die();
	} else if(strlen($test) && $single) {
		echo $text1;
		die();
	}

	

	/* avoid replacing constant value h in function names and hbar */
	$fn= str_replace($cfrom, $cto ,$fn);
debug_writeline("Hin: " . $fn);
	$fn= replaceConstants($fn);
	/* '$a' will be used as variable in eval() */
	$fn= str_replace('x', '$a' ,$fn);
debug_writeline("const: " . $fn);
	/* and go vice-versa where needed */
	$fn= str_replace($cto, $cfrom ,$fn);
debug_writeline("back: " . $fn);

	
	/* backwards compatibility: allow for R() instead of round() and
	 * Improve PHP functions: use some better definitions instead
	 * (see modules/defs.php).
	 */
	$fn= str_replace($coldfunc, $cnfunc ,$fn);
debug_writeline("fn: " . $fn);

	/* 
	 * extract the value for a perpendicular asymptote and set
	 * the x-coordinate as 'asyval'.
	 * As formula simply 'asy' is preserved
	 */
	if(!$single && $fn!= str_replace('asy(','' ,$fn)) {
		$fn= substr($fn, 0, strpos($fn,')'));
		$fn= substr($fn, strpos($fn,'asy'), strlen($fn));
		$fn= str_replace('asy', '', $fn);
		$fn= str_replace('(', '', $fn);
		$fn= str_replace(')', '', $fn);
		@eval('$fn='.$fn.';');
		if(is_numeric($fn)) {
			if($logskx)
				$fn= nlogn($logskx ,$fn);
			$asyval[$fnum] = round($width/($xlimit2-$xlimit1)*(abs($xlimit1-doubleval($fn))), 0);
		}
		else {
			if($errors)
				imagestring ($img,5,$height/2-80,100,$text1.($fnum+1),$color[$col[$fnum]]);
		}
		$fn= 'asy';
	}
