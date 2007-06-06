<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
rex_register_extension('OUTPUT_FILTER', 'rex_a62_insertJs');

/**
 * Fügt den nötigen JS-Code ein
 */
function rex_a62_insertJs($params)
{
	global $REX;
	
	$content = $params['subject'];

	$jsfile = $REX['INCLUDE_PATH'] .'/addons/metainfo/js/metainfo.js';
	$hdl = fopen($jsfile, 'r');
	$jscontent = fread($hdl, filesize($jsfile));
	fclose($hdl);
  
  $js ='
	  <script type="text/javascript">
	  <!--
	  '. $jscontent .'
	  //-->
	  </script>
  ';
  
  return str_replace('</head>', $js . '</head>', $content);
}

/**
 * Erstellt den nötigen HTML Code um ein Formular zu erweitern
 * 
 * @param $sqlFields rex_sql-objekt, dass die zu verarbeitenden Felder enthält
 * @param $activeItem objekt, dass mit getValue() die Werte des akuellen Eintrags zurückgibt
 * @param $formatCallback callback, dem die infos als Array übergeben werden und den formatierten HTML Text zurückgibt
 */
function rex_a62_metaFields($sqlFields, $activeItem, $formatCallback, $params)
{
  $s = '';
  
  // Startwert für MEDIABUTTON, MEDIALIST, LINKLIST
  $media_id = 1;
  $mlist_id = 1;
  $link_id  = 1;
  
  for($i = 0; $i < $sqlFields->getRows(); $i++)
  {
    // Umschliessendes Tag von Label und Formularelement
    $tag = 'p';
    $tag_attr = '';
  
    $name = $sqlFields->getValue('name');
    $title = $sqlFields->getValue('title');
    $params = $sqlFields->getValue('params');
    $typeLabel = $sqlFields->getValue('label');
    $attr = $sqlFields->getValue('attributes');
    $dbvalues = explode('|+|', $activeItem->getValue($name));
    
    if($title != '')
      $label = htmlspecialchars($title);
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
        $field = '<input type="'. $sqlFields->getValue('label') .'" name="'. $name .'" value="'. $dbvalues[0] .'" id="'. $id .'" '. $attr .' />';
        break;
      }
      case 'checkbox':
        $name .= '[]';
      case 'radio':
      {
        $tag = '';
        $labelIt = false;
        $values = array();
        if(preg_match('/^\s*?(SELECT)/i', $params, $matches))
        {
          $sql = new rex_sql();
          $value_groups = $sql->getArray($params, MYSQL_NUM);
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
        
        if($params['extension_point'] != 'CAT_META_FORM_EDIT')
          $field .= '<span>'. $label .'</span>';
          
        $class = $typeLabel == 'radio' ? 'rex-radio' : 'rex-chckbx';
        foreach($values as $key => $value)
        {
          $id = preg_replace('/[^a-zA-Z\-0-9_]/', '_', $id . $key);
          
          $selected = '';
          if(in_array($key, $dbvalues))
            $selected = ' checked="checked"';
            
          $field .= '<p class="'. $class .'">'."\n";
          $field .= '<label for="'. $id .'">'. htmlspecialchars($value) .'</label>';
          $field .= '<input type="'. $typeLabel .'" name="'. $name .'" value="'. $key .'" id="'. $id .'" '. $attr . $selected .' />'."\n";
          $field .= '</p>'."\n";
        }
        break;
      }
      case 'select':
      {
        $select = new rex_select();
        $select->setName($name);
        $select->setId($id);
        $select->setSelected($dbvalues);

        foreach(explode(' ',$attr) as $pair)
        {
          if(strpos($pair, '=') === false) continue;
          
          $temp = explode('=', $pair);
          $select->setAttribute($temp[0], str_replace(array('"', "'"),'',$temp[1]));
          
          if($temp[0] == 'multiple')
            $select->setName($name.'[]');
        }

        if(preg_match('/^\s*?(SELECT)/i', $params, $matches))
        {
          $select->addSqlOptions($params);
        }
        else
        {
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
        if($dbvalues[0] == '')
          $dbvalues[0] = time();
        
        $style = 'width: 19%';
        $yearStyle = $style;
        if($typeLabel == 'datetime')
        {
          $style = 'width: 10%';
          $yearStyle = 'width: 12%'; // Jahr mit extra Style, da sonst zu klein
        }
          
        $yearSelect = new rex_select();
        $yearSelect->addOptions(range(2005,date('Y')+2), true);
        $yearSelect->setName($name.'[year]');
        $yearSelect->setSize(1);
        $yearSelect->setId($id);
        $yearSelect->setStyle($yearStyle);
        $yearSelect->setSelected(date('Y', $dbvalues[0]));
        
        $monthSelect = new rex_select();
        $monthSelect->addOptions(range(1,12), true);
        $monthSelect->setName($name.'[month]');
        $monthSelect->setSize(1);
        $monthSelect->setStyle($style);
        $monthSelect->setSelected(date('m', $dbvalues[0]));
        
        $daySelect = new rex_select();
        $daySelect->addOptions(range(1,31), true);
        $daySelect->setName($name.'[day]');
        $daySelect->setSize(1);
        $daySelect->setStyle($style);
        $daySelect->setSelected(date('j', $dbvalues[0]));
        
        if($typeLabel == 'datetime')
        {
          $hourSelect = new rex_select();
          $hourSelect->addOptions(range(1,23), true);
          $hourSelect->setName($name.'[hour]');
          $hourSelect->setSize(1);
          $hourSelect->setStyle($style);
          $hourSelect->setSelected(date('G', $dbvalues[0]));
          
          $minuteSelect = new rex_select();
          $minuteSelect->addOptions(range(0,59), true);
          $minuteSelect->setName($name.'[minute]');
          $minuteSelect->setSize(1);
          $minuteSelect->setStyle($style);
          $minuteSelect->setSelected(date('i', $dbvalues[0]));
          
          $field = $daySelect->get() . $monthSelect->get() . $yearSelect->get() .'-'. $hourSelect->get() . $minuteSelect->get();
        }
        else
        {
          $field = $daySelect->get() . $monthSelect->get() . $yearSelect->get();
        }
        break;
      }
      case 'textarea':
      {
        $field = '<textarea name="'. $name .'" id="'. $id .'" '. $attr .' cols="50" rows="6">'. $dbvalues[0] .'</textarea>';
        break;
      }
      case 'REX_MEDIA_BUTTON':
      {
        $tag = 'div';
        $tag_attr = ' class="rex-ptag"';
        
        $field = rex_var_media::getMediaButton($media_id);
        $field = str_replace('REX_MEDIA['. $media_id .']', $dbvalues[0], $field);
        $field = str_replace('MEDIA['. $media_id .']', $name, $field);
        $id = 'REX_MEDIA_'. $media_id;
        $media_id++;
        break;
      }
      case 'REX_MEDIALIST_BUTTON':
      {
        $tag = 'div';
        $tag_attr = ' class="rex-ptag"';
        
        $name .= '[]';
        $field = rex_var_media::getMediaListButton($mlist_id, implode(',',$dbvalues));
        $field = str_replace('MEDIALIST['. $mlist_id .']', $name, $field);
        $id = 'REX_MEDIALIST_'. $mlist_id;
        
        $mlist_id++;
        break;
      }
      case 'REX_LINK_BUTTON':
      {
        $tag = 'div';
        $tag_attr = ' class="rex-ptag"';
        
        $field = rex_var_link::getLinkButton($link_id, $dbvalues[0], $activeItem->getValue('category_id'));
        $field = str_replace('LINK['. $link_id .']', $name, $field);
        $id = 'LINK_'. $link_id;
        
        $link_id++;
        break;
      }
    }
    
    $s .= rex_call_func($formatCallback, array($field, $tag, $tag_attr, $id, $label, $labelIt), false); 
    
    $sqlFields->next();
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
  if($_SERVER['REQUEST_METHOD'] != 'POST') return;
  
  for($i = 0;$i < $sqlFields->getRows(); $i++)
  {
    $fieldName = $sqlFields->getValue('name');
    $postValue = rex_post($fieldName, 'array');
    
    // handle date types with timestamps
    if(isset($postValue['year']) && isset($postValue['month']) && isset($postValue['day']) && isset($postValue['hour']) && isset($postValue['minute']))
    {
      $saveValue = mktime($postValue['hour'],$postValue['minute'],0, $postValue['month'], $postValue['day'], $postValue['year']);
    }
    elseif(isset($postValue['year']) && isset($postValue['month']) && isset($postValue['day']))
    {
      $saveValue = mktime(0,0,0, $postValue['month'], $postValue['day'], $postValue['year']);
    }
    else
    {
      $saveValue = implode('|+|', $postValue);
    }
    
    // Wert in SQL zum speichern
    $sqlSave->setValue($fieldName, $saveValue);
    
    // Werte im aktuellen Objekt speichern, dass zur Anzeige verwendet wird
    $params['activeItem']->setValue($fieldName, $saveValue);
    
    $sqlFields->next();
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
  
  $s = '';
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
//  $fields->debugsql = true;
  $sqlFields->setQuery($qry);
  
  $params = rex_call_func($saveCallback, array($params, $sqlFields), false);
  
  $s = rex_a62_metaFields($sqlFields, $params['activeItem'], 'rex_a62_metainfo_form_item', $params);
  
  return $s;
}

/**
 * Artikel & Kategorien:
 * 
 * Übernimmt die gePOSTeten werte in ein rex_sql-Objekt und speichert diese
 */
function _rex_a62_metainfo_cat_handleSave($params, $sqlFields)
{
  return _rex_a62_metainfo_art_handleSave($params, $sqlFields);
}

function _rex_a62_metainfo_art_handleSave($params, $sqlFields)
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

/**
 * Medien:
 * 
 * Übernimmt die gePOSTeten werte in ein rex_sql-Objekt und speichert diese
 */
function _rex_a62_metainfo_med_handleSave($params, $sqlFields)
{
  if($_SERVER['REQUEST_METHOD'] != 'POST') return $params;
  
  global $REX;
  
  $media = rex_sql::getInstance();
//  $media->debugsql = true;
  $media->setTable($REX['TABLE_PREFIX']. 'file');
  $media->setWhere('file_id='. $params['file_id']);
  
  _rex_a62_metainfo_handleSave($params, $media, $sqlFields);
  
  $media->update();
  
  return $params;
}
?>