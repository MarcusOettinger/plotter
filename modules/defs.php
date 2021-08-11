<?php
/*
modules/defs.php: definitions and functions for the sanity
check of function terms, transformation of expressions into
an evaluable form.
Required by: init.php

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

/*
 * Error messages used in images
 */
if($c!=2) {//German
	$text1 = 'Fehlerhafte Formel ';
	$text2 = 'Illegaler Wertebereich Formel ';
	$text3 = 'Kein Wert gefunden oder';
	$text4 = 'Fehler in Formel ';
	$text5 = 'Klammerfehler in Formel ';
	$text6 = 'Zu viele D oder S in Formel ';
} else {//English
	$text1 = 'Error in function term ';
	$text2 = 'Illegal range in function ';
	$text3 = 'No results found or';
	$text4 = 'Error in term ';
	$text5 = 'Inconsistent brackets in function ';
	$text6 = 'Too many D or S in function ';
}

/*
 * transform some redundant symbols and replace standard 
 * trigonometric and hyperbolic functions by those used 
 * internally.
 */
$trfrom = array(
	'D1(', 'D01(', 'S1(', ':', '{', '}', '[', ']', '<', '>',
	',', '#', ';', 'ggt', 'kgv', 'sechd', 'arsinh', 'arcosh',
	'artanh', 'arcoth', 'arcosech', 'arsech', 'arcsin',
	'arccos', 'arctan', 'arccot', 'arccosec', 'arcsec',
	'cosech', 'sech', 'cosec', 'sec', 'deg(', 'rad(',
	'rand'
);
$trto = array(
	'D(', 'D0(', 'S(', '/', '(', ')', '(', ')', '(', ')',
	'.', ',', ',', 'gcf', 'lcm', 'scahd', 'asinh', 'acosh',
	'atanh', 'acoth', 'arcsch', 'arscah', 'asin',
	'acos', 'atan', 'acot', 'acsc', 'acsa',
	'csch', 'scah', 'csc', 'csch', 'rad2deg(', 'deg2rad(',
	'mt_rand'
);


/* 
 * Sanity check of function terms:
 * calculations are performed by eval(), so possible malicious code has to 
 * be stripped. To sanitize the expressions, all allowed substrings will be
 * removed from a test string $test and if anything remains in the test
 * string, calculation is cancelled.
 * $tok contains known tokens to remove from test string (digits are to
 * be removed by preg_replace later on)
 */
$tok = array(	'digamma',	'igammad',	'arcsch',	'arscah',	'gammad',	'igauss',
			'lambda',	'sichi2',	'acosh', 'acoth', 'asinh',	'atanh', 'betad',
			'betap', 'blanc', 'gamma',	'hlogd', 'lnorm', 'omega',	'prime',
			'rossi', 'rwcon', 'scahd', 'theta', 'acos', 'acot', 'acsc', 'round',
			'ceil', 'floor', 'adig', 'asca', 'asin', 'atan',	'beta', 'bind',
			'bool', 'bump',	'cosd', 'cosh', 'coth', 'csch',	'dist', 'even', 'fmod', 
			'gbsc',	'haar', 'hgeo', 'hubb', 'ichi', 'lapc', 'levy', 'logd', 'logn',
			'logs', 'mean', 'nbin', 'norm', 'pear', 'poly', 'pyth', 'ramp',
			'mt_rand', 'rect', 'rcon', 'rlgh', 'rlng', 'scah', 'scir', 'sinc',
			'sinh', 'skel', 'step', 'stir', 'stud', 'tanc', 'tanh', 'toti', 'g_acc',
			'traj', 'trap', 'trid', 'wcon', 'yule', 'zeta', 'zipf', 'abs',
			'asy', 'bin', 'brw', 'bsc', 'cat', 'cau', 'chi', 'con', 'cos',
			'cot', 'csc', 'deg', 'dig', 'div', 'ell', 'erf', 'eta', 'exp',
			'fac', 'fib', 'gcf', 'gen', 'geo', 'gom', 'gum', 'HY4', 'kum',
			'lcm', 'lmn', 'log', 'man', 'max', 'min', 'nak', 'odd', 'par',
			'phi', 'poi', 'pll', 'pon', 'pow', 'rad', 'rsf', 'saw', 'sca',
			'sgm', 'shg', 'sig', 'sin', 'siv', 'srp', 'sq2', 'sqrt', 'sqr',
			 'tak', 'tan', 'thr', 'tri', 'uni', 'wig', 'dc', 'Ft', 'Fz', 'gd',
			'gk', 'go', 'Hm', 'mo', 'pi', 'PI', 'wb', 'wf', 'zm', '-', ',', '.',
			'(', ')', '*', '/', '%', '+', '^', 'd', 'D', 'e', 'E', 'F',
			'H', 'L', 'M', 'R', 'S', 'x', 'y', 'ln', 'h', 'hbar', 'c', 'Na',
			';');
/* 
	 * some function names contain one of the named constants, so
	 * they will be renamed before the constants are replaced by
	 * numbers:
	 */
	$cfrom = array(
		/* convert function names (avoid named constants e, x) */
		'exp', 'max', 'ceil', 'round', 'deg2rad', 'rad2deg',
		'levy', 'ell', 'zeta', 'rect', 'geo', 'betad',
		'beta', 'theta', 'eta', 'pear', 'yule', 'step',
		'erf', 'omega', 'betap', 'gen', 'skel', 'prime',
		'mean', 'even',
		/* (avoid named constants h, c) */
		'hbar', 'sinh', 'cosh', 'tanh', 'coth', 'asinh',
		'arscah', 'acosh', 'atanh', 'acoth', 'csc(', 'csch(',
		'scah(', 'haar', 'cos', 'cat(', 'cot(', 'cot2(',
		'gcf(', 'lcm(', 'chi(', 'chi2(', 'cau(', 'lapc(',
		'logd(', 'con(',
		/* avoid d  */
		'lambda', 'fmod', 'stud', 'logd', 'cosd', 'scahd', 
		'gammad', 'trid', 'dc', 'gd', 'rand', 'digamma',
		'dist', 'bind', 'div', 'dig', 'odd', 'pr('
	);
	$cto = array(
		/* (avoid named constants e, x) */
		'FN_00', 'FN_01', 'FN_02', 'FN_03', 'FN_04', 'FN_05', 
		'FN_06', 'FN_07', 'FN_08', 'FN_09', 'FN_10', 'FN_11', 
		'FN_12', 'FN_13', 'FN_14', 'FN_15', 'FN_16', 'FN_17', 
		'FN_18', 'FN_19', 'FN_20', 'FN_21', 'FN_22', 'FN_23',
		'FN_24', 'FN_25',
		/* (avoid named constant h) */
		'FN_41', 'FN_42', 'FN_43', 'FN_44', 'FN_45', 'FN_46',
		'FN_47', 'FN_48', 'FN_49', 'FN_50', 'FN_51', 'FN_52',
		'FN_53', 'FN_54', 'FN_55', 'FN_56', 'FN_57', 'FN_58',
		'FN_59', 'FN_60', 'FN_61', 'FN_62', 'FN_63', 'FN_64',
		'FN_65', 'FN_66', 'FN_67',
		/* avoid d  */
		'FN_71', 'FN_72', 'FN_73', 'FN_74', 'FN_75', 'FN_76',
		'FN_77', 'FN_78', 'FN_79', 'FN_80', 'FN_81', 'FN_82',
		'FN_83', 'FN_84', 'FN_85', 'FN_86', 'FN_87', 'FN_88'
	);
	
$coldfunc = array(
	/* backwards compatibility: allow for R() instead of round() */
	'R(', 'R0(', 'R1(',
	/* Improve PHP functions: use some better definitions instead */
	'pow', 'ln(', 'log(', 'sqrt', 'asin', 'acos', 'atanh'
);
$cnfunc = array(
	/* backwards compatibility: allow for R() instead of round() */
	'round(', 'floor(', 'ceil(',
	/* Improve PHP functions: use some better definitions instead */
	'npow', 'log(', 'nlog(', 'sqr', 'nasin', 'nacos', 'natanh'
);
	

/*
 * multiop($term):
 * add multiplication operators where omitted, e.g. 3x -> 3*x
 * @param $term: string
 */
function multiop($term) {
	for($i=0; $i<strlen($term); $i++) {
		if(preg_match("/[0-9]/", substr($term,$i,1))) {
			if(preg_match("/[a-zA-Z]/", substr($term,$i+1, 1)) && substr($term, $i+1, 1) != "E") {
				$term = substr($term,0,$i+1) . "*"
				      . substr($term, $i+1, strlen($term));
				--$i;
			}
		}
	}
	return $term;
}


/*
 * calculate a function value and return the result.
 * Parameters $a: variable value,
 *         $expr: string - sanitized function expression to eval()
*/
function graph($a, $expr) {
debug_writeline("function graph(): evaluating ".$expr." for ".$a);
	global $single, $logsk, $logskx, $iter, $iter2, $istep;

	if($logsk)	$expr = 'nlogn('.$logsk.', '.$expr.')';
	$a = (double)$a;
	if (!$single && abs($a)>PHP_INT_MAX)	return PHP_INT_MAX;
	if($logskx) $a = pow($logskx, $a);
	// calculate function value using eval()	
	@eval('$out='.$expr.';'); 
	if (is_nan($out)) return NULL;
	if (!$single && abs($out)>PHP_INT_MAX) return PHP_INT_MAX;
	$iter2 = $iter;
	$iter = $out;
	++$istep;
	
//echo ", result is ".$out."<br>";

	return $out;
}
?>
