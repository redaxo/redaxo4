<?php
$Basedir = dirname(__FILE__);
$id = rex_request('id','int');
$func = rex_request('func','string');

echo '<div class="rex-addon-output-v2">';
if ($func == '')
{	
	$query = 'SELECT * FROM '.$REX['TABLE_PREFIX'].'679_types';
	$list = rex_list::factory($query);
//  $list->setCaption($I18N->msg('module_caption'));
//  $list->addTableAttribute('summary', $I18N->msg('module_summary'));
//  $list->addTableColumnGroup(array(40, 40, '*', 153));
	
	$list->removeColumn('id');	
	$list->setColumnLabel('name',$I18N->msg('imanager_type_name'));
	$list->setColumnLabel('description',$I18N->msg('imanager_type_description'));

	// icon column
  $thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">'. $I18N->msg('imanager_create_type') .'</span></a>';
  $tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
  $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($thIcon, array('func' => 'edit', 'id' => '###id###'));
  $list->setColumnParams('name', array('func' => 'edit', 'id' => '###id###'));
  
  // functions column
  $funcs = $I18N->msg('imanager_effect_functions');
  $list->addColumn($funcs, '', -1);
  $list->setColumnParams($funcs, array('type_id' => '###id###'));
  $list->setColumnFormat($funcs, 'custom',
    create_function(
      '$params',
      'global $REX;
       $list = $params["list"];
       $edit = $list->getColumnLink("'. $funcs .'","'. $I18N->msg('imanager_effects_edit') .'", array("subpage" => "effects")); 
       $del = $list->getColumnLink("'. $funcs .'","'. $I18N->msg('imanager_type_delete') .'", array("func" => "delete"));
       return $edit . $del;'
    )
  );
//       '<a href="">'.$I18N->msg('imanager_effect_edit'). '</a> <a href="">'.$I18N->msg('imanager_effect_delete'). '</a>';
  
	$list->show();
	
} 
elseif ($func == 'edit' || $func == 'add')
{
  if($func == 'edit')
  {
    $formLabel = $I18N->msg('imanager_type_edit');
  }
  else if ($func == 'add')
  {
    $formLabel = $I18N->msg('imanager_type_create');
  }
	$form = rex_form::factory($REX['TABLE_PREFIX'].'679_types',$formLabel,'id='.$id);

	$field =& $form->addTextField('name');
	$field->setLabel($I18N->msg('imanager_type_name'));

	$field =& $form->addTextareaField('description');
	$field->setLabel($I18N->msg('imanager_type_description'));

	if($func == 'edit')
	{
		$form->addParam('id', $id);
	}	
	$form->show();
}

echo '</div>';
?>