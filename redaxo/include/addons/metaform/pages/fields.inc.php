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
$field_id = rex_request('field_id', 'int');

$section = new rex_sql();
$section->setQuery('SELECT * FROM '. $REX['TABLE_PREFIX'] .'62_section WHERE id='. $section_id);
if($section->getRows() != 1)
{
	echo '<p class="rex_warning">Section with ID "'. $section_id .'" not found!</p>';
	return;
}
else
{
	echo '
	<p>
		<span>'. $I18N_META_FORM->msg('field_label_section') .'</span>
		<ul>
			<li><span>'. $I18N_META_FORM->msg('section_label_id') .'</span>'. $section->getValue('id') .'</li>
			<li><span>'. $I18N_META_FORM->msg('section_label_name') .'</span>'. $section->getValue('name') .'</li>
			<li><span>'. $I18N_META_FORM->msg('section_label_submit') .'</span>'. $section->getValue('submit_label') .'</li>
		</ul>
	</p>';
}

//echo "<pre>";
//
//$style = 'class="a"';
//$pattern = '/class=["\']?([^"\']*)["\']?/i';
//preg_match($pattern, $style, $matches);
//var_dump($matches);
//
//$style = ' class="a" ';
//preg_match($pattern, $style, $matches);
//var_dump($matches);
//
//$style = ' class=a ';
//preg_match($pattern, $style, $matches);
//var_dump($matches);
//
//$style = ' class=a" ';
//preg_match($pattern, $style, $matches);
//var_dump($matches);
//
//$style = ' class="a ';
//preg_match($pattern, $style, $matches);
//var_dump($matches);
//
//$style = 'class="ab"';
//preg_match($pattern, $style, $matches);
//var_dump($matches);
//
//$style = "class='abc'";
//preg_match($pattern, $style, $matches);
//var_dump($matches);
//
//echo "</pre>";


//------------------------------> Eintragsliste
if ($func == '')
{
  $list = new rex_list('
	SELECT 
		f.id, f.name, t.name as typename 
	FROM 
		'. $REX['TABLE_PREFIX'] .'62_field f,'. $REX['TABLE_PREFIX'] .'62_type t 
	WHERE 
		f.type_id = t.id AND section_id = '. $section_id);
		
	$list->setCaption($I18N_META_FORM->msg('field_list_caption'));
	$list->addParam('section_id', $section_id);
	
	$list->setColumnLabel('id', $I18N_META_FORM->msg('field_label_id'));
	$list->setColumnLabel('name', $I18N_META_FORM->msg('field_label_name'));
	$list->setColumnLabel('typename', $I18N_META_FORM->msg('field_label_typename'));
	$list->setColumnParams('name', array('func' => 'edit', 'section_id' => $section_id, 'field_id' => '%id%'));
	
	$list->show();
}
//------------------------------> Formular
elseif ($func == 'edit' || $func == 'add')
{
	$form = new rex_form($REX['TABLE_PREFIX'] .'62_field', $I18N_META_FORM->msg('field_form_fieldset'),'id='. $field_id);
	$form->addParam('section_id', $section_id);
	$form->addParam('field_id', $field_id);
	
	$field =& $form->addHiddenField('section_id');
	$field->setValue($section_id);
	
	$field =& $form->addTextField('name');
	$field->setLabel($I18N_META_FORM->msg('field_label_name'));
	
	$field =& $form->addSelectField('type_id');
	$field->setLabel($I18N_META_FORM->msg('field_label_typename'));
	$select =& $field->getSelect();
	$select->addSqlOptions('SELECT name,id FROM '. $REX['TABLE_PREFIX'] .'62_type');
	
	$field =& $form->addTextAreaField('attribute');
	$field->setLabel($I18N_META_FORM->msg('field_label_attribute'));
	
	$form->show();
}
 
?>