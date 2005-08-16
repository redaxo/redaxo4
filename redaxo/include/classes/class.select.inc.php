<?php

// class select 1.0 [redaxo]
// 
// erstellt 01.12.2001
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

################ Class SQL
class select{

	var $select_name;		//
    var $select_id; 
//	var $counter;			// 
	var $options;		// 
	var $option_selected;	//
//	var $option_anzahl;		//
	var $select_size;		// 
	var $select_multiple;	//
	var $select_style;

	################ Konstruktor
	function select(){
        $this->init();
	}

	############### multiple felder ? 
	function multiple($mul){
		if ($mul == 1){
			$this->select_multiple = " multiple";
		}else{
			$this->select_multiple = "";
		}
	}

	################ init 
	function init(){
//		$this->counter		= 0;
		$this->select_name	= "standard";
		$this->select_size	= 5;
		$this->select_multiple	= "";
        $this->option_selected = array();
//		$this->option_anzahl= 0;
		
	}
	
	################ select name
	function set_name($name){
		$this->select_name	= $name;
	}

    ################ select id
    function set_id($id){
        $this->select_id  = $id;
    }
    
	################ select style
	function set_style($style){
		$this->select_style	= $style;
	}
	
	################ select size
	function set_size($size){
		$this->select_size	= $size;
	}

	################ selected feld - option value uebergeben
	function set_selected($selected){
		$this->option_selected[]	= $selected;
//		$this->option_anzahl++;
	}
	
	function resetSelected()
	{
//		unset($this->option_selected);
//		$this->option_anzahl= 0;
	}
	
	################ optionen hinzufuegen
	function add_option($name,$value, $id = 0, $re_id = 0){
		$this->options[$re_id][] = array( $name, $value, $id);
//		$this->counter++;
	}
	
	############### show select
	function out(){
        
		global $STYLE;
		$ausgabe = "\n<select $STYLE ".$this->select_multiple." name='".$this->select_name."' size='".$this->select_size."' style='".$this->select_style."' id='".$this->select_id."'>\n";
        if ( is_array( $this->options)) $ausgabe .= $this->out_group( 0);
		$ausgabe .= "</select>\n";	
		return $ausgabe;	
	}
    
    function out_group( $re_id, $level = 0) {

		if ($level > 100)
		{
			// nur mal so zu sicherheit .. man weiss nie ;)
			echo "select->out_group overflow ($groupname)";
			exit; 
		}
	
		$ausgabe = '';
		$group = $this->get_group( $re_id);
		foreach( $group as $option) {
			$name = $option[0] ;
			$value = $option[1];
			$id = $option[2];
			$ausgabe .= $this->out_option( $name, $value, $level);
			
			$subgroup = $this->get_group( $id, true);
			if ( $subgroup !== false) {
				$ausgabe .= $this->out_group( $id, $level + 1);
			}
		}
		return $ausgabe;   
	}

	function out_option( $name, $value, $level = 0) 
	{
	
		for ($i=0;$i<$level;$i++) $bsps .= "&nbsp;&nbsp;&nbsp;";
		$selected = '';
		if ( $this->option_selected !== null) {
			$selected = in_array( $value, $this->option_selected) ? ' selected="selected"' : '';
		}
		return '    <option value="'. $value .'"'. $style . $selected .'>'. $bsps.$name .'</option>'. "\n";
	}
    
	function get_group( $re_id, $ignore_main_group = false) 
	{

		if ( $ignore_main_group && $re_id == 0) {
			return false;
		}

		foreach ( $this->options as $gname => $group) {
			if ( $gname == $re_id) {
				return $group;
			}
		}

		return false;
	}
}

?>