<?php
/*
modules/init.php: perform some sanity checks on function Terms,
  transform expressions into an evaluable form and calculate function
  values.
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

// add a multiplication operator where omitted, e.g. 3x -> 3*x
function multisign($term) {
	for($i=0;$i<strlen($term);$i++) {
		if(preg_match("/[0-9]/",substr($term,$i,1))) {
			if(preg_match("/[a-zA-Z]/",substr($term,$i+1,1)) && substr($term,$i+1,1)!="E") {
				$term=substr($term,0,$i+1)."*".substr($term,$i+1,strlen($term));
				--$i;
			}
		}
	}
	return $term;
}

// error messages
if($c!=2) {//German
	$text1 = 'Fehlerhafte Formel ';
	$text2 = 'Illegaler Wertebereich Formel ';
	$text3 = 'Kein Wert gefunden oder';
	$text4 = 'Fehler in Formel ';
	$text5 = 'Klammerfehler in Formel ';
	$text6 = 'Zu viele D oder S in Formel ';
} else {//English
	$text1 = 'Defective formula ';
	$text2 = 'Illegal range in formula ';
	$text3 = 'No results found or';
	$text4 = 'Error in formula ';
	$text5 = 'Bracket error in formula ';
	$text6 = "Too many D or S in formula ";
}

// make some necessary changes in the formulae
for($i=0;$i<3;$i++) {
	$formula[$i] = rawurldecode($func[$i]);
	// delete spaces
	$formula[$i] = str_replace(' ','',$formula[$i]);
	$Y=str_replace(' ','',$Y);
	$qq=str_replace(' ','',$qq);
	// add multiplication signs
	$Y=multisign($Y);
	$qq=multisign($qq);
	$formula[$i]=multisign($formula[$i]);
	// insert defined formulae for substitutions Y and Q,
	if($Y && $formula[$i])
		$formula[$i] = str_replace('Y','('.$formula[$i].')',$Y);
	$formula[$i] = str_replace('Q','('.$qq.')',$formula[$i]);
	// transform redundant symbols
	$formula[$i] = str_replace('D1(','D(',$formula[$i]);
	$formula[$i] = str_replace('D01(','D0(',$formula[$i]);
	$formula[$i] = str_replace('S1(','S(',$formula[$i]);
	$formula[$i] = str_replace(':','/',$formula[$i]);
	$formula[$i] = str_replace('[','(',$formula[$i]);
	$formula[$i] = str_replace(']',')',$formula[$i]);
	$formula[$i] = str_replace('{','(',$formula[$i]);
	$formula[$i] = str_replace('}',')',$formula[$i]);
	$formula[$i] = str_replace('<','(',$formula[$i]);
	$formula[$i] = str_replace('>',')',$formula[$i]);
	$formula[$i] = str_replace(',','.',$formula[$i]);
	$formula[$i] = str_replace('#',',',$formula[$i]);
	$formula[$i] = str_replace(';',',',$formula[$i]);
	// ggt is the German term for gcf
	$formula[$i] = str_replace('ggt','gcf',$formula[$i]);
	// kgv is the German term for lcm
	$formula[$i] = str_replace('kgv','lcm',$formula[$i]);

	// Replace standard trigonometric and hyperbolic functions
	// by those used internally.
	$formula[$i] = str_replace("sechd","scahd",$formula[$i]);
	$formula[$i] = str_replace("arsinh","asinh",$formula[$i]);
	$formula[$i] = str_replace("arcosh","acosh",$formula[$i]);
	$formula[$i] = str_replace("artanh","atanh",$formula[$i]);
	$formula[$i] = str_replace("arcoth","acoth",$formula[$i]);
	$formula[$i] = str_replace("arcosech","arcsch",$formula[$i]);
	$formula[$i] = str_replace("arsech","arscah",$formula[$i]);
	$formula[$i] = str_replace("arcsin","asin",$formula[$i]);
	$formula[$i] = str_replace("arccos","acos",$formula[$i]);
	$formula[$i] = str_replace("arctan","atan",$formula[$i]);
	$formula[$i] = str_replace("arccot","acot",$formula[$i]);
	$formula[$i] = str_replace("arccosec","acsc",$formula[$i]);
	$formula[$i] = str_replace("arcsec","asca",$formula[$i]);
	$formula[$i] = str_replace("cosech","csch",$formula[$i]);
	$formula[$i] = str_replace("sech","scah",$formula[$i]);
	$formula[$i] = str_replace("cosec","csc",$formula[$i]);
	$formula[$i] = str_replace("sec","sca",$formula[$i]);
	// catch 0 as a function
	if($formula[$i]=='0') $formula[$i]='0*1';

	// The ** operator was added in PHP 5.6
	if (PHP_VERSION_ID < 50600) {
		include("modules/caretPow.php");
		if($formula[$i]!=str_replace("^","",$formula[$i])) {
		$t = new term($formula[$i]);
		$formula[$i]=$t->ToString();
	}
	} else {
		// convert ^ to **
		$formula[$i] = str_replace("^","**",$formula[$i]);
	}

	// look for bracket errors
	$bracketerror[$i]=0;
	if (substr_count($formula[$i], "(")!= substr_count($formula[$i], ")")) {
		// stop if there are bracket errors found
		if( !$single) {
			imagefill ($img,0,0,$color[$bg]);
			imagestring ($img,5,$height/2-80,100,$text5.($i+1),$color[$col[$i]]);
			ImagePng($img);
			die();
		} else if( $single) {
			echo $text5;
			die();
		}
	}
}

/* sanity check of formulae:
 * calculations are performed by eval(), so possible malicious code must be
 * stripped. To sanitize the expressions, all allowed substrings will be
 * removed from a test string $test and if anything remains in the test
 * string, do a full stop.
 * $tok contains known tokens to remove from test string (digits are to
 * be removed by preg_replace later on)
 */
$tok = array(	'digamma',	'igammad',	'arcsch',	'arscah',	'gammad',	'igauss',
			'lambda',	'sichi2',	'acosh', 'acoth', 'asinh',	'atanh', 'betad',
			'betap', 'blanc', 'gamma',	'hlogd', 'lnorm', 'omega',	'prime',
			'rossi', 'rwcon', 'scahd', 'theta', 'acos', 'acot', 'acsc',
			'adig', 'asca',	'asin', 'atan',	'beta', 'bind',	'bool', 'bump',
			'cosd', 'cosh', 'coth', 'csch',	'dist', 'even', 'fmod', 'gbsc',
			'haar', 'hgeo', 'hubb', 'ichi', 'lapc', 'levy', 'logd', 'logn',
			'logs', 'mean', 'nbin', 'norm', 'pear', 'poly', 'pyth', 'ramp',
			'rand', 'rect', 'rcon', 'rlgh', 'rlng', 'scah', 'scir', 'sinc',
			'sinh', 'skel', 'step', 'stir', 'stud', 'tanc', 'tanh', 'toti',
			'traj', 'trap', 'trid', 'wcon', 'yule', 'zeta', 'zipf', 'abs',
			'asy', 'bin', 'brw', 'bsc', 'cat', 'cau', 'chi', 'con', 'cos',
			'cot', 'csc', 'deg', 'dig', 'div', 'ell', 'erf', 'eta', 'exp',
			'fac', 'fib', 'gcf', 'gen', 'geo', 'gom', 'gum', 'HY4', 'kum',
			'lcm', 'lmn', 'log', 'man', 'max', 'min', 'nak', 'odd', 'par',
			'phi', 'poi', 'pll', 'pon', 'pow', 'rad', 'rsf', 'saw', 'sca',
			'sgm', 'shg', 'sig', 'sin', 'siv', 'srp', 'sq2', 'sqrt', 'sqr',
			 'tak', 'tan', 'thr', 'tri', 'uni', 'wig', 'dc', 'Ft', 'Fz', 'gd',
			'gk', 'go', 'Hm', 'mo', 'pi', 'wb', 'wf', 'zm', '-', ',', '.',
			'(', ')', '*', '/', '%', '+', '^', 'd', 'D', 'e', 'E', 'F',
			'H', 'L', 'M', 'R', 'S', 'x', 'y');


for($i=0;$i<3;$i++) {
	// stop if more than one D, D2, D3, D0, D02, D03, or more than any one S in formula
	if((strlen($formula[$i])-strlen(str_replace('D(','',$formula[$i]))>2 || strlen($formula[$i])-strlen(str_replace('D2(','',$formula[$i]))>3 || strlen($formula[$i])-strlen(str_replace('D3(','',$formula[$i]))>3 || strlen($formula[$i])-strlen(str_replace('D0(','',$formula[$i]))>3 || strlen($formula[$i])-strlen(str_replace('D02(','',$formula[$i]))>4 || strlen($formula[$i])-strlen(str_replace('D03(','',$formula[$i]))>4 || strlen($formula[$i])-strlen(str_replace('S','',$formula[$i]))>1) && !$single) {
		if($errors) {
			imagefill ($img,0,0,$color[$bg]);
			imagestring ($img,5,$height/2-80,100,$text6.($i+1),$color[$col[$i]]);
		}
		ImagePng($img);
		die();
	}

	// check for sane expressions in $formula[$i]:
	$test=$formula[$i];


	for ($t=0; $t<count($tok); $t++)
		$test = str_replace($tok[$t],'',$test);
	$test=preg_replace('/[0-9]/','',$test);

	// stop if anything remains in $test
	if(strlen($test) && !$single) {
		if($errors) {
			imagefill ($img,0,0,$color[$bg]);
			imagestring ($img,5,$height/2-80,100,$text1.($i+1),$color[$col[$i]]);
		}
		ImagePng($img);
		die();
	} else if(strlen($test) && $single) {
		echo $text1;
		die();
	}

	/* transform formulae - don't change the sequence!
	This may look a bit chaotic, but it works. You have 
	to be very careful when editing*/
	$formula[$i] = str_replace('exp','yp',$formula[$i]);
	$formula[$i] = str_replace('max','yy',$formula[$i]);
	$formula[$i] = str_replace('x','$a',$formula[$i]);
	$formula[$i] = str_replace('yy','max',$formula[$i]);

	$formula[$i] = str_replace('deg','ggg',$formula[$i]);
	$formula[$i] = str_replace('rad','rrr',$formula[$i]);
	$formula[$i] = str_replace('levy','isl',$formula[$i]);
	$formula[$i] = str_replace('ell','fll',$formula[$i]);
	$formula[$i] = str_replace('zeta','zt',$formula[$i]);
	$formula[$i] = str_replace('rect','rct',$formula[$i]);
	$formula[$i] = str_replace('geo','gxo',$formula[$i]);
	$formula[$i] = str_replace('betad','btta',$formula[$i]);
	$formula[$i] = str_replace('beta','bta',$formula[$i]);
	$formula[$i] = str_replace('theta','thta',$formula[$i]);
	$formula[$i] = str_replace('eta','dic',$formula[$i]);
	$formula[$i] = str_replace('pear','pr',$formula[$i]);
	$formula[$i] = str_replace('yule','yul',$formula[$i]);
	$formula[$i] = str_replace('step','stxp',$formula[$i]);
	$formula[$i] = str_replace('erf','rf',$formula[$i]);
	$formula[$i] = str_replace('omega','omga',$formula[$i]);
	$formula[$i] = str_replace('betap','btap',$formula[$i]);
	$formula[$i] = str_replace('gen','gxn',$formula[$i]);
	$formula[$i] = str_replace('skel','skl',$formula[$i]);
	$formula[$i] = str_replace('prime','rim',$formula[$i]);
	$formula[$i] = str_replace('mean','mxan',$formula[$i]);
	$formula[$i] = str_replace('even','xvxn',$formula[$i]);
	$formula[$i] = str_replace('e','2.718281828459',$formula[$i]);
	$formula[$i] = str_replace('xvxn','even',$formula[$i]);
	$formula[$i] = str_replace('mxan','mean',$formula[$i]);
	$formula[$i] = str_replace('skl','skel',$formula[$i]);
	$formula[$i] = str_replace('gxn','gen',$formula[$i]);
	$formula[$i] = str_replace('btap','betap',$formula[$i]);
	$formula[$i] = str_replace('thta','theta',$formula[$i]);
	$formula[$i] = str_replace('omga','omega',$formula[$i]);
	$formula[$i] = str_replace('rf','erf',$formula[$i]);
	$formula[$i] = str_replace('fll','ell',$formula[$i]);
	$formula[$i] = str_replace('zt','zeta',$formula[$i]);
	$formula[$i] = str_replace('rct','rect',$formula[$i]);
	$formula[$i] = str_replace('gxo','geo',$formula[$i]);
	$formula[$i] = str_replace('bta','beta',$formula[$i]);
	$formula[$i] = str_replace('dic','eta',$formula[$i]);
	$formula[$i] = str_replace('yul','yule',$formula[$i]);
	$formula[$i] = str_replace('stxp','step',$formula[$i]);
	$formula[$i] = str_replace('yp','exp',$formula[$i]);

	$formula[$i] = str_replace('pro','pp',$formula[$i]);
	$formula[$i] = str_replace('pr','pear',$formula[$i]);
	$formula[$i] = str_replace('pp','pro',$formula[$i]);
	$formula[$i] = str_replace('pi2','1.5707963267949',$formula[$i]);
	$formula[$i] = str_replace('pi','3.1415926535898',$formula[$i]);
	$formula[$i] = str_replace('gom','gxm',$formula[$i]);
	$formula[$i] = str_replace('go','1.6180339887499',$formula[$i]);
	$formula[$i] = str_replace('gxm','gom',$formula[$i]);
	$formula[$i] = str_replace('rim','prime',$formula[$i]);

	$formula[$i] = str_replace('lambda','lamba',$formula[$i]);
	$formula[$i] = str_replace('fmod','fmo',$formula[$i]);
	$formula[$i] = str_replace('stud','stu',$formula[$i]);
	$formula[$i] = str_replace('logd','logx',$formula[$i]);
	$formula[$i] = str_replace('cosd','cosx',$formula[$i]);
	$formula[$i] = str_replace('scahd','scahx',$formula[$i]);
	$formula[$i] = str_replace('gammad','gammax',$formula[$i]);
	$formula[$i] = str_replace('trid','trix',$formula[$i]);
	$formula[$i] = str_replace('dc','xc',$formula[$i]);
	$formula[$i] = str_replace('gd','gx',$formula[$i]);
	$formula[$i] = str_replace('rand','ranx',$formula[$i]);
	$formula[$i] = str_replace('digamma','xigamma',$formula[$i]);
	$formula[$i] = str_replace('dist','ist',$formula[$i]);
	$formula[$i] = str_replace('bind','binx',$formula[$i]);
	$formula[$i] = str_replace('div','tiv',$formula[$i]);
	$formula[$i] = str_replace('dig','tig',$formula[$i]);
	$formula[$i] = str_replace('odd','oxx',$formula[$i]);
	$formula[$i] = str_replace('d','4.669201609103',$formula[$i]);
	$formula[$i] = str_replace('oxx','odd',$formula[$i]);
	$formula[$i] = str_replace('tig','dig',$formula[$i]);
	$formula[$i] = str_replace('tiv','div',$formula[$i]);
	$formula[$i] = str_replace('binx','bind',$formula[$i]);
	$formula[$i] = str_replace('ist','dist',$formula[$i]);
	$formula[$i] = str_replace('xigamma','digamma',$formula[$i]);
	$formula[$i] = str_replace('lamba','lambda',$formula[$i]);
	$formula[$i] = str_replace('fmo','fmod',$formula[$i]);
	$formula[$i] = str_replace('stu','stud',$formula[$i]);
	$formula[$i] = str_replace('logx','logd',$formula[$i]);
	$formula[$i] = str_replace('cosx','cosd',$formula[$i]);
	$formula[$i] = str_replace('scahx','scahd',$formula[$i]);
	$formula[$i] = str_replace('gammax','gammad',$formula[$i]);
	$formula[$i] = str_replace('btta','betad',$formula[$i]);
	$formula[$i] = str_replace('trix','trid',$formula[$i]);
	$formula[$i] = str_replace('xc','dc',$formula[$i]);
	$formula[$i] = str_replace('gx','gd',$formula[$i]);
	$formula[$i] = str_replace('ranx','mt_rand',$formula[$i]);
	$formula[$i] = str_replace('isl','levy',$formula[$i]);

	$formula[$i] = str_replace('ggg','rad2deg',$formula[$i]);
	$formula[$i] = str_replace('rrr','deg2rad',$formula[$i]);
	$formula[$i] = str_replace('R0','floor',$formula[$i]);
	$formula[$i] = str_replace('R1','ceil',$formula[$i]);
	$formula[$i] = str_replace('R','round',$formula[$i]);
	$formula[$i] = str_replace('sq2','1.4142135623731',$formula[$i]);

	// Improve PHP functions: use some better definitions instead
	$formula[$i] = str_replace('pow','npow',$formula[$i]);
	$formula[$i] = str_replace('log','nlog',$formula[$i]);
	$formula[$i] = str_replace('sqrt','sqr',$formula[$i]);
	$formula[$i] = str_replace('asin','nasin',$formula[$i]);
	$formula[$i] = str_replace('acos','nacos',$formula[$i]);
	$formula[$i] = str_replace('atanh','natanh',$formula[$i]);

	/* extract the value for a perpendicular asymptote and set
	 * the x-coordinate as 'asyval'.
	 * As formula simply 'asy' is preserved
	 */
	if(!$single && $formula[$i] != str_replace('asy(','',$formula[$i])) {
		$formula[$i] = substr($formula[$i],0,strpos($formula[$i],')'));
		$formula[$i] = substr($formula[$i],strpos($formula[$i],'asy'),strlen($formula[$i]));
		$formula[$i] = str_replace('asy','',$formula[$i]);
		$formula[$i] = str_replace('(','',$formula[$i]);
		$formula[$i] = str_replace(')','',$formula[$i]);
		@eval('$formula[$i]='.$formula[$i].';');
		if(is_numeric($formula[$i])) {
			if($logskx)
				$formula[$i] = nlogn($logskx,$formula[$i]);
			$asyval[$i] = round($width/($rulex2-$rulex1)*(abs($rulex1-doubleval($formula[$i]))),0);
		}
		else {
			if($errors)
				imagestring ($img,5,$height/2-80,100,$text1.($i+1),$color[$col[$i]]);
		}
		$formula[$i] = 'asy';
	}
}

// include non-PHP function definitions
include "modules/functions_extra.inc";

// include differential and integral functions
include "modules/diffint.inc";

/*
 * calculate function value and return the result.
 * Parameters $a: variable value,
 *         $expr: string - sanitized expression to eval()
*/
function graph($a,$expr) {
	global $single;
	global $logsk;
	global $logskx;
	global $iter;
	global $iter2;
	global $istep;
	if($logsk)
		$expr = 'nlogn('.$logsk.','.$expr.')';
	$a = (double)$a;
	if (!$single && abs($a)>100000)
		return 999999;
	if($logskx)
		$a = pow($logskx,$a);
	// calculate function value using eval
	@eval('$out='.$expr.';'); 
	if (is_nan($out)) return NULL;
	if (!$single && abs($out)>100000) return 999999;
	$iter2 = $iter;
	$iter = $out;
	++$istep;

	return $out;
}
?>
