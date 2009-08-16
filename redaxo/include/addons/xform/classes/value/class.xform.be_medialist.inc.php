<?php

class rex_xform_be_medialist extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	
		
		global $I18N;
		
		$this->label = $this->elements[1];
		
		
		if (!isset($tmp_medialist)) $tmp_medialist = 0;
		$tmp_medialist++;
		
		$ausgabe = '';
		$options = '';
		$medialistarray = explode(",",$this->value);
		if (is_array($medialistarray))
		{
			for($j=0;$j<count($medialistarray);$j++)
			{
				if (current($medialistarray)!="")
					$options .= "<option value='".current($medialistarray)."'>".current($medialistarray)."</option>\n";
				next($medialistarray);
			}
		}
		
		
		
		$ausgabe .='
    <div class="rex-widget">
      <div class="rex-widget-medialist">
				<input type="hidden" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" id="REX_MEDIALIST_'.$tmp_medialist.'" value="'.htmlspecialchars(stripslashes($this->value)) . '" />
        <p class="rex-widget-field">
				  <select name="MEDIALIST_SELECT[1]" id="REX_MEDIALIST_SELECT_'.$tmp_medialist.'" size="8" style="width:250px;">
            ' . $options . '
          </select>
        </p>
        <p class="rex-widget-icons">
          <a href="#" class="rex-icon-file-top" onclick="moveREXMedialist(' . $tmp_medialist . ',\'top\');return false;"><img src="media/file_top.gif" width="16" height="16" title="'. $I18N->msg('var_medialist_move_top') .'" alt="'. $I18N->msg('var_medialist_move_top') .'" /></a>
          <a href="#" class="rex-icon-file-open" onclick="openREXMedialist(' . $tmp_medialist . ');return false;"><img src="media/file_open.gif" width="16" height="16" title="'. $I18N->msg('var_media_open') .'" alt="'. $I18N->msg('var_media_open') .'" /></a><br />
          <a href="#" class="rex-icon-file-up" onclick="moveREXMedialist(' . $tmp_medialist . ',\'up\');return false;"><img src="media/file_up.gif" width="16" height="16" title="'. $I18N->msg('var_medialist_move_up') .'" alt="'. $I18N->msg('var_medialist_move_top') .'" /></a>
          <a href="#" class="rex-icon-file-add" onclick="addREXMedialist('. $tmp_medialist .');return false;"><img src="media/file_add.gif" width="16" height="16" title="'. $I18N->msg('var_media_new') .'" alt="'. $I18N->msg('var_media_new') .'" /></a><br />
          <a href="#" class="rex-icon-file-down" onclick="moveREXMedialist(' . $tmp_medialist . ',\'down\');return false;"><img src="media/file_down.gif" width="16" height="16" title="'. $I18N->msg('var_medialist_move_down') .'" alt="'. $I18N->msg('var_medialist_move_down') .'" /></a>
          <a href="#" class="rex-icon-file-delete" onclick="deleteREXMedialist(' . $tmp_medialist . ');return false;"><img src="media/file_del.gif" width="16" height="16" title="'. $I18N->msg('var_media_remove') .'" alt="'. $I18N->msg('var_media_remove') .'" /></a><br />
          <a href="#" class="rex-icon-file-bottom" onclick="moveREXMedialist(' . $tmp_medialist . ',\'bottom\');return false;"><img src="media/file_bottom.gif" width="16" height="16" title="'. $I18N->msg('var_medialist_move_bottom') .'" alt="'. $I18N->msg('var_medialist_move_bottom') .'" /></a>
        </p>
        <div class="rex-media-preview"></div>
      </div>
    </div>
	 	<div class="rex-clearer"></div>
		
		
		';
		
		
		
		
		
		
		
		
		
		
		
		
		
		

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) $wc = $warning["el_" . $this->getId()];
		
		
		
		$form_output[] = '
			<div class="xform-element formbe_medialist formlabel-'.$this->label.'">
			
				<label class="text ' . $wc . '" for="el_' . $this->id . '" >' . $this->elements[2] . '</label>
				
				'.$ausgabe.'
				
			</div>';





		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if (!isset($this->elements[3]) || $this->elements[3] != "no_db") $sql_elements[$this->elements[1]] = $this->value;

	}
	
	function getDescription()
	{
		return "be_medialist -> Beispiel: be_medialist|label|Bezeichnung|no_db";
	}
}

?>