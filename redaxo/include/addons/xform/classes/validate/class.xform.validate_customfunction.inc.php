<?php

class rex_xform_validate_customfunction extends rex_xform_validate_abstract 
{

  function enterObject(&$warning, $send, &$warning_messages)
  {
    if($send=="1")
    {
    	
      $f = $this->xaElements[3];
      $l = $this->xaElements[2];
      $p = $this->xaElements[4];
      
      foreach($this->xaObjects as $xoObject)
      {
      	if(function_exists($f))
      	{
      		if($f($l,$xoObject->getValue(),$p))
      		{
            $warning["el_" . $xoObject->getId()] = $this->params["error_class"];
            $warning_messages[] = $this->xaElements[5];
      		}
      	}else
      	{
      		$warning["el_" . $xoObject->getId()] = $this->params["error_class"];
          $warning_messages[] = 'ERROR: customfunction "'.$f.'" not found';
      	}
      	
      }
    }
  }
  
  function getDescription()
  {
    return "customfunction -> prueft Ÿber customfunc, beispiel: validate|customfunction|label|functionname|weitere_parameter|warning_message";
  }
  
  function getLongDesription()
  {
  	
  	
  }

}

?>