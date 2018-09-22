<?php
/*
modules/init.php: perform some sanity checks on function Terms,
  transform expressions into an evaluable form and calculate function
  values.
Required by: graph.php, single.php

Copyright (C) 2015 Marcus Oettinger,
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

/* parse ^ as pow
made by Matthias Bilger
this is the only OOP part of the program */
$opsh = array("*", "/", "%");
$opsl = array("+", "-", ",", "#");
$ops = array("*", "/","+", "-", ",", "#", "%");
$pow = "^";
class term{

	private $cont = "";
	private $literal = true;
	private $brackets = false;

	private $tl = null;
	private $tr = null;
	private $lvl = 0;

	public function __construct($term, $level=0) {
		$term = str_replace(" ", "", $term);
		$this->ParseLine($term);
		$this->lvl = $level;
	}

	private function ParseLine($str){
		$index = $this->SplitIt($str);
		if($index == -1){
			$this->cont = $str;
			$this->literal = true;
		}
	}
	private function AddChilds($str, $index){
		$this->cont = substr($str, $index, 1);	
		$this->tl = new term(substr($str, 0, $index), $this->lvl + 1);
		$this->tr = new term(substr($str, $index+1), $this->lvl + 1);
		$this->literal = false;
	}
	private function SplitIt(&$str){
		global $opsh,$opsl,$ops, $fcts, $unary, $pow;
		$index = -1;
		$openbrackets = 0;
		$outerbrackets = 0;
		$outerbrackets_number = 0;
		$positions = array();
		do{
			for($i = strlen($str)-1; $i >= 0 ; $i--){
				if ($str[$i] == "("){
					$openbrackets++;
					if($openbrackets == 0){
						$outerbrackets++;
						array_push($positions, $i);
					}
				}
				else if ($str[$i] == ")"){
					$openbrackets--;
				}
				else if($openbrackets == 0){
					array_push($positions, $i);
				}
			}
		}while($outerbrackets == ++$outerbrackets_number && $this->RemoveUnusedBrackets($str));
		if($index == -1){
			for($i = 0; $i < count($positions) ; $i++){
				if(in_array($str[$positions[$i]], $opsl)){
					$index = $positions[$i];
					$this->AddChilds($str, $index);
				}
			}
		}
		
		if($index == -1){
			for($i = 0; $i < count($positions) ; $i++){
				if(in_array($str[$positions[$i]], $opsh)){
					$index = $positions[$i];
					$this->AddChilds($str, $index);
				}
			}
		}
		if($index == -1){
			for($i = 0; $i < count($positions) ; $i++){
				if($str[$positions[$i]] == $pow){
					$index = $positions[$i];
					$this->AddChilds($str, $index);
				}	
			}
		}
		if($index == -1 && $outerbrackets == $outerbrackets_number)	{

			$tmppos =  strpos($str, "(");
			$tmp = substr($str, 0, $tmppos);
			if($tmppos != 0){
				$this->cont = substr($str,0, $tmppos);
				$tmppos++;
				$this->tr = new term(substr($str, $tmppos, strlen($str)-$tmppos-1));
				$this->literal = false;
				$index = $tmppos;
			}
		}
		return $index;
	}

	private function RemoveUnusedBrackets(&$str){
		$tmp = $str;
		if($str[0] == "(" && $str[strlen($str)-1] == ")"){
			$str = substr($str, 1, strlen($str)-2);
			$this->brackets = true;
		}
		return ($tmp != $str);
	}

	public function ContainsBrackets(){
		return $this->brackets;
	}

	public function ToString(){
		global $ops, $fcts, $unary, $pow;
  	if($this->literal == true)
		{
			return (string)$this->cont;
		}
		else{
			if($this->cont == $pow){
				return "pow(".$this->tl->ToString().",".$this->tr->ToString().")";
			}
			else if($this->cont == "#" || $this->cont == ","){
				return $this->tl->ToString().",".$this->tr->ToString();
			}
			else if($this->tl == null)
			{
				return $this->cont."(".$this->tr->ToString().")";
			}
			else
			{
				return (($this->lvl != 0 && $this->brackets)?"(":"").$this->tl->ToString()."".$this->cont."".$this->tr->ToString().(($this->lvl != 0 && $this->brackets)?")":"");
			}
		}
	}
};

// add multiplication operator where omitted, e.g. 3x -> 3*x
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
	$text1='Fehlerhafte Formel ';
	$text2='Illegaler Wertebereich Formel ';
	$text3='Kein Wert gefunden oder';
	$text4='Fehler in Formel ';
	$text5='Klammerfehler in Formel ';
	$text6='Zu viele D oder S in Formel ';
} else {//English
	$text1='Defective formula ';
	$text2='Illegal range in formula ';
	$text3='No results found or';
	$text4='error in formula ';
	$text5='Bracket error in formula ';
	$text6="Too many D or S in formula ";
}

// read formulae into array
$formula[0]=rawurldecode($formula1);
$formula[1]=rawurldecode($formula2);
$formula[2]=rawurldecode($formula3);

// make some necessary changes in the formulae
for($i=0;$i<3;$i++) {
	// delete spaces
	$formula[$i]=str_replace(' ','',$formula[$i]);
	$Y=str_replace(' ','',$Y);
	$qq=str_replace(' ','',$qq);
	// add multiplication signs
	$Y=multisign($Y);
	$qq=multisign($qq);
	$formula[$i]=multisign($formula[$i]);
	// insert defined formula for substitutions Y and Q,
	if($Y && $formula[$i])
		$formula[$i]=str_replace('Y','('.$formula[$i].')',$Y);
	$formula[$i]=str_replace('Q','('.$qq.')',$formula[$i]);
	// transform redundant symbols
	$formula[$i]=str_replace('D1(','D(',$formula[$i]);
	$formula[$i]=str_replace('D01(','D0(',$formula[$i]);
	$formula[$i]=str_replace('S1(','S(',$formula[$i]);
	$formula[$i]=str_replace(':','/',$formula[$i]);
	$formula[$i]=str_replace('[','(',$formula[$i]);
	$formula[$i]=str_replace(']',')',$formula[$i]);
	$formula[$i]=str_replace('{','(',$formula[$i]);
	$formula[$i]=str_replace('}',')',$formula[$i]);
	$formula[$i]=str_replace('<','(',$formula[$i]);
	$formula[$i]=str_replace('>',')',$formula[$i]);
	$formula[$i]=str_replace(',','.',$formula[$i]);
	$formula[$i]=str_replace('#',',',$formula[$i]);
	// ggt is the German term for gcf
	$formula[$i]=str_replace('ggt','gcf',$formula[$i]);
	// kgv is the German term for lcm
	$formula[$i]=str_replace('kgv','lcm',$formula[$i]);
	// replace standard trigonometric and hyperbolic terms to those used internally
	// this is to make the standard terms work as well as the formerly used terms
	$formula[$i]=str_replace("sechd","scahd",$formula[$i]);
	$formula[$i]=str_replace("arsinh","asinh",$formula[$i]);
	$formula[$i]=str_replace("arcosh","acosh",$formula[$i]);
	$formula[$i]=str_replace("artanh","atanh",$formula[$i]);
	$formula[$i]=str_replace("arcoth","acoth",$formula[$i]);
	$formula[$i]=str_replace("arcosech","arcsch",$formula[$i]);
	$formula[$i]=str_replace("arsech","arscah",$formula[$i]);
	$formula[$i]=str_replace("arcsin","asin",$formula[$i]);
	$formula[$i]=str_replace("arccos","acos",$formula[$i]);
	$formula[$i]=str_replace("arctan","atan",$formula[$i]);
	$formula[$i]=str_replace("arccot","acot",$formula[$i]);
	$formula[$i]=str_replace("arccosec","acsc",$formula[$i]);
	$formula[$i]=str_replace("arcsec","asca",$formula[$i]);
	$formula[$i]=str_replace("cosech","csch",$formula[$i]);
	$formula[$i]=str_replace("sech","scah",$formula[$i]);
	$formula[$i]=str_replace("cosec","csc",$formula[$i]);
	$formula[$i]=str_replace("sec","sca",$formula[$i]);
	// catch 0 as a function
	if($formula[$i]=='0') $formula[$i]='0*1';
	// convert ^ to pow()
	if($formula[$i]!=str_replace("^","",$formula[$i])) {
		$t = new term($formula[$i]);
		$formula[$i]=$t->ToString();
	}
	// look for bracket errors
	$bracketerror[$i]=0;
	$brleft=strlen($formula[$i])-strlen(str_replace("(","",$formula[$i]));
	$brright=strlen($formula[$i])-strlen(str_replace(")","",$formula[$i]));
	if($brleft!=$brright) $bracketerror[$i]=1;
}

/* sanity check of  formulae:
calculations are performed  via 'eval', so possible malicious code must be stripped.
The following code only allows for functions and characters the program knows.
To check, allowed combinations are removed from a test string and if anything is
left in the end, the program  stops.
*/
for($i=0;$i<3;$i++) {
	// stop at more than one D, D2, D3, D0, D02, D03, or more than any one S in formula
	if((strlen($formula[$i])-strlen(str_replace('D(','',$formula[$i]))>2 || strlen($formula[$i])-strlen(str_replace('D2(','',$formula[$i]))>3 || strlen($formula[$i])-strlen(str_replace('D3(','',$formula[$i]))>3 || strlen($formula[$i])-strlen(str_replace('D0(','',$formula[$i]))>3 || strlen($formula[$i])-strlen(str_replace('D02(','',$formula[$i]))>4 || strlen($formula[$i])-strlen(str_replace('D03(','',$formula[$i]))>4 || strlen($formula[$i])-strlen(str_replace('S','',$formula[$i]))>1) && !$single) {
		if($errors) {
			imagefill ($img,0,0,$color[$bg]);
			imagestring ($img,5,$height/2-80,100,$text6.($i+1),$color[$col[$i]]);
		}
		ImagePng($img);
		die();
	}

	// check for correct formulae
	$test=$formula[$i];
	$test=str_replace('digamma','',$test);
	$test=str_replace('igammad','',$test);
	$test=str_replace('arcsch','',$test);
	$test=str_replace('arscah','',$test);
	$test=str_replace('gammad','',$test);
	$test=str_replace('igauss','',$test);
	$test=str_replace('lambda','',$test);
	$test=str_replace('sichi2','',$test);
	$test=str_replace('acosh','',$test);
	$test=str_replace('acoth','',$test);
	$test=str_replace('asinh','',$test);
	$test=str_replace('atanh','',$test);
	$test=str_replace('betad','',$test);
	$test=str_replace('betap','',$test);
	$test=str_replace('blanc','',$test);
	$test=str_replace('gamma','',$test);
	$test=str_replace('hlogd','',$test);
	$test=str_replace('lnorm','',$test);
	$test=str_replace('omega','',$test);
	$test=str_replace('prime','',$test);
	$test=str_replace('rossi','',$test);
	$test=str_replace('rwcon','',$test);
	$test=str_replace('scahd','',$test);
	$test=str_replace('theta','',$test);
	$test=str_replace('acos','',$test);
	$test=str_replace('acot','',$test);
	$test=str_replace('acsc','',$test);
	$test=str_replace('adig','',$test);
	$test=str_replace('asca','',$test);
	$test=str_replace('asin','',$test);
	$test=str_replace('atan','',$test);
	$test=str_replace('beta','',$test);
	$test=str_replace('bind','',$test);
	$test=str_replace('bool','',$test);
	$test=str_replace('bump','',$test);
	$test=str_replace('cosd','',$test);
	$test=str_replace('cosh','',$test);
	$test=str_replace('coth','',$test);
	$test=str_replace('csch','',$test);
	$test=str_replace('dist','',$test);
	$test=str_replace('even','',$test);
	$test=str_replace('fmod','',$test);
	$test=str_replace('gbsc','',$test);
	$test=str_replace('haar','',$test);
	$test=str_replace('hgeo','',$test);
	$test=str_replace('hubb','',$test);
	$test=str_replace('ichi','',$test);
	$test=str_replace('lapc','',$test);
	$test=str_replace('levy','',$test);
	$test=str_replace('logd','',$test);
	$test=str_replace('logn','',$test);
	$test=str_replace('logs','',$test);
	$test=str_replace('mean','',$test);
	$test=str_replace('nbin','',$test);
	$test=str_replace('norm','',$test);
	$test=str_replace('pear','',$test);
	$test=str_replace('poly','',$test);
	$test=str_replace('pyth','',$test);
	$test=str_replace('ramp','',$test);
	$test=str_replace('rand','',$test);
	$test=str_replace('rect','',$test);
	$test=str_replace('rcon','',$test);
	$test=str_replace('rlgh','',$test);
	$test=str_replace('rlng','',$test);
	$test=str_replace('scah','',$test);
	$test=str_replace('scir','',$test);
	$test=str_replace('sinc','',$test);
	$test=str_replace('sinh','',$test);
	$test=str_replace('skel','',$test);
	$test=str_replace('step','',$test);
	$test=str_replace('stir','',$test);
	$test=str_replace('stud','',$test);
	$test=str_replace('tanc','',$test);
	$test=str_replace('tanh','',$test);
	$test=str_replace('toti','',$test);
	$test=str_replace('traj','',$test);
	$test=str_replace('trap','',$test);
	$test=str_replace('trid','',$test);
	$test=str_replace('wcon','',$test);
	$test=str_replace('yule','',$test);
	$test=str_replace('zeta','',$test);
	$test=str_replace('zipf','',$test);
	$test=str_replace('abs','',$test);
	$test=str_replace('asy','',$test);
	$test=str_replace('bin','',$test);
	$test=str_replace('brw','',$test);
	$test=str_replace('bsc','',$test);
	$test=str_replace('cat','',$test);
	$test=str_replace('cau','',$test);
	$test=str_replace('chi','',$test);
	$test=str_replace('con','',$test);
	$test=str_replace('cos','',$test);
	$test=str_replace('cot','',$test);
	$test=str_replace('csc','',$test);
	$test=str_replace('deg','',$test);
	$test=str_replace('dig','',$test);
	$test=str_replace('div','',$test);
	$test=str_replace('ell','',$test);
	$test=str_replace('erf','',$test);
	$test=str_replace('eta','',$test);
	$test=str_replace('exp','',$test);
	$test=str_replace('fac','',$test);
	$test=str_replace('fib','',$test);
	$test=str_replace('gcf','',$test);
	$test=str_replace('gen','',$test);
	$test=str_replace('geo','',$test);
	$test=str_replace('gom','',$test);
	$test=str_replace('gum','',$test);
	$test=str_replace('HY4','',$test);
	$test=str_replace('kum','',$test);
	$test=str_replace('lcm','',$test);
	$test=str_replace('lmn','',$test);
	$test=str_replace('log','',$test);
	$test=str_replace('man','',$test);
	$test=str_replace('max','',$test);
	$test=str_replace('min','',$test);
	$test=str_replace('nak','',$test);
	$test=str_replace('odd','',$test);
	$test=str_replace('par','',$test);
	$test=str_replace('phi','',$test);
	$test=str_replace('poi','',$test);
	$test=str_replace('pll','',$test);
	$test=str_replace('pon','',$test);
	$test=str_replace('pow','',$test);
	$test=str_replace('rad','',$test);
	$test=str_replace('rsf','',$test);
	$test=str_replace('saw','',$test);
	$test=str_replace('sca','',$test);
	$test=str_replace('sgm','',$test);
	$test=str_replace('shg','',$test);
	$test=str_replace('sig','',$test);
	$test=str_replace('sin','',$test);
	$test=str_replace('siv','',$test);
	$test=str_replace('srp','',$test);
	$test=str_replace('sq2','',$test);
	$test=str_replace('sqr','',$test);
	$test=str_replace('tak','',$test);
	$test=str_replace('tan','',$test);
	$test=str_replace('thr','',$test);
	$test=str_replace('tri','',$test);
	$test=str_replace('uni','',$test);
	$test=str_replace('wig','',$test);
	$test=str_replace('dc','',$test);
	$test=str_replace('Ft','',$test);
	$test=str_replace('Fz','',$test);
	$test=str_replace('gd','',$test);
	$test=str_replace('gk','',$test);
	$test=str_replace('go','',$test);
	$test=str_replace('Hm','',$test);
	$test=str_replace('mo','',$test);
	$test=str_replace('pi','',$test);
	$test=str_replace('wb','',$test);
	$test=str_replace('wf','',$test);
	$test=str_replace('zm','',$test);
	$test=str_replace('-','',$test);
	$test=str_replace(',','',$test);
	$test=str_replace('.','',$test);
	$test=str_replace('(','',$test);
	$test=str_replace(')','',$test);
	$test=str_replace('*','',$test);
	$test=str_replace('/','',$test);
	$test=str_replace('%','',$test);
	$test=str_replace('+','',$test);
	$test=str_replace('^','',$test);//^ isn't replaced with certain syntax errors in a formula
	$test=str_replace('d','',$test);
	$test=str_replace('D','',$test);
	$test=str_replace('e','',$test);
	$test=str_replace('E','',$test);
	$test=str_replace('F','',$test);
	$test=str_replace('H','',$test);
	$test=str_replace('L','',$test);
	$test=str_replace('M','',$test);
	$test=str_replace('R','',$test);
	$test=str_replace('S','',$test);
	$test=str_replace('x','',$test);
	$test=str_replace('y','',$test);
	$test=preg_replace('/[0-9]/','',$test);

	// stop if anything remains
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
	$formula[$i]=str_replace('exp','yp',$formula[$i]);
	$formula[$i]=str_replace('max','yy',$formula[$i]);
	$formula[$i]=str_replace('x','$a',$formula[$i]);
	$formula[$i]=str_replace('yy','max',$formula[$i]);

	$formula[$i]=str_replace('deg','ggg',$formula[$i]);
	$formula[$i]=str_replace('rad','rrr',$formula[$i]);
	$formula[$i]=str_replace('levy','isl',$formula[$i]);
	$formula[$i]=str_replace('ell','fll',$formula[$i]);
	$formula[$i]=str_replace('zeta','zt',$formula[$i]);
	$formula[$i]=str_replace('rect','rct',$formula[$i]);
	$formula[$i]=str_replace('geo','gxo',$formula[$i]);
	$formula[$i]=str_replace('betad','btta',$formula[$i]);
	$formula[$i]=str_replace('beta','bta',$formula[$i]);
	$formula[$i]=str_replace('theta','thta',$formula[$i]);
	$formula[$i]=str_replace('eta','dic',$formula[$i]);
	$formula[$i]=str_replace('pear','pr',$formula[$i]);
	$formula[$i]=str_replace('yule','yul',$formula[$i]);
	$formula[$i]=str_replace('step','stxp',$formula[$i]);
	$formula[$i]=str_replace('erf','rf',$formula[$i]);
	$formula[$i]=str_replace('omega','omga',$formula[$i]);
	$formula[$i]=str_replace('betap','btap',$formula[$i]);
	$formula[$i]=str_replace('gen','gxn',$formula[$i]);
	$formula[$i]=str_replace('skel','skl',$formula[$i]);
	$formula[$i]=str_replace('prime','rim',$formula[$i]);
	$formula[$i]=str_replace('mean','mxan',$formula[$i]);
	$formula[$i]=str_replace('even','xvxn',$formula[$i]);
	$formula[$i]=str_replace('e','2.718281828459',$formula[$i]);
	$formula[$i]=str_replace('xvxn','even',$formula[$i]);
	$formula[$i]=str_replace('mxan','mean',$formula[$i]);
	$formula[$i]=str_replace('skl','skel',$formula[$i]);
	$formula[$i]=str_replace('gxn','gen',$formula[$i]);
	$formula[$i]=str_replace('btap','betap',$formula[$i]);
	$formula[$i]=str_replace('thta','theta',$formula[$i]);
	$formula[$i]=str_replace('omga','omega',$formula[$i]);
	$formula[$i]=str_replace('rf','erf',$formula[$i]);
	$formula[$i]=str_replace('fll','ell',$formula[$i]);
	$formula[$i]=str_replace('zt','zeta',$formula[$i]);
	$formula[$i]=str_replace('rct','rect',$formula[$i]);
	$formula[$i]=str_replace('gxo','geo',$formula[$i]);
	$formula[$i]=str_replace('bta','beta',$formula[$i]);
	$formula[$i]=str_replace('dic','eta',$formula[$i]);
	$formula[$i]=str_replace('yul','yule',$formula[$i]);
	$formula[$i]=str_replace('stxp','step',$formula[$i]);
	$formula[$i]=str_replace('yp','exp',$formula[$i]);

	$formula[$i]=str_replace('pro','pp',$formula[$i]);
	$formula[$i]=str_replace('pr','pear',$formula[$i]);
	$formula[$i]=str_replace('pp','pro',$formula[$i]);
	$formula[$i]=str_replace('pi2','1.5707963267949',$formula[$i]);
	$formula[$i]=str_replace('pi','3.1415926535898',$formula[$i]);
	$formula[$i]=str_replace('gom','gxm',$formula[$i]);
	$formula[$i]=str_replace('go','1.6180339887499',$formula[$i]);
	$formula[$i]=str_replace('gxm','gom',$formula[$i]);
	$formula[$i]=str_replace('rim','prime',$formula[$i]);

	$formula[$i]=str_replace('lambda','lamba',$formula[$i]);
	$formula[$i]=str_replace('fmod','fmo',$formula[$i]);
	$formula[$i]=str_replace('stud','stu',$formula[$i]);
	$formula[$i]=str_replace('logd','logx',$formula[$i]);
	$formula[$i]=str_replace('cosd','cosx',$formula[$i]);
	$formula[$i]=str_replace('scahd','scahx',$formula[$i]);
	$formula[$i]=str_replace('gammad','gammax',$formula[$i]);
	$formula[$i]=str_replace('trid','trix',$formula[$i]);
	$formula[$i]=str_replace('dc','xc',$formula[$i]);
	$formula[$i]=str_replace('gd','gx',$formula[$i]);
	$formula[$i]=str_replace('rand','ranx',$formula[$i]);
	$formula[$i]=str_replace('digamma','xigamma',$formula[$i]);
	$formula[$i]=str_replace('dist','ist',$formula[$i]);
	$formula[$i]=str_replace('bind','binx',$formula[$i]);
	$formula[$i]=str_replace('div','tiv',$formula[$i]);
	$formula[$i]=str_replace('dig','tig',$formula[$i]);
	$formula[$i]=str_replace('odd','oxx',$formula[$i]);
	$formula[$i]=str_replace('d','4.669201609103',$formula[$i]);
	$formula[$i]=str_replace('oxx','odd',$formula[$i]);
	$formula[$i]=str_replace('tig','dig',$formula[$i]);
	$formula[$i]=str_replace('tiv','div',$formula[$i]);
	$formula[$i]=str_replace('binx','bind',$formula[$i]);
	$formula[$i]=str_replace('ist','dist',$formula[$i]);
	$formula[$i]=str_replace('xigamma','digamma',$formula[$i]);
	$formula[$i]=str_replace('lamba','lambda',$formula[$i]);
	$formula[$i]=str_replace('fmo','fmod',$formula[$i]);
	$formula[$i]=str_replace('stu','stud',$formula[$i]);
	$formula[$i]=str_replace('logx','logd',$formula[$i]);
	$formula[$i]=str_replace('cosx','cosd',$formula[$i]);
	$formula[$i]=str_replace('scahx','scahd',$formula[$i]);
	$formula[$i]=str_replace('gammax','gammad',$formula[$i]);
	$formula[$i]=str_replace('btta','betad',$formula[$i]);
	$formula[$i]=str_replace('trix','trid',$formula[$i]);
	$formula[$i]=str_replace('xc','dc',$formula[$i]);
	$formula[$i]=str_replace('gx','gd',$formula[$i]);
	$formula[$i]=str_replace('ranx','mt_rand',$formula[$i]);
	$formula[$i]=str_replace('isl','levy',$formula[$i]);

	$formula[$i]=str_replace('ggg','rad2deg',$formula[$i]);
	$formula[$i]=str_replace('rrr','deg2rad',$formula[$i]);
	$formula[$i]=str_replace('R0','floor',$formula[$i]);
	$formula[$i]=str_replace('R1','ceil',$formula[$i]);
	$formula[$i]=str_replace('R','round',$formula[$i]);
	$formula[$i]=str_replace('sq2','1.4142135623731',$formula[$i]);

	// new definition for the improvement of those functions included in php
	$formula[$i]=str_replace('pow','npow',$formula[$i]);
	$formula[$i]=str_replace('log','nlog',$formula[$i]);
	$formula[$i]=str_replace('asin','nasin',$formula[$i]);
	$formula[$i]=str_replace('acos','nacos',$formula[$i]);
	$formula[$i]=str_replace('atanh','natanh',$formula[$i]);

	// extracts the value for an upright asymptote and sets as 'asyval' the x-coordinate where the asymptote is drawn
	// then as formula simply 'asy' is preserved
	if(!$single && $formula[$i]!=str_replace('asy(','',$formula[$i])) {
		$formula[$i]=substr($formula[$i],0,strpos($formula[$i],')'));
		$formula[$i]=substr($formula[$i],strpos($formula[$i],'asy'),strlen($formula[$i]));
		$formula[$i]=str_replace('asy','',$formula[$i]);
		$formula[$i]=str_replace('(','',$formula[$i]);
		$formula[$i]=str_replace(')','',$formula[$i]);
		@eval('$formula[$i]='.$formula[$i].';');
		if(is_numeric($formula[$i])) {
			if($logskx)
				$formula[$i]=nlogn($logskx,$formula[$i]);
			$asyval[$i]=round($width/($rulex2-$rulex1)*(abs($rulex1-doubleval($formula[$i]))),0);
		}
		else {
			if($errors)
				imagestring ($img,5,$height/2-80,100,$text1.($i+1),$color[$col[$i]]);
		}
		$formula[$i]='asy';
	}
}

// additional functions - These are not included in PHP:

// pow with odd roots of negative values
function npow($base, $exp) {
	$testodd=(1/$exp);
	if($base<0 && $exp!=intval($exp) && $testodd==intval($testodd) && $testodd/2!=intval($testodd/2))
		return -pow(-$base,$exp);
	if($base<0 && $exp!=intval($exp))
		return NULL;
	return pow($base,$exp);
}

// roots, logarithms
// ------------------------------------------------------------------------
// a square root returning NULL for negative variable values
function sqr($a) { return $a<0?NULL:sqrt($a); }
// natural logarithm returning  NULL for x<=0
function nlog($a) { return $a<0?NULL:log($a); }
// decadic logarithm returning  NULL for x<=0
function nlog10($a) { return $a<0?NULL:log10($a); }

// arcsine with NULL as return value for |x|>1
function nasin($a) {
	if(abs($a)>1)
		return NULL;
	return asin($a);
}

// arccosine with NULL as return value for |x|>1
function nacos($a) {
	if(abs($a)>1)
		return NULL;
	return acos($a);
}

// area hyperbolic sine
function nasinh($a) {
	return asinh($a);
}

// area hyperbolic cosine with NULL as return value for x<1
function nacosh($a) {
	if($a<1)
		return NULL;
	return acosh($a);
}

// area hyperbolic tangent NULL as return value for |x|>1
function natanh($a) {
	if(abs($a)>=1)
		return NULL;
	return atanh($a);
}

// normal distribution (Gaussian),
// parameters are center, standard deviation and variable
function norm($e,$s,$x) {
	return 1/($s*sqrt(2*M_PI))*exp(-.5*(($x-$e)/$s)*(($x-$e)/$s));
}

// logarithmic normal distribution
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

// Cauchi distribution
function cau($t,$s,$x) {
	return $s/(M_PI*($s*$s+($x-$t)*($x-$t)));
}

// Laplace distribution
function lapc($m,$s,$x) {
	if($s<=0)
		return 999999;
	return 1/(2*$s)*exp(-(abs($x-$m)/$s));
}

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

// signum function
function sig($a) {  return $a==0?0:$a/(abs($a)); }

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
function gd($a) {
	return atan(sinh($a));
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
	$a+=.5;
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
		//this is the Euler and Weierstrass definition of the gamma function. It is used here only for low values (< 5.858) of x, because of the heavy computing power needed. From 5.858 upwards, the Stirling function is fitting better anyway.
		$g=1;
		$l=.57721566490153;
		for($i=1;$i<1000;$i++)
			$g*=1/(1+$a/$i)*exp($a/$i);
		$g*=exp(-$l*$a)/$a;
		return $g;
	}
	//for higher values, we use the Stirling function.
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

// condition function
function con($a,$t,$b) {
	if($a>$t || $t>$b)
		return 0;
	return 1;
}

// reverse condition function
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
function srp($a,$b,$x) {
	return $a*$a*$x/($x*$x+$a*$b);
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

//differential and integral functions are in another file
include "modules/diffint.inc";

/* calculate function value
This is the heart of the program, where the main 
calculation is done. It returns the result. */
function graph($a,$expr) {
	global $single;
	global $logsk;
	global $logskx;
	global $iter;
	global $iter2;
	global $istep;
	if($logsk)
		$expr='nlogn('.$logsk.','.$expr.')';
	$a=(double)$a;
	if (!$single && abs($a)>100000)
		return 999999;
	if($logskx)
		$a=pow($logskx,$a);
	@eval('$out='.$expr.';'); //this line does the calculation
	if (is_nan($out))
		return NULL;
	if (!$single && abs($out)>100000)
		return 999999;
	$iter2=$iter;
	$iter=$out;
	++$istep;
	return $out;
}
?>