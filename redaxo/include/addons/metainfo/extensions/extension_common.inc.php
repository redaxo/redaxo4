<?php

/**
 * MetaForm Addon
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id: extension_common.inc.php,v 1.18 2008/03/25 16:53:41 kills Exp $
 */

rex_register_extension('OOMEDIA_IS_IN_USE_QUERY', 'rex_a62_media_is_in_use');

/**
 * Erstellt den nötigen HTML Code um ein Formular zu erweitern
 *
 * @param $sqlFields rex_sql-objekt, dass die zu verarbeitenden Felder enthält
 * @param $activeItem objekt, dass mit getValue() die Werte des akuellen Eintrags zurückgibt
 * @param $formatCallback callback, dem die infos als Array übergeben werden und den formatierten HTML Text zurückgibt
 */
function rex_a62_metaFields($sqlFields, $activeItem, $formatCallback, $epParams)
{
  global $I18N, $REX_USER;

  $s = '';

  // Startwert für MEDIABUTTON, MEDIALIST, LINKLIST zähler
  $media_id = 1;
  $mlist_id = 1;
  $link_id  = 1;
  $llist_id = 1;

  $sqlFields->reset();
  for($i = 0; $i < $sqlFields->getRows(); $i++, $sqlFields->next())
  {
    // Umschliessendes Tag von Label und Formularelement
    $tag      = 'p';
    $tag_attr = '';

    $name      = $sqlFields->getValue('name');
    $title     = $sqlFields->getValue('title');
    $params    = $sqlFields->getValue('params');
    $typeLabel = $sqlFields->getValue('label');
    $attr      = $sqlFields->getValue('attributes');
    $dblength  = $sqlFields->getValue('dblength');
    
    $attrArray = rex_split_string($attr);
    if(isset($attrArray['perm']))
    {
      if(!$REX_USER->hasPerm($attrArray['perm']))
      {
        continue;
      }
      unset($attrArray['perm']);
    }
    
    $dbvalues = array(htmlspecialchars($sqlFields->getValue('default')));
    $dbvalues_esc = $dbvalues;
    if($activeItem)
    {
      $itemValue = $activeItem->getValue($name);

      if(strpos($itemValue, '|+|') !== false)
      {
        // Alte notation mit |+| als Trenner
        $dbvalues = explode('|+|', $activeItem->getValue($name));
      }
      else
      {
        // Neue Notation mit | als Trenner
        $dbvalues = explode('|', $activeItem->getValue($name));
      }

      $dbvalues_esc = array_map('htmlspecialchars', $dbvalues);
    }

    if($title != '')
      $label = htmlspecialchars(rex_translate($title));
    else
      $label = htmlspecialchars($name);

    $id = preg_replace('/[^a-zA-Z\-0-9_]/', '_', $label);
    $attr .= rex_tabindex();
    $labelIt = true;

    $field = '';
    switch($typeLabel)
    {
      case 'text':
      {
        $tag_attr = ' class="rex-form-text"';
        $field = '<input class="rex-form-text" type="'. $typeLabel .'" name="'. $name .'" value="'. $dbvalues_esc[0] .'" id="'. $id .'" maxlength="'. $dblength .'" '. $attr .' />';
        break;
      }
      case 'checkbox':
        $name .= '[]';
      case 'radio':
      {
        $values = array();
        if(rex_sql::getQueryType($params) == 'SELECT')
        {
          $sql = new rex_sql();
          $value_groups = $sql->getDBArray($params, MYSQL_NUM);
          foreach($value_groups as $value_group)
          {
            if(isset($value_group[1]))
              $values[$value_group[1]] = $value_group[0];
            else
              $values[$value_group[0]] = $value_group[0];
          }
        }
        else
        {
          $value_groups = explode('|', $params);
          foreach($value_groups as $value_group)
          {
            if(strpos($value_group, ':') !== false)
            {
              $temp = explode(':', $value_group);
              $values[$temp[0]] = $temp[1];
            }
            else
            {
              $values[$value_group] = $value_group;
            }
          }
        }

        $class_s = $typeLabel;
        $class_p = $typeLabel == 'radio' ? 'radios' : 'checkboxes';
        $oneValue = (count($values) == 1);

        if(!$oneValue)
        {
          $labelIt = false;
          $tag = 'div';
          $tag_attr = ' class="rex-form-col-a rex-form-'.$class_p.'"';
          $field .= '<p class="rex-form-label">'. $label .'</p><div class="rex-form-'.$class_p.'-wrapper">';
        }

        foreach($values as $key => $value)
        {
          $id = preg_replace('/[^a-zA-Z\-0-9_]/', '_', $id . $key);
          $key = htmlspecialchars($key);

          // wenn man keine Werte angibt (Boolean Chkbox/Radio)
          // Dummy Wert annehmen, damit an/aus unterscheidung funktioniert
          if($oneValue && $key == '')
            $key = 'true';

          $selected = '';
          if(in_array($key, $dbvalues_esc))
            $selected = ' checked="checked"';

          if($oneValue)
          {
            $tag_attr = ' class="rex-form-col-a rex-form-'. $class_s .'"';
            $field .= '<input class="rex-form-'.$class_s.'" type="'. $typeLabel .'" name="'. $name .'" value="'. $key .'" id="'. $id .'" '. $attr . $selected .' />'."\n";
          }
          else
          {
            $field .= '<p class="rex-form-'. $class_s .' rex-form-label-right">'."\n";
            $field .= '<input class="rex-form-'. $class_s .'" type="'. $typeLabel .'" name="'. $name .'" value="'. $key .'" id="'. $id .'" '. $attr . $selected .' />'."\n";
            $field .= '<label for="'. $id .'">'. htmlspecialchars($value) .'</label>';
            $field .= '</p>'."\n";
          }

        }
        if(!$oneValue)
        {
        	$field .= '</div>';
        }

        break;
      }
      case 'select':
      {
        $tag_attr = ' class="rex-form-select"';
        
        $select = new rex_select();
				$select->setStyle('class="rex-form-select"');
        $select->setName($name);
        $select->setId($id);
        // hier mit den "raw"-values arbeiten, da die rex_select klasse selbst escaped
        $select->setSelected($dbvalues);

				$multiple = FALSE;
        foreach(rex_split_string($attr) as $attr_name => $attr_value)
        {
          if(empty($attr_name)) continue;

          $select->setAttribute($attr_name, $attr_value);

          if($attr_name == 'multiple')
          {
          	$multiple = TRUE;
            $select->setName($name.'[]');
          }
        }
        
        if(!$multiple)
        	$select->setSize(1);

        if(rex_sql::getQueryType($params) == 'SELECT')
        {
          // Werte via SQL Laden
          $select->addDBSqlOptions($params);
        }
        else
        {
          // Optionen mit | separiert
          // eine einzelne Option kann mit key:value separiert werden
          $values = array();
          $value_groups = explode('|', $params);
          foreach($value_groups as $value_group)
          {
            if(strpos($value_group, ':') !== false)
            {
              $temp = explode(':', $value_group);
              $values[$temp[0]] = $temp[1];
            }
            else
            {
              $values[$value_group] = $value_group;
            }
          }
          $select->addOptions($values);
        }


        $field .= $select->get();
        break;
      }
      case 'datetime':
      case 'date':
      {
        $tag_attr = ' class="rex-form-select-date"';
        
        $active = $dbvalues_esc[0] != 0;
        if($dbvalues_esc[0] == '')
          $dbvalues_esc[0] = time();

        $style = 'class="rex-form-select-date"';
        $yearStyle = 'class="rex-form-select-year"';

        $yearSelect = new rex_select();
        $yearSelect->addOptions(range(2005,date('Y')+10), true);
        $yearSelect->setName($name.'[year]');
        $yearSelect->setSize(1);
        $yearSelect->setId($id);
        $yearSelect->setStyle($yearStyle);
        $yearSelect->setSelected(date('Y', $dbvalues_esc[0]));

        $monthSelect = new rex_select();
        $monthSelect->addOptions(range(1,12), true);
        $monthSelect->setName($name.'[month]');
        $monthSelect->setSize(1);
        $monthSelect->setStyle($style);
        $monthSelect->setSelected(date('m', $dbvalues_esc[0]));

        $daySelect = new rex_select();
        $daySelect->addOptions(range(1,31), true);
        $daySelect->setName($name.'[day]');
        $daySelect->setSize(1);
        $daySelect->setStyle($style);
        $daySelect->setSelected(date('j', $dbvalues_esc[0]));

        if($typeLabel == 'datetime')
        {
          $hourSelect = new rex_select();
          $hourSelect->addOptions(range(0,23), true);
          $hourSelect->setName($name.'[hour]');
          $hourSelect->setSize(1);
          $hourSelect->setStyle($style);
          $hourSelect->setSelected(date('G', $dbvalues_esc[0]));

          $minuteSelect = new rex_select();
          $minuteSelect->addOptions(range(0,59), true);
          $minuteSelect->setName($name.'[minute]');
          $minuteSelect->setSize(1);
          $minuteSelect->setStyle($style);
          $minuteSelect->setSelected(date('i', $dbvalues_esc[0]));

          $field = $daySelect->get() . $monthSelect->get() . $yearSelect->get() .'-'. $hourSelect->get() . $minuteSelect->get();
        }
        else
        {
          $field = $daySelect->get() . $monthSelect->get() . $yearSelect->get();
        }
        $checked = $active ? ' checked="checked"' : '';
        $field .= '<input class="rex-form-select-checkbox rex-metainfo-checkbox" type="checkbox" name="'. $name .'[active]" value="1"'. $checked .' />';
        break;
      }
      case 'textarea':
      {
        $tag_attr = ' class="rex-form-textarea"';
        
        $field = '<textarea class="rex-form-textarea" name="'. $name .'" id="'. $id .'" cols="50" rows="6" '. $attr .'>'. $dbvalues_esc[0] .'</textarea>';
        break;
      }
      case 'legend':
      {
        $tag = '';
        $tag_attr = '';
        $labelIt = false;
        
        $field = '</div></fieldset><fieldset class="rex-form-col-1"><legend id="'. $id .'">'. $label .'</legend><div class="rex-form-wrapper">';
        break;
      }
      case 'REX_MEDIA_BUTTON':
      {
        $tag = 'div';
        $tag_attr = ' class="rex-form-widget"';

        $field = rex_var_media::getMediaButton($media_id);
        $field = str_replace('REX_MEDIA['. $media_id .']', $dbvalues_esc[0], $field);
        $field = str_replace('MEDIA['. $media_id .']', $name, $field);
        $id = 'REX_MEDIA_'. $media_id;
        $media_id++;
        break;
      }
      case 'REX_MEDIALIST_BUTTON':
      {
        $tag = 'div';
        $tag_attr = ' class="rex-form-widget"';

        $name .= '[]';
        $field = rex_var_media::getMediaListButton($mlist_id, implode(',',$dbvalues_esc));
        $field = str_replace('MEDIALIST['. $mlist_id .']', $name, $field);
        $id = 'REX_MEDIALIST_'. $mlist_id;

        $mlist_id++;
        break;
      }
      case 'REX_LINK_BUTTON':
      {
        $tag = 'div';
        $tag_attr = ' class="rex-form-widget"';

        $category = '';
        if($activeItem)
          $category = $activeItem->getValue('category_id');

        $field = rex_var_link::getLinkButton($link_id, $dbvalues_esc[0], $category);
        $field = str_replace('LINK['. $link_id .']', $name, $field);
        $id = 'LINK_'. $link_id;

        $link_id++;
        break;
      }
      case 'REX_LINKLIST_BUTTON':
      {
        $tag = 'div';
        $tag_attr = ' class="rex-form-widget"';

        $category = '';
        if($activeItem)
          $category = $activeItem->getValue('category_id');

        $name .= '[]';
        $field = rex_var_link::getLinklistButton($llist_id, implode(',',$dbvalues), $category);
        $field = str_replace('LINKLIST['. $llist_id .']', $name, $field);
        $id = 'REX_LINKLIST_'. $llist_id;

        $llist_id++;
        break;
      }
      default :
      {
        // ----- EXTENSION POINT
        list($field, $tag, $tag_attr, $id, $label, $labelIt) =
          rex_register_extension_point( 'A62_CUSTOM_FIELD',
            array(
              $field,
              $tag,
              $tag_attr,
              $id,
              $label,
              $labelIt,
              'values' => $dbvalues_esc,
              'rawvalues' => $dbvalues,
              'type' => $typeLabel,
              'sql' => $sqlFields)
            );
      }
    }

    $s .= rex_call_func($formatCallback, array($field, $tag, $tag_attr, $id, $label, $labelIt, $typeLabel), false);
  }

  return $s;
}

/**
 * Übernimmt die gePOSTeten werte in ein rex_sql-Objekt
 *
 * @param $sqlSave rex_sql-objekt, in das die aktuellen Werte gespeichert werden sollen
 * @param $sqlFields rex_sql-objekt, dass die zu verarbeitenden Felder enthält
 */
function _rex_a62_metainfo_handleSave(&$params, &$sqlSave, $sqlFields)
{
  global $REX_USER;
  
  if($_SERVER['REQUEST_METHOD'] != 'POST') return;

  for($i = 0;$i < $sqlFields->getRows(); $i++, $sqlFields->next())
  {
    $fieldName = $sqlFields->getValue('name');
    $fieldType = $sqlFields->getValue('type');
    $fieldAttributes = $sqlFields->getValue('attributes');
    $postValue = rex_post($fieldName, 'array');

    // dont save restricted fields
    $attrArray = rex_split_string($fieldAttributes);
    if(isset($attrArray['perm']))
    {
      if(!$REX_USER->hasPerm($attrArray['perm']))
      {
        continue;
      }
      unset($attrArray['perm']);
    }
    
    // handle date types with timestamps
    if(isset($postValue['year']) && isset($postValue['month']) && isset($postValue['day']) && isset($postValue['hour']) && isset($postValue['minute']))
    {
      if(isset($postValue['active']))
        $saveValue = mktime((int)$postValue['hour'],(int)$postValue['minute'],0,(int)$postValue['month'],(int)$postValue['day'],(int)$postValue['year']);
      else
        $saveValue = 0;
    }
    // handle date types without timestamps
    elseif(isset($postValue['year']) && isset($postValue['month']) && isset($postValue['day']))
    {
      if(isset($postValue['active']))
        $saveValue = mktime(0,0,0,(int)$postValue['month'],(int)$postValue['day'],(int)$postValue['year']);
      else
        $saveValue = 0;
    }
    else
    {
      if(count($postValue) > 1)
      {
        // Mehrwertige Felder
        $saveValue = '|'. implode('|', $postValue) .'|';
      }
      else
      {
        $postValue = isset($postValue[0]) ? $postValue[0] : '';
        if($fieldType == REX_A62_FIELD_SELECT && strpos($fieldAttributes, 'multiple') !== false ||
           $fieldType == REX_A62_FIELD_CHECKBOX)
        {
          // Mehrwertiges Feld, aber nur ein Wert ausgewählt
          $saveValue = '|'. $postValue .'|';
        }
        else
        {
          // Einwertige Felder
          $saveValue = $postValue;
        }
      }
    }

    // Wert in SQL zum speichern
    $sqlSave->setValue($fieldName, $saveValue);

    // Werte im aktuellen Objekt speichern, dass zur Anzeige verwendet wird
    if(isset($params['activeItem']))
      $params['activeItem']->setValue($fieldName, stripslashes($saveValue));
  }
}

/**
 * Erweitert das Meta-Formular um die neuen Meta-Felder
 *
 * @param $prefix Feldprefix
 * @param $params EP Params
 * @param $saveCallback callback, dass die
 */
function _rex_a62_metainfo_form($prefix, $params, $saveCallback)
{
  global $REX;

  $debug = false;

  $qry = 'SELECT
            *
          FROM
            '. $REX['TABLE_PREFIX'] .'62_params p,
            '. $REX['TABLE_PREFIX'] .'62_type t
          WHERE
            `p`.`type` = `t`.`id` AND
            `p`.`name` LIKE "'. $prefix .'%"
          ORDER BY
            prior';

  $sqlFields = new rex_sql();
  // $sqlFields->debugsql = true;
  $sqlFields->setQuery($qry);

  $params = rex_call_func($saveCallback, array($params, $sqlFields), false);

  // Beim ADD gibts noch kein activeItem
  $activeItem = null;
  if(isset($params['activeItem']))
    $activeItem = $params['activeItem'];

  return rex_a62_metaFields($sqlFields, $activeItem, 'rex_a62_metainfo_form_item', $params);
}

/**
 * Artikel & Kategorien:
 *
 * Übernimmt die gePOSTeten werte in ein rex_sql-Objekt und speichert diese
 */
function _rex_a62_metainfo_cat_handleSave($params, $sqlFields)
{
  if($_SERVER['REQUEST_METHOD'] != 'POST') return $params;

  global $REX;

  $article = rex_sql::getInstance();
//  $article->debugsql = true;
  $article->setTable($REX['TABLE_PREFIX']. 'article');
  $article->setWhere('id='. $params['id'] .' AND clang='. $params['clang']);

  _rex_a62_metainfo_handleSave($params, $article, $sqlFields);

  $article->update();

  // Artikel nochmal mit den zusätzlichen Werten neu generieren
  rex_generateArticle($params['id']);

  return $params;
}

function _rex_a62_metainfo_art_handleSave($params, $sqlFields)
{ 
	// Nur speichern wenn auch das MetaForm ausgefüllt wurde
	// z.b. nicht speichern wenn über be_search select navigiert wurde
  if(rex_post('meta_article_name', 'string', null) === null) return $params;
  
  return _rex_a62_metainfo_cat_handleSave($params, $sqlFields);
}

/**
 * Medien:
 *
 * Übernimmt die gePOSTeten werte in ein rex_sql-Objekt und speichert diese
 */
function _rex_a62_metainfo_med_handleSave($params, $sqlFields)
{
  if($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($params['file_id'])) return $params;

  global $REX;

  $media = rex_sql::getInstance();
//  $media->debugsql = true;
  $media->setTable($REX['TABLE_PREFIX']. 'file');
  $media->setWhere('file_id='. $params['file_id']);

  _rex_a62_metainfo_handleSave($params, $media, $sqlFields);

  $media->update();

  return $params;
}

function rex_a62_media_is_in_use($params)
{
  $query = $params['subject'];

  $sql = new rex_sql();
  $sql->setQuery('SELECT name,type FROM rex_62_params WHERE type IN(6,7)');

  $rows = $sql->getRows();
  if($rows == 0)
    return $query;

  $where = array();
  $filename = addslashes($params['filename']);
  for($i = 0; $i < $rows; $i++)
  {
    switch ($sql->getValue('type'))
    {
      case '6':
        $where[] = $sql->getValue('name') .'="'. $filename .'"';
        break;
      case '7':
        $where[] = $sql->getValue('name') .' LIKE "%|'. $filename .'"|%';
        break;
      default :
        trigger_error ('Unexpected fieldtype "'. $sql->getValue('type') .'"!', E_USER_ERROR);
    }
  }

  $query .= "\n" .'UNION'. "\n";
  $query .='SELECT DISTINCT id, clang FROM rex_article WHERE '. implode(' OR ', $where);

  return $query;
}