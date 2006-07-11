<?php

/**
 * 
 * @package redaxo3
 * @version $Id$
 */
 
// -------------- Defaults
$type_id = rex_request('type_id', 'int');
$typname = rex_request('typname', 'string');
$description = rex_request('description', 'string');

// -------------- Form Submits
$add_article_type = rex_post('add_article_type', 'string');
$edit_article_type = rex_post('edit_article_type', 'string');
$delete_article_type = rex_post('delete_article_type', 'string');


if (!empty ($edit_article_type))
{
  if ($type_id != '' && $typname != '')
  {
    $update = new sql;
    $update->setTable($REX['TABLE_PREFIX']."article_type");
    $update->where("type_id='$type_id'");
    $update->setValue("name", $typname);
    $update->setValue("description", $description);
    $update->update();
    $type_id = 0;
    $message = $I18N->msg("article_type_updated");
  }
  else
  {
    $func = 'edit';
    $message = '';
  }
}
elseif (!empty ($delete_article_type))
{
  if ($type_id != 1)
  {
    $delete = new sql;
    $result = $delete->get_array("SELECT name,id FROM ".$REX['TABLE_PREFIX']."article WHERE type_id = $type_id");
    if (is_array($result))
    {
      $message = $I18N->msg("article_type_still_used")."<br>";
      foreach ($result as $var)
      {
        $message .= '<br /><a href="index.php?page=content&amp;article_id='.$var['id'].'&amp;mode=meta">'.$var['name'].'</a>';
      }
      $message .= '<br /><br />';
    }
    else
    {
      $delete->query("DELETE FROM ".$REX['TABLE_PREFIX']."article_type WHERE type_id = '$type_id' LIMIT 1");
      $delete->query("UPDATE ".$REX['TABLE_PREFIX']."article SET type_id = '1' WHERE type_id = '$type_id'");
      $message = $I18N->msg("article_type_deleted");
    }
  }
  else
  {
    $message = $I18N->msg("article_type_could_not_be_deleted");
  }
}
elseif (!empty ($add_article_type))
{
  if ($type_id != '' && $typname != '')
  {
    $add = new sql;
    $add->setTable($REX['TABLE_PREFIX']."article_type");
    $add->setValue("name", $typname);
    $add->setValue("type_id", $type_id);
    $add->setValue("description", $description);
    $add->insert();
    $type_id = 0;
    $message = $I18N->msg("article_type_added");
  }
  else
  {
    // Add form wieder anzeigen
    $func = 'add';
    $message = array ();
    if ($type_id == '')
    {
      $message[] = $I18N->msg('article_type_miss_id');
    }
    if ($typname == '')
    {
      $message[] = $I18N->msg('article_type_miss_name');
    }
    $message = implode('<br />', $message);
  }
}

if ($message != "")
{
  echo '<p class="rex-warning">'.$message.'</p>';
}

if ($func == 'add' || $func == 'edit')
{
  $legend = $func == 'add' ? $I18N->msg('article_type_add') : $I18N->msg('article_type_edit');

  echo '
      <form action="index.php" method="post">
        <fieldset>
          <legend><span class="rex-hide">'.$legend.'</span></legend>
          <input type="hidden" name="page" value="specials" />
          <input type="hidden" name="subpage" value="type" />
          <input type="hidden" name="type_id" value="'.$type_id.'" />
          ';
}

echo '<table class="rex-table" summary="'.$I18N->msg('article_type_summary').'">
        <caption class="rex-hide">'.$I18N->msg('article_type_caption').'</caption>
        <colgroup>
          <col width="5%" />
          <col width="6%" />
          <col width="20%" />
          <col width="*" />
          <col width="40%" />
        </colgroup>
        <thead>
          <tr>
            <th><a href="index.php?page=specials&amp;subpage=type&amp;func=add">+</a></th>
            <th>'.$I18N->msg("article_type_id").'</th>
            <th>'.$I18N->msg("article_type_name").'</th>
            <th>'.$I18N->msg("article_type_description").'</th>
            <th>'.$I18N->msg("article_type_functions").'</th>
          </tr>
        </thead>
        <tbody>
    ';

if ($func == 'add')
{
  echo '
        <tr class="rex-trow-actv">
          <td>&nbsp;</td>
          <td><input type="text" maxlength="2" name="type_id" value="'.$type_id.'" /></td>
          <td><input type="text" name="typname" value="'.$typname.'" /></td>
          <td><input type="text" name="description" value="'.$description.'" /></td>
          <td><input type="submit" class="rex-fsubmit" name="add_article_type" value="'.$I18N->msg('article_type_add').'" /></td>
        </tr>';
}

$sql = new sql;
$sql->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'article_type ORDER BY type_id');

for ($i = 0; $i < $sql->getRows(); $i++)
{
  if ($func == 'edit' && $type_id == $sql->getValue("type_id"))
  {
    echo '
            <tr class="rex-trow-actv">
              <td>&nbsp;</td>
              <td>'.htmlspecialchars($sql->getValue("type_id")).'</td>
              <td><input type="text" name="typname" value="'.htmlspecialchars($sql->getValue("name")).'" /></td>
              <td><input type="text" name="description" value="'.htmlspecialchars($sql->getValue("description")).'" /></td>
              <td>
                <input type="submit" class="rex-fsubmit" name="edit_article_type" value="'.$I18N->msg("article_type_update").'"/>
                <input type="submit" class="rex-fsubmit" name="delete_article_type" value="'.$I18N->msg("article_type_delete").'" onclick="return confirm(\''.$I18N->msg('delete').' ?\')" />
              </td>
            </tr>';
  }
  else
  {
    echo '
            <tr>
              <td>&nbsp;</td>
              <td>'.htmlspecialchars($sql->getValue("type_id")).'</td>
              <td><a href="index.php?page=specials&amp;subpage=type&amp;func=edit&amp;type_id='.$sql->getValue("type_id").'">'.htmlspecialchars($sql->getValue("name")).'&nbsp;</a></td>
              <td colspan="2">'.nl2br($sql->getValue("description")).'&nbsp;</td>
            </tr>';
  }
  $sql->counter++;
}

echo '
        </tbody>
      </table>';

if ($func == 'add' || $func == 'edit')
{
  echo '
        </fieldset>
      </form>';
}
?>