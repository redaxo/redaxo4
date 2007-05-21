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
  
  function prepareSave($fieldsetName, $fieldName, $fieldValue)
  {
    if($fieldsetName == $this->getFieldsetName() && $fieldName == 'name')
    {
      $fieldValue = preg_replace('/[^a-z0-9\_]/','', strtolower($fieldValue));
      if(substr($fieldValue, 0, strlen($this->metaPrefix)) !== $this->metaPrefix)
      {
        // Das name feld mit Prefix versehen
        return $this->metaPrefix . $fieldValue;
      }
    }
    return parent::prepareSave($fieldsetName, $fieldName, $fieldValue);
  }
  
  function save()
  {
    global $I18N_META_INFOS;
    
    $postName = $this->getElementPostValue($this->getFieldsetName(), 'name');
    if(!$this->isEditMode() && $postName == '')
      return $I18N_META_INFOS->msg('field_error_name');
      
    if(preg_match('/[^a-z0-9\_]/', $postName))
      return $I18N_META_INFOS->msg('field_error_chars_name');
      
    $sql = new rex_sql();
    $sql->setQuery('SELECT * FROM '. $this->tableName .' WHERE name="'. $this->metaPrefix . $postName .'" LIMIT 1');
    if($sql->getRows() == 1)
    {
      return $I18N_META_INFOS->msg('field_error_unique_name');
    }
      
    // Da die POST werte erst in parent::save() übernommen werden, 
    // kann hier noch der vorhergehende Wert abgegriffen werden
    $fieldOldName = $this->getFieldValue('name');
    
    if(parent::save())
    {
      global $REX, $I18N;
      
      $fieldName = $this->getFieldValue('name');
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
        if($this->tableManager->addColumn($fieldName, $fieldDbType, $fieldDbLength, $fieldDefault))
        {
          // Alles ok, Meldung zurückgeben
          return $I18N_META_INFOS->msg('field_successfull_saved');
        }
      }
    }
    
    return false;
  }
}
?>