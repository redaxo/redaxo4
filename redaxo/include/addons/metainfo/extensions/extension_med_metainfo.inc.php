<?php

/**
 * MetaForm Addon
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 * @package redaxo4
 * @version $Id: extension_med_metainfo.inc.php,v 1.5 2008/03/11 16:03:32 kills Exp $
 */

rex_register_extension('MEDIA_FORM_EDIT', 'rex_a62_metainfo_form');
rex_register_extension('MEDIA_FORM_ADD', 'rex_a62_metainfo_form');

rex_register_extension('MEDIA_ADDED', 'rex_a62_metainfo_form');
rex_register_extension('MEDIA_UPDATED', 'rex_a62_metainfo_form');

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

  // Nur beim EDIT gibts auch ein Medium zum bearbeiten
  if($params['extension_point'] == 'MEDIA_FORM_EDIT')
  {
    $params['activeItem'] = $params['media'];
    // Hier die category_id setzen, damit keine Warnung entsteht (REX_LINK_BUTTON)
    $params['activeItem']->setValue('category_id', 0);
  }
  else if($params['extension_point'] == 'MEDIA_ADDED')
  {
    global $REX;

    $sql = new rex_sql();
    $qry = 'SELECT file_id FROM '. $REX['TABLE_PREFIX'] .'file WHERE filename="'. $params['filename'] .'"';
    $sql->setQuery($qry);
    if($sql->getRows() == 1)
    {
      $params['file_id'] = $sql->getValue('file_id');
    }
    else
    {
      trigger_error('Error occured during file upload', E_USER_ERROR);
      exit();
    }
  }

  return _rex_a62_metainfo_form('med_', $params, '_rex_a62_metainfo_med_handleSave');
}

?>