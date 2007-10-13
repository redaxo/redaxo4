<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo4
 * @version $Id$
 */


rex_register_extension('MEDIA_FORM_EDIT', 'rex_a62_metainfo_form');

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

  if($tag != '')
    $s .='</'.$tag.'>'. "\n";

  return $s;
}

/**
 * Erweitert das Meta-Formular um die neuen Meta-Felder
 */
function rex_a62_metainfo_form($params)
{
  $params['activeItem'] = $params['media'];
  // Hier die category_id setzen, damit keine Warnung entsteht (REX_LINK_BUTTON)
  $params['activeItem']->setValue('category_id', 0);

  return _rex_a62_metainfo_form('med_', $params, '_rex_a62_metainfo_med_handleSave');
}

?>