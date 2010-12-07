<?PHP

class rex_xform_com_auth_register extends rex_xform_abstract 
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{

		$query = array();
		// $query[] = 'status=0';

		// $this->params["submit_btn_show"] = FALSE;

		$e = explode(";",$this->elements[1]);
		$c = 0;
		$f = TRUE;
		foreach($e as $v)
		{
			$c++;
			$w = explode("=",$v);
			$label = $w[0];
			$value = rex_request($w[1],"string","");
			if($value == "")
				$f = FALSE;

			$query[] = '`'.$label.'`="USER_VALUE_'.$c.'"';
			$labels['USER_VALUE_'.$c] = $value;
			


		}
		$REX['COM_USER'] = new rex_com_user();

		if($this->params["debug"])
		{
			echo 'select * from rex_com_user where '.implode(" AND ",$query).'';
			var_dump($labels);
		}


		if( 
			$f 
			&& 
			$REX['COM_USER']->checkQuery(
					'select * from rex_com_user where '.implode(" AND ",$query).'',
					$labels
				)
		)
		{
			// Auth OK
			
			$this->params["main_where"] = 'id='.$REX['COM_USER']->getValue('id');
			$this->params["main_table"] = 'rex_com_user';
			
			// rex_com_auth_jump verwenden
			
		}else
		{
			// Auth failed
			$warning[$this->id] = $this->params["error_class"];
			$this->params["warning_messages"][] = $this->elements[2];
		}



		return;


	}
	
	function getDescription()
	{
		return "com_auth_register -> prüft felder vorhanden und setzt status auf 1, beispiel: com_auth_register|label1=request1;label2=request2|warning_message| ";
	}
}
?>