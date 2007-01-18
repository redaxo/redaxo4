<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */

rex_register_extension('ART_META_PARAMS', 'rex_a62_meta_params');
rex_register_extension('ART_READ_QUERY', 'rex_a62_meta_query');

/**
 * Modifiziert das Parameter Array und fügt diesem die neuen Meta-Felder hinzu (Variablenerweiterung der OO-Klassen)
 */
function rex_a62_meta_params($params)
{
	global $REX;
	
	$new_params = array();
	$fields = new rex_sql();
	$fields->setQuery('SELECT s.name as section_name, f.name FROM `'. $REX['TABLE_PREFIX'] .'62_section` s,`'. $REX['TABLE_PREFIX'] .'62_field` f WHERE s.id = f.section_id');
	
	for($i = 0; $i < $fields->getRows(); $i++)
	{
		$new_params[] = strtolower($fields->getValue('section_name').'_'.$fields->getValue('name')); 
		$fields->next();
	}
	
	return array_merge($params['subject'], $new_params);
}

/**
 * Modifiziert den SQL beim Auslesen eines Artikels, damit dieser auch die zusätzlichen Meta-Daten enthält.
 */
function rex_a62_meta_query($params)
{
	global $REX;
	
	$sql = new rex_sql();
	$qry = 'SELECT LOWER(CONCAT(s.name,"_", f.name)) as name, value FROM rex_62_section s,rex_62_field f LEFT JOIN rex_62_value v ON (v.field_id = f.id AND article_id = '. $params['article_id'] .' AND clang='. $params['clang'] .') WHERE s.id = f.section_id';
	
	$s = '';
	foreach($sql->getArray($qry) as $row)
	{
		$s .= ',"'. str_replace('"', '\"', $row['value']) .'" as `'. $row['name'] .'`';
	}
	return str_replace('FROM', $s. ' FROM', $params['subject']); 
}

?>