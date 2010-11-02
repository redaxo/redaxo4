<?php

class rex_xform_block extends rex_xform_abstract
{

	function init()
	{

		return;

		if(!isset($this->params["value"]["block"]))
			$this->params["value"]["block"] = array();
		
		if(!isset($this->params["value"]["block"]["counter"]))
			$this->params["value"]["block"]["counter"] = 1;
		else
			$this->params["value"]["block"]["counter"]++;

		if(!isset($this->params["value"]["block"]["last"]))
			$this->params["value"]["block"]["last"] = $this->getId();
		else
			$this->params["value"]["block"]["last"] = $this->getId();

		if(!isset($this->params["value"]["block"]["first"]))
			$this->params["value"]["block"]["first"] = $this->getId();

		if(!isset($this->params["value"]["block"]["blocks"]))
			$this->params["value"]["block"]["blocks"] = array();

		$this->params["value"]["block"]["blocks"][] = $this->getId();

	}

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		
		return;
		
		$this->block_ids = array();

		$track = FALSE;
		foreach($this->obj as $o)
		{
		
			if($o->getId() == $this->getId())
			{
				// sich selbst gefunden - tracking start
				$track = TRUE;
			}elseif($track && in_array($o->getId(),$this->params["value"]["block"]["blocks"]))
			{
				// nächster Block gefunden - tracking stop
				$track = FALSE;
			}elseif($track)
			{
				$this->block_ids[] = $o->getId();
			}
					
		}

		echo "<br />------------";
		foreach($this->block_ids as $o)
		{
			echo '<br />-- '.$o.$this->obj[$o]->getName();
		}		

		
		$form_output[$this->getId()] = "###################################################";

return;
		
		$this->params["value"]["block"][$this->getId()] = array();
		$this->params["value"]["block"][$this->getId()]['form_output'] = $this->params["form_output"];
		$this->params["value"]["block"][$this->getId()]['warning'] = $this->params["warning"];
		$this->params["value"]["block"][$this->getId()]['warning_messages'] = $this->params["warning_messages"];

		$this->params["form_output"] = array();
		$this->params["warning"] = array();
		$this->params["warning_messages"] = array();


		return;

	}

	function postFormAction()
	{
	
		return;
	
		if($this->params["value"]["block"]["last"] == $this->getId())
		{
	
			foreach($this->params["form_output"] as $k => $v)
			{
				echo '<br />**** '.$k.' => '.htmlspecialchars($v);		
			}
		}
	
return;
		$form_output = array();
		foreach($this->block_ids as $id)
		{echo "<br />".$id;
			$form_output[$id] = $this->params["form_output"][$id];
		}
	
		$this->params["form_output"] = $form_output;
		return;
	
	
	
	
	
	
	
		$this->params["form_output"] = $this->params["value"]["block"][$this->getId()]['form_output'];
		$this->params["warning"] = $this->params["value"]["block"][$this->getId()]['warning'];
		$this->params["warning_messages"] = $this->params["value"]["block"][$this->getId()]['warning_messages'];

	
	// echo '<pre>';var_dump($this->params["value"]["block"][$this->getId()]['form_output']);echo '</pre>';

	$this->params["form_output"]['tt'] = "jjjjj";
	$this->params["warning"]['tt'] = "jjjjj";
	$this->params["warning_messages"]['tt'] = "jjjjj";

	return;
	
	$this->params["form_output"] = array('tt' => "jjjjj");
	$this->params["warning"] = array('tt' => "jjjjj");
	$this->params["warning_messages"] = array('tt' => "jjjjj");
	
return;

		echo "**********".$this->getId()."************";
		return;	

		$this->params["warning"] = $this->params["value"]["block"][$this->getId()]['warning'];
		$this->params["warning_messages"] = $this->params["value"]["block"][$this->getId()]['warning_messages'];
		$this->params["form_output"] = $this->params["value"]["block"][$this->getId()]['form_output'];
		echo "POST";


	}





	function getDescription()
	{
		return "block -> Beispiel: block|label|Bezeichnung|1 wenn letzter Block - wichtig - muss vorhanden sein";
	}

	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'block',
						'values' => array(
									array( 'type' => 'name',   'label' => 'Feld' ),
									array( 'type' => 'text',    'label' => 'Bezeichnung'),
									array( 'type' => 'boolean', 'label' => 'Pflichtfeld'),
		        		),
						'description' => 'Dient zur Unterteilung von einzelnen Bereichen',
						'dbtype' => 'text'
						);

	}
}

?>