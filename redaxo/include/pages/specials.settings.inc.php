<?php

/**
 * 
 * @package redaxo3
 * @version $Id$
 */

if ($func == "setup")
{
  // REACTIVATE SETUP

  $h = @ fopen($REX['INCLUDE_PATH']."/master.inc.php", "r");
  $cont = fread($h, filesize($REX['INCLUDE_PATH']."/master.inc.php"));
  $cont = ereg_replace("(REX\['SETUP'\].?\=.?)[^;]*", "\\1"."true", $cont);
  fclose($h);
  // echo nl2br(htmlspecialchars($cont));
  $h = @ fopen($REX['INCLUDE_PATH']."/master.inc.php", "w+");
  if (fwrite($h, $cont, strlen($cont)) > 0)
  {
    $message = $I18N->msg("setup_error1");
  }
  else
  {
    $message = $I18N->msg("setup_error2");
  }
  fclose($h);

}
elseif ($func == "generate")
{

  // generate all articles,cats,templates,caches
  $message = rex_generateAll();

}
elseif ($func == "linkchecker")
{
  $LART = array ();

  for ($j = 1; $j < 11; $j++)
  {
    $LC = new sql;
    // $LC->debugsql = 1;
    $LC->setQuery("SELECT ".$REX['TABLE_PREFIX']."article_slice.article_id,".$REX['TABLE_PREFIX']."article_slice.id FROM ".$REX['TABLE_PREFIX']."article_slice
              LEFT JOIN ".$REX['TABLE_PREFIX']."article ON ".$REX['TABLE_PREFIX']."article_slice.link$j=".$REX['TABLE_PREFIX']."article.id
              WHERE
              ".$REX['TABLE_PREFIX']."article_slice.link$j>0 and ".$REX['TABLE_PREFIX']."article.id IS NULL");
    for ($i = 0; $i < $LC->getRows(); $i++)
    {
      $LART[$LC->getValue($REX['TABLE_PREFIX']."article_slice.article_id")] = 1;
      $LSLI[$LC->getValue($REX['TABLE_PREFIX']."article_slice.article_id")] = $LC->getValue($REX['TABLE_PREFIX']."article_slice.id");
      $LC->next();
    }
  }

  if (count($LART) > 0)
    reset($LART);

  for ($i = 0; $i < count($LART); $i++)
  {
    $message .= ' | <a href="index.php?page=content&amp;article_id='.key($LART).'&amp;mode=edit&amp;slice_id='.$LSLI[key($LART)].'&amp;function=edit#editslice">'.key($LART).'</a>';
    next($LART);
  }

  if (count($LART) == 0)
    $message = $I18N->msg("links_ok");
  else
    $message = $I18N->msg("links_not_ok")."<br /> ".$message." |";

}
elseif ($func == 'updateinfos')
{
  $REX['LANG'] = $neu_lang;

  $h = fopen("include/master.inc.php", "r");
  $cont = fread($h, filesize("include/master.inc.php"));

  $cont = ereg_replace("(REX\['START_ARTICLE_ID'\].?\=.?)[^;]*", "\\1".strtolower($neu_startartikel), $cont);
  $cont = ereg_replace("(REX\['NOTFOUND_ARTICLE_ID'\].?\=.?)[^;]*", "\\1".strtolower($neu_notfoundartikel), $cont);
  $cont = ereg_replace("(REX\['ERROR_EMAIL'\].?\=.?)[^;]*", "\\1\"".strtolower($neu_error_emailaddress)."\"", $cont);
  $cont = ereg_replace("(REX\['LANG'\].?\=.?)[^;]*", "\\1\"".$neu_lang."\"", $cont);
  $cont = ereg_replace("(REX\['SERVER'\].?\=.?)[^;]*", "\\1\"". ($neu_SERVER)."\"", $cont);
  $cont = ereg_replace("(REX\['SERVERNAME'\].?\=.?)[^;]*", "\\1\"". ($neu_SERVERNAME)."\"", $cont);

  // DB2 nur updaten, wenn das Formular unten aktiviert ist
  if (isset ($neu_db2_host) && $neu_db2_host != '')
  {
    $cont = ereg_replace("(REX\['DB'\]\['2'\]\['HOST'\].?\=.?)[^;]*", "\\1\"". ($neu_db2_host)."\"", $cont);
    $cont = ereg_replace("(REX\['DB'\]\['2'\]\['LOGIN'\].?\=.?)[^;]*", "\\1\"". ($neu_db2_login)."\"", $cont);
    $cont = ereg_replace("(REX\['DB'\]\['2'\]\['PSW'\].?\=.?)[^;]*", "\\1\"". ($neu_db2_psw)."\"", $cont);
    $cont = ereg_replace("(REX\['DB'\]\['2'\]\['NAME'\].?\=.?)[^;]*", "\\1\"". ($neu_db2_name)."\"", $cont);
  }

  // Mod-Rewrite
  $cont = ereg_replace("(REX\['MOD_REWRITE'\].?\=.?)[^;]*", "\\1".strtolower($neu_modrewrite), $cont);

  //      var_dump( $cont);

  fclose($h);
  $h = fopen("include/master.inc.php", "w+");
  fwrite($h, $cont, strlen($cont));
  fclose($h);

  if ($neu_modrewrite != "TRUE")
    $REX['MOD_REWRITE'] = false;
  else
    $REX['MOD_REWRITE'] = true;

  $REX['START_ARTICLE_ID'] = $neu_startartikel;
  $REX['NOTFOUND_ARTICLE_ID'] = $neu_notfoundartikel;
  $REX['EMAIL'] = $neu_error_emailaddress;
  $REX['ERROR_EMAIL'] = $neu_error_emailaddress;
  $REX['SERVER'] = $neu_SERVER;
  $REX['SERVERNAME'] = $neu_SERVERNAME;

  if (!isset ($neu_db2_host))
    $neu_db2_host = '';
  if (!isset ($neu_db2_login))
    $neu_db2_login = '';
  if (!isset ($neu_db2_psw))
    $neu_db2_psw = '';
  if (!isset ($neu_db2_name))
    $neu_db2_name = '';
  $REX['DB']['2']['HOST'] = $neu_db2_host;
  $REX['DB']['2']['LOGIN'] = $neu_db2_login;
  $REX['DB']['2']['PSW'] = $neu_db2_psw;
  $REX['DB']['2']['NAME'] = $neu_db2_name;

  $message = $I18N->msg("info_updated");

}

$sel_lang = new select();
$sel_lang->set_name('neu_lang');
$sel_lang->set_id('rex_lang');
$sel_lang->set_size(1);
$sel_lang->set_selected($REX['LANG']);

foreach ($REX['LOCALES'] as $l)
{
  $sel_lang->add_option($l, $l);
}

$sel_mod_rewrite = new select();
$sel_mod_rewrite->set_name('neu_modrewrite');
$sel_mod_rewrite->set_id('rex_mod_rewrite');
$sel_mod_rewrite->set_size(1);
$sel_mod_rewrite->set_selected($REX['MOD_REWRITE']);

$sel_mod_rewrite->add_option('TRUE', '1');
$sel_mod_rewrite->add_option('FALSE', '0');

if ($message != "")
  echo '<p class="rex-warning">'.$message.'</p>';
  
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
            '.$sel_lang->out().'
          </p>
          <p>
            <label for="rex_mod_rewrite">$REX[\'MOD_REWRITE\']</label>
            '.$sel_mod_rewrite->out().'
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
  
?>