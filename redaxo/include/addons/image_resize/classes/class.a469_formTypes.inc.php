<?php
/**
 * Image-Resize Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author <a href="http://www.vscope.at">www.vscope.at</a>
 *
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 *
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 * 
 * @author dh[at]daveholloway[dot]co[dot]uk Dave Holloway
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class a469_formTypes extends rex_form
{
	function rex_form($tableName, $fieldset, $whereCondition, $method = 'post', $debug = false)
	{
		parent::rex_form($tableName, $fieldset, $whereCondition, $method = 'post', $debug = false);
	}
	
	function validate()
	{
		global $I18N;
		$errors = array();
		
		//validate name
		$elem = $this->getElement($I18N->msg('iresize_edittype'),'name');
		if ($elem->getValue()=="")
		{
			$errors[] = $I18N->msg('iresize_error_name');
		}
		
		//validate settings
		$elem = $this->getElement($I18N->msg('iresize_edittype'),'settings');
		if ($elem->getValue()=="")
		{
			$errors[] = $I18N->msg('iresize_error_settings');
		}
		
		if (count($errors)>0)
		{
			$error = implode('<br />',$errors);
			return $error;
		} else {
			return parent::validate();
		}
	}
	
	function save()
	{	global $I18N;
		global $REX;
	
		if (($result = parent::save()) === true)
		{
			$name = $this->getElement($I18N->msg('iresize_edittype'),'name');
			$settings = $this->getElement($I18N->msg('iresize_edittype'),'settings');
			
			$name = $name->getValue();
			$name = str_replace('/','',$name);
			$name = str_replace('.','',$name);
			$cachefile = $REX['INCLUDE_PATH'].'/generated/files/image_resize_settings__'.$name.'.txt';
			$fh = fopen($cachefile, 'w');
			fwrite($fh, $settings->getValue());
			fclose($fh);
		}
		return $result;
	}
	
	
	
}
?>