<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_a630_form extends rex_form
{
  function rex_a630_form($tableName, $fieldset, $whereCondition, $method = 'post', $debug = false)
  {
    parent::rex_form($tableName, $fieldset, $whereCondition, $method, $debug);
  }
  
  function &addIntervalField($name, $value = null, $attributes = array())
  {
    $attributes['internal::fieldClass'] = 'rex_a630_form_interval_element';
    $attributes['class'] = 'rex-form-text rex-form-select';
    $field =& $this->addField('', $name, $value, $attributes, true);
    return $field;
  }
  
  function preSave($fieldsetName, $fieldName, $fieldValue, &$saveSql)
  {
    global $REX;

    static $setOnce = false;
    
    if ($fieldName == 'interval') 
    {
      $value = explode('|',$fieldValue);
      $interval = array('h'=>3600, 'd'=>86400, 'w'=>604800, 'm'=>2635200, 'y'=>31557600);
      $saveSql->setValue('interval_sec', $value[1] * $interval[$value[2]]);
    }

    if(!$setOnce)
    {
      $saveSql->setValue('updateuser', $REX['USER']->getValue('login'));

      $saveSql->setValue('updatedate', time());

      if(!$this->isEditMode())
      {
        $saveSql->setValue('createuser', $REX['USER']->getValue('login'));

        $saveSql->setValue('createdate', time());
      }
      $setOnce = true;
    }

    return $fieldValue;
  }
  
  function save() 
  {
    $return = parent::save();
    rex_a630_cronjob::saveNextTime();
    return $return;
  }
}

class rex_a630_form_interval_element extends rex_form_element
{
  function rex_a630_form_interval_element($tag, &$table, $attributes = array(), $separateEnding = false)
  {
    parent::rex_form_element($tag, $table, $attributes, $separateEnding);
  }
  
  function formatElement()
  {
    global $I18N;
    $name = $this->getAttribute('name').'[]';
    $value = explode('|',htmlspecialchars($this->getValue()));
    if (count($value) != 4)
      $value = array(null,1,'w');
    
    $select = new rex_select();
    $select->setAttribute('class','rex-form-select rex-a630-interval');
    $select->setStyle('width:120px');
    $select->setName($name);
    $select->setSize(1);
    $select->addOption($I18N->msg('cronjob_interval_hour'),'h');
    $select->addOption($I18N->msg('cronjob_interval_day'),'d');
    $select->addOption($I18N->msg('cronjob_interval_week'),'w');
    $select->addOption($I18N->msg('cronjob_interval_month'),'m');
    $select->addOption($I18N->msg('cronjob_interval_year'),'y');
    $select->setSelected($value[2]);

    return '
      <input type="text" class="rex-form-text rex-a630-interval" name="'.$name.'" style="width:20px; margin-right: 5px;" value="'.$value[1].'" />
      '. $select->get() . "\n";

  }
}