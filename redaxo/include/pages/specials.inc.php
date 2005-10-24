<?php
/** 
 *  
 * @package redaxo3 
 * @version $Id$ 
 */ 

$ERRMSG = "";

if (!isset($subpage)) $subpage = '';

$subline = array( 
  array( '', $I18N->msg("main_preferences")),
  array( 'lang', $I18N->msg("languages")),
  array( 'type', $I18N->msg("types")),
);
title($I18N->msg("specials_title"),$subline);


if ($subpage == '')
{
  
  if (isset($func) and $func == "setup")
  {
    // REACTIVATE SETUP
    
    $h = @fopen($REX['INCLUDE_PATH']."/master.inc.php","r");
    $cont = fread($h,filesize($REX['INCLUDE_PATH']."/master.inc.php"));
    $cont = ereg_replace("(REX\['SETUP'\].?\=.?)[^;]*","\\1"."true",$cont);
    fclose($h);
    // echo nl2br(htmlspecialchars($cont));
    $h = @fopen($REX['INCLUDE_PATH']."/master.inc.php","w+");
    if (fwrite($h,$cont,strlen($cont)) > 0)
    {
      $MSG = $I18N->msg("setup_error1");
    }else
    {
      $MSG = $I18N->msg("setup_error2");
    }
    fclose($h);
  
  } elseif (isset($func) and $func == "generate")
  {
    
    // generate all articles,cats,templates,caches
    $MSG = rex_generateAll();
  
  } elseif (isset($func) and $func == "linkchecker")
  {
    $LART = array();
  
    for ($j=1; $j<11; $j++)
    {
      $LC = new sql;
      // $LC->debugsql = 1;
      $LC->setQuery("SELECT rex_article_slice.article_id,rex_article_slice.id FROM rex_article_slice
          LEFT JOIN rex_article ON rex_article_slice.link$j=rex_article.id
          WHERE
          rex_article_slice.link$j>0 and rex_article.id IS NULL");
      for ($i=0; $i<$LC->getRows(); $i++)
      {
        $LART[$LC->getValue("rex_article_slice.article_id")] = 1;
        $LSLI[$LC->getValue("rex_article_slice.article_id")] = $LC->getValue("rex_article_slice.id");
        $LC->next();
      }
    }
  
    if (count($LART) > 0) reset($LART);
  
    for ($i=0; $i<count($LART); $i++)
    {
      $MSG .= ' | <a href="index.php?page=content&amp;article_id='.key($LART).'&amp;mode=edit&amp;slice_id='.$LSLI[key($LART)].'&amp;function=edit#editslice">'.key($LART).'</a>';
      next($LART);
    }
  
    if (count($LART)==0) $MSG = $I18N->msg("links_ok");
    else $MSG = "<b>".$I18N->msg("links_not_ok")."</b> ". $MSG. " |";
  
  } elseif (isset($func) and $func == 'updateinfos')
  {
  
  
    $h = fopen("include/master.inc.php","r");
    $cont = fread($h,filesize("include/master.inc.php"));
  
    $cont = ereg_replace("(REX\['STARTARTIKEL_ID'\].?\=.?)[^;]*","\\1".strtolower($neu_startartikel),$cont);
    $cont = ereg_replace("(REX\['EMAIL'\].?\=.?)[^;]*","\\1\"".strtolower($neu_error_emailaddress)."\"",$cont);
    $cont = ereg_replace("(REX\['error_emailaddress'\].?\=.?)[^;]*","\\1\"".strtolower($neu_error_emailaddress)."\"",$cont);
    $cont = ereg_replace("(REX\['LANG'\].?\=.?)[^;]*","\\1\"".$neu_lang."\"",$cont);
    $REX['LANG'] = $neu_lang;
    $cont = ereg_replace("(REX\['SERVER'\].?\=.?)[^;]*","\\1\"".($neu_SERVER)."\"",$cont);
    $cont = ereg_replace("(REX\['SERVERNAME'\].?\=.?)[^;]*","\\1\"".($neu_SERVERNAME)."\"",$cont);
    
    // DB2 nur updaten, wenn das Formular unten aktiviert ist
    if ( isset( $neu_db2_host) && $neu_db2_host != '') {
      $cont = ereg_replace("(DB\['2'\]\['HOST'\].?\=.?)[^;]*","\\1\"".($neu_db2_host)."\"",$cont);
      $cont = ereg_replace("(DB\['2'\]\['LOGIN'\].?\=.?)[^;]*","\\1\"".($neu_db2_login)."\"",$cont);
      $cont = ereg_replace("(DB\['2'\]\['PSW'\].?\=.?)[^;]*","\\1\"".($neu_db2_psw)."\"",$cont);
      $cont = ereg_replace("(DB\['2'\]\['NAME'\].?\=.?)[^;]*","\\1\"".($neu_db2_name)."\"",$cont);
    }
  
    // Caching
    if($REX_USER->isValueOf("rights","caching[]")){
      $cont = ereg_replace("(REX\['CACHING'\].?\=.?)[^;]*","\\1".strtolower($neu_caching),$cont);
      $cont = ereg_replace("(REX\['CACHING_DEBUG'\].?\=.?)[^;]*","\\1".strtolower($neu_caching_debug),$cont);
    }

    $cont = ereg_replace("(REX\['MOD_REWRITE'\].?\=.?)[^;]*","\\1".strtolower($neu_modrewrite),$cont);
      
//      var_dump( $cont);
  
    fclose($h);
    $h = fopen("include/master.inc.php","w+");
    fwrite($h,$cont,strlen($cont));
    fclose($h);
  
    if ($neu_caching != "TRUE") $REX['CACHING'] = false;
    else $REX['CACHING'] = true;
    
    if ($neu_caching_debug != "TRUE") $REX['CACHING_DEBUG'] = false;
    else $REX['CACHING_DEBUG'] = true;
    
    if ($neu_modrewrite != "TRUE") $REX['MOD_REWRITE'] = false;
    else $REX['MOD_REWRITE'] = true;
  
  
    $REX['STARTARTIKEL_ID'] = $neu_startartikel;
    $REX['EMAIL'] = $neu_error_emailaddress;
    $REX['error_emailaddress'] = $neu_error_emailaddress;
    $REX['SERVER'] = $neu_SERVER;
    $REX['SERVERNAME'] = $neu_SERVERNAME;
  
    if (!isset ($neu_db2_host))  $neu_db2_host = '';
    if (!isset ($neu_db2_login)) $neu_db2_login = '';
    if (!isset ($neu_db2_psw))   $neu_db2_psw = '';
    if (!isset ($neu_db2_name))  $neu_db2_name = '';
    $DB['2']['HOST'] = $neu_db2_host;
    $DB['2']['LOGIN'] = $neu_db2_login;
    $DB['2']['PSW'] = $neu_db2_psw;
    $DB['2']['NAME'] = $neu_db2_name;
  
    $MSG = $I18N->msg("info_updated");
  
  }
  
  echo '<table class="rex" style="table-layout:auto;" cellpadding="5" cellspacing="1">
    <tr>
      <th colspan="2">'.$I18N->msg("special_features").'</th>
    </tr>';
  
  if (isset($MSG) and $MSG != "") echo '<tr class="warning"><td colspan="2"><b>'.$MSG.'</b></td></tr>';
  
  echo '<tr><td width="50%" valign="top"><br>';
  
  echo '<b><a href="index.php?page=specials&amp;func=generate">'.$I18N->msg("regenerate_article").'</a></b><br />'.$I18N->msg("regeneration_message").'<br /><br />';
  echo '<b><a href="index.php?page=specials&amp;func=linkchecker">'.$I18N->msg("link_checker").'</a></b><br />'.$I18N->msg("check_links_text").'<br /><br />';
  echo '<b><a href="index.php?page=specials&amp;func=setup">'.$I18N->msg("setup").'</a></b><br />'.$I18N->msg("setup_text").'<br />';
  
  echo '<br /></td><td valign="top"><br />';
  
  echo '<table width="100%" cellpadding="0" cellspacing="1">';
  echo '<form action="index.php" method="post">';
  echo '<input type="hidden" name="page" value="specials">';
  echo '<input type="hidden" name="func" value="updateinfos">';
  echo '<tr><td colspan="3"><b>'.$I18N->msg("general_info_header").'</b></td></tr>';
  echo '<tr><td width="170">$REX[\'version\']:</td><td width="10"><img src="pics/leer.gif" width="10" height="20"></td><td>"'.$REX['version'].'"</td></tr>';
  echo '<tr><td>$REX[\'SERVER\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td><input type="text" size="5" name="neu_SERVER" value="'.$REX['SERVER'].'" class="inp100"></td></tr>';
  echo '<tr><td>$REX[\'SERVERNAME\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td><input type="text" size="5" name="neu_SERVERNAME" value="'.$REX['SERVERNAME'].'" class="inp100"></td></tr>';
  
  echo '<tr><td colspan="3"><br /><b>'.$I18N->msg("db1_can_only_be_changed_by_setup").'</b></td></tr>';
  
  echo '<tr><td>$DB[\'1\'][\'HOST\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td>"'.$DB['1']['HOST'].'"</td></tr>';
  echo '<tr><td>$DB[\'1\'][\'LOGIN\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td>"'.$DB['1']['LOGIN'].'"</td></tr>';
  echo '<tr><td>$DB[\'1\'][\'PSW\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td>-</td></tr>';
  echo '<tr><td>$DB[\'1\'][\'NAME\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td>"'.$DB['1']['NAME'].'"</td></tr>';
  
  /*
  echo "<tr><td colspan=3><br><b>".$I18N->msg("db2_text")."</b></td></tr>";
  echo "<tr><td>\$DB['2']['HOST']:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_db2_host value=\"".$DB['2']['HOST']."\" class=inp100></td></tr>";
  echo "<tr><td>\$DB['2']['LOGIN']:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_db2_login value=\"".$DB['2']['LOGIN']."\" class=inp100></td></tr>";
  echo "<tr><td>\$DB['2']['PSW']:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_db2_psw value=\"".$DB['2']['PSW']."\" class=inp100></td></tr>";
  echo "<tr><td>\$DB['2']['NAME']:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_db2_name value=\"".$DB['2']['NAME']."\" class=inp100></td></tr>";
  */
  
  echo '<tr><td colspan="3"><br /><b>'.$I18N->msg("specials_others").'</b></td></tr>';
  echo '<tr><td>$REX[\'WWW_PATH\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td>"'.$REX['WWW_PATH'].'"</td></tr>';
  echo '<tr><td>$REX[\'INCLUDE_PATH\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td>"'.$REX['INCLUDE_PATH'].'"</td></tr>';
  echo '<tr><td>$REX[\'error_emailaddress\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td><input type="text" size="5" name="neu_error_emailaddress" value="'.$REX['error_emailaddress'].'" class="inp100"></td></tr>';
  echo '<tr><td>$REX[\'STARTARTIKEL_ID\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td><input type="text" size="5" name="neu_startartikel" value="'.$REX['STARTARTIKEL_ID'].'"></td></tr>';
  echo '<tr><td>$REX[\'LANG\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td><select name="neu_lang" size="1">';
  foreach ($REX['LOCALES'] as $l) {
    $selected = ($l == $REX['LANG'] ? "selected" : "");
    echo '<option value="'.$l.'" '.$selected.'>'.$l.'</option>';
  }
  echo '</select></td></tr>';

  if ($REX['MOD_REWRITE']) {
    $modcheck = 'selected'; 
    $modcheck_false = '';
  } else {
    $modcheck = '';
    $modcheck_false = 'selected';
  }
  echo '<tr><td>$REX[\'MOD_REWRITE\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td><select name="neu_modrewrite" size="1"><option '.$modcheck.'>TRUE</option><option '.$modcheck_false.'>FALSE</option></select></td></tr>';


  if ($REX_USER->isValueOf("rights","caching[]")){
    
    echo '<tr><td colspan="3"><br /><b>Caching</b></td></tr>';
    if ($REX['CACHING']) {
      $cachingcheck = 'selected';
      $cachingcheck_false = '';
    } else {
      $cachingcheck = '';
      $cachingcheck_false = 'selected';
    }
    echo '<tr><td>$REX[\'CACHING\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td><select name="neu_caching" size="1"><option '.$cachingcheck.'>TRUE</option><option '.$cachingcheck_false.'>FALSE</option></select></td></tr>';
    if ($REX['CACHING_DEBUG']) {
      $cachingdebugcheck = 'selected';
      $cachingdebugcheck_false = '';
    } else {
      $cachingdebugcheck = '';
      $cachingdebugcheck_false = 'selected';
    }
    echo '<tr><td>$REX[\'CACHING_DEBUG\']:</td><td><img src="pics/leer.gif" width="10" height="20"></td><td><select name="neu_caching_debug" size="1"><option '.$cachingdebugcheck.'>TRUE</option><option '.$cachingdebugcheck_false.'>FALSE</option></select></td></tr>';
  }
  echo '</td></tr>';
  
  
  
  echo '<tr><td></td><td><img src="pics/leer.gif" width="10" height="20"></td><td><br /><input type="submit" name="sendit" value="'.$I18N->msg("specials_update").'"></td></tr>';
  echo '</form>
        </table>';
  
  echo '<br /></td></tr></table>';


} elseif ($subpage == "lang")
{
  
  // ------------------------------ clang definieren (sprachen)
  
  echo '<a name="clang"></a>';
  
  // ----- delete clang
  if (isset($delclang) and $delclang != "")
  {
    if ($clang_id>0)
    {
      rex_deleteCLang($clang_id);
      $message = $I18N->msg("clang_deleted");
      unset($func);
      unset($clang_id);
    }
  }
  
  // ----- add clang
  if (isset($func) and $func == "addclangsave")
  {
    if ($clang_name != "")
    {
      if (!($clang_id>0 && $clang_id<100)) $clang_id = 0;
      if (!array_key_exists($clang_id,$REX['CLANG']))
      {
        $message = $I18N->msg("clang_created");
        rex_addCLang($clang_id,$clang_name);
        unset($clang_id);
        unset($func);
      } else
      {
        $message = $I18N->msg("id_exists");
        $func = "addclang";
      }
    } else {
      $message = $I18N->msg("enter_name");
      $func = "addclang";
    }
    
  } elseif (isset($func) and $func == "editclangsave")
  {
    rex_editCLang($clang_id,$clang_name);
    $message = $I18N->msg("clang_edited");
    unset($func);
    unset($clang_id);
  }
  
  // seltype
  $sel = new select;
  $sel->set_name("clang_id");
  $sel->set_size(1);
  $sel->add_option("1","1");
  $sel->add_option("2","2");
  $sel->add_option("3","3");
  $sel->add_option("4","4");
  $sel->add_option("5","5");
  $sel->add_option("6","6");
  $sel->add_option("7","7");
  $sel->add_option("8","8");
  $sel->add_option("8","8");
  $sel->add_option("9","9");
  $sel->add_option("10","10");
  $sel->add_option("11","11");
  $sel->add_option("12","12");
  $sel->add_option("13","13");
  $sel->set_style("width:40px");
  
  echo '<table class="rex" style="table-layout:auto;" cellpadding="5" cellspacing="1">
           <tr>
      <th class="icon"><a href="index.php?page=specials&amp;subpage=lang&amp;func=addclang#clang">+</a></th>
      <th style="width:40px; text-align:center;">ID</th>
      <th width="250">'.$I18N->msg("clang_desc").'</th>
      <th colspan="2">-</th></tr>';
  
  if (isset($message) and $message != "")
  {
    echo '<tr class="warning"><td class="icon"><img src="pics/warning.gif" width="16" height="16"></td><td colspan="4">'.$message.'</td></tr>';
    $message = "";
  }
  
  if (isset($func) and $func == "addclang")
  {
    if (!isset($clang_id)) $clang_id = '';
    if (!isset($clang_name)) $clang_name = '';
    $sel->set_selected($clang_id);
    echo '<tr><form action="index.php#clang" method="post">
          <input type="hidden" name="page" value="specials">
          <input type="hidden" name="subpage" value="lang">
          <input type="hidden" name="func" value="addclangsave">';
    echo '<td></td>';
    echo '<td>'.$sel->out().'</td>';
    echo '<td><input type="text" size="10" class="inp100" name="clang_name" value="'.htmlspecialchars($clang_name).'"></td>';
    echo '<td><input type="submit" value="submit"></td>';
    echo '</form></tr>';
  }
  
  reset($REX['CLANG']);
  for ($i=0; $i<count($REX['CLANG']); $i++)
  {
    if (isset($clang_id) and $clang_id == key($REX['CLANG']) and $clang_id != "" and $func == "editclang")
    {
      echo '<tr><form action="index.php#clang" method="post">
              <input type="hidden" name="page" value="specials">
              <input type="hidden" name="subpage" value="lang">
              <input type="hidden" name="clang_id" value="'.$clang_id.'">
              <input type="hidden" name="func" value="editclangsave">';
      echo '<td>edit</td>';
      echo '<td align="center" class="grey">'.key($REX['CLANG']).'</td>';
      echo '<td><input type="text" size="10" class="inp100" name="clang_name" value="'.htmlspecialchars(current($REX['CLANG'])).'"></td>';
      echo '<td><input type="submit" name="edit" value="'.$I18N->msg('edit').'">';
      if ($clang_id > 0) echo '<input type="submit" name="delclang" value="'.$I18N->msg("delete_button").'" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">';
      echo '</td>';
      echo '</form></tr>';
      
    } else
    {
      echo '<tr>
          <td>&#160;</td>
          <td align="center">'.key($REX['CLANG']).'</td>
          <td><a href="index.php?page=specials&amp;subpage=lang&amp;func=editclang&amp;clang_id='.key($REX['CLANG']).'#clang">'.htmlspecialchars(current($REX['CLANG'])).'</a></td>
          <td>&#160;</td></tr>';
    }
    next($REX['CLANG']);
  }
  echo "</table>";  
  
  
} else
{
  
  // ----- eigene typen definieren
    
  if (isset($function) and ($function == "Update" or $function == "Ändern"))
  {
    $update = new sql;
    $update->setTable("rex_article_type");
    $update->where("type_id='$type_id'");
    $update->setValue("name",$typname);
    $update->setValue("description",$description);
    $update->update();
    $type_id = 0;
    $function = "";
    $message = $I18N->msg("article_type_updated");
  
  } elseif (isset($function) and ($function == "Delete" or $function == "Löschen"))
  {
    if ($type_id!=1)
    {
      $delete = new sql;
      $result = $delete->get_array("SELECT name,id FROM rex_article WHERE type_id = $type_id");
      if (is_array($result)){
        $message = $I18N->msg("article_type_still_used")."<br>";
        foreach ($result as $var){
          $message .= '<br /><a href="index.php?page=content&amp;article_id='.$var['id'].'&amp;mode="meta" target="_blank">'.$var['name'].'</a>';
        }
        $message .= '<br /><br />';
      } else {
        $delete->query("DELETE FROM rex_article_type WHERE type_id = '$type_id' LIMIT 1");
        $delete->query("UPDATE rex_article SET type_id = '1' WHERE type_id = '$type_id'");
        $message = $I18N->msg("article_type_deleted");
      }
    } else
    {
      $message = $I18N->msg("article_type_could_not_be_deleted");
    }
  } elseif (isset($function) and $function == "add" && isset($save) and $save == 1)
  {
    $add = new sql;
    $add->setTable("rex_article_type");
    $add->setValue("name",$typname);
    $add->setValue("type_id",$type_id);
    $add->setValue("description",$description);
    $add->insert();
    $type_id = 0;
    $function = "";
    $message = $I18N->msg("article_type_added");
  }
  
  
  
  echo '  <table class="rex" style="table-layout:auto;" cellpadding="5" cellspacing="1">
    <tr>
      <th class="icon"><a href="index.php?page=specials&amp;subpage=type&amp;function=add">+</a></th>
      <th class="icon">'.$I18N->msg("article_type_list_id").'</th>
      <th width="250">'.$I18N->msg("article_type_list_name").'</th>
      <th colspan="2">'.$I18N->msg("article_type_list_description").'</th>
    </tr>
    ';
  
  if (isset($message) and $message != "")
  {
    echo '<tr class="warning"><td class="icon"><img src="pics/warning.gif" width="16" height="16"></td><td colspan="5">'.$message.'</td></tr>';
  }
  
  $sql = new sql;
  $sql->setQuery("SELECT * FROM rex_article_type ORDER BY type_id");
  
  if (isset($function) and $function == "add")
  {
    echo '  <tr>
        <form action="index.php" method="post">
        <input type="hidden" name="page" value="specials">
        <input type="hidden" name="subpage" value="type">
        <input type="hidden" name="save" value="1">
        <td>&nbsp;</td>
        <td valign="top"><input style="width:30px;" type="text" size="5" maxlength="2" name="type_id" value=""></td>
        <td valign="top"><input class="inp100" type="text" size="20" name="typname" value=""></td>
        <td><input style="width:100%" type="text" size="20" name="description" value=""></td>
        <td valign="top"><input type="submit" name="function" value="add"></td>
        </form>
      </tr>';
  }
  
  
  for ($i=0;$i<$sql->getRows();$i++)
  {
    if (isset($type_id) and $type_id == $sql->getValue("type_id"))
    {
      echo '  <tr>
          <form action="index.php" method="post">
          <input type="hidden" name="page" value="specials">
          <input type="hidden" name="subpage" value="type">
          <input type="hidden" name="type_id" value="'.$type_id.'">
          <td>&nbsp;</td>
          <td valign="middle" align="center">'.htmlspecialchars($sql->getValue("type_id")).'</td>
          <td valign="top"><input class="inp100" type="text" size="20" name="typname" value="'.htmlspecialchars($sql->getValue("name")).'"></td>
          <td><input class="inp100" type="text" size="20" name="description" value="'.htmlspecialchars($sql->getValue("description")).'"></td>
          <td valign="top"><input type="submit" name="function" value="'.$I18N->msg("update_button").'">
            <input type="submit" name="function" value="'.$I18N->msg("delete_button").'" onclick="return confirm(\''.$I18N->msg('delete').' ?\')"></td>
          </form>
        </tr>';
    } else
    {
      echo '  <tr>
          <td>&nbsp;</td>
          <td align="center">'.htmlspecialchars($sql->getValue("type_id")).'</td>
          <td><a href="index.php?page=specials&amp;subpage=type&amp;type_id='.$sql->getValue("type_id").'">'.htmlspecialchars($sql->getValue("name")).'&nbsp;</a></td>
          <td colspan="2">'.nl2br($sql->getValue("description")).'&nbsp;</td>
        </tr>';
    }
    $sql->counter++;
  }
  
  echo '</table>';
}


?>