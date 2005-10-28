<?php
  
$OUT = TRUE;

// ---------------------------- ACTIONSFUNKTIONEN FÜR MODULE

if (isset($function_action) and $function_action == 'add')
{
  $aa = new sql;
  $aa->query("INSERT INTO rex_module_action SET module_id='$modul_id', action_id='$action_id'");
  $message = $I18N->msg("action_taken");
  
} elseif (isset($function_action) and $function_action == 'delete')
{
  $aa = new sql;
  $aa->query("DELETE FROM rex_module_action WHERE module_id='$modul_id' and id='$iaction_id' LIMIT 1");
  $message = $I18N->msg("action_deleted_from_modul");
} 



// ---------------------------- FUNKTIONEN FÜR MODULE

if (isset($function) and $function == 'delete')
{
  $del = new sql;
  $del->setQuery("SELECT DISTINCT rex_article_slice.article_id, rex_modultyp.name FROM rex_article_slice 
      LEFT JOIN rex_modultyp ON rex_article_slice.modultyp_id=rex_modultyp.id 
      WHERE rex_article_slice.modultyp_id='$modul_id'");
  
  if ($del->getRows() >0)
  {
    $module = '<font class="black">|</font> ';
    $modulname = htmlspecialchars($del->getValue("rex_modultyp.name"));
    for ($i=0; $i<$del->getRows(); $i++)
    {
     $module .= '<a href="index.php?page=content&amp;article_id='.$del->getValue("rex_article_slice.article_id").'">'.$del->getValue("rex_article_slice.article_id").'</a> <font class="black">|</font> ';
     $del->next();
    }
    
    $message = '<b>'.$I18N->msg("module_cannot_be_deleted",$modulname).'</b><br /> '.$module;
  } else
  {
    $del->query("DELETE FROM rex_modultyp WHERE id='$modul_id'");
    $message = $I18N->msg("module_deleted");
  }
}

if (isset($function) and ($function == 'add' or $function == 'edit'))
{

  if (isset($save) and $save == 'ja')
  {
    $modultyp = new sql;

    if ($function == 'add')
    {
      $modultyp->query("INSERT INTO rex_modultyp (category_id, name, eingabe, ausgabe) VALUES ('$category_id', '$mname', '$eingabe', '$ausgabe')");
      $message = '<p class="warning">'.$I18N->msg("module_added").'</p>';
    } else {
      $modultyp->query("UPDATE rex_modultyp SET name='$mname', eingabe='$eingabe', ausgabe='$ausgabe' WHERE id='$modul_id'");
      $message = '<p class="warning">'.$I18N->msg("module_updated").' | '.$I18N->msg("articel_updated").'</font></p>';
      
      // article updaten
      $gc = new sql;
      $gc->setQuery("SELECT DISTINCT(rex_article.id) FROM rex_article 
          LEFT JOIN rex_article_slice ON rex_article.id=rex_article_slice.article_id 
          WHERE rex_article_slice.modultyp_id='$modul_id'");
      for ($i=0; $i<$gc->getRows(); $i++)
      {
        rex_generateArticle($gc->getValue("rex_article.id"));
        $gc->next();
      }
    }
    
    if (isset($goon) and $goon != '')
    {
      $save = 'nein';
    } else
    {
      $function = '';
    }
  }



  if (!isset($save) or $save != 'ja')
  {
    echo '<a name="edit"><table class="rex" style="table-layout:auto;" cellpadding="5" cellspacing="1">';
  
    if ($function == 'edit'){

      $hole = new sql;
      $hole->setQuery("SELECT * FROM rex_modultyp WHERE id='$modul_id'");
      $category_id  = $hole->getValue("category_id");
      $mname    = $hole->getValue("name");
      $include  = $hole->getValue("include");
      $ausgabe  = $hole->getValue("ausgabe");
      $eingabe  = $hole->getValue("eingabe");
            
      echo '  <tr><th colspan="3">'.$I18N->msg("module_edit").' [ID='.$modul_id.']</th></tr>';

    } else {
      echo '  <tr><th colspan="3">'.$I18N->msg("create_module").'</th></tr>';
    }

    if (isset($message) and $message != '')
    {
      echo '<tr class="warning"><td colspan="3">'.$message.'</td></tr>';
    }

    if (!isset($modul_id)) $modul_id = '';
    if (!isset($mname)) $mname = '';
    if (!isset($eingabe)) $eingabe = '';
    if (!isset($ausgabe)) $ausgabe = '';
    echo '  
      <form action="index.php" method="post">
      <input type="hidden" name="page" value="module">
      <input type="hidden" name="function" value="'.$function.'">
      <input type="hidden" name="save" value="ja">
      <input type="hidden" name="category_id" value="0">
      <input type="hidden" name="modul_id" value="'.$modul_id.'">
      <tr>
        <td width="100">'.$I18N->msg("module_name").'</td>
        <td colspan="2"><input type="text" size="10" name="mname" value="'.htmlspecialchars($mname).'" style="width:100%;"></td>
      </tr>
      <tr>
        <td valign="top">'.$I18N->msg("input").'</td>
        <td colspan="2">
          <textarea cols="20" rows="70" name="eingabe" id="eingabe" style="width:100%; height: 150;">'.htmlspecialchars($eingabe).'</textarea>
        </td>
      </tr>
      <tr>
        <td valign="top">'.$I18N->msg("output").'</td>
        <td colspan="2">
          <textarea cols="20" rows="70" name="ausgabe" id="ausgabe" style="width:100%; height: 150;">'.htmlspecialchars($ausgabe).'</textarea>
        </td>
      </tr>'."\n";
      
    echo '
      <tr>
        <td>&nbsp;</td>
        <td width="200"><input type="submit" value="'.$I18N->msg("save_module_and_quit").'"></td>
        <td>'."\n";
    
    if ($function != 'add') echo '<input type="submit" name="goon" value="'.$I18N->msg("save_module_and_continue").'">';
    
    echo '</td>
      </tr>
      </form>';

    if ($function == 'edit')
    {
      
      $gaa = new sql;
      $gaa->setQuery("SELECT * FROM rex_action ORDER BY name");

      if ($gaa->getRows()>0)
      {     
      
        echo '<tr><td colspan="3"></td></tr><tr><td colspan="3" align="left"><a name="action"></a><b>'.$I18N->msg("actions").'</b></td></tr>';
  
        $gma = new sql;
        $gma->setQuery("SELECT * FROM rex_module_action, rex_action WHERE rex_module_action.action_id=rex_action.id and rex_module_action.module_id='$modul_id'");
        for ($i=0; $i<$gma->getRows(); $i++)
        {
          $iaction_id = $gma->getValue("rex_module_action.id");
          $action_id = $gma->getValue("rex_module_action.action_id");

          echo '<tr>
            <td>&nbsp;</td>
            <td>';
          
          echo '<a href="index.php?page=module&amp;subpage=actions&amp;action_id='.$action_id.'&amp;function=edit">'.$gma->getValue("name").'</a>';
          echo ' [';
          echo $PREPOST[$gma->getValue("prepost")];
          
          if ($gma->getValue("sadd")==1) echo "|".$ASTATUS[0];
          if ($gma->getValue("sedit")==1) echo "|".$ASTATUS[1];
          if ($gma->getValue("sdelete")==1) echo "|".$ASTATUS[2];
          
          echo '] </td>';
          echo '<td><a href="index.php?page=module&amp;modul_id='.$modul_id.'&amp;function_action=delete&amp;function=edit&amp;iaction_id='.$iaction_id.'" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg("action_delete").'</a></td>';
          echo '</tr>';
          $gma->next();
        }
        
        $gaa_sel = new select();
        $gaa_sel->set_name("action_id");
        $gaa_sel->set_size(1);
        $gaa_sel->set_style("' class='inp100");
        
        for ($i=0; $i<$gaa->getRows(); $i++)
        {
          $status = "";
          if ($gaa->getValue("sadd")==1) $status .= "|".$ASTATUS[0];
          if ($gaa->getValue("sedit")==1) $status .= "|".$ASTATUS[1];
          if ($gaa->getValue("sdelete")==1) $status .= "|".$ASTATUS[2];
          
          $gaa_sel->add_option($gaa->getValue("name")." [".$PREPOST[$gaa->getValue("prepost")]."$status]",$gaa->getValue("id"));
          $gaa->next();
        }

        echo '<form action="index.php#action" method="post">';
        echo '<input type="hidden" name="page" value="module">';
        echo '<input type="hidden" name="modul_id" value="'.$modul_id.'">';
        echo '<input type="hidden" name="function" value="edit">';
        echo '<input type="hidden" name="function_action" value="add">';
        
        echo '<tr><td colspan="3"></td></tr><tr>
          <td>&nbsp;</td>
          <td>'.$gaa_sel->out().'</td>
          <td><input type="submit" value="'.$I18N->msg("action_add").'"></td>
          </tr>'."\n";
        
        echo '</form>';

      }

    }
  
    echo '</table>';
  
    $OUT = false;

  }
}

if ($OUT)
{
  // ausgabe modulliste !
  echo '<table class="rex" style="table-layout:auto;" cellpadding="5" cellspacing="1">
    <tr>
      <th class="icon"><a href="index.php?page=module&amp;function=add"><img src="pics/modul_plus.gif" width="16" height="16" border="0" alt="'.$I18N->msg("create_module").'" title="'.$I18N->msg("create_module").'"></a></th>
      <th class="icon">ID</th>
      <th width="300">'.$I18N->msg("module_description").'</th>
      <th>'.$I18N->msg("module_functions").'</th>
    </tr>
    '."\n";
  
  if (isset($message) and $message != '')
  {
    echo '<tr class="warning"><td align="center"><img src="pics/warning.gif" width="16" height="16"></td><td colspan="3">'.$message.'</td></tr>';
  }
  
  $sql = new sql;
  $sql->setQuery("SELECT * FROM rex_modultyp ORDER BY name");
  
  for($i=0; $i<$sql->getRows(); $i++){
  
    echo '  <tr>
        <td class="icon"><a href="index.php?page=module&amp;modul_id='.$sql->getValue("id").'&amp;function=edit"><img src="pics/modul.gif" width="16" height="16" border="0"></a></td>
        <td class="icon">'.$sql->getValue("id").'</td>
        <td><a href="index.php?page=module&amp;modul_id='.$sql->getValue("id").'&amp;function=edit">'.htmlspecialchars($sql->getValue("name")).'</a>'."\n";
    
    if ($REX_USER->isValueOf("rights","expertMode[]")) echo ' ['.$sql->getValue("id").']';
    
    echo '</td>
        <td><a href="index.php?page=module&amp;modul_id='.$sql->getValue("id").'&amp;function=delete" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg("delete_module").'</a></td>
      </tr>'."\n";
    $sql->counter++;
  }
  
  echo '</table>';
}

?>