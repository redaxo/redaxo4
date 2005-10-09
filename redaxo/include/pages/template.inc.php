<?php

title($I18N->msg("title_templates"),"");

$OUT = TRUE;

if (isset($function) and $function == "delete")
{
  $del = new sql;
  $del->setQuery("SELECT rex_article.id,rex_template.name FROM rex_article 
      LEFT JOIN rex_template ON rex_article.template_id=rex_template.id 
      WHERE rex_article.template_id='$template_id' LIMIT 0,10");  
  
  if ($template_id == 1)
  {
    $message = $I18N->msg("cant_delete_default_template");
  } else if ($del->getRows() > 0)
  {
    $message = $I18N->msg("cant_delete_template_because_its_in_use",htmlspecialchars($del->getValue("rex_template.name")));
  } else
  {
    $del->query("DELETE FROM rex_template WHERE id = '$template_id' LIMIT 1"); // max. ein Datensatz darf loeschbar sein
    $message = $I18N->msg("template_deleted");

    rex_deleteDir($REX['INCLUDE_PATH']."/generated/templates/".$template_id.".template",0);
  }
}

if (isset($function) and ($function == "add" or $function == "edit")){

  if (isset($save) and $save == "ja")
  {
    
    if ($function == "add")
    {
      $ITPL = new sql;
      $ITPL->setTable("rex_template");
      $ITPL->setValue("name",$templatename);
      $ITPL->setValue("active",$active);
      $ITPL->setValue("content",$content);
      $ITPL->insert();
      $template_id = $ITPL->last_insert_id;
      $message = $I18N->msg("template_added");
    }else{
      if (!isset($active)) $active = 0;
      $TMPL = new sql;
      $TMPL->setTable("rex_template");
      $TMPL->where("id='$template_id'");
      $TMPL->setValue("name",$templatename);
      $TMPL->setValue("content",$content);
      $TMPL->setValue("active",$active);
      $TMPL->update();
      $message = $I18N->msg("template_added");
    } 

    $gt = new sql;
    $gt->setQuery("SELECT * FROM rex_template WHERE id = '$template_id'");

    $fp = fopen ($REX['INCLUDE_PATH']."/generated/templates/".$template_id.".template", "w");
    fputs($fp,$gt->getValue("content"));
    fclose($fp);

    if (isset($goon) and $goon != "")
    {
      $function = "edit";
      $save = "nein";
    }else
    {
      $function = "";
    }
  } else {
  
    echo '<a name="edit"><table class="rex" style="table-layout:auto"; cellpadding="5" cellspacing="1">';
  
    if ($function == "edit"){
      echo '  <tr><th colspan="3"><b>'.$I18N->msg("edit_template").' [ID='.$template_id.']</b></th></tr>';

      $hole = new sql;
      $hole->setQuery("SELECT * FROM rex_template WHERE id = '$template_id'");
      $templatename = $hole->getValue("name");
      $content  = $hole->getValue("content");
      $active = $hole->getValue("active");
      
    }else{
      echo '  <tr><th colspan="3">'.$I18N->msg("create_template").'</th></tr>';
    }

    echo '  <form action="index.php" method="post">
      <input type="hidden" name="page" value="template">
      <input type="hidden" name="function" value="'.$function.'">
      <input type="hidden" name="save" value="ja">
      <input type="hidden" name="template_id" value="'.$template_id.'">
      <tr>
        <td width="100">'.$I18N->msg("template_name").'</td>
        <td colspan="2"><input type="text" size="10" name="templatename" value="'.htmlspecialchars($templatename).'" style="width:100%;"></td>
      </tr>';
    
    echo '
      <tr>
        <td width="100" align="right"><input type="checkbox" id="active" name="active" value="1"';
    if ($active == 1) echo ' checked';
    echo '></td>
        <td colspan="2"><label for="active">'.$I18N->msg("checkbox_template_active").'</label></td>
      </tr>';
    
    echo '
      <tr>
        <td>&nbsp;</td>
        <td width="200"><input type="submit" value="'.$I18N->msg("save_template_and_quit").'"></td>
        <td><input type="submit" name="goon" value="'.$I18N->msg("save_template_and_continue").'"></td>
      </tr>';
    
    echo '
      <tr>
        <td valign="top">'.$I18N->msg("header_template").'</td>
        <td colspan="2">
                  <textarea name="content" id="content" cols="40" rows="5" style="width: 100%;height: 400px;">'.htmlspecialchars($content).'</textarea>
                </td>
      </tr>';
    
    echo '  </form>';
    echo '</table>';

    $OUT = false;

  }
}

if ($OUT)
{
  // ausgabe templateliste !
  echo '<table class="rex" style="table-layout:auto"; cellpadding="5" cellspacing="1">
    <tr>
      <th class="icon"><a href="index.php?page=template&amp;function=add"><img src="pics/template_plus.gif" width="16" height="16" border="0" alt="'.$I18N->msg("create_template").'" title="'.$I18N->msg("create_template").'"></a></th>
      <th class="icon">ID</th>
      <th width="300">'.$I18N->msg("header_template_description").'</th>
      <th width="50">'.$I18N->msg("header_template_active").'</th>
      <th >'.$I18N->msg("header_template_functions").'</th>
    </tr>
    ';
  
  if (isset($message) and $message != "")
  {
    echo '<tr class="warning"><td align="center"><img src="pics/warning.gif" width="16" height="16"></td><td colspan="4">'.$message.'</td></tr>';
  }
  
  $sql = new sql;
  $sql->setQuery("SELECT * FROM rex_template ORDER BY name");
  
  for ($i=0; $i<$sql->getRows(); $i++)
  {
    echo '  <tr>
        <td class="icon"><a href="index.php?page=template&amp;template_id='.$sql->getValue("id").'&amp;function=edit"><img src="pics/template.gif" width="16" height="16" border="0"></a></td>
        <td class="icon">'.$sql->getValue("id").'</td>
        <td><a href="index.php?page=template&amp;template_id='.$sql->getValue("id").'&amp;function=edit">'.htmlspecialchars($sql->getValue("name")).'</a>';
    
    if ($REX_USER->isValueOf("rights","expertMode[]")) echo " [".$sql->getValue("id")."]";
      
    echo '</td>
        <td>';
      
    if ($sql->getValue("active") == 1) echo $I18N->msg("yes");
    else echo $I18N->msg("no");

    echo '</td>
        <td><a href="index.php?page=template&amp;template_id='.$sql->getValue("id").'&amp;function=delete" onclick="return confirm("'.$I18N->msg('delete').' ?")">'.$I18N->msg("delete_template").'</a></td>
      </tr>';
    $sql->counter++;
  }
  
  echo '</table>';
}

?>