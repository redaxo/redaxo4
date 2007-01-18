<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
rex_register_extension('ART_META_FORM_SECTION', 'rex_a62_meta_form');

/**
 * Erweitert das Meta-Formular um die neuen Meta-Felder	
 */
function rex_a62_meta_form($params)
{
  global $article_id, $clang, $REX;
  
  $s = '';
  $debug = false;
  
  $section = new rex_sql;
  $section->debugsql = $debug;
  $field = new rex_sql;
  $field->debugsql = $debug;
  
  $qry = 'SELECT * FROM '. $REX['TABLE_PREFIX'] .'62_section';
  $section->setQuery($qry);
  
  for($i = 0; $i < $section->getRows(); $i++)
  {
    $submit_label = htmlspecialchars($section->getValue('submit_label'));
    $submit_name = rex_a62_get_field_name($section->getValue('name'),$section->getValue('submit_label'));
    $post = rex_post($submit_name, 'string');
    
    // Hier so umständlich selektieren, da gleichnamige Tabellen-Felder vorhanden sind, die sich sonst gegenseitig überschreiben
    $qry = '
		SELECT
			f.*,t.*,v.*,t.name as typename 
		FROM 
			'. $REX['TABLE_PREFIX'] .'62_type t,'. $REX['TABLE_PREFIX'] .'62_field f 
		LEFT JOIN 
			'. $REX['TABLE_PREFIX'] .'62_value v 
		ON 
			(v.field_id = f.id AND article_id = '. $article_id . ' AND clang='. $clang .')
		WHERE
			f.type_id = t.id AND section_id='. $section->getValue('id');
		
    $field->setQuery($qry);
    
    // handle save
    if($post != '')
    {
      $save = new rex_sql();
      $save->debugsql = $debug;
      $save->setTable($REX['TABLE_PREFIX'] .'62_value');
      $save->setValue('article_id', $article_id);
      $save->setValue('clang', $clang);
      
      for($t = 0; $t < $field->getRows(); $t++)
      {
        $name = rex_a62_get_field_name($section->getValue('name'),$field->getValue('name'));
        $save->setValue('field_id', $field->getValue('id'));
        
        $value = rex_post($name);
        if(is_array($value)) $value = implode('###', $value);
        
        $save->setValue('value', $value);
        $save->replace();
        
        $field->next();
      }
      
      // Werte im SQL-Objekt aktualisieren
      $field->setQuery($qry);
    }
    
    $s .= '<fieldset>
					 	 <legend class="rex-lgnd">'. $section->getValue('name') .'</legend>
					 	 <div class="rex-fldst-wrppr">'. "\n";
    
    // handle view
    for($t = 0; $t < $field->getRows(); $t++)
    {
      $attr = '';
      foreach(preg_split('/[\r\n]+/', $field->getValue('attribute')) as $att)
      {
        if($att == '') continue;
        
        $parts = explode(':', $att);
        $attr .= $parts[0]. '="'. $parts[1] .'" ';
      }
      
      $name = rex_a62_get_field_name($section->getValue('name'),$field->getValue('name'));
      $id = rex_a62_get_field_id($section->getValue('name'),$field->getValue('name'));
      $value = $field->getValue('value');
      $type = $field->getValue('typename');
      $extras = $field->getValue('extras');
      
      $tag = $type;
      if(in_array($type, array('text', 'checkbox', 'radio')))
      {
        $tag = 'input';
      }
      
      if($tag == 'select')
      {
        $out = rex_a62_Select($section, $field, $debug);
      }
      elseif($tag == 'textarea')
      {
        $out = '<'. $tag .' id="'. $id .'" name="'. $name .'"'. $attr .'>'. $value . '</'. $tag .'>';
      }
      elseif($tag == 'dateselect')
      {
        $out = rex_a62_dateSelect($section, $field, $debug);
      }
      else
      {
        if($type == 'checkbox')
        {
          if($value != '')
          {
            $attr .= ' checked="checked"';
          }
          $value = 'true';
        }
        $out = '<'. $tag .' type="'. $type .'" id="'. $id .'" name="'. $name .'" value="'. $value .'" '. $attr .' />';
      }
      
      $s .= '<p>
	             <label for="'. $id .'">'. htmlspecialchars($field->getValue('name')) .'</label>
	             '. $out .'
             </p>';
            
      $field->next();
    }
    
    $s .= '
						  <p>
								<input class="rex-sbmt" type="submit" name="'. $submit_name .'" value="'. $submit_label .'" />
						  </p>
					  </div>
          </fieldset>';
              
    $section->next();
  }
  
  return $s;
}

function rex_a62_Select(&$section, &$field, $debug = false)
{
  $name = rex_a62_get_field_name($section->getValue('name'),$field->getValue('name'));
  $id = rex_a62_get_field_id($section->getValue('name'),$field->getValue('name'));
  $value = $field->getValue('value');
  
  $select = new rex_select();

  foreach(preg_split('/[\r\n]+/', $field->getValue('attribute')) as $att)
  {
    if($att == '') continue;
    $parts = explode(':', $att);
    
    if($parts[0] == 'multiple')
    {
	    $name .= '[]';
    }
    
    $select->setAttribute($parts[0], $parts[1]);
  }
  
  $select->setName($name);
  $select->setId($id);
  
  $value = explode('###', $value);
  foreach($value as $val)
    $select->setSelected($val);
    
  foreach(preg_split('/[\r\n]+/', $field->getValue('extras')) as $xtr)
  {
    if($xtr == '') continue;
    
    $parts = explode(':', $xtr);
    
    if(startsWith($xtr, 'SQL'))
    {
      $qry = substr($xtr, 4);
      $select->addSqlOptions($qry);
    }
    else
    {
      if($parts[1] == '') $parts[1] = $parts[0];
    
      $select->addOption($parts[0], $parts[1]);          
    }
  }
  
  return $select->get();  
}

function rex_a62_dateSelect(&$section, &$field, $debug = false)
{
  $name = rex_a62_get_field_name($section->getValue('name'),$field->getValue('name'));
  $id = rex_a62_get_field_id($section->getValue('name'),$field->getValue('name'));
  $value = explode('###', $field->getValue('value'));

	$select = new rex_select();
	$select->setSize(1);
	$select->setStyle('width:19%;');
  $select->setName($name. '[]');
  $select->setId($id);
	 
	$days = $select;
	$months = $select;
	$years = $select;
	
	if(!empty($value))
	{
		$days->setSelected($value[0]);
		$months->setSelected($value[1]);
		$years->setSelected($value[2]);
	}
	
  $days->addArrayOptions(range(1,31),false);
  $months->addArrayOptions(range(1,12),false);
  $years->addArrayOptions(range(2000,2020),false);
  
  return $days->get() . $months->get() . $years->get();
}

function rex_a62_get_field_name($section_name, $field_name)
{
  return htmlspecialchars(preg_replace('/[^a-zA-Z\-0-9_]/', '', $section_name .'_'. $field_name));
}

function rex_a62_get_field_id($section_name, $field_name)
{
  return 'a62_'. rex_a62_get_field_name($section_name, $field_name);
}
?>
