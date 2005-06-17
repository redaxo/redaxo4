<?

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
	function add_option($name,$value, $group = ""){
		$this->options[$group][] = array( $name, $value);
//		$this->counter++;
	}
	
	############### show select
	function out(){
        
		global $STYLE;

		$ausgabe = "\n<select $STYLE ".$this->select_multiple." name='".$this->select_name."' size='".$this->select_size."' style='".$this->select_style."' id='".$this->select_id."'>\n";

        if ( is_array( $this->options)) $ausgabe .= $this->out_group( '');

//		for ($i=0;$i<$this->counter;$i++){
//		
//			// if ($this->option_name[$i] != ""){
//				$ausgabe .= "<option value='".$this->option_value[$i]."'";
//				
//				for ($j=0;$j<$this->option_anzahl;$j++){			
//					if ($this->option_selected[$j] == $this->option_value[$i]){
//						$ausgabe .= " selected";
//						$j=1000;
//					}
//				}
//	
//				$ausgabe .= ">".$this->option_name[$i]."</option>\n";
//			// }		
//		}
		$ausgabe .= "</select>\n";	
		return $ausgabe;	
	}
    
    function out_group( $groupname, $level = 0) {
        $ausgabe = '';
        $group = $this->get_group( $groupname);
        
        if ( $groupname != '') {
            $ausgabe .= '  <optgroup>'. "\n";
        }
        
        foreach( $group as $option) {
            $name = $option[0] ;
            $value = $option[1];
            $ausgabe .= $this->out_option( $name, $value, $level);
            
            $subgroup = $this->get_group( $name, true);
            if ( $subgroup !== false) {
                $ausgabe .= $this->out_group( $name, $level + 1);
            }
        }
        
        if ( $groupname != '') {
            $ausgabe .= '  </optgroup>'. "\n"; 
        }
        
        return $ausgabe;   
    }
    
    function out_option( $name, $value, $level = 0) {
        $style = ' style="padding-left:'. ( $level * 9 + 1) .'px;"';
        
        $selected = '';
        if ( $this->option_selected !== null) {
            $selected = in_array( $value, $this->option_selected) ? ' selected="selected"' : '';
        }
        
        return '    <option value="'. $value .'"'. $style . $selected .'>'. $name .'</option>'. "\n";
    }
    
    function get_group( $groupname, $ignore_main_group = false) {
        if ( $ignore_main_group && $groupname == '') {
            return false;
        }
        
        foreach ( $this->options as $gname => $group) {
            if ( $gname == $groupname) {
                return $group;
            }
        }
        
        return false;
    }
}

?>