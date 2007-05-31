<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
rex_register_extension('ALL_GENERATED', 'rex_a62_metainfo_regenerate_all');
rex_register_extension('OUTPUT_FILTER', 'rex_a62_insertJs');

/**
 * Führt das nötige Cleanup nach einem "regenerate all" her. 	
 */
function rex_a62_metainfo_regenerate_all($params)
{
	rex_set_session('A62_MESSAGE', '');
}

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

function rex_a62_metaFields($sqlFields, $article, $formatCallback)
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
    $default = $sqlFields->getValue('default');
    $typeLabel = $sqlFields->getValue('label');
    $attr = $sqlFields->getValue('attributes');
    $dbvalues = explode('|+|', $article->getValue($name));
    
    if(count($dbvalues) == 1 && isset($dbvalues[0]) && $dbvalues[0] == '')
      $dbvalues[0] = $default;
      
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
      case 'date':
      {
        $name .= '[]';
        
        if(!isset($dbvalues[1])) $dbvalues[1] = ''; 
        if(!isset($dbvalues[2])) $dbvalues[2] = '';
         
        $yearSelect = new rex_select();
        $yearSelect->addOptions(range(2005,date('Y')+2));
        $yearSelect->setName($name);
        $yearSelect->setSize(1);
        $yearSelect->setStyle('width: 19%');
        $yearSelect->setSelected($dbvalues[0]);
        
        $monthSelect = new rex_select();
        $monthSelect->addOptions(range(1,12));
        $monthSelect->setName($name);
        $monthSelect->setSize(1);
        $monthSelect->setStyle('width: 19%');
        $monthSelect->setSelected($dbvalues[1]);
        
        $daySelect = new rex_select();
        $daySelect->addOptions(range(1,31));
        $daySelect->setName($name);
        $daySelect->setSize(1);
        $daySelect->setStyle('width: 19%');
        $daySelect->setSelected($dbvalues[2]);
        
        $field = $yearSelect->get() . $monthSelect->get() . $daySelect->get();
        break;
      }
      case 'textarea':
      {
        $field = '<textarea name="'. $name .'" id="'. $id .'" '. $attr .' >'. $dbvalues[0] .'</textarea>';
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
        $field = str_replace('MEDIALIST['. $media_id .']', $name, $field);
        $id = 'REX_MEDIALIST_'. $mlist_id;
        
        $mlist_id++;
        break;
      }
      case 'REX_LINK_BUTTON':
      {
        $tag = 'div';
        $tag_attr = ' class="rex-ptag"';
        
        $field = rex_var_link::getLinkButton($link_id, $dbvalues[0], $article->getValue('category_id'));
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

function rex_a62_metainfo_handleSave($params, $fields)
{
  if($_SERVER['REQUEST_METHOD'] != 'POST') return $params;
  
  global $REX;
  
  $article = rex_sql::getInstance();
//  $article->debugsql = true;
  $article->setTable($REX['TABLE_PREFIX']. 'article');
  $article->setWhere('id='. $params['id'] .' AND clang='. $params['clang']);
  
  for($i = 0;$i < $fields->getRows(); $i++)
  {
    $fieldName = $fields->getValue('name');
    
    $postValue = rex_post($fieldName, 'array');
    $saveValue = implode('|+|', $postValue);
    
    // Wert in SQL zum speichern
    $article->setValue($fieldName, $saveValue);
    // Wert in das SQL Objekt speichern, dass zur Anzeige verwendet wird
    $params['article']->setValue($fieldName, $saveValue);
    
    $fields->next();
  }
  
  $article->update();
  
  // Artikel nochmal mit den zusätzlichen Werten neu generieren
  rex_generateArticle($params['id']);
  
  return $params;
}

/**
 * Erweitert das Meta-Formular um die neuen Meta-Felder  
 */
function _rex_a62_metainfo_form($prefix, $params)
{
  global $REX;
  
  $s = '';
  $debug = false;
  
  $fields = new rex_sql();
//  $fields->debugsql = true;
  $fields->setQuery('SELECT * FROM '. $REX['TABLE_PREFIX'] .'62_params p,'. $REX['TABLE_PREFIX'] .'62_type t WHERE `p`.`type` = `t`.`id` AND `p`.`name` LIKE "'. $prefix .'%"');
  
  $params = rex_a62_metainfo_handleSave($params, $fields);
  $article = new rex_article($params['id'], $params['clang']);
  
  $s = rex_a62_metaFields($fields, $article, 'rex_a62_metainfo_form_item');
  
  return $s;
}
