<?php

/**
 * Verwaltung der Content Sprachen
 * @package redaxo4
 * @version $Id: specials.clangs.inc.php,v 1.3 2008/03/25 10:01:41 kills Exp $
 */

// -------------- Defaults
$clang_id = rex_request('clang_id', 'int');
$clang_name = rex_request('clang_name', 'string');
$func = rex_request('func', 'string');

// -------------- Form Submits
$add_clang_save = rex_post('add_clang_save', 'string');
$edit_clang_save = rex_post('edit_clang_save', 'string');
$del_clang_save = rex_post('del_clang_save', 'string');

$warning = '';
$info = '';


// ----- delete clang
if (!empty ($del_clang_save))
{
  if (array_key_exists($clang_id, $REX['CLANG']))
  {
    rex_deleteCLang($clang_id);
    $info = $I18N->msg('clang_deleted');
    $func = '';
    unset ($clang_id);
  }
}

// ----- add clang
if (!empty ($add_clang_save))
{
  if ($clang_name != '' && $clang_id > 0)
  {
    if (!array_key_exists($clang_id, $REX['CLANG']))
    {
      $info = $I18N->msg('clang_created');
      rex_addCLang($clang_id, $clang_name);
      unset ($clang_id);
   	  $func = '';
    }
    else
    {
      $warning = $I18N->msg('id_exists');
      $func = 'addclang';
    }
  }
  else
  {
    $warning = $I18N->msg('enter_name');
    $func = 'addclang';
  }

}
elseif (!empty ($edit_clang_save))
{
  if (array_key_exists($clang_id, $REX['CLANG']))
  {
    rex_editCLang($clang_id, $clang_name);
    $info = $I18N->msg('clang_edited');
    $func = '';
    unset ($clang_id);
  }
}

// seltype
$sel = new rex_select;
$sel->setName('clang_id');
$sel->setId('clang_id');
$sel->setSize(1);
foreach (array_diff(range(0, 14), array_keys($REX['CLANG'])) as $clang)
{
  $sel->addOption($clang, $clang);
}

if ($info != '')
  echo rex_info($info);

if ($warning != '')
  echo rex_warning($warning);

if ($func == 'addclang' || $func == 'editclang')
{
  $legend = $func == 'add_clang' ? $I18N->msg('clang_add') : $I18N->msg('clang_edit');
  echo '
      <form action="index.php#clang" method="post">
        <fieldset>
          <legend><span class="rex-hide">'.$legend.'</span></legend>
          <input type="hidden" name="page" value="specials" />
          <input type="hidden" name="subpage" value="lang" />
          <input type="hidden" name="clang_id" value="'.$clang_id.'" />
      ';
}

echo '
    <table class="rex-table" summary="'.$I18N->msg('clang_summary').'">
      <caption class="rex-hide">'.$I18N->msg('clang_caption').'</caption>
      <colgroup>
        <col width="40" />
        <col width="40" />
        <col width="*" />
        <col width="250" />
      </colgroup>
      <thead>
        <tr>
          <th class="rex-icon"><a href="index.php?page=specials&amp;subpage=lang&amp;func=addclang#clang"'. rex_accesskey($I18N->msg('clang_add'), $REX['ACKEY']['ADD']) .'>+</a></th>
          <th class="rex-icon">ID</th>
          <th>'.$I18N->msg('clang_name').'</th>
          <th colspan="2">'.$I18N->msg('clang_function').'</th>
        </tr>
      </thead>
      <tbody>
  ';

// Add form
if ($func == 'addclang')
{
  //ggf wiederanzeige des add forms, falls ungueltige id uebermittelt
  echo '
        <tr class="rex-trow-actv">
          <td class="rex-icon"></td>
          <td class="rex-icon">'.$sel->get().'</td>
          <td><input type="text" id="clang_name" name="clang_name" value="'.htmlspecialchars($clang_name).'" /></td>
          <td><input type="submit" class="rex-fsubmit" name="add_clang_save" value="'.$I18N->msg('clang_add').'"'. rex_accesskey($I18N->msg('clang_add'), $REX['ACKEY']['SAVE']) .' /></td>
        </tr>
      ';
}
foreach ($REX['CLANG'] as $lang_id => $lang)
{
  // Edit form
  if ($func == "editclang" && $clang_id == $lang_id)
  {
    echo '
          <tr class="rex-trow-actv">
            <td class="rex-icon"></td>
            <td class="rex-icon">'.$lang_id.'</td>
            <td><input type="text" id="clang_name" name="clang_name" value="'.htmlspecialchars($lang).'" /></td>
            <td>
              <input type="submit" class="rex-fsubmit" name="edit_clang_save" value="'.$I18N->msg('clang_update').'"'. rex_accesskey($I18N->msg('clang_update'), $REX['ACKEY']['SAVE']) .' />
              <input type="submit" class="rex-fsubmit" name="del_clang_save" value="'.$I18N->msg('clang_delete').'"'. rex_accesskey($I18N->msg('clang_delete'), $REX['ACKEY']['DELETE']) .' onclick="return confirm(\''.$I18N->msg('clang_delete').' ?\')" />
            </td>
          </tr>';

  }
  else
  {
    echo '
          <tr>
            <td class="rex-icon"></td>
            <td class="rex-icon">'.$lang_id.'</td>
            <td><a href="index.php?page=specials&amp;subpage=lang&amp;func=editclang&amp;clang_id='.$lang_id.'#clang">'.htmlspecialchars($lang).'</a></td>
            <td></td>
          </tr>';
  }
}

echo '
    </tbody>
  </table>';

if ($func == 'addclang' || $func == 'editclang')
{
  echo '
          <script type="text/javascript">
            <!--
            jQuery(function($){
              $("#clang_name").focus();
            });
            //-->
          </script>
        </fieldset>
      </form>';
}
?>