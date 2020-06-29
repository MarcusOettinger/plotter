<!DOCTYPE html><html>
<?php
/*
calc_results.php - calculate distinct function values.
part of plotter, M. Oettinger 06/2020.
Changes:
 * eliminated inline styles (CSP capable)
 * reworked the code to get it more readable (maybe shorter)
 * added the possibility to write a a simple latex-table
-----------------------------------------------------------

original code:
openPlaG: Copyright (C) 2011 Juergen Kummer

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
 * This file calculates distinct values using a given function
 * and a space-separated list of variable values and writes the
 * result into an iframe on the main page.
 */

include_once("config.inc");
include_once("modules/helpers.php");

?>
<head><title></title>
<meta http-equiv="expires" content="0" />
<meta name="robots" content="noindex,nofollow" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="include/plotstyle.min.css" type="text/css" />
</head><body class="calcpage">
<?php

// header and footer for html table
define('HD_table', '<table class="calctable" cellspacing="0" cellpadding="1"><tr><th>x </th>');
define('FT_table', '</tr></table>');
// header and footer for Latex table
define('HD_latex', '\begin{tabular}{c|c}<br />');
define('FT_latex', '<br />\end{tabular}');

$format = 0;			// Format is one of
				// 0: values blank-separated
				// 1: a html table
				// 2: csv lines
				// 3: a latex-formatted table
				
// get variables from querystring
//
if (isset($_POST['single1']) && $_POST['single1'] <> "") {
	$single1 = $_POST['single1'];
} else {
	// no function to calculate
	echo '</body></html>';
	return;
}
if (isset($_POST['c'])) $c = $_POST['c'];
if (isset($_POST['qqsingle'])) $qqsingle = $_POST['qqsingle'];
if  (isset($_POST['inpval'])) $inpval = $_POST['inpval'];
if  (isset($_POST['decis'])) $decis = $_POST['decis'];
if  (isset($_POST['format'])) $format = $_POST['format'];


// called for each pair of (value/result)				
function printValue($val, $erg, $format, $tk) {
	if ($format < 2) $val = "";
	echo $tk[0].$val.$tk[1].$erg.$tk[2];
}


// transform formula and input value into a PHP-readable form
//
if(!$inpval) $inpval=0;
// functions containing Diff or Integration cannot be evaluated by php
if ($single1!=str_replace('D','',$single1) || $single1!=str_replace('S','',$single1) || $single1!=str_replace('phi','',$single1)) {
	echo '<br />[unable to calculate]</body></html>';
	return;
}

$formula1=$single1;

// rectify string of variable values
$inpval=trim($inpval);
while($inpval!=str_replace("  "," ",$inpval))
	$inpval=str_replace("  "," ",$inpval);
$inpvals=explode(" ",$inpval);

$qq=$qqsingle;//substituted formulas should be calculable too
$single=1; //we don't want to compute the whole graph, only a single value
include 'modules/init.php';

switch ($format){
	case 0:	$tk = array("", "", "&nbsp;");
	break;

	case 1:	$tk = array("<td>", "", "</td>");
	//table Header line
	echo HD_table;
	// use input values (variables) with named constants
	// for table output
	$orgvals=explode(" ",$inpval);
	foreach($orgvals as $val) echo "<td>&nbsp;$val&nbsp;</td>";
	echo "</tr>\n<tr><th>$single1 </th>";
	$ft = FT_table;
	break;
		
	case 2:	$tk = array("", ",", "<br />");
	//csv header line
	echo"x;$single1<br/>\n";
	break;
		
	case 3:	$tk = array("\\\\<br />", "&", "");
	echo HD_latex;
	echo '$x$&$' . str_replace("*", "\cdot", $single1) . '$\\\\ \hline';
	$ft = FT_latex;
	break;	
}
	
// execute calculation via function graph() (see init.php)
// and print the results
//
foreach($inpvals as $val) {
	$erg = graph(handleConstants($val),$formula[0]);
	if ($erg==999999) 		$erg = $text2;	// illegal range
	else if($bracketerror[0]) 	$erg = $text5;	// bracket error
	else if(is_numeric($erg))	$erg=round($erg,$decis);
	else $erg = "undef";

	printValue($val, $erg, $format, $tk);
}

echo $ft;	// echo footer
?>
</body></html>
