<?php
/** 
 *  
 * @package redaxo3
 * @version $Id$
 */

$OUT = TRUE;

// ---------------------------- ACTIONSFUNKTIONEN FÜR MODULE
if (!empty($add_action))
{
  $aa = new rex_sql;
  $aa->query("INSERT INTO ".$REX['TABLE_PREFIX']."module_action SET module_id='$modul_id', action_id='$action_id'");
  $message = $I18N->msg("action_taken");
  $goon = 'ja';
}
elseif (isset($function_action) and $function_action == 'delete')
{
  $aa = new rex_sql;
  $aa->query("DELETE FROM ".$REX['TABLE_PREFIX']."module_action WHERE module_id='$modul_id' and id='$iaction_id' LIMIT 1");
  $message = $I18N->msg("action_deleted_from_modul");
} 



// ---------------------------- FUNKTIONEN FÜR MODULE

if (isset($function) and $function == 'delete')
{
  $del = new rex_sql;
  $del->setQuery("SELECT DISTINCT ".$REX['TABLE_PREFIX']."article_slice.article_id, ".$REX['TABLE_PREFIX']."modultyp.name FROM ".$REX['TABLE_PREFIX']."article_slice 
      LEFT JOIN ".$REX['TABLE_PREFIX']."modultyp ON ".$REX['TABLE_PREFIX']."article_slice.modultyp_id=".$REX['TABLE_PREFIX']."modultyp.id 
      WHERE ".$REX['TABLE_PREFIX']."article_slice.modultyp_id='$modul_id'");
  
  if ($del->getRows() >0)
  {
    $module = '';
    $modulname = htmlspecialchars($del->getValue($REX['TABLE_PREFIX']."modultyp.name"));
    for ($i=0; $i<$del->getRows(); $i++)
    {
     $module .= '<li><a href="index.php?page=content&amp;article_id='.$del->getValue($REX['TABLE_PREFIX']."article_slice.article_id").'">'.$del->getValue($REX['TABLE_PREFIX']."article_slice.article_id").'</a></li>';
     $del->next();
    }
    
    if($module != '')
    {
      $module = '<ul>'. $module .'</ul>';
    }
    
    $message = $I18N->msg("module_cannot_be_deleted",$modulname).'<br /> '.$module;
  } else
  {
    $del->query("DELETE FROM ".$REX['TABLE_PREFIX']."modultyp WHERE id='$modul_id'");
    $del->query("DELETE FROM ".$REX['TABLE_PREFIX']."module_action WHERE module_id='$modul_id'");
  
    $message = $I18N->msg("module_deleted");
  }
}

if (isset($function) and ($function == 'add' or $function == 'edit'))
{

  if (isset($save) and $save == 'ja')
  {
    $modultyp = new rex_sql;

    if ($function == 'add')
    {
      // $modultyp->query("INSERT INTO ".$REX['TABLE_PREFIX']."modultyp (category_id, name, eingabe, ausgabe) VALUES ('$category_id', '$mname', '$eingabe', '$ausgabe')");
      
      $IMOD = new rex_sql;
      $IMOD->setTable($REX['TABLE_PREFIX']."modultyp");
      $IMOD->setValue("name",$mname);
      $IMOD->setValue("eingabe",$eingabe);
      $IMOD->setValue("ausgabe",$ausgabe);
      $IMOD->setValue("createdate",time());
      $IMOD->setValue("createuser",$REX_USER->getValue("login"));
      $IMOD->insert();
      $message = $I18N->msg("module_added");


    } else {
      $modultyp->setQuery("select * from ".$REX['TABLE_PREFIX']."modultyp where id='$modul_id'");
      if ($modultyp->getRows()==1)
      {
        $old_ausgabe = $modultyp->getValue("ausgabe");
    
        // $modultyp->query("UPDATE ".$REX['TABLE_PREFIX']."modultyp SET name='$mname', eingabe='$eingabe', ausgabe='$ausgabe' WHERE id='$modul_id'");
        
        $UMOD = new rex_sql;
        $UMOD->setTable($REX['TABLE_PREFIX']."modultyp");
        $UMOD->where("id='$modul_id'");
        $UMOD->setValue("name",$mname);
        $UMOD->setValue("eingabe",$eingabe);
        $UMOD->setValue("ausgabe",$ausgabe);
        $UMOD->setValue("updatedate",time());
        $UMOD->setValue("updateuser",$REX_USER->getValue("login"));
        $UMOD->update();

        $message = $I18N->msg("module_updated").' | '.$I18N->msg("articel_updated");

        $new_ausgabe = stripslashes($ausgabe);

		if ($old_ausgabe != $new_ausgabe)
		{
          // article updaten - nur wenn ausgabe sich veraendert hat
          $gc = new rex_sql;
          $gc->setQuery("SELECT DISTINCT(".$REX['TABLE_PREFIX']."article.id) FROM ".$REX['TABLE_PREFIX']."article 
              LEFT JOIN ".$REX['TABLE_PREFIX']."article_slice ON ".$REX['TABLE_PREFIX']."article.id=".$REX['TABLE_PREFIX']."article_slice.article_id 
              WHERE ".$REX['TABLE_PREFIX']."article_slice.modultyp_id='$modul_id'");
          for ($i=0; $i<$gc->getRows(); $i++)
          {
            rex_generateArticle($gc->getValue($REX['TABLE_PREFIX']."article.id"));
            $gc->next();
          }
        }
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
    if (!isset($modul_id)) $modul_id = '';
    if (!isset($mname)) $mname = '';
    if (!isset($eingabe)) $eingabe = '';
    if (!isset($ausgabe)) $ausgabe = '';
    
    if ($function == 'edit')
    {
      $legend = $I18N->msg("module_edit").' [ID='.$modul_id.']';

      $hole = new rex_sql;
      $hole->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."modultyp WHERE id='$modul_id'");
      $category_id  = $hole->getValue("category_id");
      $mname    = $hole->getValue("name");
      $include  = $hole->getValue("include");
      $ausgabe  = $hole->getValue("ausgabe");
      $eingabe  = $hole->getValue("eingabe");
            
    }
    else
    {
      $legend = $I18N->msg("create_module");
    }
    
    $btn_update = '';
    if ($function != 'add') $btn_update = '<input type="submit" class="rex-sbmt" name="goon" value="'.$I18N->msg("save_module_and_continue").'" />';
    
    if (isset($message) and $message != '')
    {
      echo '<p class="rex-warning">'.$message.'</p';
    }

    echo '  
		<div class="rex-mdl-editmode">
      <form action="index.php" method="post">
        <fieldset>
          <legend class="rex-lgnd" id="module">'. $legend .'</legend>
          <input type="hidden" name="page" value="module" />
          <input type="hidden" name="function" value="'.$function.'" />
          <input type="hidden" name="save" value="ja" />
          <input type="hidden" name="category_id" value="0" />
          <input type="hidden" name="modul_id" value="'.$modul_id.'" />
          <p>
            <label for="mname">'.$I18N->msg("module_name").'</label>
            <input type="text" size="10" id="mname" name="mname" value="'.htmlspecialchars($mname).'" />
          </p>
          <p>
            <label for="eingabe">'.$I18N->msg("input").'</label>
            <textarea class="rex-txtr-cd" cols="50" rows="6" name="eingabe" id="eingabe">'.htmlspecialchars($eingabe).'</textarea>
          </p>
          <p>
            <label for="ausgabe">'.$I18N->msg("output").'</label>
            <textarea class="rex-txtr-cd" cols="50" rows="6" name="ausgabe" id="ausgabe">'.htmlspecialchars($ausgabe).'</textarea>
          </p>
          <p>
            <input class="rex-sbmt" type="submit" value="'.$I18N->msg("save_module_and_quit").'" />
            '. $btn_update .'
          </p>
        </fieldset>
    ';

    if ($function == 'edit')
    {
      // Im Edit Mode Aktionen bearbeiten
      
      $gaa = new rex_sql;
      $gaa->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."action ORDER BY name");

      if ($gaa->getRows()>0)
      {     
        $gma = new rex_sql;
        $gma->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."module_action, ".$REX['TABLE_PREFIX']."action WHERE ".$REX['TABLE_PREFIX']."module_action.action_id=".$REX['TABLE_PREFIX']."action.id and ".$REX['TABLE_PREFIX']."module_action.module_id='$modul_id'");
        $actions = '';
        
        for ($i=0; $i<$gma->getRows(); $i++)
        {
          $iaction_id = $gma->getValue($REX['TABLE_PREFIX']."module_action.id");
          $action_id = $gma->getValue($REX['TABLE_PREFIX']."module_action.action_id");
          
          $actions .= '
          <li>
            <a href="index.php?page=module&amp;subpage=actions&amp;action_id='.$action_id.'&amp;function=edit">'.$gma->getValue("name").'</a>
            [ '. $PREPOST[$gma->getValue("prepost")];
          
          if ($gma->getValue("sadd")==1) $actions .= "|".$ASTATUS[0];
          if ($gma->getValue("sedit")==1) $actions .= "|".$ASTATUS[1];
          if ($gma->getValue("sdelete")==1) $actions .= "|".$ASTATUS[2];
          
          $actions .= '
            ] 
            <a href="index.php?page=module&amp;modul_id='.$modul_id.'&amp;function_action=delete&amp;function=edit&amp;iaction_id='.$iaction_id.'" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg("action_delete").'</a>
          </li>';
          
          $gma->next();
        }
        
        if($actions !='')
        {
          $actions = '
            <ul>
              '. $actions .'
            </ul>
          ';
        }
        
        $gaa_sel = new rex_select();
        $gaa_sel->set_name("action_id");
        $gaa_sel->set_id("action_id");
        $gaa_sel->set_size(1);
        $gaa_sel->set_style('class="inp100"');
        
        for ($i=0; $i<$gaa->getRows(); $i++)
        {
          $status = "";
          if ($gaa->getValue("sadd")==1) $status .= "|".$ASTATUS[0];
          if ($gaa->getValue("sedit")==1) $status .= "|".$ASTATUS[1];
          if ($gaa->getValue("sdelete")==1) $status .= "|".$ASTATUS[2];
          
          $gaa_sel->add_option($gaa->getValue("name")." [".$PREPOST[$gaa->getValue("prepost")]."$status]",$gaa->getValue("id"));
          $gaa->next();
        }

        echo '
        <fieldset>
          <legend class="rex-lgnd" id="action">'.$I18N->msg("action_add").'</legend>
          '. $actions .'
          <p>
            <label for="action_id">'.$I18N->msg("action").'</label>
            '.$gaa_sel->out().'
          </p>
          <p>
            <input class="rex-sbmt" type="submit" value="'.$I18N->msg("action_add").'" name="add_action" />
          </p>
        </fieldset>';
      }
    }
    
    echo '
    </form></div>
    ';
    
    $OUT = false;
  }
}

if ($OUT)
{
  if (isset($message) and $message != '')
  {
    echo '<p class="rex-warning">'.$message.'</p>';
  }
  
  // ausgabe modulliste !
  echo '
  <table class="rex-table" summary="'.$I18N->msg("module_summary").'">
  	<caption class="rex-hide">'.$I18N->msg("module_caption").'</caption>
    <colgroup>
      <col width="5%" />
      <col width="5%" />
      <col width="*" />
      <col width="17%" />
    </colgroup>
    <thead>
      <tr>
        <th><a href="index.php?page=module&amp;function=add"><img src="pics/modul_plus.gif" width="16" height="16" alt="'.$I18N->msg("create_module").'" title="'.$I18N->msg("create_module").'" /></a></th>
        <th>ID</th>
        <th>'.$I18N->msg("module_description").'</th>
        <th>'.$I18N->msg("module_functions").'</th>
      </tr>
    </thead>
    <tbody>
  ';
  
  
  $sql = new rex_sql;
  $sql->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."modultyp ORDER BY name");
  
  for($i=0; $i<$sql->getRows(); $i++){
  
    echo '
      <tr>
        <td><a href="index.php?page=module&amp;modul_id='.$sql->getValue("id").'&amp;function=edit"><img src="pics/modul.gif" width="16" height="16" alt="'. $sql->getValue("name") .'" title="'. $sql->getValue("name") .'"/></a></td>
        <td>'.$sql->getValue("id").'</td>
        <td><a href="index.php?page=module&amp;modul_id='.$sql->getValue("id").'&amp;function=edit">'.htmlspecialchars($sql->getValue("name")).'</a></td>
        <td><a href="index.php?page=module&amp;modul_id='.$sql->getValue("id").'&amp;function=delete" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg("delete_module").'</a></td>
      </tr>'."\n";
    $sql->counter++;
  }
  
  echo '
    </tbody>
  </table>';
}

?>