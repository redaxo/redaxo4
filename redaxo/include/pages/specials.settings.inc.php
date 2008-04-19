<?php

/**
 *
 * @package redaxo4
 * @version $Id: specials.settings.inc.php,v 1.6 2008/03/25 09:54:44 kills Exp $
 */

$info = '';
$warning = '';

if ($func == 'setup')
{
  // REACTIVATE SETUP

  $master_file = $REX['INCLUDE_PATH'].'/master.inc.php';
  $cont = rex_get_file_contents($master_file);
  $cont = ereg_replace("(REX\['SETUP'\].?\=.?)[^;]*", '\\1true', $cont);
  // echo nl2br(htmlspecialchars($cont));
  if (rex_put_file_contents($master_file, $cont))
  {
    $info = $I18N->msg('setup_error1', '<a href="index.php">', '</a>');
  }
  else
  {
    $warning = $I18N->msg('setup_error2');
  }
}
elseif ($func == 'generate')
{
  // generate all articles,cats,templates,caches
  $info = rex_generateAll();
}
elseif ($func == 'updateinfos')
{
  $neu_startartikel       = rex_post('neu_startartikel', 'int');
  $neu_notfoundartikel    = rex_post('neu_notfoundartikel', 'int');
  $neu_lang               = rex_post('neu_lang', 'string');
  // ' darf nichtg escaped werden, da in der Datei der Schlüssel nur zwischen " steht
  $neu_error_emailaddress = str_replace("\'", "'", rex_post('neu_error_emailaddress', 'string'));
  $neu_SERVER             = str_replace("\'", "'", rex_post('neu_SERVER', 'string'));
  $neu_SERVERNAME         = str_replace("\'", "'", rex_post('neu_SERVERNAME', 'string'));
  $neu_modrewrite         = rex_post('neu_modrewrite', 'string');

  $startArt = OOArticle::getArticleById($neu_startartikel);
  $notFoundArt = OOArticle::getArticleById($neu_notfoundartikel);

  if(!OOArticle::isValid($startArt))
    $warning .= $I18N->msg('settings_invalid_sitestart_article');

  if(!OOArticle::isValid($notFoundArt))
    $warning .= $I18N->msg('settings_invalid_notfound_article');

  if($warning == '')
  {
    $REX['LANG'] = $neu_lang;
    $master_file = $REX['INCLUDE_PATH'] .'/master.inc.php';
    $cont = rex_get_file_contents($master_file);

    $cont = ereg_replace("(REX\['START_ARTICLE_ID'\].?\=.?)[^;]*", "\\1".strtolower($neu_startartikel), $cont);
    $cont = ereg_replace("(REX\['NOTFOUND_ARTICLE_ID'\].?\=.?)[^;]*", "\\1".strtolower($neu_notfoundartikel), $cont);
    $cont = ereg_replace("(REX\['ERROR_EMAIL'\].?\=.?)[^;]*", "\\1\"".strtolower($neu_error_emailaddress)."\"", $cont);
    $cont = ereg_replace("(REX\['LANG'\].?\=.?)[^;]*", "\\1\"".$neu_lang."\"", $cont);
    $cont = ereg_replace("(REX\['SERVER'\].?\=.?)[^;]*", "\\1\"". ($neu_SERVER)."\"", $cont);
    $cont = ereg_replace("(REX\['SERVERNAME'\].?\=.?)[^;]*", "\\1\"". ($neu_SERVERNAME)."\"", $cont);
    $cont = ereg_replace("(REX\['MOD_REWRITE'\].?\=.?)[^;]*","\\1".strtolower($neu_modrewrite),$cont);

    rex_put_file_contents($master_file, $cont);
    $info = $I18N->msg('info_updated');
  }

  // Zuweisungen für Wiederanzeige
  $REX['MOD_REWRITE'] = $neu_modrewrite === 'TRUE';
  $REX['START_ARTICLE_ID'] = $neu_startartikel;
  $REX['NOTFOUND_ARTICLE_ID'] = $neu_notfoundartikel;
  // Für die Wiederanzeige Slashes strippen
  $REX['ERROR_EMAIL'] = stripslashes($neu_error_emailaddress);
  $REX['SERVER'] = stripslashes($neu_SERVER);
  $REX['SERVERNAME'] = stripslashes($neu_SERVERNAME);
}

$sel_lang = new rex_select();
$sel_lang->setName('neu_lang');
$sel_lang->setId('rex_lang');
$sel_lang->setSize(1);
$sel_lang->setSelected($REX['LANG']);

foreach ($REX['LOCALES'] as $l)
{
  $sel_lang->addOption($l, $l);
}

$sel_mod_rewrite = new rex_select();
$sel_mod_rewrite->setSize(1);
$sel_mod_rewrite->setName('neu_modrewrite');
$sel_mod_rewrite->setId('rex_mod_rewrite');
$sel_mod_rewrite->setSelected($REX['MOD_REWRITE'] === false ? 'FALSE' : 'TRUE');

$sel_mod_rewrite->addOption('TRUE', 'TRUE');
$sel_mod_rewrite->addOption('FALSE', 'FALSE');

if ($info != '')
  echo rex_info($info);

if ($warning != '')
  echo rex_warning($warning);

echo '
	<div class="rex-spc-stn">
  <form action="index.php" method="post">
    <input type="hidden" name="page" value="specials" />
    <input type="hidden" name="func" value="updateinfos" />

    <div class="rex-cnt-cols">
    <div class="rex-cnt-col2">
      <p class="rex-hdl">'.$I18N->msg("specials_features").'</p>
      <div class="rex-spc-stn-cnt">
        <p><a href="index.php?page=specials&amp;func=generate">'.$I18N->msg("delete_cache").'</a></p>
        <p>'.$I18N->msg("delete_cache_description").'</p>

        <p><a href="index.php?page=specials&amp;func=setup" onclick="return confirm(\''.$I18N->msg("setup").'?\');"
>'.$I18N->msg("setup").'</a></p>
        <p>'.$I18N->msg("setup_text").'</p>
      </div>
    </div>

    <div class="rex-cnt-col2">
      <p class="rex-hdl">'.$I18N->msg("specials_settings").'</p>
      <div class="rex-spc-stn-cnt">
        <fieldset>
          <legend class="rex-lgnd">'.$I18N->msg("general_info_header").'</legend>
          <p>
            <label for="rex_version">$REX[\'VERSION\']</label>
            <span id="rex_version">&quot;'.$REX['VERSION'].'&quot;</span>
          </p>
          <p>
            <label for="rex_subversion">$REX[\'SUBVERSION\']</label>
            <span id="rex_subversion">&quot;'.$REX['SUBVERSION'] .'&quot;</span>
          </p>
          <p>
            <label for="rex_minorversion">$REX[\'MINORVERSION\']</label>
            <span id="rex_minorversion">&quot;'.$REX['MINORVERSION'] .'&quot;</span>
          </p>
          <p>
            <label for="rex_server">$REX[\'SERVER\']</label>
            <input type="text" id="rex_server" name="neu_SERVER" value="'. htmlspecialchars($REX['SERVER']).'" />
          </p>
          <p>
            <label for="rex_servername">$REX[\'SERVERNAME\']</label>
            <input type="text" id="rex_servername" name="neu_SERVERNAME" value="'. htmlspecialchars($REX['SERVERNAME']).'" />
          </p>
        </fieldset>
        <fieldset>
          <legend class="rex-lgnd">'.$I18N->msg("db1_can_only_be_changed_by_setup").'</legend>
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
            <span id="rex_db_name">&quot;'.htmlspecialchars($REX['DB']['1']['NAME']).'&quot;</span>
          </p>
        </fieldset>
        <fieldset>
          <legend class="rex-lgnd">'.$I18N->msg("specials_others").'</legend>
          <p>
            <label for="rex_include_path">$REX[\'INCLUDE_PATH\']</label>
            <span id="rex_include_path" title="'. $REX['INCLUDE_PATH'] .'">&quot;';

   $tmp = $REX['INCLUDE_PATH'];
   if (strlen($REX['INCLUDE_PATH'])>24) $tmp = substr($tmp,0,8)."...".substr($tmp,strlen($tmp)-14);
   echo $tmp;
   echo '&quot;</span>
          </p>
          <p>
            <label for="rex_error_email">$REX[\'ERROR_EMAIL\']</label>
            <input type="text" id="rex_error_email" name="neu_error_emailaddress" value="'.htmlspecialchars($REX['ERROR_EMAIL']).'" />
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
            '.$sel_lang->get().'
          </p>
          <p>
            <label for="rex_mod_rewrite">$REX[\'MOD_REWRITE\']</label>
            '.$sel_mod_rewrite->get().'
          </p>
          <p>
            <input type="submit" class="rex-sbmt" name="sendit" value="'.$I18N->msg("specials_update").'"'. rex_accesskey($I18N->msg('specials_update'), $REX['ACKEY']['SAVE']) .' />
          </p>
        </fieldset>
      </div>
    </div>
  </div>
  </form>
  </div>
  ';

?>