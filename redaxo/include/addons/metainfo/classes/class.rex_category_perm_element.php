<?php

class rex_form_category_perm_element extends rex_category_select
{
  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_form_category_perm_element($tag = '', &$table, $attributes = array())
	{
		parent::rex_form_select_element($tag, $table, $attributes);
		$this->setSelect(new rex_category_select());
	}
	
  function formatElement()
  {
  	global $I18N;
  	
  	$format = parent::formatElement();
  	
    $format = 
      '<p class="rex-form-col-a rex-form-checkbox rex-form-label-right">
         <input class="rex-form-checkbox" id="allmodules'.$i.'" type="checkbox" name="xzy" value="1" />
         <label for="allmodules'.$i.'">'.$I18N->msg("modules_available_all").'</label> 
       </p>'. $format;
    
    return $format;
  }
}