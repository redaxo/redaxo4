<?php
$Basedir = dirname(__FILE__);
$id = rex_request('id','int');
$type_id = rex_request('type_id','int');
$func = rex_request('func','string');

// TODO
if($type_id == 0)
{
  echo "FEHLER";
}

echo '<div class="rex-addon-output-v2">';
if ($func == '')
{	
	$query = 'SELECT * FROM '.$REX['TABLE_PREFIX'].'679_type_effects';
	$list = rex_list::factory($query);
//  $list->setCaption($I18N->msg('module_caption'));
//  $list->addTableAttribute('summary', $I18N->msg('module_summary'));
//  $list->addTableColumnGroup(array(40, 40, '*', 153));
	
	$list->removeColumn('parameters');	
	$list->setColumnLabel('effect',$I18N->msg('imanager_type_name'));

	// icon column
  $thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('type_id' => $type_id, 'func' => 'add')) .'"><span class="rex-i-element-text">'. $I18N->msg('imanager_create_type') .'</span></a>';
  $tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
  $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($thIcon, array('func' => 'edit', 'id' => '###id###'));
  
  // functions column
  $thIcon = $I18N->msg('imanager_effect_functions');
  $tdIcon = '<a href="">'.$I18N->msg('imanager_effect_edit'). '</a> <a href="">'.$I18N->msg('imanager_effect_delete'). '</a>';
  $list->addColumn($thIcon, $tdIcon, -1);
	
	$list->show();
	
} 
elseif ($func == 'edit' || $func == 'add')
{
  if($func == 'edit')
  {
    $formLabel = $I18N->msg('imanager_effect_edit');
  }
  else if ($func == 'add')
  {
    $formLabel = $I18N->msg('imanager_effect_create');
  }
	$form = rex_form::factory($REX['TABLE_PREFIX'].'679_type_effects',$formLabel,'id='.$id);

	// effect name als SELECT
	$field =& $form->addTextField('name');
	$field->setLabel($I18N->msg('imanager_effect_name'));

	$field =& $form->addTextareaField('description');
	$field->setLabel($I18N->msg('imanager_effect_parameters'));

	if($func == 'edit')
	{
		$form->addParam('id', $id);
	}	
	$form->show();
}

echo '</div>';
?>