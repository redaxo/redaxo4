<?php

class rex_xform_action_showtext extends rex_xform_action_abstract
{
	
	function execute()
	{
	
		$text = "";
		if (isset($this->action["elements"][2])) 
			$text .= $this->action["elements"][2];
		if ($text == "") 
			$text = $this->params["answertext"];

		if (isset($this->action["elements"][5]) && $this->action["elements"][5] == "0")
			$text = nl2br(htmlspecialchars($text));

		if (isset($this->action["elements"][3])) 
			$text = $this->action["elements"][3].$text;

		if (isset($this->action["elements"][4])) 
			$text .= $this->action["elements"][4];


		foreach ($this->elements_email as $search => $replace)
		{
			$text = str_replace('###'. $search .'###', $replace, $text);
		}

		$this->params["output"] = $text;
	}

	function getDescription()
	{
		return "action|showtext|Antworttext|<p>|</p>|0/1 html";
	}

}

?>