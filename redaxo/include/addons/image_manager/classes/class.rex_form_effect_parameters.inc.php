<?php

class rex_imanager_effect_parameters extends rex_form_element
{
  var $effectFields;

  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_imanager_effect_parameters($tag = '', &$table, $attributes = array())
  {
    parent::rex_form_element('', $table, $attributes);
  }

  function formatElement()
  {
    $format = '';
    $effects = rex_imanager_supportedEffects();
     
    foreach($effects as $effectClass => $effectFile)
    {
      require_once($effectFile);
      $effectObj = new $effectClass();
      $effectParams = $effectObj->getParams();
      $id = $effectClass;
      
      if(empty($effectParams)) continue;

      $format .= '<div id="rex-effect-'. $id .'" style="display: none">';
      foreach($effectParams as $param)
      {
        $field = null;
        $name = $effectClass.'_'.$param['name'];
        $value = isset($param['default']) ? $param['default'] : null;
        switch($param['type'])
        {
          case 'int' :
          case 'float' :
          case 'string' :
            {
              $type = 'text';
              $field =& $this->table->createInput($type, $name, $value, $attributes = array());
              $field->setLabel($param['label']);
              break;
            }
          case 'select' :
            {
              $type = $param['type'];
              $field =& $this->table->createInput($type, $name, $value, $attributes = array());
              $field->setLabel($param['label']);
              $select =& $field->getSelect();
              $select->addOptions($param['options'], true);
              break;
            }
          case 'media' :
            {
              $type = $param['type'];
              $field =& $this->table->createInput($type, $name, $value, $attributes = array());
              $field->setLabel($param['label']);
              break;
            }
          default:var_dump($param);
        }

        if($field)
        {
          $this->effectFields[] = $field;
          $format .= $field->get();
        }
      }
      $format .= '</div>';
    }
     
    return $format;
  }
}