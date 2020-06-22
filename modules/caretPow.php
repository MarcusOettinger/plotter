<?php
// parsing is needed for PHP<5.6 - then an operator
// ** was added - very nice!
//
/* parse ^ as pow
made by Matthias Bilger
this is the only OOP part of the program
NOTE: this is way too complicated - PHP knows an ** operator!!
==============================================================
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
?>
