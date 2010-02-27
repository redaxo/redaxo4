<?php
$Basedir = dirname(__FILE__);

$type_id = rex_request('type_id','int');
$func = rex_request('func','string');

$info = '';
$warning = '';

//-------------- delete type
if($func == 'delete' && $type_id > 0)
{
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $sql->setTable($REX['TABLE_PREFIX'].'679_types');
  $sql->setWhere('id='. $type_id . ' LIMIT 1');
  
  if($sql->delete())
  {
     $info = $I18N->msg('imanager_type_deleted') ;
  }
  else
  {
    $warning = $sql->getErrro();
  }
  $func = '';
}

//-------------- delete cache by type-id
if($func == 'delete_cache' && $type_id > 0)
{
  $qry = 'SELECT * FROM '. $REX['TABLE_PREFIX'].'679_types' . ' WHERE id='. $type_id;
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $sql->setQuery($qry);
  
  $counter = 0;
  while($sql->hasNext())
  {
    $counter += rex_image_cacher::deleteCache(null, $sql->getValue('name'));
    $sql->next();
  }
  
  $info = $I18N->msg('imanager_cache_files_removed', $counter);
  
  $func = '';
}

//-------------- output messages
if ($info != '')
  echo rex_info($info);

if ($warning != '')
  echo rex_warning($warning);

echo '<div class="rex-addon-output-v2">';
if ($func == '')
{	
	$query = 'SELECT * FROM '.$REX['TABLE_PREFIX'].'679_types';
	
	$list = rex_list::factory($query);
	$list->setNoRowsMessage($I18N->msg('imanager_type_no_types'));
  $list->setCaption($I18N->msg('imanager_type_caption'));
  $list->addTableAttribute('summary', $I18N->msg('imanager_type_summary'));
  $list->addTableColumnGroup(array(40, 100, '*', 130, 130, 130));
	
	$list->removeColumn('id');	
	$list->removeColumn('system');	
	$list->setColumnLabel('name',$I18N->msg('imanager_type_name'));
  $list->setColumnParams('name', array('func' => 'edit', 'type_id' => '###id###'));
	$list->setColumnLabel('description',$I18N->msg('imanager_type_description'));

	// icon column
  $thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">'. $I18N->msg('imanager_type_create') .'</span></a>';
  $tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
  $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($thIcon, array('func' => 'edit', 'type_id' => '###id###'));
  
  // functions column spans 2 data-columns
  $funcs = $I18N->msg('imanager_type_functions');
  $list->addColumn($funcs, $I18N->msg('imanager_type_effekts_edit'), -1, array('<th colspan="3">###VALUE###</th>','<td>###VALUE###</td>'));
  $list->setColumnParams($funcs, array('type_id' => '###id###', 'subpage' => 'effects'));
  
  $delete = 'deleteCache';
  $list->addColumn($delete, $I18N->msg('imanager_type_cache_delete'), -1, array('','<td>###VALUE###</td>'));
  $list->setColumnParams($delete, array('type_id' => '###id###', 'func' => 'delete_cache'));
  $list->addLinkAttribute($delete, 'onclick', 'return confirm(\''.$I18N->msg('imanager_type_cache_delete').' ?\')');
  
  $delete = 'deleteType';
  $list->addColumn($delete, $I18N->msg('imanager_type_delete'), -1, array('','<td>###VALUE###</td>'));
  $list->setColumnParams($delete, array('type_id' => '###id###', 'func' => 'delete'));
  $list->addLinkAttribute($delete, 'onclick', 'return confirm(\''.$I18N->msg('delete').' ?\')');
  
	$list->show();
	
} 
elseif ($func == 'add' ||
        $func == 'edit' && $type_id > 0)
{
  if($func == 'edit')
  {
    $formLabel = $I18N->msg('imanager_type_edit');
  }
  else if ($func == 'add')
  {
    $formLabel = $I18N->msg('imanager_type_create');
  }
  
	$form = rex_form::factory($REX['TABLE_PREFIX'].'679_types',$formLabel,'id='.$type_id);
  rex_register_extension('REX_FORM_'.strtoupper($form->getName()).'_SAVE', 'rex_imanager_handle_type_change'); 
	
	$field =& $form->addTextField('name');
	$field->setLabel($I18N->msg('imanager_type_name'));

	$field =& $form->addTextareaField('description');
	$field->setLabel($I18N->msg('imanager_type_description'));

	if($func == 'edit')
	{
		$form->addParam('type_id', $type_id);
	}
	
	$form->show();
}

echo '</div>';
?>