<?php

$OUT = TRUE;
$type['add']  = 1;
$type['edit'] = 2;
$type['del']  = 4;

if (isset($function) and $function == "delete")
{
  $del = new sql;
  $del->setQuery("SELECT * FROM rex_module_action WHERE action_id='$action_id'"); // module mit dieser aktion vorhanden ?
  
  if ($del->getRows()>0)
  {
    $module = '<font class="black">|</font> ';
    $modulname = htmlspecialchars($del->getValue("rex_module_action.module_id"));
    for ($i=0;$i<$del->getRows();$i++)
    {
     $module .= '<a href="index.php?page=module&amp;function=edit&amp;modul_id='.$del->getValue("rex_module_action.module_id").'">'.$del->getValue("rex_module_action.module_id").'</a> <font class="black">|</font> ';
     $del->next();
    }
    
    $message = '<b>'.$I18N->msg("action_cannot_be_deleted",$action_id).'</b><br /> '.$module;
  } else
  {
    $del->query("DELETE FROM rex_action WHERE id='$action_id' LIMIT 1");
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
      $faction->query("insert into rex_action (name,action,prepost,sadd,sedit,sdelete) VALUES ('$mname','$actioninput','$prepost','$sadd','$sedit','$sdelete')");
      $message = '<p class="warning">'.$I18N->msg("action_added").'</p>';
    }else{
      $faction->query("update rex_action set name='$mname',action='$actioninput',prepost='$prepost',sadd='$sadd',sedit='$sedit',sdelete='$sdelete' where id='$action_id'");
      $message = '<p class="warning">'.$I18N->msg("action_updated").'</p>';
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
    echo '<a name="edit"><table class="rex" style="table-layout:auto;" cellpadding="5" cellspacing="1">';
  
    if ($function == "edit"){
      echo '  <tr><th colspan="3">'.$I18N->msg("action_edit").' [ID='.$action_id.']</th></tr>';

      $hole = new sql;
      $hole->setQuery("SELECT * FROM rex_action WHERE id='$action_id'");
      $mname = $hole->getValue("name");
      $actioninput = $hole->getValue("action");
      $prepost = $hole->getValue("prepost");
      $sadd = $hole->getValue("sadd");
      $sedit = $hole->getValue("sedit");
      $sdelete = $hole->getValue("sdelete");
            
    } else {
      echo '  <tr><th colspan="3">'.$I18N->msg("action_create").'</th></tr>';
      $prepost  = 0; // 0=pre / 1=post
      $sadd = 0;
      $sedit = 0;
      $sdelete = 0;
    }

    if (isset($message) and $message != '')
    {
      echo '<tr class="warning"><td colspan="3">'.$message.'</td></tr>';
    }

    $sel_prepost = new select();
    $sel_prepost->set_name("prepost");
    $sel_prepost->add_option($PREPOST[0],"0");
    $sel_prepost->add_option($PREPOST[1],"1");
    $sel_prepost->set_size(1);
    $sel_prepost->set_style("width:100px;");
    $sel_prepost->set_selected($prepost);

    $sel_status = new select();
    $sel_status->set_name("status[]");
    $sel_status->multiple(1);
    $sel_status->add_option($ASTATUS[0],"1");
    $sel_status->add_option($ASTATUS[1],"2");
    $sel_status->add_option($ASTATUS[2],"4");
    $sel_status->set_size(3);
    $sel_status->set_style("width:100px;");
    
    if ($sadd == 1) $sel_status->set_selected(1);
    if ($sedit == 1) $sel_status->set_selected(2);
    if ($sdelete == 1) $sel_status->set_selected(4);
    
    if (!isset($action_id)) $action_id = '';
    if (!isset($mname)) $mname = '';
    if (!isset($actioninput)) $actioninput = '';
    echo '  
      <form action="index.php" method="post">
      <input type="hidden" name="page" value="module">
      <input type="hidden" name="subpage" value="actions">
      <input type="hidden" name="function" value="'.$function.'">
      <input type="hidden" name="save" value="ja">
      <input type="hidden" name="action_id" value="'.$action_id.'">
      <tr>
        <td width="100">'.$I18N->msg("action_name").'</td>
        <td class="grey" colspan="2"><input type="text" size="10" name="mname" value="'.htmlspecialchars($mname).'" style="width:100%;"></td>
      </tr>
      <tr>
        <td valign="top">'.$I18N->msg("input").'</td>
        <td colspan="2">
          <textarea cols="20" rows="70" name="actioninput" id="actioninput" style="width:100%; height: 150;">'.htmlspecialchars($actioninput).'</textarea>
        </td>
      </tr>'."\n";
      
    echo '
      <tr>
        <td align="right" valign="middle">'.$PREPOST[0].'/'.$PREPOST[1].'</td>
        <td valign="middle" class="grey" colspan="2">'.$sel_prepost->out().'</td>
      </tr>
      <tr>
        <td align="right" valign="middle">STATUS</td>
        <td valign="middle" colspan="2">'.$sel_status->out().'</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td width="200"><input type="submit" value="'.$I18N->msg("save_action_and_quit").'"></td>
        <td>'."\n";
    
    if ($function != "add") echo '<input type="submit" name="goon" value="'.$I18N->msg("save_action_and_continue").'">';
    
    echo '</td>
      </tr>
      </form>
      </table>'."\n";

    $OUT = false;

  }
}

if ($OUT)
{
  // ausgabe actionsliste !
  echo '<table class="rex" style="table-layout:auto;" cellpadding="5" cellspacing="1">
    <tr>
      <th class="icon"><a href="index.php?page=module&amp;subpage=actions&amp;function=add"><img src="pics/modul_plus.gif" width="16" height="16" border="0" alt="'.$I18N->msg("action_create").'" title="'.$I18N->msg("action_create").'"></a></th>
      <th class="icon">ID</th>
      <th width="200">'.$I18N->msg("action_name").'</th>
      <th>'.$I18N->msg("action_add").'</th>
      <th>'.$I18N->msg("action_edit").'</th>
      <th>'.$I18N->msg("action_delete").'</th>
      <th>'.$I18N->msg("action_functions").'</th>
    </tr>
    '."\n";
  
  if (isset($message) and $message != "")
  {
    echo '<tr class="warning">
      <td align="center"><img src="pics/warning.gif" width="16" height="16"></td><td colspan="6">'.$message.'</td></tr>';
  }
  
  $sql = new sql;
  $sql->setQuery("SELECT * FROM rex_action ORDER BY name");
  
  for ($i=0; $i<$sql->getRows(); $i++) {
  
    echo '  <tr bgcolor="#eeeeee">
        <td class="icon"><a href="index.php?page=module&amp;subpage=actions&amp;action_id='.$sql->getValue("id").'&amp;function=edit"><img src="pics/modul.gif" width="16" height="16" border="0"></a></td>
        <td class="icon">'.$sql->getValue("id").'</td>
        <td ><a href="index.php?page=module&amp;subpage=actions&amp;action_id='.$sql->getValue("id").'&amp;function=edit">'.htmlspecialchars($sql->getValue("name")).'</a> ['.$PREPOST[$sql->getValue("prepost")].']</td>
        <td>'."\n";
    if ($sql->getValue("sadd") == 1) echo 'X';
    echo '</td><td>';
    if ($sql->getValue("sedit") == 1) echo 'X';
    echo '</td><td>';
    if ($sql->getValue("sdelete") == 1) echo 'X';
    echo '</td>
        <td><a href="index.php?page=module&amp;subpage=actions&amp;action_id='.$sql->getValue("id").'&amp;function=delete" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg("action_delete").'</a></td>
      </tr>'."\n";
    $sql->counter++;
  }
  
  echo '</table>';
}

?>