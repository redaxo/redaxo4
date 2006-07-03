<?php
/** 
 *  
 * @package redaxo3 
 * @version $Id$ 
 */ 

// -------------- Defaults

if(!isset($subpage)) $subpage = '';
if(!isset($func)) $func = '';
if(!isset($message)) $message = '';

// -------------- Header

$subline = array( 
  array( '', $I18N->msg("main_preferences")),
  array( 'lang', $I18N->msg("languages")),
  array( 'type', $I18N->msg("types")),
);

rex_title($I18N->msg("specials_title"),$subline);


// -------------- SubPages

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
      $message = $I18N->msg("setup_error1");
    }else
    {
      $message = $I18N->msg("setup_error2");
    }
    fclose($h);
  
  } elseif (isset($func) and $func == "generate")
  {
    
    // generate all articles,cats,templates,caches
    $message = rex_generateAll();
  
  } elseif (isset($func) and $func == "linkchecker")
  {
    $LART = array();
  
    for ($j=1; $j<11; $j++)
    {
      $LC = new sql;
      // $LC->debugsql = 1;
      $LC->setQuery("SELECT ".$REX['TABLE_PREFIX']."article_slice.article_id,".$REX['TABLE_PREFIX']."article_slice.id FROM ".$REX['TABLE_PREFIX']."article_slice
          LEFT JOIN ".$REX['TABLE_PREFIX']."article ON ".$REX['TABLE_PREFIX']."article_slice.link$j=".$REX['TABLE_PREFIX']."article.id
          WHERE
          ".$REX['TABLE_PREFIX']."article_slice.link$j>0 and ".$REX['TABLE_PREFIX']."article.id IS NULL");
      for ($i=0; $i<$LC->getRows(); $i++)
      {
        $LART[$LC->getValue($REX['TABLE_PREFIX']."article_slice.article_id")] = 1;
        $LSLI[$LC->getValue($REX['TABLE_PREFIX']."article_slice.article_id")] = $LC->getValue($REX['TABLE_PREFIX']."article_slice.id");
        $LC->next();
      }
    }
  
    if (count($LART) > 0) reset($LART);
  
    for ($i=0; $i<count($LART); $i++)
    {
      $message .= ' | <a href="index.php?page=content&amp;article_id='.key($LART).'&amp;mode=edit&amp;slice_id='.$LSLI[key($LART)].'&amp;function=edit#editslice">'.key($LART).'</a>';
      next($LART);
    }
  
    if (count($LART)==0) $message = $I18N->msg("links_ok");
    else $message = $I18N->msg("links_not_ok")."<br /> ". $message. " |";
  
  } elseif (isset($func) and $func == 'updateinfos')
  {
  
    $REX['LANG'] = $neu_lang;
  
    $h = fopen("include/master.inc.php","r");
    $cont = fread($h,filesize("include/master.inc.php"));
  
    $cont = ereg_replace("(REX\['START_ARTICLE_ID'\].?\=.?)[^;]*","\\1".strtolower($neu_startartikel),$cont);
    $cont = ereg_replace("(REX\['NOTFOUND_ARTICLE_ID'\].?\=.?)[^;]*","\\1".strtolower($neu_notfoundartikel),$cont);
    $cont = ereg_replace("(REX\['ERROR_EMAIL'\].?\=.?)[^;]*","\\1\"".strtolower($neu_error_emailaddress)."\"",$cont);
    $cont = ereg_replace("(REX\['LANG'\].?\=.?)[^;]*","\\1\"".$neu_lang."\"",$cont);
    $cont = ereg_replace("(REX\['SERVER'\].?\=.?)[^;]*","\\1\"".($neu_SERVER)."\"",$cont);
    $cont = ereg_replace("(REX\['SERVERNAME'\].?\=.?)[^;]*","\\1\"".($neu_SERVERNAME)."\"",$cont);
    
    // DB2 nur updaten, wenn das Formular unten aktiviert ist
    if ( isset( $neu_db2_host) && $neu_db2_host != '') {
      $cont = ereg_replace("(REX\['DB'\]\['2'\]\['HOST'\].?\=.?)[^;]*","\\1\"".($neu_db2_host)."\"",$cont);
      $cont = ereg_replace("(REX\['DB'\]\['2'\]\['LOGIN'\].?\=.?)[^;]*","\\1\"".($neu_db2_login)."\"",$cont);
      $cont = ereg_replace("(REX\['DB'\]\['2'\]\['PSW'\].?\=.?)[^;]*","\\1\"".($neu_db2_psw)."\"",$cont);
      $cont = ereg_replace("(REX\['DB'\]\['2'\]\['NAME'\].?\=.?)[^;]*","\\1\"".($neu_db2_name)."\"",$cont);
    }
  
    // Mod-Rewrite
    $cont = ereg_replace("(REX\['MOD_REWRITE'\].?\=.?)[^;]*","\\1".strtolower($neu_modrewrite),$cont);
      
//      var_dump( $cont);
  
    fclose($h);
    $h = fopen("include/master.inc.php","w+");
    fwrite($h,$cont,strlen($cont));
    fclose($h);
  
    if ($neu_modrewrite != "TRUE") $REX['MOD_REWRITE'] = false;
    else $REX['MOD_REWRITE'] = true;
  
    $REX['START_ARTICLE_ID'] = $neu_startartikel;
    $REX['NOTFOUND_ARTICLE_ID'] = $neu_notfoundartikel;
    $REX['EMAIL'] = $neu_error_emailaddress;
    $REX['ERROR_EMAIL'] = $neu_error_emailaddress;
    $REX['SERVER'] = $neu_SERVER;
    $REX['SERVERNAME'] = $neu_SERVERNAME;
  
    if (!isset ($neu_db2_host))  $neu_db2_host = '';
    if (!isset ($neu_db2_login)) $neu_db2_login = '';
    if (!isset ($neu_db2_psw))   $neu_db2_psw = '';
    if (!isset ($neu_db2_name))  $neu_db2_name = '';
    $REX['DB']['2']['HOST'] = $neu_db2_host;
    $REX['DB']['2']['LOGIN'] = $neu_db2_login;
    $REX['DB']['2']['PSW'] = $neu_db2_psw;
    $REX['DB']['2']['NAME'] = $neu_db2_name;
  
    $message = $I18N->msg("info_updated");
  
  }
  
  if (isset($message) and $message != "") echo '<p class="rex-warning">'.$message.'</p>';
 
/*
<div class="rex-cnt-col2">
  <p class="rex-hdl">Hilfe für import_export</p>
  <div class="rex-cnt">Seite 1</div>
</div>

<div class="rex-cnt-col2">
  <p class="rex-hdl">Hilfe für import_export</p>
  <div class="rex-cnt">Seite 2</div>
</div>
*/
  $sel_lang = new select();
  $sel_lang->set_name('neu_lang');
  $sel_lang->set_id('rex_lang');
  $sel_lang->set_size(1);
  $sel_lang->set_selected($REX['LANG']);
  
  foreach ($REX['LOCALES'] as $l) {
    $sel_lang->add_option($l, $l);
  }
  
  $sel_mod_rewrite = new select();
  $sel_mod_rewrite->set_name('neu_modrewrite');
  $sel_mod_rewrite->set_id('rex_mod_rewrite');
  $sel_mod_rewrite->set_size(1);
  $sel_mod_rewrite->set_selected($REX['MOD_REWRITE']);
  
  $sel_mod_rewrite->add_option('TRUE', '1');
  $sel_mod_rewrite->add_option('FALSE', '0');

  echo '
  <form action="index.php" method="post">
    <input type="hidden" name="page" value="specials" />
    <input type="hidden" name="func" value="updateinfos" />

    <div class="rex-cnt-cols">
    <div class="rex-cnt-col2">
      <p class="rex-hdl">'.$I18N->msg("specials_features").'</p>
      <div class="rex-cnt">
        <p><a href="index.php?page=specials&amp;func=generate">'.$I18N->msg("regenerate_article").'</a></p>
        <p>'.$I18N->msg("regeneration_message").'</p>
  
        <p><a href="index.php?page=specials&amp;func=linkchecker">'.$I18N->msg("link_checker").'</a></p>
        <p>'.$I18N->msg("check_links_text").'</p>
        
        <p><a href="index.php?page=specials&amp;func=setup">'.$I18N->msg("setup").'</a></p>
        <p>'.$I18N->msg("setup_text").'</p>
      </div>
    </div>
    
    <div class="rex-cnt-col2">
      <p class="rex-hdl">'.$I18N->msg("specials_settings").'</p>
      <div class="rex-cnt">
        <fieldset>  
          <legend>'.$I18N->msg("general_info_header").'</legend>
          <p>
            <label for="rex_version">$REX[\'VERSION\']</label>
            <span id="rex_version">&quot;'.$REX['VERSION'].'&quot;</span>
          </p>
          <p>
            <label for="rex_subversion">$REX[\'SUBVERSION\']</label>
            <span id="rex_subversion">&quot;'.$REX['SUBVERSION'].'&quot;</span>
          </p>
          <p>
            <label for="rex_server">$REX[\'SERVER\']</label>
            <input type="text" id="rex_server" name="neu_SERVER" value="'.$REX['SERVER'].'" />
          </p>
          <p>
            <label for="rex_servername">$REX[\'SERVERNAME\']</label>
            <input type="text" id="rex_servername" name="neu_SERVERNAME" value="'.$REX['SERVERNAME'].'" />
          </p>
        </fieldset>
        <fieldset>
          <legend>'.$I18N->msg("db1_can_only_be_changed_by_setup").'</legend>
          <p>
            <label for="rex_db_host">$REX[\'DB\'][\'1\'][\'HOST\']</label>
            <span id="rex_db_host">&quot;'.$REX['DB']['1']['HOST'].'&quot;</span>
          </p>
          <p>
            <label for="rex_db_login">$REX[\'DB\'][\'1\'][\'LOGIN\']</label>
            <span id="rex_db_login">&quot;'.$REX['DB']['1']['LOGIN'].'&quot;</span>
          </p>
          <p>
            <label for="rex_db_psw">$REX[\'DB\'][\'1\'][\'PSW\']</label>
            <span id="rex_db_psw">&quot;****&quot;</span>
          </p>
          <p>
            <label for="rex_db_name">$REX[\'DB\'][\'1\'][\'NAME\']</label>
            <span id="rex_db_name">&quot;'.$REX['DB']['1']['NAME'].'&quot;</span>
          </p>
        </fieldset>
        <fieldset>
          <legend>'.$I18N->msg("specials_others").'</legend>
          <p>
            <label for="rex_include_path">$REX[\'INCLUDE_PATH\']</label>
            <span id="rex_include_path">&quot;'.$REX['INCLUDE_PATH'].'&quot;</span>
          </p>
          <p>
            <label for="rex_error_email">$REX[\'ERROR_EMAIL\']</label>
            <input type="text" id="rex_error_email" name="neu_error_emailaddress" value="'.$REX['ERROR_EMAIL'].'" />
          </p>
          <p>
            <label for="rex_startarticle_id">$REX[\'START_ARTICLE_ID\']</label>
            <input type="text" id="rex_startarticle_id" name="neu_startartikel" value="'.$REX['START_ARTICLE_ID'].'" />
          </p>
          <p>
            <label for="rex_notfound_article_id">$REX[\'NOTFOUND_ARTICLE_ID\']</label>
            <input type="text" id="rex_notfound_article_id" name="neu_notfoundartikel" value="'.$REX['NOTFOUND_ARTICLE_ID'].'" />
          </p>
          <p>
            <label for="rex_lang">$REX[\'LANG\']</label>
            '. $sel_lang->out() .'
          </p>
          <p>
            <label for="rex_mod_rewrite">$REX[\'MOD_REWRITE\']</label>
            '. $sel_mod_rewrite->out() .'
          </p>
          <p>
            <input type="submit" class="rex-fsubmit" name="sendit" value="'.$I18N->msg("specials_update").'" />
          </p>
        </fieldset>
      </div>
    </div>
	</div>
  </form>
  ';
  
}
elseif ($subpage == "lang")
{
  
  // ------------------------------ clang definieren (sprachen)
  
  echo '<a name="clang"></a>';
  
  // ----- delete clang
  if (!empty($del_clang_save))
  {
    if ($clang_id>0)
    {
      rex_deleteCLang($clang_id);
      $message = $I18N->msg('clang_deleted');
      unset($func);
      unset($clang_id);
    }
  }
  
  // ----- add clang
  if (!empty($add_clang_save))
  {
    if ($clang_name != '' && $clang_id>0)
    {
      if (!array_key_exists($clang_id,$REX['CLANG']))
      {
        $message = $I18N->msg('clang_created');
        rex_addCLang($clang_id,$clang_name);
        unset($clang_id);
        unset($func);
      } else
      {
        $message = $I18N->msg('id_exists');
        $func = 'addclang';
      }
    } else {
      $message = $I18N->msg('enter_name');
      $func = 'addclang';
    }
    
  } elseif (!empty($edit_clang_save))
  {
    if ($clang_id>0)
    {
      rex_editCLang($clang_id,$clang_name);
      $message = $I18N->msg('clang_edited');
      unset($func);
      unset($clang_id);
    }
  }
  
  // seltype
  $sel = new select;
  $sel->set_name('clang_id');
  $sel->set_id('clang_id');
  $sel->set_size(1);
  foreach ( array_diff( range( 0,14), array_keys( $REX['CLANG'])) as $clang) 
  {
    $sel->add_option($clang,$clang);
  }
  
  if (isset($message) and $message != '')
  {
    echo '<p class="rex-warning">'.$message.'</td></tr>';
    $message = "";
  }
  
  if (!isset($clang_id)) $clang_id = '';
  if (!isset($clang_name)) $clang_name = '';
  if (!isset($func)) $func = '';
  
  if($func == 'addclang' || $func == 'editclang')
  {
    $legend = $func == 'add_clang' ? $I18N->msg('clang_add') : $I18N->msg('clang_edit');  
    echo '
    <form action="index.php#clang" method="post">
      <fieldset>
        <legend><span class="rex-hide">'. $legend .'</span></legend>
        <input type="hidden" name="page" value="specials" />
        <input type="hidden" name="subpage" value="lang" />
        <input type="hidden" name="clang_id" value="'.$clang_id.'" />
    ';
  }
  
  echo '
    <table class="rex-table" summary="'.$I18N->msg('clang_summary').'">
      <caption>'.$I18N->msg('clang_caption').'</caption>
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
        <td><input type="text" name="clang_name" value="'.htmlspecialchars($clang_name).'" /></td>
        <td><input type="submit" class="rex-fsubmit" name="add_clang_save" value="'.$I18N->msg('clang_add').'" /></td>
      </tr>
    ';
  }
  
  foreach($REX['CLANG'] as $lang_id => $lang)
  {
    // Edit form
    if ($func == "editclang" && $clang_id == $lang_id)
    {
      echo '
      <tr class="rex-trow-actv">
        <td></td>
        <td align="center" class="grey">'.$lang_id.'</td>
        <td><input type="text" name="clang_name" value="'.htmlspecialchars($lang).'" /></td>
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
    
  if($func == 'addclang' || $func == 'editclang')
  {
    echo '
      </fieldset>
    </form>';
  }
  
  
}
else
{
  // ----- eigene typen definieren
  
  if(!isset($type_id)) $type_id = '';
  if(!isset($typname)) $typname = '';
  if(!isset($description)) $description = '';
    
  if (!empty($edit_article_type))
  {
    if($type_id != '' && $typname != '')
    {
      $update = new sql;
      $update->setTable($REX['TABLE_PREFIX']."article_type");
      $update->where("type_id='$type_id'");
      $update->setValue("name",$typname);
      $update->setValue("description",$description);
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
  elseif (!empty($delete_article_type))
  {
    if ($type_id!=1)
    {
      $delete = new sql;
      $result = $delete->get_array("SELECT name,id FROM ".$REX['TABLE_PREFIX']."article WHERE type_id = $type_id");
      if (is_array($result)){
        $message = $I18N->msg("article_type_still_used")."<br>";
        foreach ($result as $var){
          $message .= '<br /><a href="index.php?page=content&amp;article_id='.$var['id'].'&amp;mode=meta">'.$var['name'].'</a>';
        }
        $message .= '<br /><br />';
      } else {
        $delete->query("DELETE FROM ".$REX['TABLE_PREFIX']."article_type WHERE type_id = '$type_id' LIMIT 1");
        $delete->query("UPDATE ".$REX['TABLE_PREFIX']."article SET type_id = '1' WHERE type_id = '$type_id'");
        $message = $I18N->msg("article_type_deleted");
      }
    } else
    {
      $message = $I18N->msg("article_type_could_not_be_deleted");
    }
  }
  elseif (!empty($add_article_type))
  {
    if($type_id != '' && $typname != '')
    {
      $add = new sql;
      $add->setTable($REX['TABLE_PREFIX']."article_type");
      $add->setValue("name",$typname);
      $add->setValue("type_id",$type_id);
      $add->setValue("description",$description);
      $add->insert();
      $type_id = 0;
      $message = $I18N->msg("article_type_added");
    }
    else
    {
      // Add form wieder anzeigen
      $func = 'add';
      $message = array();
      if($type_id == '')
      {
        $message[] = $I18N->msg('article_type_miss_id');
      }
      if($typname == '')
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
  
  if($func == 'add' || $func == 'edit')
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
        <caption>'.$I18N->msg('article_type_caption').'</caption>
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
  
  $sql = new sql;
  $sql->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."article_type ORDER BY type_id");
  
  if ($func == 'add')
  {
    echo '
      <tr class="rex-trow-actv">
        <td>&nbsp;</td>
        <td><input type="text" maxlength="2" name="type_id" value="'. $type_id .'" /></td>
        <td><input type="text" name="typname" value="'. $typname .'" /></td>
        <td><input type="text" name="description" value="'. $description .'" /></td>
        <td><input type="submit" class="rex-fsubmit" name="add_article_type" value="'.$I18N->msg('article_type_add').'" /></td>
      </tr>';
  }
  
  
  for ($i=0;$i<$sql->getRows();$i++)
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
      
  if($func == 'add' || $func == 'edit')
  {
      echo '
      </fieldset>
    </form>';
  }
}


?>