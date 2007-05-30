<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
//------------------------------> Parameter

if(!isset($prefix))
{
	trigger_error('Fehler: Prefix nicht definiert!', E_USER_ERROR);
	exit();
}

$Basedir = dirname(__FILE__);
$field_id = rex_request('field_id', 'int');

//------------------------------> Eintragsliste
if ($func == '')
{
  $list = new rex_list('SELECT field_id, name FROM '. $REX['TABLE_PREFIX'] .'62_params WHERE `name` LIKE "'. $prefix .'%"');
	
	$list->setCaption($I18N_META_INFOS->msg('field_list_caption'));
	$list->addColumn('<a href="'. $list->getUrl(array('func' => 'add')) .'"><img src="pics/modul_plus.gif" alt="add" title="add" /></a>', '<img src="pics/modul.gif" alt="field" title="field" />', 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
	
	$list->setColumnLabel('field_id', $I18N_META_INFOS->msg('field_label_id'));
	$list->setColumnLayout('field_id',  array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));

	$list->setColumnLabel('name', $I18N_META_INFOS->msg('field_label_name'));
	$list->setColumnParams('name', array('func' => 'edit', 'field_id' => '###field_id###'));
	
	$list->show();
}
//------------------------------> Formular
elseif ($func == 'edit' || $func == 'add')
{
	require_once $REX['INCLUDE_PATH'].'/addons/metainfo/classes/class.rex_tableExpander.inc.php';

	$form = new rex_a62_tableExpander($prefix, $REX['TABLE_PREFIX'] .'article', $REX['TABLE_PREFIX'] .'62_params', $I18N_META_INFOS->msg('field_fieldset'),'field_id='. $field_id);
	
	if($func == 'edit')
		$form->addParam('field_id', $field_id);
		
	$form->show();
}
 
?>