<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */

rex_register_extension('ART_META_PARAMS', 'rex_a62_oof_metainfo_params');

/**
 * Modifiziert das Parameter Array und fügt diesem die neuen Meta-Felder hinzu (Variablenerweiterung der OO-Klassen)
 */
function rex_a62_oof_metainfo_params($params)
{
	global $REX;
	
	$new_params = array();
	$fields = new rex_sql();
  $fields->setQuery('SELECT name FROM '. $REX['TABLE_PREFIX'] .'62_params p WHERE `p`.`name` LIKE "art_%"');
	
	for($i = 0; $i < $fields->getRows(); $i++)
	{
		$new_params[] = $fields->getValue('name'); 
		$fields->next();
	}
	
	return array_merge($params['subject'], $new_params);
}

?>