<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
//------------------------------> Parameter

$Basedir = dirname(__FILE__);

$section_id = rex_request('section_id', 'int');

//------------------------------> Eintragsliste
if ($func == '')
{
  $list = new rex_list('SELECT * FROM '. $REX['TABLE_PREFIX'] .'62_section');
	$list->setCaption($I18N_META_FORM->msg('section_list_caption'));
	
	$list->setColumnLabel('id', $I18N_META_FORM->msg('section_label_id'));
	$list->setColumnLabel('name', $I18N_META_FORM->msg('section_label_name'));
	$list->setColumnLabel('submit_label', $I18N_META_FORM->msg('section_label_submit'));
	$list->setColumnParams('name', array('func' => 'edit', 'section_id' => '%id%'));
	$list->addColumn($I18N_META_FORM->msg('functions'),'<a href="'. $list->getUrl(array('subpage' => 'fields', 'section_id' => '%id%')) .'">'. $I18N_META_FORM->msg('function_edit_fields') .'</a>',-1);
	
	$list->show();
}
//------------------------------> Formular
elseif ($func == 'edit' || $func == 'add')
{
	$form = new rex_form(''. $REX['TABLE_PREFIX'] .'62_section', $I18N_META_FORM->msg('section_form_fieldset'),'id='.$section_id);
	$form->addParam('section_id', $section_id);
	
	$field =& $form->addTextField('name');
	$field->setLabel($I18N_META_FORM->msg('section_label_name'));
	
	$field =& $form->addTextField('submit_label');
	$field->setLabel($I18N_META_FORM->msg('section_label_submit'));
	
	$form->show();
}
 
?>