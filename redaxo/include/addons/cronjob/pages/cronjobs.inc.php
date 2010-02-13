<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

if ($func == 'setstatus')
{
  $sql = rex_sql::factory();
  //$sql->debugsql = true;
  $sql->setTable($table);
  $sql->setWhere('id = '.$oid);
  $status = (rex_request('oldstatus','int') +1) % 2;
  $sql->setValue('status',$status);
  $sql->addGlobalUpdateFields();
  if ($sql->update())
    echo rex_info($I18N->msg('cronjob_status_success'));
  else
    echo rex_warning($I18N->msg('cronjob_status_error'));
  rex_a630_manager::saveNextTime();
  $func = '';
}

if ($func == 'delete')
{
  $sql = rex_sql::factory();
  //$sql->debugsql = true;
  $sql->setTable($table);
  $sql->setWhere('id = '.$oid);
  if ($sql->delete())
    echo rex_info($I18N->msg('cronjob_delete_success'));
  else
    echo rex_warning($I18N->msg('cronjob_delete_error'));
  rex_a630_manager::saveNextTime();
  $func = '';
}

if ($func == '') 
{
  echo '<table cellpadding=5 class=rex-table><tr><td><a href=index.php?page=cronjob&amp;func=add><b>+ '.$I18N->msg('cronjob_add').'</b></a></td></tr></table><br />';

  $query = 'SELECT id, name, `interval`, environment, status FROM '.$table.' ORDER BY name';
  
  $list = rex_list::factory($query, 30, 'cronjobs', false);
  
  $list->removeColumn('id');
  
  $list->setColumnLabel('name',$I18N->msg('cronjob_name'));
  $list->setColumnParams('name',array('func'=>'edit', 'oid'=>'###id###'));
  
  $list->setColumnLabel('interval',$I18N->msg('cronjob_interval'));
  $list->setColumnFormat('interval', 'custom', 
    create_function( 
      '$params', 
      'global $I18N;
       $list = $params["list"]; 
       $value = explode("|",$list->getValue("interval"));
       $str = $value[1]." ";
       $array = array("h"=>"hour", "d"=>"day", "w"=>"week", "m"=>"month", "y"=>"year");
       $str .= $I18N->msg("cronjob_interval_".$array[$value[2]]);
       return $str;' 
    ) 
  );
  
  $list->setColumnLabel('environment',$I18N->msg('cronjob_environment'));
  $list->setColumnFormat('environment', 'custom', 
    create_function( 
      '$params', 
      'global $I18N;
       $list = $params["list"];
       $value = $list->getValue("environment");
       $env = array();
       if (strpos($value, "|0|") !== false) 
         $env[] = $I18N->msg("cronjob_environment_frontend");
       if (strpos($value, "|1|") !== false) 
         $env[] = $I18N->msg("cronjob_environment_backend");
       return implode(", ", $env);' 
    ) 
  );
  
  $list->setColumnLabel('status',$I18N->msg('cronjob_status'));
  $list->setColumnParams('status', array('func'=>'setstatus', 'oldstatus'=>'###status###', 'oid'=>'###id###'));
  $list->setColumnLayout('status',array('<th>###VALUE###</th>','<td style="text-align:center;">###VALUE###</td>'));
  $list->setColumnFormat('status', 'custom', 
    create_function( 
      '$params', 
      'global $I18N;
       $list = $params["list"]; 
       if ($list->getValue("status") == 1) 
         $str = $list->getColumnLink("status","<span class=\"rex-online\">".$I18N->msg("cronjob_status_activated")."</span>"); 
       else 
         $str = $list->getColumnLink("status","<span class=\"rex-offline\">".$I18N->msg("cronjob_status_deactivated")."</span>"); 
       return $str;' 
    ) 
  );
  
  $list->addColumn('delete',$I18N->msg('cronjob_delete'),-1,array("<th>&nbsp;</th>",'<td style="text-align:center;">###VALUE###</td>'));
  $list->setColumnParams('delete', array('func' => 'delete', 'oid' => '###id###'));
  $list->addLinkAttribute('delete','onclick',"return confirm('".$I18N->msg('cronjob_really_delete')."');");
  
  $list->show();
  
} elseif ($func == 'edit' || $func == 'add') 
{
  require_once $REX['INCLUDE_PATH'].'/addons/cronjob/classes/class.rex_a630_form.inc.php';
  
  $fieldset = $func == 'edit' ? $I18N->msg('cronjob_edit') : $I18N->msg('cronjob_add');
  
  $form = rex_form::factory($table, $fieldset, 'id = '.$oid, 'post', false, 'rex_a630_form');
  $form->addParam('oid', $oid);
  $form->addParam('list','cronjobs');
  
  $field =& $form->addTextField('name');
  $field->setLabel($I18N->msg('cronjob_name'));
  
  $field =& $form->addIntervalField('interval');
  $field->setLabel($I18N->msg('cronjob_interval'));
  
  $field =& $form->addSelectField('type');
  $field->setLabel($I18N->msg('cronjob_type'));
  $field->setAttribute('class',$field->getAttribute('class').' rex-a630-type-select');
  $select =& $field->getSelect();
  $select->setSize(1);
  $select->addOption($I18N->msg('cronjob_type_phpcode'),1);
  $select->addOption($I18N->msg('cronjob_type_phpcallback'),2);
  $select->addOption($I18N->msg('cronjob_type_urlrequest'),3);
  $extensions = rex_register_extension_point('REX_CRONJOB_EXTENSIONS', array());
  if (!empty($extensions)) 
  {
    $select->addOption($I18N->msg('cronjob_type_extension'),4);
  }
  if ($func == 'add')
    $select->setSelected(1);
  $type = $field->getValue();
  if ($type == 0 || $type == '')
    $type = 1;
    
  $field =& $form->addTextAreaField('content');
  $field->setLabel($I18N->msg('cronjob_type_phpcode'));
  $field->setAttribute('rows',20);
  $class = '';
  if ($type != 1) {
    $class = ' rex-a630-hidden';
    $field->setValue('');
  }
  $field->setAttribute('class',$field->getAttribute('class').' rex-a630-type rex-a630-type-1'.$class);
  
  $field =& $form->addTextField('content');
  $field->setLabel($I18N->msg('cronjob_type_phpcallback'));
  $class = '';
  if ($type != 2) {
    $class = ' rex-a630-hidden';
    $field->setValue('');
  }
  $field->setAttribute('class',$field->getAttribute('class').' rex-a630-type rex-a630-type-2'.$class);
  $field->setNotice($I18N->msg('cronjob_examples').': foo(), foo(1, \'string\'), foo::bar()');
  
  $field =& $form->addTextField('content');
  $field->setLabel('URL');
  $class = '';
  if ($type != 3) {
    $class = ' rex-a630-hidden';
    $field->setValue('http://');
  }
  $field->setAttribute('class',$field->getAttribute('class').' rex-a630-type rex-a630-type-3'.$class);
  if ($func == 'add')
    $field->setValue('http://');
  
  $js = '';  
  if (!empty($extensions)) 
  {
    $field =& $form->addSelectField('content');
    $field->setLabel($I18N->msg('cronjob_type_extension'));
    $class = '';
    if ($type != 4) {
      $class = ' rex-a630-hidden';
    }
    $field->setAttribute('class',$field->getAttribute('class').' rex-a630-type rex-a630-type-4'.$class);
    $select =& $field->getSelect();
    $select->setSize(1);
    foreach ($extensions as $extension => $values) 
    {
      $select->addOption(rex_translate($values[0]),$extension);
      $disabled = array();
      if (isset($values[3])) 
      {
        if (!is_array($values[3]))
          $values[3] = array($values[3]);
        if (!in_array('frontend',$values[3]))
          $disabled[] = 0;
        if (!in_array('backend',$values[3]))
          $disabled[] = 1;
        if (count($disabled) > 0)
          $js = '
        if ($("select.rex-a630-type-4 option:selected").val() == "'.$extension.'")
          $(".rex-a630-environment option[value=\''.implode('\'], .rex-a630-environment option[value=\'',$disabled).'\']").attr("disabled","disabled").attr("selected","");
';
      }
    }
    $select->addOption('test','test');
  }
  
  $field =& $form->addSelectField('environment');
  $field->setLabel($I18N->msg('cronjob_environment'));
  $field->setAttribute('multiple','multiple');
  $field->setAttribute('class',$field->getAttribute('class').' rex-a630-environment');
  $select =& $field->getSelect();
  $select->setSize(2);
  $select->addOption($I18N->msg('cronjob_environment_frontend'),0);
  $select->addOption($I18N->msg('cronjob_environment_backend'),1);
  if ($func == 'add')
    $select->setSelected(array(0,1));
    
  $field =& $form->addSelectField('status');
  $field->setLabel($I18N->msg('cronjob_status'));
  $select =& $field->getSelect();
  $select->setSize(1);
  $select->addOption($I18N->msg('cronjob_status_activated'),1);
  $select->addOption($I18N->msg('cronjob_status_deactivated'),0);
  if ($func == 'add')
    $select->setSelected(1);
  
  $form->show();

?>
  
  <script type="text/javascript">
  // <![CDATA[
    jQuery(function($){
      var names = new Array(5);
      names[1] = $('textarea.rex-a630-type-1').attr('name');
      names[2] = $('input.rex-a630-type-2').attr('name');
      names[3] = $('input.rex-a630-type-3').attr('name');
      names[4] = $('select.rex-a630-type-4').attr('name');
      $('.rex-a630-hidden').parent().hide().children('input, textarea, select').attr('name','');
      $('.rex-a630-type-select select').change(function(){
        var key = $(this).children('option:selected').val();
        $('.rex-a630-type').addClass('rex-a630-hidden');
        $('.rex-a630-type-'+key).removeClass('rex-a630-hidden').parent().show().children('input, textarea, select').attr('name',names[key]);
        $('.rex-a630-hidden').parent().hide().children('input, textarea, select').attr('name','');
        if (key == 4)
          $('select.rex-a630-type-4').change();
        else
          $('.rex-a630-environment option').attr('disabled','');
      });
      $('select.rex-a630-type-4').change(function(){
        $('.rex-a630-environment option').attr('disabled','');<?php echo $js; ?>
      });
      if ($('.rex-a630-type-select option:eq(3)').is(':selected'))
        $('select.rex-a630-type-4').change();
    });
  // ]]>
  </script>
  
<?php 

}