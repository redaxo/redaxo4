<?php

class rex_xform_uniqueform extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	
	
		$this->label = $this->elements[1];
		$table = $this->elements[2];
	
		// ***** ERSTER AUFRUF -> key erstellen
		if (!$send)
		{
			$this->value = md5($_SERVER["REMOTE_ADDR"].time());

		}else
		{
			// in tabelle nachsehen ob formcode vorhanden
			$sql = 'select '.$this->label.' from '.$table.' WHERE '.$this->label.'="'.$this->value.'" LIMIT 1';
			$cd = new rex_sql;
			if ($this->params["debug"]) $cd->debugsql = true;
			$cd->setQuery($sql);
			if ($cd->getRows()==1)
			{
				$this->params["warning"][] = $this->elements[3];
				$this->params["warning_messages"][] = $this->elements[3];
			}
	
		}
	
		$form_output[] = '<input type="hidden" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" value="'.htmlspecialchars(stripslashes($this->value)).'" />';

		$email_elements[$this->label] = stripslashes($this->value);
		$sql_elements[$this->label] = stripslashes($this->value);
	
		return;
	
	}
	
	function getDescription()
	{
		return "uniqueform -> Beispiel: uniqueform|label|table|Fehlermeldung";
	}
}

?>