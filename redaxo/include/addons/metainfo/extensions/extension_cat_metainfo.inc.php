<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
//rex_register_extension('CAT_META_FORM_ADD', 'rex_a62_metainfo_form');
rex_register_extension('CAT_META_FORM_EDIT', 'rex_a62_metainfo_form');

//rex_register_extension('CAT_ADDED', 'rex_a62_metainfo_form');
rex_register_extension('CAT_UPDATED', 'rex_a62_metainfo_form');

rex_register_extension('CAT_FORM_BUTTON_ADD', 'rex_a62_metainfo_button');

function rex_a62_metainfo_button($params)
{
	global $REX;
	
	$fields = new rex_sql();
  $fields->setQuery('SELECT * FROM '. $REX['TABLE_PREFIX'] .'62_params p,'. $REX['TABLE_PREFIX'] .'62_type t WHERE `p`.`type` = `t`.`id` AND `p`.`name` LIKE "cat_%" LIMIT 1');
	
	$return = '<div class="rex-meta-button"><script><!--

function rex_metainfo_toggle()
{
	var trs = getElementsByClass("rex-metainfo-cat");
	for(i=0;i<trs.length;i++)
  {
		toggleElement(trs[i]);
	}
}

//--></script><a href=javascript:rex_metainfo_toggle();><img src="pics/file_down.gif" /></a></div>';

	if ($fields->getRows()==1) return $return;
}

/**
 * Callback, dass ein Formular item formatiert
 */
function rex_a62_metainfo_form_item($field, $tag, $tag_attr, $id, $label, $labelIt)
{
  $s = '';
  
  $s .= '<tr class="rex-trow-actv rex-metainfo-cat" style="display:none;">'. "\n";
  $s .= '  <td>&nbsp;</td>'. "\n";
  $s .= '  <td class="rex-mt-fld">'.$field. '</td>'. "\n";
  $s .= '  <td class="rex-mt-lbl" colspan="3"><label for="'. $id .'">'. $label .'</label></td>'. "\n";
  $s .= '</tr>';
    
  return $s;
}

/**
 * Erweitert das Meta-Formular um die neuen Meta-Felder
 */
function rex_a62_metainfo_form($params)
{
  $params['activeItem'] = $params['category'];
  // Hier die category_id setzen, damit beim klick auf den REX_LINK_BUTTON der Medienpool in der aktuellen Kategorie startet
  $params['activeItem']->setValue('category_id', $params['id']);
  
  $result = _rex_a62_metainfo_form('cat_', $params, '_rex_a62_metainfo_cat_handleSave');
  
  // Bei CAT_ADDED und CAT_UPDATED nur speichern und kein Formular zurückgeben
  if($params['extension_point'] == 'CAT_UPDATED' 
     //|| $params['extension_point'] == 'CAT_ADDED'
     )
    return $params['subject'];
  else
    return $result;
}

?>