<?php
$Basedir = dirname(__FILE__);
$id = rex_request('id','string');
$func = rex_request('func','string');


echo '<div class="rex-addon-output-v2">';
if ($func == '')
{	
	$query = 'SELECT id,name,LEFT(settings,100) as settings FROM '.$REX['TABLE_PREFIX'].'469_types ORDER BY `name` asc';
	$list = new a469_listTypes($query,20);

	$list->removeColumn('id');	
	$list->setColumnLabel('id',$I18N->msg('imanager_id'));
	$list->setColumnLabel('name',$I18N->msg('imanager_name'));
	$list->setColumnLabel('settings',$I18N->msg('imanager_settings'));
	
	$imgHeader = '<a href="'. $list->getUrl(array('func' => 'add')) .'"><img src="media/metainfo_plus.gif" alt="" title="" /></a>';
	$list->addColumn(	$imgHeader, 
		'<img src="media/metainfo.gif" alt="" title="" />', 
		0, 
		array
		(
			'<th class="rex-icon">###VALUE###</th>',
			'<td class="rex-icon">###VALUE###</td>')
		);
	$list->setColumnParams(
		$imgHeader, 
		array('func' => 'edit', 'id' => '###id###')
	);
	$list->setColumnParams(
		'name', 
		array('func' => 'edit', 'id' => '###id###')
	);	
	
	$list->setColumnLayout(
		'name',
		array('<th style="width:150px">###VALUE###</th>','<td>###VALUE###</td>')
	);
	$list->show();
	
} 
elseif ($func == 'edit' || $func == 'add')
{
	$form = new a469_formTypes($REX['TABLE_PREFIX'].'469_types',$I18N->msg('imanager_edittype'),"id=".$id,"post",false);

	$field = &$form->addTextField('name');
	$field->setLabel($I18N->msg('imanager_name'));

	$field = &$form->addTextareaField('settings');
	$field->setLabel($I18N->msg('imanager_settings'));

	
	if($func == 'edit')
	{
		$form->addParam('id', $id);
	}	
	$form->show();
}

echo '</div>';
?>