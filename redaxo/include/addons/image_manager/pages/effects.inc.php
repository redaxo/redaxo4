<?php
$Basedir = dirname(__FILE__);

$effect_id = rex_request('effect_id','int');
$type_id = rex_request('type_id','int');
$func = rex_request('func','string');

$info = '';
$warning = '';

//-------------- delete cache on effect changes or deletion
if((rex_post('func') != '' || $func == 'delete')
   && $type_id > 0)
{
  $counter = rex_imanager_deleteCacheByType($type_id);
//  $info = $I18N->msg('imanager_cache_files_removed', $counter);
}

//-------------- delete effect
if($func == 'delete' && $effect_id > 0)
{
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $sql->setTable($REX['TABLE_PREFIX'].'679_type_effects');
  $sql->setWhere('id='. $effect_id . ' LIMIT 1');
  
  if($sql->delete())
  {
     $info = $I18N->msg('imanager_effect_deleted') ;
  }
  else
  {
    $warning = $sql->getErrro();
  }
  $func = '';
}

if ($info != '')
  echo rex_info($info);

if ($warning != '')
  echo rex_warning($warning);
  
echo '<div class="rex-addon-output-v2">';
if ($func == '')
{	
	$query = 'SELECT * FROM '.$REX['TABLE_PREFIX'].'679_type_effects WHERE type_id='.$type_id;
	
	$list = rex_list::factory($query);
  $list->setNoRowsMessage($I18N->msg('imanager_effect_no_effects'));
  $list->setCaption($I18N->msg('imanager_effect_caption'));
  $list->addTableAttribute('summary', $I18N->msg('imanager_effect_summary'));
  $list->addTableColumnGroup(array(40, '*', 130, 130));
	
	$list->removeColumn('id');	
	$list->removeColumn('type_id');	
	$list->removeColumn('parameters');	
	$list->setColumnLabel('effect',$I18N->msg('imanager_type_name'));
	
	// TODO Prio Spalte um Reihenfolge der Filter festzulegen
	$list->removeColumn('prior');	

	// icon column
  $thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('type_id' => $type_id, 'func' => 'add')) .'"><span class="rex-i-element-text">'. $I18N->msg('imanager_create_type') .'</span></a>';
  $tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
  $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($thIcon, array('func' => 'edit', 'type_id' => $type_id, 'effect_id' => '###id###'));
  
  // functions column spans 2 data-columns
  $funcs = $I18N->msg('imanager_effect_functions');
  $list->addColumn($funcs, $I18N->msg('imanager_effect_edit'), -1, array('<th colspan="2">###VALUE###</th>','<td>###VALUE###</td>'));
  $list->setColumnParams($funcs, array('func' => 'edit', 'type_id' => $type_id, 'effect_id' => '###id###'));
  
  $delete = 'deleteCol';
  $list->addColumn($delete, $I18N->msg('imanager_effect_delete'), -1, array('','<td>###VALUE###</td>'));
  $list->setColumnParams($delete, array('type_id' => $type_id, 'effect_id' => '###id###', 'func' => 'delete'));
  $list->addLinkAttribute($delete, 'onclick', 'return confirm(\''.$I18N->msg('delete').' ?\')');
  	
	$list->show();
} 
elseif ($func == 'add' && $type_id > 0 ||
        $func == 'edit' && $effect_id > 0 && $type_id > 0)
{

  $effectNames = rex_imanager_supportedEffectNames();
  
  if($func == 'edit')
  {
    $formLabel = $I18N->msg('imanager_effect_edit');
  }
  else if ($func == 'add')
  {
    $formLabel = $I18N->msg('imanager_effect_create');
  }
  
	$form = rex_form::factory($REX['TABLE_PREFIX'].'679_type_effects',$formLabel,'id='.$effect_id);
	
	// image_type_id for reference for saving into the db
  $form->addHiddenField('type_id', $type_id);

	// effect name als SELECT
	$field =& $form->addSelectField('effect');
	$field->setLabel($I18N->msg('imanager_effect_name'));
	$select =& $field->getSelect();
	$select->addOptions($effectNames, true);
	$select->setSize(1);
	
  $script = '
  <script type="text/javascript">
  <!--

  (function($) {
    var currentShown = null;
    $("#'. $field->getAttribute('id') .'").change(function(){
      if(currentShown) currentShown.hide();
      
      var effectParamsId = "#rex-rex_effect_"+ jQuery(this).val();
      currentShown = $(effectParamsId);
      currentShown.show();
    }).change();
  })(jQuery);
  
  //--></script>';
	
  $fieldContainer =& $form->addContainerField('parameters');
  $fieldContainer->setAttribute('style', 'display: none');
	$fieldContainer->setSuffix($script);
	
  $effects = rex_imanager_supportedEffects();
   
  foreach($effects as $effectClass => $effectFile)
  {
    require_once($effectFile);
    $effectObj = new $effectClass();
    $effectParams = $effectObj->getParams();
    $group = $effectClass;
    
    if(empty($effectParams)) continue;

    foreach($effectParams as $param)
    {
      $name = $effectClass.'_'.$param['name'];
      $value = isset($param['default']) ? $param['default'] : null;
      switch($param['type'])
      {
        case 'int' :
        case 'float' :
        case 'string' :
          {
            $type = 'text';
            $field =& $fieldContainer->addGroupedField($group, $type, $name, $value, $attributes = array());
            $field->setLabel($param['label']);
            break;
          }
        case 'select' :
          {
            $type = $param['type'];
            $field =& $fieldContainer->addGroupedField($group, $type, $name, $value, $attributes = array());
            $field->setLabel($param['label']);
            $select =& $field->getSelect();
            $select->addOptions($param['options'], true);
            break;
          }
        case 'media' :
          {
            $type = $param['type'];
            $field =& $fieldContainer->addGroupedField($group, $type, $name, $value, $attributes = array());
            $field->setLabel($param['label']);
            break;
          }
        default:var_dump($param);
      }
    }
  }
	
  // parameters for url redirects
	$form->addParam('type_id', $type_id);
	if($func == 'edit')
	{
		$form->addParam('effect_id', $effect_id);
	}	
	$form->show();
}

echo '</div>';
?>