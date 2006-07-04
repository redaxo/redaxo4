<?php
/** 
 *  
 * @package redaxo3
 * @version $Id$
 */

$OUT = TRUE;
$type['add']  = 1;
$type['edit'] = 2;
$type['del']  = 4;

if (isset($function) and $function == "delete")
{
  $del = new sql;
  $del->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."module_action WHERE action_id='$action_id'"); // module mit dieser aktion vorhanden ?
  
  if ($del->getRows()>0)
  {
    $module = '';
    $modulname = htmlspecialchars($del->getValue($REX['TABLE_PREFIX']."module_action.module_id"));
    for ($i=0;$i<$del->getRows();$i++)
    {
     $module .= '<li><a href="index.php?page=module&amp;function=edit&amp;modul_id='.$del->getValue($REX['TABLE_PREFIX']."module_action.module_id").'">'.$del->getValue($REX['TABLE_PREFIX']."module_action.module_id").'</a></li>';
     $del->next();
    }
    
    if($module != '')
    {
      $module = '<ul>'. $module . '</ul>';
    }
    
    $message = $I18N->msg("action_cannot_be_deleted",$action_id).'<br /> '.$module;
  }
  else
  {
    $del->query("DELETE FROM ".$REX['TABLE_PREFIX']."action WHERE id='$action_id' LIMIT 1");
    $message = $I18N->msg("action_deleted");
  }
}

if (isset($function) and ($function == "add" or $function == "edit"))
{

  if (isset($save) and $save == "ja")
  {
    $faction = new sql;

    $sadd = 0;
    if (@in_array("1",$status)) $sadd = 1;
    $sedit = 0;
    if (@in_array("2",$status)) $sedit = 1;
    $sdelete = 0;
    if (@in_array("4",$status)) $sdelete = 1;

    if ($function == "add")
    {
      $faction->query("insert into ".$REX['TABLE_PREFIX']."action (name,action,prepost,sadd,sedit,sdelete) VALUES ('$mname','$actioninput','$prepost','$sadd','$sedit','$sdelete')");
      $message = $I18N->msg("action_added");
    }else{
      $faction->query("update ".$REX['TABLE_PREFIX']."action set name='$mname',action='$actioninput',prepost='$prepost',sadd='$sadd',sedit='$sedit',sdelete='$sdelete' where id='$action_id'");
      $message = $I18N->msg("action_updated");
    }
    
    if (isset($goon) and $goon != "")
    {
      $save = "nein";
    } else
    {
      $function = "";
    }
  }

  if (!isset($save) or $save != "ja")
  {
    if (!isset($action_id)) $action_id = '';
    if (!isset($mname)) $mname = '';
    if (!isset($actioninput)) $actioninput = '';
    
    
    if ($function == "edit")
    {
      $legend = $I18N->msg("action_edit"). ' [ID='.$action_id.']';

      $hole = new sql;
      $hole->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."action WHERE id='$action_id'");
      $mname = $hole->getValue("name");
      $actioninput = $hole->getValue("action");
      $prepost = $hole->getValue("prepost");
      $sadd = $hole->getValue("sadd");
      $sedit = $hole->getValue("sedit");
      $sdelete = $hole->getValue("sdelete");
            
    }
    else
    {
      $legend = $I18N->msg("action_create");
      
      $prepost  = 0; // 0=pre / 1=post
      $sadd = 0;
      $sedit = 0;
      $sdelete = 0;
    }

    $sel_prepost = new select();
    $sel_prepost->set_name("prepost");
    $sel_prepost->set_id("prepost");
    $sel_prepost->add_option($PREPOST[0] .' - '.$I18N->msg("action_time_pre"),"0");
    $sel_prepost->add_option($PREPOST[1] .' - '.$I18N->msg("action_time_post"),"1");
    $sel_prepost->set_size(1);
    $sel_prepost->set_selected($prepost);

    $sel_status = new select();
    $sel_status->set_name("status[]");
    $sel_status->set_id("status");
    $sel_status->multiple(1);
    $sel_status->add_option($ASTATUS[0] .' - '.$I18N->msg("action_event_add") ,"1");
    $sel_status->add_option($ASTATUS[1] .' - '.$I18N->msg("action_event_edit") ,"2");
    $sel_status->add_option($ASTATUS[2] .' - '.$I18N->msg("action_event_delete") ,"4");
    $sel_status->set_size(3);
    
    if ($sadd == 1) $sel_status->set_selected(1);
    if ($sedit == 1) $sel_status->set_selected(2);
    if ($sdelete == 1) $sel_status->set_selected(4);
    
    $btn_update = '';
    if ($function != "add") $btn_update = '<input type="submit" class="rex-fsubmit" name="goon" value="'.$I18N->msg("save_action_and_continue").'" />';
    
    if (isset($message) and $message != '')
    {
      echo '<p class="rex-warning">'.$message.'</p>';
    }
    
    echo '
	<div class="rex-mdl-moduleform">
    <form action="index.php" method="post">
      <fieldset>
        <legend id="edit">'. $legend .' </legend>
        <input type="hidden" name="page" value="module" />
        <input type="hidden" name="subpage" value="actions" />
        <input type="hidden" name="function" value="'.$function.'" />
        <input type="hidden" name="save" value="ja" />
        <input type="hidden" name="action_id" value="'.$action_id.'" />
        <p>
          <label for="mname">'.$I18N->msg("action_name").'</label>
          <input type="text" size="10" id="mname" name="mname" value="'.htmlspecialchars($mname).'" />
        </p>
        <p>
          <label for="actioninput">'.$I18N->msg("input").'</label>
          <textarea cols="50" rows="6" name="actioninput" id="actioninput" class="rex-ftxtr-cd">'.htmlspecialchars($actioninput).'</textarea>
        </p>
        <p>
          <label for="prepost">'.$I18N->msg("action_time").'</label>
          '.$sel_prepost->out().'
        </p>
        <p>
          <label for="status">'.$I18N->msg("action_event").'<br />('.$I18N->msg("ctrl").')</label>
          '.$sel_status->out().'
        </p>
        <p>
          <input type="submit" class="rex-fsubmit" value="'.$I18N->msg("save_action_and_quit").'" />
          '. $btn_update .'
        </p>
      </fieldset>
    </form>
	</div>';
    
    $OUT = false;
  }
}

if ($OUT)
{
  if (isset($message) and $message != "")
  {
    echo '<p class="rex-warning>"'.$message.'</p>';
  }
  
  // ausgabe actionsliste !
  echo '
  <table class="rex-table" summary="'.$I18N->msg("action_summary").'">
  	<caption class="rex-hide">'.$I18N->msg("action_caption").'</caption>
    <colgroup>
      <col width="5%" />
      <col width="5%" />
      <col width="*" />
      <col width="17%" />
      <col width="30%" />
      <col width="17%" />
    </colgroup>
    <thead>
      <tr>
        <th><a href="index.php?page=module&amp;subpage=actions&amp;function=add"><img src="pics/modul_plus.gif" width="16" height="16" alt="'.$I18N->msg("action_create").'" title="'.$I18N->msg("action_create").'" /></a></th>
        <th>ID</th>
        <th>'.$I18N->msg("action_name").'</th>
        <th>'.$I18N->msg("action_time").'</th>
        <th>'.$I18N->msg("action_event").'</th>
        <th>'.$I18N->msg("action_functions").'</th>
      </tr>
    <tbody>
  ';
  
  $sql = new sql;
  $sql->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."action ORDER BY name");
  
  for ($i=0; $i<$sql->getRows(); $i++) 
  {
    $events = array();
    if ($sql->getValue("sadd") == 1) $events[] = $ASTATUS[0];
    if ($sql->getValue("sedit") == 1) $events[] = $ASTATUS[1];
    if ($sql->getValue("sdelete") == 1) $events[] = $ASTATUS[2];
    $events = implode(' / ', $events);
    
    echo '  
      <tr>
        <td><a href="index.php?page=module&amp;subpage=actions&amp;action_id='.$sql->getValue("id").'&amp;function=edit"><img src="pics/modul.gif" width="16" height="16" alt="'. $sql->getValue("name") .'" title="'. $sql->getValue("name") .'" /></a></td>
        <td>'.$sql->getValue("id").'</td>
        <td><a href="index.php?page=module&amp;subpage=actions&amp;action_id='.$sql->getValue("id").'&amp;function=edit">'.htmlspecialchars($sql->getValue("name")).'</a></td>
        <td>'.$PREPOST[$sql->getValue("prepost")].'</td>
        <td>'. $events .'</td>
        <td><a href="index.php?page=module&amp;subpage=actions&amp;action_id='.$sql->getValue("id").'&amp;function=delete" onclick="return confirm(\''.$I18N->msg('action_delete').' ?\')">'.$I18N->msg("action_delete").'</a></td>
      </tr>
    ';
    
    $sql->counter++;
  }
  
  echo '
    </tbody>
  </table>';
}

?>