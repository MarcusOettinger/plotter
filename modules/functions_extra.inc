<?php
/*
functions_extra.inc: additional function definitions for mathematical funtions
not included in PHP.

M. Oettinger 06/2020: moved funtion definitions into an extra file to
keep init.php readable (well, more or less...)
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


// powers usable with odd roots of negative values
function npow($base, $exp) {
	$testodd=(1/$exp);
	if($base<0 && $exp!=intval($exp) && $testodd==intval($testodd) && $testodd/2!=intval($testodd/2))
		return -pow(-$base,$exp);
	if($base<0 && $exp!=intval($exp))
		return NULL;
	return pow($base,$exp);
}

// extra roots and logarithms
// ------------------------------------------------------------------------

// square root returning NULL for negative variable values (compared to
// NaN returned by PHP's sqrt()
function sqr($a) { return $a<0?NULL:sqrt($a); }

// natural logarithm returning  NULL for x<=0
function nlog($a) { return $a<0?NULL:log($a); }

// decadic logarithm returning  NULL for x<=0
function nlog10($a) { return $a<0?NULL:log10($a); }

//
// distribution functions
//

// normal distribution (Gaussian distribution),
// parameters are 
//     center $e,
//     standard deviation $s,
//     variable $x
function norm($e,$s,$x) {
	return 1/($s*sqrt(2*M_PI))*exp(-.5*(($x-$e)/$s)*(($x-$e)/$s));
}

// logarithmic normal distribution
// parameters are (e/s of the Gaussian distribution)
//     center $e,
//     standard deviation $s,
//     variable $x
function lnorm($e,$s,$x) {
	if($x<0 || $x-$e<=0)
		return NULL;
	return 1/(sqrt(2*M_PI)*$s*$x)*exp(-.5*(nlog($x-$e)/$s)*(nlog($x-$e)/$s));
}

// phi, cumulative normal distribution
function phi($e,$s,$x) {
	global $sum;
	global $width;
	global $rulex;
	$sum+=(norm($e,$s,$x)/$width*$rulex);
	return $sum;
}

// Cauchy distribution (aka Lorentz- or Cauchy-Lorentz distribution)
// parameters are
//     location parameter $t,
//     scale parameter $s,
//     variable $x
function cau($t,$s,$x) {
	return $s/(M_PI*($s*$s+($x-$t)*($x-$t)));
}

// Laplace distribution
function lapc($m,$s,$x) {
	if($s<=0)
		return 999999;
	return 1/(2*$s)*exp(-(abs($x-$m)/$s));
}

//
// Step and sign functions
//

// Heaviside step function
function H($a) { return $a<=0?0:1; }


// multivariate Heaviside step function
function Hm() {
	$a=func_get_args();
	foreach($a as $b) {
		if($b<=0)
			return 0;
	}
	return 1;
}

// sign/signum function
function sig($a) {  return $a==0?0:$a/(abs($a)); }


//
// trigonometric, hyperbolic and related functions
//

// arcsine returning NULL for |x|>1
function nasin($a) {
	if(abs($a)>1) return NULL;
	return asin($a);
}

// arccosine returning NULL for |x|>1
function nacos($a) {
	if(abs($a)>1)
		return NULL;
	return acos($a);
}

// area hyperbolic sine
function nasinh($a) { return asinh($a); }

// area hyperbolic cosine returning NULL for x<1
function nacosh($a) {
	if($a<1)
		return NULL;
	return acosh($a);
}

// area hyperbolic tangent returning for |x|>1
function natanh($a) {
	if(abs($a)>=1)
		return NULL;
	return atanh($a);
}

// cotangent
function cot($a) { return 1/tan($a); }

// arccotangent
function acot($a) { return M_PI/2-atan($a); }

// hyperbolic cotangent
function coth($a) {
	return 1/tanh($a);
}

// area hyperbolic cotangent
function acoth($a) {
	if($a<=1 && $a>=-1)
		return NULL;
	return .5*nlog(($a+1)/($a-1));
}

// secant
function sca($a) { return 1/cos($a); }

// cosecant
function csc($a) { return 1/sin($a); }

// hyperbolic secant
function scah($a) { return 1/cosh($a); }

// hyperbolic cosecant
function csch($a) { return 1/sinh($a); }

// arcsecant
function asca($a) { return acos(1/$a); }

// arccosecant
function acsc($a) { return asin(1/$a); }
// area hyperbolic secant
function arscah($a) { 
	$x=(1+pow(1-$a*$a,1/2))/$a;
	return $x<=0?NULL:nlog($x);
}

// area hyperbolic cosecant
function arcsch($a) {
	$x=1/$a+sqrt(1+1/($a*$a));
	if($x<=0)
		return NULL;
	return nlog($x);
}

// logarithm to the base n
function nlogn($n,$a) {
	if($a<=0 || $n<=0)
		return NULL;
	return log($a)/log($n);
}

// Gudermannian function
function gd($a) { return atan(sinh($a) );
}

// sine square
function sin2($a) { return sin($a)*sin($a); }
// cosine square
function cos2($a) { return cos($a)*cos($a); }
// tangent square
function tan2($a) { return tan($a)*tan($a); }
// cotangent square
function cot2($a) { return cot($a)*cot($a); }

// semiversus
function siv($a) {
	return sin2($a/2);
}

// Hubbert curve
function hubb($a) {
	return 1/(2+2*cosh($a));
}

// Sigmoid function
function sgm($a) {
	return 1/(1+exp(-$a));
}

// Hyper4 function
function HY4($a,$n) {
	$n-=1;
	$n=round($n);
	if($n<0 || $a<0) 
		return NULL;
	$hbase=$a;
	for($i=0;$i<$n;$i++)
		$a=pow($hbase,$a);
	return $a;
}

// Lambda function
function lambda($a,$b) {
	if($a<0)
		return NULL;
	return pow($a,pow($a,$b-1));
}

// semicircle curve
function scir($a,$n) {
	if(abs($a)>abs($n))
		return NULL;
	return sqrt($n*$n-$a*$a);
}

// logistic distribution
function nlogd($l,$s,$a) {
	return 1/(4*$s)*scah(($a-$l)/(2*$s))*scah(($a-$l)/(2*$s));
}

// half-logistic distribution
function hnlogd($a) {
	if($a<0)
		return NULL;
	return 2*exp(-$a)/pow(1+exp(-$a),2);
}

// Erlang distribution
function rlng($k,$l,$a) {
	if($a<=0)
		return NULL;
	$k=round($k);
	return (pow($l,$k)*pow($a,$k-1)*exp(-$l*$a))/fac($k-1);
}

// exponential distribution
function pon($l,$a) {
	if($a<=0)
		return NULL;
	return $l*exp(-$l*$a);
}

// hyperbolic secant distribution
function scahd($a) {
	return .5*scah(M_PI/2*$a);
}

// Kumaraswamy distribution
function kum($a,$b,$x) {
	if($a<0 || $b<0)
		return 999999;
	if($x<0 || $x>1)
		return NULL;
	return $a*$b*pow($x,$a-1)*pow(1-pow($x,$a),$b-1);
}

// Levy distribution
function levy($c,$a) {
	if($a<=0)
		return NULL;
	if($c<0)
		return 999999;
	return pow($c/(2*M_PI),.5)*exp(-$c/(2*$a))/pow($a,3/2);
}

// raised cosine distribution
function cosd($m,$s,$a) {
	if($a<$m-$s || $a>$m+$s )
		return NULL;
	return .5/$s*(1+cos(($a-$m)/$s*M_PI));
}

// Rayleigh distribution
function rlgh($s,$a) {
	if($a<=0)
		return NULL;
	return $a*exp(-$a*$a/(2*$s*$s))/($s*$s);
}

// Weibull distribution
function wb($k,$l,$a) {
	if($a<0)
		return NULL;
	return $k/$l*pow($a/$l,$k-1)*exp(-pow($a/$l,$k));
}

// Wigner semicircle distribution
function wig($r,$a) {
	if($r*$r<$a*$a)
		return NULL;
	return 2/(M_PI*$r*$r)*sqrt($r*$r-$a*$a);
}

// pythagorean theorem
function pyth($a,$b) {
	return sqrt($a*$a+$b*$b);
}

// rule of three
function thr($a,$b,$c) {
	return $b*$c/$a;
}

// Stirling formula
function stir($a) {
	if($a<0)
		return NULL;
	return sqrt(2*M_PI*$a)*pow($a/M_E,$a);
}

// exponential decay
function dc($n,$l,$a) {
	return $n*exp(-$l*$a);
}

// catenary
function cat($a,$x) {
	return $a*cosh($x/$a);
}

// semielliptic curve
function ell($a,$b,$x) {
	if(abs($x)>abs($a))
		return NULL;
	return sqrt((1-$x*$x/($a*$a))*$b*$b);
}

// superelliptic curve
function ell2($a,$b,$n,$x) {
	if(abs($x)>abs($a))
		return NULL;
	if($a<=0 || $b<=0 || $n<=0)
		return 999999;
	$x=abs($x);
	return pow(1-pow($x/$a,$n),1/$n)*$b;
}

// rectangle curve
function rect($o,$u,$p,$a) {
	if(ceil($a/$p)/2==ceil(ceil($a/$p)/2))
         	return $u;
	return $o;
}

// sawtooth wave
function saw($p,$am,$a) {
	return $am*(1/$p*$a-floor(1/$p*$a));
}

// reverse sawtooth wave
function saw2($p,$am,$a) {
	return $am-saw($p,$am,$a);
}

// distance function
function dist($a) {
	return min(abs($a-floor($a)),abs($a-ceil($a)));
}

// triangle curve
function tri($p,$am,$a) {
	$p/=2;
	if ($a/$p/2==round($a/$p/2))
		return $am;
	if ($a/$p==round($a/$p))
		return 0;
	if (ceil($a/$p)/2==ceil(ceil($a/$p)/2))
		return saw($p,$am,$a);
	return saw2($p,$am,$a);
}

// ramp function
function ramp($s,$e,$h,$a) {
	if($a<$s)
		return 0;
	if($a<$e)
		return ($a-$s)*$h/($e-$s);
	return $h;
}

// reverse ramp function
function ramp2($s,$e,$h,$a) {
	return $h-ramp($s,$e,$h,$a);
}

// trapezium function
function trap($s1,$e1,$h,$s2,$e2,$a) {
	if($a<$e1)
		return ramp($s1,$e1,$h,$a);
	return ramp2($s2,$e2,$h,$a);
}

// polygon or chart function
function poly() {
	$a=func_get_args();
	$x=$a[sizeof($a)-1];
	if($x<$a[0] || $x>$a[sizeof($a)-3])
		return NULL;
	for($i=0;$i<sizeof($a)-4;$i+=2) {
		if($a[$i]>=$a[$i+2])
			return 999999;
		if($x>=$a[$i])
			$res=$a[$i+1]+($x-$a[$i])*($a[$i+3]-$a[$i+1])/($a[$i+2]-$a[$i]);
	}
	return $res;
}

// factorial
function fac($a) {
	if($a<0)
		return NULL;
	if($a<1)
		return 1;
//	$a+=.5;
	$factorial=1;
	for($i=1;$i<=$a;$i++)
		$factorial*=$i;
	return $factorial;
}

// gamma function
function gamma($a) {
	if($a==0 || $a==-intval(abs($a)))
		return NULL;
	if($a<5.858) {
		/* this is the Euler and Weierstrass definition of the gamma function.
                    It is used here only for low values (< 5.858) of x, because of the
                    heavy computing power needed. From 5.858 upwards, the Stirling
                    function is fitting better anyway.
                */
		$g=1;
		$l=.57721566490153;
		for($i=1;$i<1000;$i++)
			$g*=1/(1+$a/$i)*exp($a/$i);
		$g*=exp(-$l*$a)/$a;
		return $g;
	}
	// for higher values, we use the Stirling function.
	return stir($a-1);
}

// chi-square distribution
function chi2($k,$a) {
	if($a<=0)
		return NULL;
	return pow(.5,$k/2)/gamma($k/2)*pow($a,$k/2-1)*exp(-$a/2);
}

// chi distribution
function chi($k,$a) {
	if($a<=0)
		return NULL;
	return pow(2,1-$k/2)*pow($a,$k-1)*exp(-$a*$a/2)/gamma($k/2);
}

// Riemann zeta function
function zeta($a) {
	if($a<=0)
		return NULL;
	$z=0;
	for($i=1;$i<1000;$i++)
		$z+=1/pow($i,$a);
	return $z;
}

// Student's t-distribution
function stud($k,$a) {
	if($k<=0)
		return NULL;
	return gamma(($k+1)/2)/sqrt($k*M_PI)/gamma($k/2)*pow(1+$a*$a/$k,-($k+1)/2);
}

// Gauss-Kuzmin distribution
function gk($a) {
	if($a<=0)
		return NULL;
	return -nlogn(2,1-1/($a+1)/($a+1));
}

// geometric distribution (variant A)
function geo($p,$a) {
	if($a>=1)
		return NULL;
	return pow(1-$p,$a-1)*$p;
}

// Poisson distribution
function poi($l,$a) {
	if($a<0 || $l<=0)
		return NULL;
	$a=round($a);
	return exp(-$l)*pow($l,$a)/fac($a);
}

// beta function
function beta($a1,$a2) {
	return gamma($a1)*gamma($a2)/gamma($a1+$a2);
}

// Yule-Simon distribution
function yule($p,$a) {
	if($p<=0 || $a<1)
		return NULL;
	return $p*beta($a,$p+1);
}

// F distribution
function F($d1,$d2,$a) {
	if($a<0)
		return NULL;
	return pow(pow($d1*$a,$d1)*pow($d2,$d2)/(pow($d1*$a+$d2,$d1+$d2)),1/2)/($a*beta($d1/2,$d2/2));
}

// Fisher's z distribution
function Fz($d1,$d2,$a) {
	if($a<=0)
		return NULL;
	return .5*nlog(F($d1,$d2,$a));
}

// Fisher-Tippett distribution
function Ft($m,$b,$x) {
	$z=exp(-($x-$m)/$b);
	return $z*exp(-$z)/$b;
}

// gamma distribution
function gammad($k,$t,$a) {
	if($a<=0)
		return NULL;
	return pow($a,$k-1)*exp(-$a/$t)/pow($t,$k)/gamma($k);
}

// beta distribution
function betad($a,$b,$x) {
	if($x<0 || $x>1 || $a<0 || $b<0)
		return NULL;
	return pow($x,$a-1)*pow(1-$x,$b-1)/beta($a,$b);
}

// beta prime distribution
function betap($a,$b,$x) {
	if($x<=0 || $a<=0 || $b<=0)
		return NULL;
	return pow($x,$a-1)*pow(1+$x,-$a-$b)/beta($a,$b);
}

// inverse-chi-square distribution
function ichi2($k,$a) {
	if($a<=0)
		return NULL;
	return pow(2,-$k/2)/gamma($k/2)*pow($a,-$k/2-1)*exp(-1/$a/2);
}

// inverse Gaussian distribution
function igauss($m,$l,$a) {
	if($a<=0)
		return NULL;
	return pow($l/(2*M_PI*$a*$a*$a),1/2)*exp((-$l*($a-$m)*($a-$m))/(2*$m*$m*$a));
}

// inverse-gamma distribution
function igammad($a,$b,$x) {
	if($x<=0)
		return NULL;
	return pow($b,$a)/gamma($a)*pow($x,-$a-1)*exp(-$b/$x);
}

// Pareto distribution
function par($k,$m,$a) {
	if($a<=$m)
		return NULL;
	return $k*pow($m,$k)/pow($a,$k+1);
}

// Pearson distribution
function pear($a,$b,$p,$x) {
	if($x<$a)
		return NULL;
	return 1/$b/gamma($p)*pow(($x-$a)/$b,$p-1)*exp(-($x-$a)/$b);
}

// relativistic Breit-Wigner distribution
function brw($m,$g,$e) {
	return 1/(pow($e*$e-$m*$m,2)+$m*$m*$g*$g);
}

// triangular distribution
function trid($a,$c,$b,$x) {
	if($a<=$x && $x<=$c)
		return 2*($x-$a)/($b-$a)/($c-$a);
	if($c<=$x && $x<=$b)
		return 2*($b-$x)/($b-$a)/($b-$c);
	return NULL;
}

// Gumbel 1 distribution
function gum1($a,$b,$x) {
	return $a*$b*exp(-($b*exp(-$a*$x)+$a*$x));
}

// Gumbel 2 distribution
function gum2($a,$b,$x) {
	if($x<=0)
		return NULL;
	return $a*$b*pow($x,-$a-1)*exp(-$b*pow($x,-$a));
}

// uniform distribution
function uni($a,$b,$x) {
	if($a<$x && $x<$b)
		return 1/($b-$a);
	if($x<$a || $x>$b)
		return 0;
	return NULL;
}

// iteration previous function value
function y($a) {
	global $iter;
	if ($iter=='')
		$iter=$a;
	return $iter;
}

// iteration pre-previous function value
function y2($a) {
	global $iter2;
	if ($iter2=='')
		$iter2=$a;
	return $iter2;
}

// iteration steps
function step($a) {
	global $istep;
	return $istep/$a;
}

// Mandelbrot function
function man($a,$b) {
	return y($a)*y($a)+$b;
}

// Gompertz curve
function gom($a,$b,$c,$x) {
	return $a*exp($b*exp($c*$x));
}

// random number with decimal places
function mt_rand2($a,$b,$c) {
	return mt_rand($a*pow(10,$c),$b*pow(10,$c))/pow(10,$c);
}

// sine cardinalis
function sinc($a) {
	if($a==0)
		return 1;
	return sin($a)/$a;
}

// Dirichlet eta function
function eta($a) {
	if($a<=0)
		return NULL;
	return (1-pow(2,1-$a))*zeta($a);
}

// condition function: 1 if $t in [$a, $b], 0 else
// parameters are 
//     lower limit $a,
//     upper limit $b,
//     variable $t
function con($a,$t,$b) {
	if($a>$t || $t>$b)
		return 0;
	return 1;
}

// reverse condition function: 0 if $t in [$a, $b], 1 else
// parameters are 
//     lower limit $a,
//     upper limit $b,
//     variable $t
function rcon($a,$t,$b) {
	return 1-con($a,$t,$b);
}

// weighted condition function
function wcon($a,$t,$b) {
	if($a>$t || $t>$b)
		return NULL;
	return $t;
}

// reverse weighted condition function
function rwcon($a,$t,$b) {
	if($a>$t || $t>$b)
		return $t;
	return NULL;
}

// Gaussian error function
function erf($a) {
	if(abs($a)>4)
		return sig($a);
	$res=0;
	for($i=0;$i<100;$i++)
		$res+=pow(-1,$i)*pow($a,2*$i+1)/(fac($i)*(2*$i+1));
	return $res*2/sqrt(M_PI);
}

// Lambert-W function (approximation)
function omega($a) {
	if($a<=-1)
		return NULL;
	if ($a<=500)
		return .665*(1+.0195*log($a+1))*log($a+1)+.04;
	return log($a-4)-(1-1/log($a))*log(log($a));
}

// Langevin function
function L($a) {
	return coth($a)-1/$a;
}

// tanc function
function tanc($a) {
	if($a==0)
		return 1;
	return tan($a)/$a;
}

// digamma function
function digamma($a) {
	return D(gamma($a))/gamma($a);
}

// bump function psi
function bump($a) {
	if(abs($a)>=1)
		return 0;
	return exp(-1/(1-$a*$a));
}

// Nakagami distribution
function nak($m,$o,$a) {
	if($a<0)
		return NULL;
	return 2*pow($m,$m)/(gamma($m)*pow($o,$m))*pow($a,2*$m-1)*exp(-$m/$o*$a*$a);
}

// Shifted Gompertz distribution
function shg($b,$e,$a) {
	if($b<0 || $e<0 || $a<0)
		return NULL;
	return $b*exp(-$b*$a)*exp(-$e*exp(-$b*$a))*(1+$e*(1-exp(-$b*$a)));
}

// logarithmic series distribution
function nlogs($p,$k) {
	if($p<0 || $p>1 || $k<1)
		return NULL;
	return -1/log(1-$p)*pow($p,$k)/$k;
}

// binomial distribution
function bind($n,$p,$k) {
	if($p<0 || $p>1 || $n<0 || $k<0)
		return NULL;
	$n=round($n);
	$k=round($k);
	return fac($n)/(fac($k)*fac($n-$k))*pow($p,$k)*pow(1-$p,$n-$k);
}

// negative binomial distribution
function nbin($r,$p,$k) {
	if($p<0 || $p>1 || $r<0 || $k<0)
		return NULL;
	$k=round($k);
	return gamma($r+$k)/fac($k)/gamma($r)*pow($p,$r)*pow(1-$p,$k);
}

// Zipf distribution
function zipf($s,$k) {
	if($s<=1 || $k<1)
		return NULL;
	return pow($k,-$s)/zeta($s);
}

// Blancmange curve
function blanc($a,$iters) {
	$res=0;
	if($iters>1000)
		$iters=1000;
	for($i=0;$i<$iters;$i++) 
		$res+=dist(pow(2,$i)*$a)/pow(2,$i);
	return $res;
}

// Takagi-Landsberg curve
function tak($a,$o,$iters) {
	$res=0;
	if($iters>1000)
		$iters=1000;
	for($i=0;$i<$iters;$i++) 
		$res+=dist(pow(2,$i)*$a)*pow($o,$i);
	return $res;
}

// Weierstrass function
function wf($x,$a,$b,$iters) {
	if($a<=0 || $a>=1)
		return NULL;
	if($b<=0 || $b!=round($b) || $b/2==round($b/2))
		return NULL;
	if($a*$b<=1+3/2*M_PI)
		return NULL;
	$res=0;
	if($iters>100)
		$iters=100;
	for($i=0;$i<$iters;$i++)
		$res+=pow($a,$i)*cos(pow($b,$i)*M_PI*$x);
	return $res;
}

// Rossi distribution
function rossi($c1,$c2,$d1,$d2,$a) {
	$s1=($a-$c1)/$d1;
	$s2=($a-$c2)/$d2;
	return (exp($s1)/$d1+exp($s2)/$d2)*exp(-exp(-$s1))*exp(-exp(-$s2));
}

// generalized extreme value distribution
function gen($m,$s,$x,$a) {
	if ($s<=0 || 1+$x*($a-$m)/$s<=0)
		return NULL;
	return 1/$s*(pow(1+$x*(($a-$m)/$s),-1/$x-1))*exp(-pow((1+$x*(($a-$m)/$s)),-1/$x));
}

// Skellam distribution
function skel($m1,$m2,$a) {
	if($m1<0 || $m2<0)
		return 999999;
	$a=round($a);
	$res=0;
	for($i=-20;$i<=20;$i++)
		$res+=pow($m1,$a+$i)*pow($m2,$i)/(fac($i)*fac($a+$i));
	return $res*exp(-($m1+$m2));
}

// binomial coefficient
function bin($n,$k) {
	$n=round($n);
	$k=round($k);
	if($k<0 || $k>$n)
		return 0;
	return fac($n)/(fac($k)*fac($n-$k));
}

// Hypergeometric distribution
function hgeo($N,$m,$n,$a) {
	$N=round($N);
	$m=round($m);
	$n=round($n);
	if($m>$N || $n>$N)
		return NULL;
	$a=round($a);
	return bin($m,$a)*bin($N-$m,$n-$a)/bin($N,$n);
}

// Zipf-Mandelbrot law
function zm($n,$q,$s,$a) {
	if($q<0 || $s<=0)
		return 999999;
	if($a<=0)
		return NULL;
	$res=0;
	if($n>100)
		$n=100;
	for($i=1;$i<=$n;$i++) 
		$res+=1/pow($i+$q,$s);
	return (1/pow($a+$q,$s))/$res;
}

// Scale-inverse-chi-square distribution
function sichi2($n,$s,$a) {
	if($a<=0)
		return NULL;
	if($n<=0 || $s<=0)
		return 999999;
	return pow($s*$n/2,$n/2)/gamma($n/2)*exp(-$n*$s/(2*$a))/pow($a,1+$n/2);
}

// Fibonacci numbers
function fib($a,$b) {
	if($a<=0)
		return NULL;
	$res=(pow(1.6180339887499,$a)/sqrt(5));
	if($b==1)
		return $res;
	return round($res);
}

// Ramanujan theta function
function theta($a,$b) {
	if(abs($a)>1 || abs($b)>1)
		return NULL;
	$res=0;
	for($i=-500;$i<=500;$i++)
		$res+=pow($a,$i*($i+1)/2)*pow($b,$i*($i-1)/2);
	return $res;
}

// prime number function
function prime($a) {
	if($a>100000)
		return 999999;
	$a=round($a);
	if($a<2)
		return NULL;
	if($a<3)
		return 2;
	if($a<5)
		return 3;
	if($a<7)
		return 5;
	if($a/2==intval($a/2))
		return prime($a-1);
	for($i=3;$i<=intval(sqrt($a));$i+=2) {
		if($a/$i==intval($a/$i))
			return prime($a-1);
	}
	return $a;
}

// prime number detecting function
function prime1($a) {
	$a=round($a);
	if($a<2)
		return NULL;
	return con(.5,prime3($a),1.5)*$a;
}

// distinct prime factor counting function
function prime2($a) {
	$a=round($a);
	if($a<2)
		return NULL;
	$i=2;
	$count=0;
	$primes=array();
	while($a>1) {
		if (($a/$i)==floor($a/$i)) {
			$primes[$count]=$i;
			++$count;
			$a=$a/$i;
		}
		else
			++$i;
		
	}
	return count(array_unique($primes));
}

// prime factor counting function
function prime3($a) {
	$a=round($a);
	if($a<2)
		return NULL;
	$i=2;
	$count=0;
	while($a>1) {
		if (($a/$i)==floor($a/$i)) {
			++$count;
			$a=$a/$i;
		}
		else
			++$i;
		
	}
	return $count;
}

// random singular function 
function rsf($a,$b) {
	return y($a)+.008*mt_rand(0,1)*mt_rand(0,1)*($b-$a);
}

// iterated arithmetic mean
function mean($a) {
	global $average;
	global $isteps;
	++$isteps;
	$average+=$a;
	return $average/$isteps;
}

// lemniscate of Bernoulli
function lmn($a,$x) {
	$b=2*$x*$x+2*$a*$a;
	$c=(-$b+sqr($b*$b-4*($x*$x*$x*$x-2*$a*$a*$x*$x)))/2;
	if($c<0)
		return NULL;
	if(!$x)
		return 0;
	return sqrt($c);
}

// lemniscate of Gerono
function lmn2($x) {
	if(abs($x)>1)
		return NULL;
	return sqrt($x*$x-$x*$x*$x*$x);
}

// lemniscate of Booth
function lmn3($a,$x) {
	$b=2*$x*$x+4-4*$a;
	$c=(-$b+sqr($b*$b-4*($x*$x*$x*$x-4*$a*$x*$x)))/2;
	if($c<0)
		return NULL;
	if(!$x)
		return 0;
	return sqrt($c);
}

// divisor function
function div($x) {
	$x=round($x);
	if($x<1)
		return NULL;
	$count=0;
	for($i=1;$i<=$x;$i++) {
		if($x/$i==intval($x/$i))
			++$count;
	}
	return $count;
}

// digit sum
function dig($x) {
	$x=round(sig($x)*$x);
	$x=strval($x);
	$dsum=0;
	for($i=0;$i<strlen($x);$i++)
		$dsum+=intval(substr($x,$i,1));
	return $dsum;
}

// iterated digit sum
function dig2($x) {
	do
		$x=dig($x);
	while($x>9);
	return $x;
}

// alternating digit sum
function adig($x) {
	$x=round(sig($x)*$x);
	$x=strrev(strval($x));
	$dsum=0;
	for($i=0;$i<strlen($x);$i++)
		$dsum=$dsum+intval(substr($x,$i,1))*pow(-1,$i);
	return $dsum;
}

// serpentine curve
// M. Oettinger 2020
function srp($a,$b,$x) {
	return $a*$b*$x/($x*$x+$a*$a);
}


// greatest common factor, ggt
function gcf($m,$n) {
	$m=round($m);
	$n=round($n);
	if ($n==0)
		return $m;
	else
		return gcf($n, $m%$n);
}

// least common multiple, kgv
function lcm($m,$n) {
	if($m<0 || $n<0)
		return NULL;
	$m=round($m);
	$n=round($n);
    return $m*$n/gcf($m,$n);
}

// Euler's totient function
function toti($n) {
	if($n<0)
		return NULL;
	$n=round($n);
	$count=0;
	for($i=2;$i<$n;$i++) {
		if(gcf($n,$i)==1)
			++$count;
	}
	return $count;
}

// find odd numbers
function odd($a) {
	$a=round($a);
	if($a/2!=round($a/2))
		return $a;
	return NULL;
}

// find even numbers
function even($a) {
	$a=round($a);
	if($a/2==round($a/2))
		return $a;
	return NULL;
}

// trajectory parabola
function traj($b,$v,$g,$a) {
	if($a<0 || $b<=-90 || $b>=90)
		return NULL;
	return tan(deg2rad($b))*$a-$g/(2*$v*$v*cos2(deg2rad($b)))*$a*$a;
}

// characteristic boolean function
function bool($a) {
	if($a)
		return 1;
	else if(is_numeric($a))
		return 0;
	else return NULL;
}

// defined boolean function
function bool0($a) {
	if($a)
		return 1;
	return 0;
}

// undefined boolean function
function bool1($a) {
	if($a)
		return 1;
	return NULL;
}

// Gaussian bell-shaped curve
function bsc($a,$x) {
	return exp(-$a*$a*$x*$x);
}

// generalized Gaussian bell-shaped curve
function gbsc($a,$b,$c,$x) {
	return $a*exp($b*$x+$c*$x*$x);
}

// Haar wavelet
function haar($x) {
	if($x<0 || $x>=1)
		return 0;
	if($x<.5)
		return 1;
	return -1;
}

// Moebius function
function mo($x) {
	$x=round($x);
	if($x<=0 || x>100000)
		return NULL;
	for($i=2;$i<=sqrt($x);$i++) {
		if($x/($i*$i)==intval($x/($i*$i)))
			return 0;
	}
	return pow(-1,prime2($x));
}

// parallel operator
function pll() {
	$a=func_get_args();
	if(sizeof($a)<1)
		return NULL;
	$b=0;
	foreach ($a as $c) {
		if($c==0)
			return 0;
		$b+=1/$c;
	}
	return 1/$b;
}

// arithmetic mean
function M1() {
	$a=func_get_args();
	if(sizeof($a)<1)
		return NULL;
	$b=0;
	foreach ($a as $c)
		$b+=$c;
	return $b/count($a);
}

// geometric mean
function M2() {
	$a=func_get_args();
	if(sizeof($a)<1)
		return NULL;
	$b=1;
	foreach ($a as $c) {
		if($c<0)
			return NULL;
		if($c==0)
			return 0;
		$b*=$c;
	}
	return pow($b,1/count($a));
}

// harmonic mean
function M3() {
	$a=func_get_args();
	if(sizeof($a)<1)
		return NULL;
	$b=0;
	foreach ($a as $c) {
		if($c<0)
			return NULL;
		if($c==0)
			return 0;
		$b+=1/$c;
	}
	return count($a)/$b;
}

// root mean square
function M4() {
	$a=func_get_args();
	if(sizeof($a)<1)
		return NULL;
	$b=0;
	foreach ($a as $c)
		$b+=$c*$c;
	return sqrt($b/count($a));
}

// median
function M5() {
	$a=func_get_args();
	if(sizeof($a)<1)
		return NULL;
	sort($a);
	if(count($a)==odd(count($a)))
		return $a[(count($a)+1)/2-1];
	else
		return ($a[count($a)/2-1]+$a[count($a)/2])/2;
}
