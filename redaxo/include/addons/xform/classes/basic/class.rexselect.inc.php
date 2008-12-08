<?php

// class rexselect 1.0 [redaxo]
// 
// erstellt 01.12.2001
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

################ Class SQL
class rexselect{

	var $select_name;		// 
	var $counter;			// 
	var $option_name;		// 
	var $option_value;		//
	var $option_selected;	//
	var $option_anzahl;		//
	var $select_size;		// 
	var $select_multiple;	//
	var $style;

	################ Konstruktor
	function rexselect(){
		$this->counter		= 0;
		$this->select_name	= "standard";
		$this->select_size	= 1;
		$this->mul			= "";
		$this->option_selected = "";
		$this->option_anzahl= 0;
	}

	############### multiple felder ? 
	function setMultiple($mul){
		if ($mul == 1){
			$this->select_multiple = " multiple";
		}else{
			$this->select_multiple = "";
		}
	}

	################ init 
	function init(){
		$this->counter		= 0;
		$this->select_name	= "standard";
		$this->select_size	= 5;
		$this->select_multiple	= "";
		unset($this->option_selected);
		$this->option_anzahl= 0;
		
	}
	
	################ select name
	function setName($name){
		$this->select_name	= $name;
	}

	################ select name
	function setStyle($style){
		$this->style	= $style;
	}
	
	################ select size
	function setSize($size){
		$this->select_size	= $size;
	}

	################ selected feld - option value uebergeben
	function setSelected($selected){
		$this->option_selected[]	= $selected;
		$this->option_anzahl++;
	}
	
	function resetSelected()
	{
		unset($this->option_selected);
		$this->option_anzahl= 0;
	}
	
	################ optionen hinzufuegen
	function addOption($name,$value){
		$this->option_name[$this->counter]	= $name;
		$this->option_value[$this->counter]	= $value;
		$this->counter++;
	}
	
	############### show select
	function out(){
	
		global $STYLE;
		$ausgabe = "<select $STYLE ".$this->select_multiple." name='".$this->select_name."' size='".$this->select_size."' style='".$this->style."'>\n";
		for ($i=0;$i<$this->counter;$i++){
		
			// if ($this->option_name[$i] != ""){
				$ausgabe .= "<option value='".$this->option_value[$i]."'";
				
				for ($j=0;$j<$this->option_anzahl;$j++){			
					if ($this->option_selected[$j] == $this->option_value[$i]){
						$ausgabe .= " selected";
						$j=1000;
					}
				}
	
				$ausgabe .= ">".$this->option_name[$i]."</option>\n";
			// }		
		}
		$ausgabe .= "</select>";	
		return $ausgabe;	
	}
}

?>