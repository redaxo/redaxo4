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
if ($func == 'deleteclang' && $clang_id != "")
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
$sel->setStyle('class="rex-form-select"');
$sel->setId('rex-form-clang-id');
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
      <div class="rex-form" id="rex-form-system-language">
      <form action="index.php#clang" method="post">
        <fieldset>
          <legend>'.$legend.'</legend>
          <input type="hidden" name="page" value="specials" />
          <input type="hidden" name="subpage" value="lang" />
          <input type="hidden" name="clang_id" value="'.$clang_id.'" />
      ';
}


echo '
    <table class="rex-table" summary="'.$I18N->msg('clang_summary').'">
      <caption>'.$I18N->msg('clang_caption').'</caption>
      <colgroup>
        <col width="40" />
        <col width="40" />
        <col width="*" />
        <col width="153" />
      </colgroup>
      <thead>
        <tr>
          <th class="rex-small"><a href="index.php?page=specials&amp;subpage=lang&amp;func=addclang#clang"'. rex_accesskey($I18N->msg('clang_add'), $REX['ACKEY']['ADD']) .'>+</a></th>
          <th class="rex-small">ID</th>
          <th>'.$I18N->msg('clang_name').'</th>
          <th>'.$I18N->msg('clang_function').'</th>
        </tr>
      </thead>
      <tbody>
  ';

// Add form
if ($func == 'addclang')
{
  //ggf wiederanzeige des add forms, falls ungueltige id uebermittelt
  echo '
        <tr class="rex-table-row-activ">
          <td class="rex-small"></td>
          <td class="rex-small">'.$sel->get().'</td>
          <td><input class="rex-form-text" type="text" id="rex-form-clang-name" name="clang_name" value="'.htmlspecialchars($clang_name).'" /></td>
          <td><input class="rex-form-submit" type="submit" name="add_clang_save" value="'.$I18N->msg('clang_add').'"'. rex_accesskey($I18N->msg('clang_add'), $REX['ACKEY']['SAVE']) .' /></td>
        </tr>
      ';
}
foreach ($REX['CLANG'] as $lang_id => $lang)
{
  
  $add_td = '';      
  $add_td = '<td class="rex-small">'.$lang_id.'</td>';
    
  // Edit form
  if ($func == "editclang" && $clang_id == $lang_id)
  {
    echo '
          <tr class="rex-trow-actv">
            <td class="rex-small"></td>
            '.$add_td.'
            <td><input class="rex-form-text" type="text" id="rex-form-clang-name" name="clang_name" value="'.htmlspecialchars($lang).'" /></td>
            <td><input class="rex-form-submit" type="submit" name="edit_clang_save" value="'.$I18N->msg('clang_update').'"'. rex_accesskey($I18N->msg('clang_update'), $REX['ACKEY']['SAVE']) .' /></td>
          </tr>';

  }
  else
  {
    echo '
          <tr>
            <td class="rex-small"></td>
            '.$add_td.'
            <td><a href="index.php?page=specials&amp;subpage=lang&amp;func=editclang&amp;clang_id='.$lang_id.'#clang">'.htmlspecialchars($lang).'</a></td>
            <td><a href="index.php?page=specials&amp;subpage=lang&amp;func=deleteclang&amp;clang_id='.$lang_id.'" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg('clang_delete').'</a></td>
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
              $("#rex-form-clang-name").focus();
            });
            //-->
          </script>
        </fieldset>
      </form>
      </div>';
}
?>