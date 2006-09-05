<?php

/**
 * Verwaltung der Content Sprachen 
 * @package redaxo3
 * @version $Id$
 */
 
// -------------- Defaults
$clang_id = rex_request('clang_id', 'int');
$clang_name = rex_request('clang_name', 'string');


// -------------- Form Submits
$add_clang_save = rex_post('add_clang_save', 'string');
$edit_clang_save = rex_post('edit_clang_save', 'string');
$del_clang_save = rex_post('del_clang_save', 'string');

// ----- delete clang
if (!empty ($del_clang_save))
{
  if ($clang_id > 0)
  {
    rex_deleteCLang($clang_id);
    $message = $I18N->msg('clang_deleted');
    unset ($func);
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
      $message = $I18N->msg('clang_created');
      rex_addCLang($clang_id, $clang_name);
      unset ($clang_id);
      unset ($func);
    }
    else
    {
      $message = $I18N->msg('id_exists');
      $func = 'addclang';
    }
  }
  else
  {
    $message = $I18N->msg('enter_name');
    $func = 'addclang';
  }

}
elseif (!empty ($edit_clang_save))
{
  if ($clang_id > 0)
  {
    rex_editCLang($clang_id, $clang_name);
    $message = $I18N->msg('clang_edited');
    unset ($func);
    unset ($clang_id);
  }
}

// seltype
$sel = new rex_select;
$sel->set_name('clang_id');
$sel->set_id('clang_id');
$sel->set_size(1);
foreach (array_diff(range(0, 14), array_keys($REX['CLANG'])) as $clang)
{
  $sel->add_option($clang, $clang);
}

if ($message != '')
{
  echo '<p class="rex-warning">'.$message.'</td></tr>';
  $message = "";
}

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
        <col width="5%" />
        <col width="6%" />
        <col width="*" />
        <col width="40%" />
      </colgroup>
      <thead>
        <tr>
          <th><a href="index.php?page=specials&amp;subpage=lang&amp;func=addclang#clang" title="'.$I18N->msg('clang_add').'">+</a></th>
          <th>ID</th>
          <th>'.$I18N->msg('clang_name').'</th>
          <th colspan="2">'.$I18N->msg('clang_function').'</th>
        </tr>
      </thead>
      <tbody>
  ';

// Add form
if ($func == 'addclang')
{
  echo '
        <tr class="rex-trow-actv">
          <td></td>
          <td>'.$sel->out().'</td>
          <td><input type="text" id="clang_name" name="clang_name" value="'.htmlspecialchars($clang_name).'" /></td>
          <td><input type="submit" class="rex-fsubmit" name="add_clang_save" value="'.$I18N->msg('clang_add').'" /></td>
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
            <td></td>
            <td align="center" class="grey">'.$lang_id.'</td>
            <td><input type="text" id="clang_name" name="clang_name" value="'.htmlspecialchars($lang).'" /></td>
            <td>
              <input type="submit" class="rex-fsubmit" name="edit_clang_save" value="'.$I18N->msg('clang_update').'" />
              <input type="submit" class="rex-fsubmit" name="del_clang_save" value="'.$I18N->msg('clang_delete').'" onclick="return confirm(\''.$I18N->msg('clang_delete').' ?\')" />
            </td>
          </tr>';

  }
  else
  {
    echo '
          <tr>
            <td></td>
            <td align="center">'.$lang_id.'</td>
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
             var needle = new getObj("clang_name");
             needle.obj.focus();
             //--> 
          </script>
        </fieldset>
      </form>';
}
?>