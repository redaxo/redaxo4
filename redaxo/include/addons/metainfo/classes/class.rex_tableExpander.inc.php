<?php
/**
 * MetaForm Addon
 * 
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */

define('REX_A62_FIELD_TEXT',                 1);
define('REX_A62_FIELD_TEXTAREA',             2);
define('REX_A62_FIELD_SELECT',               3);
define('REX_A62_FIELD_RADIO',                4);
define('REX_A62_FIELD_CHECKBOX',             5);
define('REX_A62_FIELD_REX_MEDIA_BUTTON',     6);
define('REX_A62_FIELD_REX_MEDIALIST_BUTTON', 7);
define('REX_A62_FIELD_REX_LINK_BUTTON',      8);

define('REX_A62_FIELD_COUNT',                8);
 
require_once $REX['INCLUDE_PATH'].'/addons/metainfo/classes/class.rex_tableManager.inc.php';

class rex_a62_tableExpander extends rex_form
{
  var $metaPrefix;
  var $tableManager;
  
  function rex_a62_tableExpander($metaPrefix, $metaTable, $tableName, $fieldset, $whereCondition, $method = 'post', $debug = false)
  {
    $this->metaPrefix = $metaPrefix;
    $this->tableManager = new rex_a62_tableManager($metaTable);
    
    parent::rex_form($tableName, $fieldset, $whereCondition, $method, $debug);
  }
  
  function init()
  {
    global $REX, $I18N_META_INFOS;
    
    $field =& $this->addReadOnlyField('prefix', $this->metaPrefix);
    $field->setLabel($I18N_META_INFOS->msg('field_label_prefix'));

    $field =& $this->addTextField('name');
    $field->setLabel($I18N_META_INFOS->msg('field_label_name'));

    $field =& $this->addSelectField('prior');
    $field->setLabel($I18N_META_INFOS->msg('field_label_prior'));
    $select =& $field->getSelect();
    $select->setSize(1);
    $select->addOption($I18N_META_INFOS->msg('field_first_prior'), 1);
    // Im Edit Mode das Feld selbst nicht als Position einfügen
    $qry = 'SELECT name,prior FROM '. $this->tableName .' WHERE `name` LIKE "'. $this->metaPrefix .'%"';
    if($this->isEditMode())
    {
      $qry .= ' AND field_id != '. $this->getParam('field_id');
    }
    $qry .=' ORDER BY prior';
    $sql = new rex_sql();
    $sql->setQuery($qry);
    for($i = 0; $i < $sql->getRows(); $i++)
    {
      $select->addOption(
        $I18N_META_INFOS->msg('field_after_prior', $sql->getValue('name')),
        $sql->getValue('prior')+1
      );
      $sql->next();
    }
    
    $field =& $this->addTextField('title');
    $field->setLabel($I18N_META_INFOS->msg('field_label_title'));
    
    $field =& $this->addSelectField('type');
    $field->setLabel($I18N_META_INFOS->msg('field_label_type'));
    $field->setAttribute('onchange', 'checkConditionalFields(this, new Array('. REX_A62_FIELD_SELECT .','. REX_A62_FIELD_RADIO .','. REX_A62_FIELD_CHECKBOX .'));');
    $select =& $field->getSelect();
    $select->setSize(1);
    $select->addSqlOptions('SELECT label,id FROM '. $REX['TABLE_PREFIX'] .'62_type');
    
    $notices = '';
    for($i = 1; $i < REX_A62_FIELD_COUNT; $i++)
    {
      if($I18N_META_INFOS->hasMsg('field_params_notice_'. $i))
      {
        $notices .= '<span id="a62_field_params_notice_'. $i .'" style="display:none">'. $I18N_META_INFOS->msg('field_params_notice_'. $i) .'</span>'. "\n";
      }
    }
    $notices .= '
    <script type="text/javascript">
      var needle = new getObj("'. $field->getAttribute('id') .'");
      
      checkConditionalFields(needle.obj, new Array('. REX_A62_FIELD_SELECT .','. REX_A62_FIELD_RADIO .','. REX_A62_FIELD_CHECKBOX .'));
    </script>';
    
    $field =& $this->addTextAreaField('params');
    $field->setLabel($I18N_META_INFOS->msg('field_label_params'));
    $field->setSuffix($notices);

    $field =& $this->addTextAreaField('attributes');
    $field->setLabel($I18N_META_INFOS->msg('field_label_attributes'));
    $notice = '<span id="a62_field_attributes_notice">'. $I18N_META_INFOS->msg('field_attributes_notice') .'</span>'. "\n";
    $field->setSuffix($notice);

    $field =& $this->addTextField('default');
    $field->setLabel($I18N_META_INFOS->msg('field_label_default'));

//    $field =& $this->addTextAreaField('validate');
//    $field->setLabel($I18N_META_INFOS->msg('field_label_validate'));
  }
  
  function getFieldsetName()
  {
    global $I18N_META_INFOS;
    return $I18N_META_INFOS->msg('field_fieldset');
  }
  
  function getFieldValue($fieldName)
  {
    $el =& $this->getElement($this->getFieldsetName(), $fieldName);
    return $el->getValue();
  }
  
  function delete()
  {
    if(parent::delete())
    {
      return $this->tableManager->deleteColumn($this->getFieldValue('name'));
    }
    return false;
  }
  
  function preSave($fieldsetName, $fieldName, $fieldValue, &$saveSql)
  {
    global $REX;
    
    if($fieldsetName == $this->getFieldsetName() && $fieldName == 'name')
    {
      // Den Namen mit Prefix speichern
      return $this->addPrefix($fieldValue);
    }
    
    return parent::preSave($fieldsetName, $fieldName, $fieldValue, $saveSql);
  }
  
  function preView($fieldsetName, $fieldName, $fieldValue)
  {
    if($fieldsetName == $this->getFieldsetName() && $fieldName == 'name')
    {
      // Den Namen ohne Prefix anzeigen
      return $this->stripPrefix($fieldValue);
    }
    return parent::preView($fieldsetName, $fieldName, $fieldValue);
  }
  
  function addPrefix($string)
  {
    $lowerString = strtolower($string);
    if(substr($lowerString, 0, strlen($this->metaPrefix)) !== $this->metaPrefix)
    {
      return $this->metaPrefix . $string;
    }
    return $string;
  }
  
  function stripPrefix($string)
  {
    $lowerString = strtolower($string);
    if(substr($lowerString, 0, strlen($this->metaPrefix)) === $this->metaPrefix)
    {
      return substr($string, strlen($this->metaPrefix));
    }
    return $string;
  }
  
  function save()
  {
    global $I18N_META_INFOS;
    
    $fieldName = $this->getFieldValue('name');
    if($fieldName == '')
      return $I18N_META_INFOS->msg('field_error_name');
      
    if(preg_match('/[^a-z0-9\_]/', $fieldName))
      return $I18N_META_INFOS->msg('field_error_chars_name');
     
    // Prüfen ob schon eine Spalte mit dem Namen existiert (nur beim add nötig)
    if(!$this->isEditMode())
    {
      $sql = new rex_sql();
      $sql->setQuery('SELECT * FROM '. $this->tableName .' WHERE name="'. $this->addPrefix($fieldName) .'" LIMIT 1');
      if($sql->getRows() == 1)
      {
        return $I18N_META_INFOS->msg('field_error_unique_name');
      }
    }
      
    // Den alten Wert aus der DB holen
    // Dies muss hier geschehen, da in parent::save() die Werte für die DB mit den 
    // POST werten überschrieben werden!
    $fieldOldName = '';
    $fieldOldPrior = 1;
    if($this->sql->getRows() == 1)
    { 
      $fieldOldName = $this->sql->getValue('name');
      $fieldOldPrior = $this->sql->getValue('prior');
    }
      
    if(parent::save())
    {
      global $REX, $I18N;
      
      $this->organizePriorities($this->getFieldValue('prior'), $fieldOldPrior);
      
      $fieldName = $this->addPrefix($fieldName);
      $fieldType = $this->getFieldValue('type');
      $fieldDefault = $this->getFieldValue('default');
      
      $sql = rex_sql::getInstance();
      $result = $sql->getArray('SELECT `dbtype`, `dblength` FROM `'. $REX['TABLE_PREFIX'] .'62_type` WHERE id='. $fieldType);
      $fieldDbType = $result[0]['dbtype'];
      $fieldDbLength = $result[0]['dblength'];
      
      // TEXT Spalten dürfen in MySQL keine Defaultwerte haben
      if($fieldDbType == 'text')
        $fieldDefault = null;
        
      rex_set_session('A62_MESSAGE', $I18N_META_INFOS->msg('field_update_notice'));
      
      if($this->isEditMode())
      {
        // Spalte in der Tabelle verändern
        return $this->tableManager->editColumn($fieldOldName, $fieldName, $fieldDbType, $fieldDbLength, $fieldDefault);
      }
      else
      {
        // Spalte in der Tabelle anlegen
        return $this->tableManager->addColumn($fieldName, $fieldDbType, $fieldDbLength, $fieldDefault);
      }
    }
    
    return false;
  }
  
  function organizePriorities($newPrio, $oldPrio)
  {
    if($newPrio == $oldPrio)
      return;
      
    if ($newPrio < $oldPrio)
      $addsql = 'desc';
    else
      $addsql = 'asc';
      
    $sql = new rex_sql();
//    $sql->debugsql = true;
    $sql->setQuery('SELECT field_id FROM '. $this->tableName .' WHERE name LIKE "'. $this->metaPrefix .'%" ORDER BY prior, updatedate '. $addsql);
    
    $updateSql = new rex_sql();
//    $updateSql->debugsql = true;
    $updateSql->setTable($this->tableName);
    
    for($i = 0; $i < $sql->getRows(); $i++)
    {
      $updateSql->setValue('prior', $i+1);
      $updateSql->setWhere('name LIKE "'. $this->metaPrefix .'%" AND field_id = '. $sql->getValue('field_id'));
      $updateSql->update();
      $sql->next();
    }
  }
}
?>