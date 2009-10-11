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

class a469
{
	
	function getSettingsByName($name="")
	{
		global $REX;
		$rex_resize = rex_request('rex_resize','string');
		
		if ($name!="") {
			$cachefile = $REX['INCLUDE_PATH'].'/generated/files/image_resize_settings__'.$name.'.txt';
			if (file_exists($cachefile))
			{		
				$settings = @file_get_contents($cachefile);
				if ($settings != '') {
					$settings = str_replace('##FILE##',$rex_resize,$settings);
					return $settings;
				}
			}
		}

		// read from the db if the cache file doesn't exist
		$sql = new rex_sql;
		$sql->setQuery('SELECT name,settings FROM '.$REX['TABLE_PREFIX'].'469_types WHERE name = "'.$name.'"');
		if ($sql->getRows()>0)
		{	
			$settings = $sql->getValue('settings');
			$name = str_replace('/','',$name);
			$name = str_replace('.','',$name);
			$cachefile = $REX['INCLUDE_PATH'].'/generated/files/image_resize_settings__'.$name.'.txt';
			$fh = fopen($cachefile, 'w');
			fwrite($fh, $settings);
			fclose($fh);	
			return $settings;
		} else {
			trigger_error('image_resize: image set not found');
			die();
		}
		
	}
	
	
	function initSettings($settings)
	{
		$rex_resize = rex_request('rex_resize','string');
		$rex_resize = str_replace('##FILE##',$rex_resize,$settings);
		$rex_resize = explode('&',$rex_resize);
		
		for ($i=1;$i<count($rex_resize);$i++) 
		{
			$line = explode('=',$rex_resize[$i]);
			if (count($line)==2 && $line[0]=='rex_filter[]')
			{
				$_GET['rex_filter'][] = $line[1];
			}
		}

		return $rex_resize[0];
	}
	
	
}
?>