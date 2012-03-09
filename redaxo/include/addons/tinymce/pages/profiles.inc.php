<?php

/**
 * TinyMCE Addon
 *
 * @author andreaseberhard[at]gmail[dot]com Andreas Eberhard
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$table = $REX['TABLE_PREFIX'] . 'tinymce_profiles';
$func = rex_request('func', 'string', '');
$entry_id = rex_request('entry_id', 'int', -1);
$fieldSet = $I18N->Msg('tinymce_fieldset');

// Hinzufügen / Bearbeiten
if ($func == 'add' || $func == 'edit')
{

  echo '<div class="rex-toolbar"><div class="rex-toolbar-content">';
  echo '<p><a class="rex-back" href="index.php?page='.$page.'&amp;clang='.$REX['CUR_CLANG'].'&amp;subpage='.$subpage.'">'.$I18N->Msg('tinymce_back').'</a></p>';
  echo '</div></div>';

  echo '<div class="rex-addon-output-v2">';

  require_once $REX['INCLUDE_PATH'] . '/addons/tinymce/classes/class.form.inc.php';

  $form = new rex_form_tinymce($table, $fieldSet, 'id='. $entry_id, 'post', false, 'rex_form_tinymce');
  //$form = rex_form::factory($table, $fieldSet, 'id='. $entry_id, 'post', false, 'rex_form_tinymce');

  if($func == 'edit')
  {
    $form->addParam('entry_id', $entry_id);
  }

  $field = &$form->addTextField('name');
  $field->setAttribute('maxlength', '30');
  $field->setLabel($I18N->Msg('tinymce_name'));

  $field = &$form->addTextField('description');
  $field->setLabel($I18N->Msg('tinymce_description'));

  $field = &$form->addTextAreaField('configuration');
  $field->setLabel($I18N->Msg('tinymce_configuration'));
  $field->setAttribute('style', 'height:300px;font-family:\'Courier New\';');

  $form->show();

  echo '</div>';
}


// Löschen
if ($func == 'delete')
{
  if ($entry_id==2)
  {
    $func = '';
    echo rex_warning($I18N->Msg('tinymce_profile_notdeleted'));
  }
  else
  {
    $query = "DELETE from $table WHERE id='".$entry_id."' ";
    $delsql = new rex_sql;
    $delsql->debugsql=0;
    $delsql->setQuery($query);
    $func = '';
    echo rex_info($I18N->Msg('tinymce_profile_deleted'));
  }
}


// Liste
if ($func == '')
{
  echo '<div class="rex-addon-output-v2">';

  // SQL
  $sql = 'SELECT id, name, description FROM '.$table.' WHERE ptype = 0 ORDER BY name ASC ';
  if ($REX['VERSION'] . $REX['SUBVERSION'] <= '40')
  {
    $list = new rex_list($sql);
  }
  else
  {
    $list = rex_list::factory($sql);
  }

  $list->addParam('clang', $REX['CUR_CLANG']);

  // <Caption tag>
  $list->setCaption($I18N->Msg('tinymce_listtitle'));
  // summary Attribut bei einer neuartigen Tabellen definiton
  $list->addTableAttribute('summary', $I18N->Msg('tinymce_listsummary'));

  // ICON
  $img = '<img src="../files/addons/tinymce/tinymce.gif" alt="###id### ###name###" title="###id### ###name###" />';
  $imgAdd = '<img src="../files/addons/tinymce/tinymce_plus.gif" alt="'.$I18N->Msg('tinymce_addentry').'" title="'.$I18N->Msg('tinymce_addentry').'" />';

  // ICON um eine neue Sprachersetzung zu definieren
  $imgHeader = '<a href="'. $list->getUrl(array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'add')) .'">'. $imgAdd .'</a>';

  // Das ist das ICON welches in der ICON-Spalte angezeigt wird.
  $list->addColumn($imgHeader, $img, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($imgHeader, array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'edit', 'entry_id' => '###id###'));
  
  if ($REX['VERSION'] . $REX['SUBVERSION'] <= '40')
  {
  }
  else
  {
    $list->removeColumn('id');
    $list->addTableColumnGroup(array(40, '*', '*', 90));

    $list->setColumnLabel('name', $I18N->msg('tinymce_name'));
    $list->setColumnParams('name', array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'edit', 'entry_id' => '###id###'));

    $list->setColumnLabel('description', $I18N->msg('tinymce_description'));
    $list->setColumnParams('description', array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'edit', 'entry_id' => '###id###'));

    $list->addColumn($I18N->msg('tinymce_func'), $I18N->msg('tinymce_delentry'));
    $list->setColumnParams($I18N->msg('tinymce_func'), array('page'=>$page, 'clang'=>$REX['CUR_CLANG'], 'subpage'=>$subpage, 'func' => 'delete', 'entry_id' => '###id###'));

    $list->addLinkAttribute($I18N->msg('tinymce_func'), 'onclick', 'return confirm(\'[###name###] - '.$I18N->msg('tinymce_delentry').' ?\')');
	 $list->setNoRowsMessage($I18N->Msg('tinymce_profiles_nodata'));
  }  

  $list->show();
  echo '</div>';
}
