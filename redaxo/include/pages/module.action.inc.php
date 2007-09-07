<?php

/**
 *
 * @package redaxo3
 * @version $Id$
 */

$OUT = TRUE;

$action_id = rex_request('action_id', 'int');
$function = rex_request('function', 'string');

$action_in_use_msg = '';
if ($function == "delete")
{
  $del = new rex_sql;
//  $del->debugsql = true;
  $qry = 'SELECT
            *
          FROM
            '. $REX['TABLE_PREFIX'] .'module_action a
          LEFT JOIN
           '. $REX['TABLE_PREFIX'] .'module m
          ON
            a.module_id = m.id
          WHERE
            a.action_id='. $action_id;
  $del->setQuery($qry); // module mit dieser aktion vorhanden ?
  if ($del->getRows() > 0)
  {
    $modulname = htmlspecialchars($del->getValue('a.module_id'));
    for ($i = 0; $i < $del->getRows(); $i++)
    {
      $action_in_use_msg .= '<li><a href="index.php?page=module&amp;function=edit&amp;modul_id=' . $del->getValue('a.module_id') . '">'. $del->getValue('m.name') . ' ['. $del->getValue('a.module_id') . ']</a></li>';
      $del->next();
    }

    if ($action_in_use_msg != '')
    {
      $action_in_use_msg = '<div class="rex-warning-blck"><ul>' . $action_in_use_msg . '</ul></div>';
    }

    $message = $I18N->msg("action_cannot_be_deleted", $action_id);
  }
  else
  {
    $del->setQuery("DELETE FROM " . $REX['TABLE_PREFIX'] . "action WHERE id='$action_id' LIMIT 1");
    $message = $I18N->msg("action_deleted");
  }
}

if ($function == "add" or $function == "edit")
{
  $name           = rex_post('name','string');
  $previewaction  = rex_post('previewaction','string');
  $presaveaction  = rex_post('presaveaction','string');
  $postsaveaction = rex_post('postsaveaction','string');

  $previewstatus = 0;
  $presavestatus = 0;
  $postsavestatus = 0;

  if (isset ($save) and $save == "ja")
  {
    $faction = new rex_sql;

    $previewstatus = rex_post('previewstatus', 'array');
    $presavestatus = rex_post('presavestatus', 'array');
    $postsavestatus = rex_post('postsavestatus', 'array');

    $previewmode = 0;
    foreach ($previewstatus as $status)
      $previewmode |= $status;

    $presavemode = 0;
    foreach ($presavestatus as $status)
      $presavemode |= $status;

    $postsavemode = 0;
    foreach ($postsavestatus as $status)
      $postsavemode |= $status;

    $faction->setTable($REX['TABLE_PREFIX'] . 'action');
    $faction->setValue('name', $name);
    $faction->setValue('preview', $previewaction);
    $faction->setValue('presave', $presaveaction);
    $faction->setValue('postsave', $postsaveaction);
    $faction->setValue('previewmode', $previewmode);
    $faction->setValue('presavemode', $presavemode);
    $faction->setValue('postsavemode', $postsavemode);

    if ($function == 'add')
    {
      $faction->setValue('createuser', $REX_USER->getValue('login'));
      $faction->setValue('createdate', time());

      if($faction->insert())
        $message = $I18N->msg('action_added');
      else
        $message = $faction->getError();
    }
    else
    {
      $faction->setValue('updateuser', $REX_USER->getValue('login'));
      $faction->setValue('updatedate', time());
      $faction->setWhere('id=' . $action_id);

      if($faction->update())
        $message = $I18N->msg('action_updated');
      else
        $message = $faction->getError();
    }

    if (isset ($goon) and $goon != '')
    {
      $save = 'nein';
    }
    else
    {
      $function = '';
    }
  }

  if (!isset ($save) or $save != 'ja')
  {
    if ($function == 'edit')
    {
      $legend = $I18N->msg('action_edit') . ' [ID=' . $action_id . ']';

      $hole = new rex_sql;
      $hole->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'action WHERE id='.$action_id);

      $name           = $hole->getValue('name');
      $previewaction  = $hole->getValue('preview');
      $presaveaction  = $hole->getValue('presave');
      $postsaveaction = $hole->getValue('postsave');
      $previewstatus  = $hole->getValue('previewmode');
      $presavestatus  = $hole->getValue('presavemode');
      $postsavestatus = $hole->getValue('postsavemode');
    }
    else
    {
      $legend = $I18N->msg('action_create');
    }

    $sel_preview_status = new rex_select();
    $sel_preview_status->setMultiple(1);
    $sel_preview_status->addOption($ASTATUS[0] .' - '.$I18N->msg('action_event_add') ,1);
    $sel_preview_status->addOption($ASTATUS[1] .' - '.$I18N->msg('action_event_edit') ,2);
    $sel_preview_status->addOption($ASTATUS[2] .' - '.$I18N->msg('action_event_delete') ,4);
    $sel_preview_status->setSize(3);

    $sel_preview_status->setName('previewstatus[]');
    $sel_preview_status->setId('previewstatus');

    $sel_presave_status = $sel_preview_status;
    $sel_presave_status->setName('presavestatus[]');
    $sel_presave_status->setId('presavestatus');

    $sel_postsave_status = $sel_preview_status;
    $sel_postsave_status->setName('postsavestatus[]');
    $sel_postsave_status->setId('postsavestatus');

    foreach (array (1,2,4) as $var)
      if (($previewstatus & $var) == $var)
        $sel_preview_status->setSelected($var);

    foreach (array (1,2,4) as $var)
      if (($presavestatus & $var) == $var)
        $sel_presave_status->setSelected($var);

    foreach (array (1,2,4) as $var)
      if (($postsavestatus & $var) == $var)
        $sel_postsave_status->setSelected($var);

    $btn_update = '';
    if ($function != 'add')
      $btn_update = '<input type="submit" class="rex-sbmt" name="goon" value="' . $I18N->msg('save_action_and_continue') . '"'. rex_accesskey($I18N->msg('save_action_and_continue'), $REX['ACKEY']['APPLY']) .' />';

    if (isset ($message) and $message != '')
    {
      echo rex_warning($message);
    }

    echo '
      <div class="rex-mdl-editmode">
        <form action="index.php" method="post">
          <fieldset>
            <legend class="rex-lgnd" id="edit">' . $legend . ' </legend>

           <div class="rex-fldst-wrppr">
          <input type="hidden" name="page" value="module" />
          <input type="hidden" name="subpage" value="actions" />
          <input type="hidden" name="function" value="' . $function . '" />
          <input type="hidden" name="save" value="ja" />
          <input type="hidden" name="action_id" value="' . $action_id . '" />
          <p>
            <label for="name">' . $I18N->msg('action_name') . '</label>
            <input type="text" size="10" id="name" name="name" value="' . htmlspecialchars($name) . '" />
          </p>
            </div>
          </fieldset>

          <fieldset>
            <legend class="rex-lgnd">Preview-Action ['. $I18N->msg('action_mode_preview') .']</legend>
           <div class="rex-fldst-wrppr">
          <p>
            <label for="previewaction">' . $I18N->msg('input') . '</label>
            <textarea class="rex-txtr-cd" cols="50" rows="6" name="previewaction" id="previewaction">' . htmlspecialchars($previewaction) . '</textarea>
            <span>' . $I18N->msg('action_hint') . '</span>
          </p>
          <p class="rex-slct">
            <label for="previestatus">' . $I18N->msg('action_event') . '</label>
            ' . $sel_preview_status->get() . '
            <span>' . $I18N->msg('ctrl') . '</span>
          </p>
         </div>
        </fieldset>
          <fieldset>
            <legend class="rex-lgnd">Presave-Action ['. $I18N->msg('action_mode_presave') .']</legend>
            <div class="rex-fldst-wrppr">
          <p>
            <label for="presaveaction">' . $I18N->msg('input') . '</label>
            <textarea class="rex-txtr-cd" cols="50" rows="6" name="presaveaction" id="presaveaction">' . htmlspecialchars($presaveaction) . '</textarea>
            <span>' . $I18N->msg('action_hint') . '</span>
          </p>
          <p class="rex-slct">
            <label for="presavestatus">' . $I18N->msg('action_event') . '</label>
            ' . $sel_presave_status->get() . '
            <span>' . $I18N->msg('ctrl') . '</span>
          </p>
        </div>
          </fieldset>
          <fieldset>
            <legend class="rex-lgnd">Postsave-Action ['. $I18N->msg('action_mode_postsave') .']</legend>
           <div class="rex-fldst-wrppr">
          <p>
            <label for="postsaveaction">' . $I18N->msg('input') . '</label>
            <textarea class="rex-txtr-cd" cols="50" rows="6" name="postsaveaction" id="postsaveaction">' . htmlspecialchars($postsaveaction) . '</textarea>
            <span>' . $I18N->msg('action_hint') . '</span>
          </p>
          <p class="rex-slct">
            <label for="postsavestatus">' . $I18N->msg('action_event') . '</label>
            ' . $sel_postsave_status->get() . '
            <span>' . $I18N->msg('ctrl') . '</span>
          </p>
          <p>
            <input class="rex-sbmt" type="submit" value="' . $I18N->msg('save_action_and_quit') . '"'. rex_accesskey($I18N->msg('save_action_and_quit'), $REX['ACKEY']['SAVE']) .' />
            ' . $btn_update . '
          </p>
        </div>
          </fieldset>
        </form>
      </div>';

    $OUT = false;
  }
}

if ($OUT)
{
  if (isset ($message) and $message != "")
  {
    echo rex_warning($message);
    echo $action_in_use_msg;
  }

  // ausgabe actionsliste !
  echo '
    <table class="rex-table" summary="' . $I18N->msg('action_summary') . '">
      <caption class="rex-hide">' . $I18N->msg('action_caption') . '</caption>
      <colgroup>
        <col width="40" />
        <col width="40" />
        <col width="*" />
        <col width="120" />
        <col width="120" />
        <col width="120" />
        <col width="153" />
      </colgroup>
      <thead>
        <tr>
          <th class="rex-icon"><a href="index.php?page=module&amp;subpage=actions&amp;function=add"'. rex_accesskey($I18N->msg('action_create'), $REX['ACKEY']['ADD']) .'><img src="media/modul_plus.gif" alt="' . $I18N->msg('action_create') . '" /></a></th>
          <th class="rex-icon">ID</th>
          <th>' . $I18N->msg('action_name') . '</th>
          <th>Preview-Event(s)</th>
          <th>Presave-Event(s)</th>
          <th>Postsave-Event(s)</th>
          <th>' . $I18N->msg('action_functions') . '</th>
        </tr>
      <tbody>
    ';

  $sql = new rex_sql;
  $sql->setQuery('SELECT * FROM ' . $REX['TABLE_PREFIX'] . 'action ORDER BY name');

  for ($i = 0; $i < $sql->getRows(); $i++)
  {
    $previewmode = array ();
    $presavemode = array ();
    $postsavemode = array ();

    foreach (array (1 => 'ADD',2 => 'EDIT',4 => 'DELETE') as $var => $value)
      if (($sql->getValue('previewmode') & $var) == $var)
        $previewmode[] = $value;

    foreach (array (1 => 'ADD',2 => 'EDIT',4 => 'DELETE') as $var => $value)
      if (($sql->getValue('presavemode') & $var) == $var)
        $presavemode[] = $value;

    foreach (array (1 => 'ADD',2 => 'EDIT',4 => 'DELETE') as $var => $value)
      if (($sql->getValue('postsavemode') & $var) == $var)
        $postsavemode[] = $value;

    echo '
          <tr>
            <td class="rex-icon"><a href="index.php?page=module&amp;subpage=actions&amp;action_id=' . $sql->getValue("id") . '&amp;function=edit"><img src="media/modul.gif" alt="' . $sql->getValue("name") . '" title="' . $sql->getValue("name") . '" /></a></td>
            <td class="rex-icon">' . $sql->getValue("id") . '</td>
            <td><a href="index.php?page=module&amp;subpage=actions&amp;action_id=' . $sql->getValue("id") . '&amp;function=edit">' . htmlspecialchars($sql->getValue("name")) . '</a></td>
            <td>' . implode('/', $previewmode) . '</td>
            <td>' . implode('/', $presavemode) . '</td>
            <td>' . implode('/', $postsavemode) . '</td>
            <td><a href="index.php?page=module&amp;subpage=actions&amp;action_id=' . $sql->getValue("id") . '&amp;function=delete" onclick="return confirm(\'' . $I18N->msg('action_delete') . ' ?\')">' . $I18N->msg("action_delete") . '</a></td>
          </tr>
        ';

    $sql->counter++;
  }

  echo '
      </tbody>
    </table>';
}
?>