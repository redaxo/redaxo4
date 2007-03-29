<?php
/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
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

		$field =& $this->addTextAreaField('attributes');
		$field->setLabel($I18N_META_INFOS->msg('field_label_attributes'));
		
		$field =& $this->addSelectField('type');
		$field->setLabel($I18N_META_INFOS->msg('field_label_type'));
		$select =& $field->getSelect();
		$select->setSize(1);
		$select->addSqlOptions('SELECT label,id FROM '. $REX['TABLE_PREFIX'] .'62_type');
		
		$field =& $this->addTextField('default');
		$field->setLabel($I18N_META_INFOS->msg('field_label_default'));

		$field =& $this->addTextAreaField('params');
		$field->setLabel($I18N_META_INFOS->msg('field_label_params'));
		
//		$field =& $this->addTextAreaField('validate');
//		$field->setLabel($I18N_META_INFOS->msg('field_label_validate'));
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
		// TODO Translate
		if(!$this->isEditMode() && $this->getElementPostValue($this->getFieldsetName(), 'name') == '')
			return 'Bitte Feldnamen eingeben!';
			
		// Da die POST werte erst in parent::save() übernommen werden, 
		// kann hier noch der vorhergehende Wert abgegriffen werden
		$fieldOldName = $this->getFieldValue('name');
		
		if(parent::save())
		{
			global $REX;
			
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
}
?>