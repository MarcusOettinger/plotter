<?php
/*
modules/init.php: sanity check function terms, transform
  expressions into an evaluable form and calculate function
  values.
Required by: graph.php, calc_results.php

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

include_once("modules/defs.php");

$Y = preg_replace('/\s/', '', $Y);
$qq = preg_replace('/\s/', '', $qq);

// add multiplication signs where omitted
$Y = multiop($Y);
$qq = multiop($qq);

for($i=0;$i<3;$i++) {
	// remove all whitespace in math terms
	$formula[$i] = rawurldecode(preg_replace('/\s/', '', $func[$i]));
	$fn = $formula[$i];
	$fnum = $i;
	include("modules/parse.php");
	$formula[$i] = $fn;

debug_writeline($formula[$i]);
}

// include non-PHP function definitions
include "modules/functions_extra.inc";
// include differential and integral functions
include "modules/diffint.inc";

