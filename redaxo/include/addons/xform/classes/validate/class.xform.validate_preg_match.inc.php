<?php

class rex_xform_validate_preg_match extends rex_xform_validate_abstract 
{

  function enterObject(&$warning, $send, &$warning_messages)
  {
    if($send=="1")
    {
    	
    	$pm = $this->xaElements[3];
    	
      foreach($this->xaObjects as $xoObject)
      {
   
      	preg_match($pm, $xoObject->getValue(), $matches);

      	if(count($matches) != 1 || current($matches) == $xoObject->getValue())
      	{
      		
      		
      	}else
      	{
          if (!isset($this->xaElements[4])) 
            $this->xaElements[4] = "";
      		$warning["el_" . $xoObject->getId()] = $this->params["error_class"];
          $warning_messages[] = $this->xaElements[4];
      	}      	
      	
      }
    }
  }
  
  function getDescription()
  {
    return "preg_match -> prueft Ÿber preg_match, beispiel: validate|preg_match|label|/[a-z]/i|warning_message ";
  }
}
?>