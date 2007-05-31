<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
rex_register_extension('ART_META_FORM', 'rex_a62_metainfo_form');

/**
 * Callback, dass ein Formular item formatiert
 */
function rex_a62_metainfo_form_item($field, $tag, $tag_attr, $id, $label, $labelIt)
{
  $s = '';
  
  if($tag != '')
    $s .= '<'. $tag . $tag_attr  .'>'. "\n";
  
  if($labelIt)
    $s .= '<label for="'. $id .'">'. $label .'</label>'. "\n";
    
  $s .= $field. "\n";
  $s .= '<br class="rex-clear" />';
  
  if($tag != '')
    $s .='</'.$tag.'>'. "\n";
    
  return $s;
}

/**
 * Erweitert das Meta-Formular um die neuen Meta-Felder  
 */
function rex_a62_metainfo_form($params)
{
  return _rex_a62_metainfo_form('art_', $params);
}

?>