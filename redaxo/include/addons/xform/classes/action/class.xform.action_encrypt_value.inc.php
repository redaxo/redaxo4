<?php

class rex_xform_action_encrypt_value extends rex_xform_action_abstract
{

	function execute()
	{
		
		$f = $this->action["elements"][3]; // the function
		if(!function_exists($f)){ $f = "md5"; } // default func = md5

		// Labels to get
		$l = explode(",",$this->action["elements"][2]);
		
		// Label to save in
		$ls = @$this->action["elements"][4];
		if($ls == "")
			$ls = $l[0];
		if($ls == "")
			return FALSE;
	
		// $this->elements_sql = Array for database
		$k = '';
		foreach($this->elements_sql as $key => $value)
		{
			if(in_array($key,$l)){ $k .= $value; }
		}

		if($k != ''){ $this->elements_sql[$ls] = $f($k); $this->elements_email[$ls] = $f($k); }

		return;

	}

	function getDescription()
	{
		return "action|encrypt|label[,label2,label3]|md5|[save_in_this_label]";
	}

}

?>