<?php

/**
 * Direkter Aufruf, um zu testen, ob der Ordner redaxo/include
 * erreichbar ist. Dies darf aus Sicherheitsgründen nicht möglich sein!
 */
if (!isset($REX))
{
  echo '<html>
          <title></title>
          <head>
            <script src="../../js/standard.js" type="text/javascript"></script>
            <script type="text/javascript">
              var needle = new parent.getObj("security_warning");
              var span = needle.obj;
              span.style.display="";
              var needle = new parent.getObj("nextstep");
              var span = needle.obj;
              span.style.display="none";
            </script>
          </head>
          <body></body>
        </html>';
  exit();
}


/**
 *
 * @package redaxo4
 * @version svn:$Id$
 */

// --------------------------------------------- SETUP FUNCTIONS

/**
 * Ausgabe des Setup spezifischen Titels
 */
function rex_setup_title($title)
{
  rex_title($title);

  echo '<div id="rex-setup" class="rex-area">';
}

function rex_setup_import($import_sql, $import_archiv = null)
{
  global $REX, $I18N, $export_addon_dir;

  $err_msg = '';

  if (!is_dir($export_addon_dir))
  {
    $err_msg .= $I18N->msg('setup_03703').'<br />';
  }
  else
  {
    if (file_exists($import_sql) && ($import_archiv === null || $import_archiv !== null && file_exists($import_archiv)))
    {
      // Hier I18N_IM_EXPORT global definieren, damit es aus der config.inc.php übernommen
      // wird und auch in der danach includeten function verfügbar ist
      global $I18N_IM_EXPORT;

      $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/import_export/lang/');
      require_once $export_addon_dir.'/classes/class.tar.inc.php';
      require_once $export_addon_dir.'/classes/class.rex_tar.inc.php';

      // DB Import
      $state_db = rex_a1_import_db($import_sql);
      if ($state_db['state'] === false)
      {
        $err_msg .= nl2br($state_db['message']) .'<br />';
      }

      // Archiv optional importieren
      if ($state_db['state'] === true && $import_archiv !== null)
      {
        $state_archiv = rex_a1_import_files($import_archiv);
        if ($state_archiv['state'] === false)
        {
          $err_msg .= $state_archiv['message'].'<br />';
        }
      }
    }
    else
    {
      $err_msg .= $I18N->msg('setup_03702').'<br />';
    }
  }

  return $err_msg;
}

function rex_setup_is_writable($items)
{
  global $REX;
  $res = array();

  foreach($items as $item)
  {
    $is_writable = _rex_is_writable($item);

    // 0 => kein Fehler
    if($is_writable != 0)
    {
      $res[$is_writable][] = $item;
    }
  }

  return $res;
}

// -------------------------- System AddOns prüfen
function rex_setup_addons($uninstallBefore = false, $installDump = true)
{
  global $REX, $I18N;

  require_once $REX['INCLUDE_PATH'].'/functions/function_rex_addons.inc.php';

  $addonErr = '';
  $ADDONS = rex_read_addons_folder();
  $addonManager = new rex_addonManager($ADDONS);
  foreach($REX['SYSTEM_ADDONS'] as $systemAddon)
  {
    $state = true;

    if($state === true && $uninstallBefore)
    $state = $addonManager->uninstall($systemAddon);

    if($state === true && !OOAddon::isInstalled($systemAddon))
    $state = $addonManager->install($systemAddon, $installDump);

    if($state === true && !OOAddon::isActivated($systemAddon))
    $state = $addonManager->activate($systemAddon);

    if($state !== true)
    $addonErr .= '<li>'. $systemAddon .'<ul><li>'. $state .'</li></ul></li>';
  }

  if($addonErr != '')
  {
    $addonErr = '<ul class="rex-ul1">
                   <li>
                     <h3 class="rex-hl3">'. $I18N->msg('setup_011', '<span class="rex-error">', '</span>') .'</h3>
                     <ul>'. $addonErr .'</ul>
                   </li>
                 </ul>';
  }

  return $addonErr;
}


/**
 * returns formated sql errors from rex_sql object
 * @param  object $obj     rex_sql object
 * @return string $err_msg formated Error message
 */
function rex_formated_sqlerror($obj)
{

  $err_msg = '<strong style="display:inline-block;width:50px;">Query</strong> <code style="color:#000;">'.$obj->query.'</code><br />'.PHP_EOL.
             '<strong style="display:inline-block;width:50px;">Error</strong> <code style="color:#000;">#'.$obj->getErrno().' - '.$obj->getError().'</code><br /><hr />'.PHP_EOL;
  return $err_msg;
}


/**
 * change default charset of database
 * @param  string $charset      sql charset
 * @return  string              error message
 */
function rex_setup_setDBcharset($charset='')
{
  global $REX;
  $err_msg = '';

  if($charset!='')
  {
    $ad = new rex_sql();
    $ad->setQuery('ALTER DATABASE `'.$REX['DB']['1']['NAME'].'` DEFAULT CHARACTER SET '.$charset);
    $err_msg .= $ad->hasError() ? rex_formated_sqlerror($ad) : '';
    unset($ad);
  }
  else
  {
    $err_msg = 'rex_setup_setDBcharset() error: no charset defined..';
  }

  return $err_msg;
}


/**
 * drop all rex_* tables
 * @return  string    SQL error message
 */
function rex_setup_dropREXtables()
{
  global $REX;
  $err_msg = '';

  // get rex_* tables
  $rt = new rex_sql();
  $rt->setQuery('SHOW TABLES LIKE \''.$REX['TABLE_PREFIX'].'%\';');
  $err_msg .= $rt->hasError() ? rex_formated_sqlerror($rt) : '';

  // drop tables
  $dt = new rex_sql();
  foreach($rt->getArray() as $t)
  {
    $table = $t['Tables_in_'.$REX['DB']['1']['NAME'].' ('.$REX['TABLE_PREFIX'].'%)'];
    $dt->setQuery('DROP TABLE `'.$table.'`');
    $err_msg .= $dt->hasError() ? rex_formated_sqlerror($dt) : '';
}

  unset($rt,$dt);
  return $err_msg;
}


  // --------------------------------------------- END: SETUP FUNCTIONS


  $MSG['err'] = "";

  $checkmodus = rex_request('checkmodus', 'float');
  $send       = rex_request('send', 'string');
  $dbanlegen  = rex_request('dbanlegen', 'string');
  $noadmin    = rex_request('noadmin', 'string');
  $lang       = rex_request('lang', 'string');

  $export_addon_dir = $REX['INCLUDE_PATH'].'/addons/import_export';
  require_once $export_addon_dir.'/functions/function_folder.inc.php';
  require_once $export_addon_dir.'/functions/function_import_folder.inc.php';
  require_once $export_addon_dir.'/functions/function_import_export.inc.php';


  // ---------------------------------- MODUS 0 | Start
  if (!($checkmodus > 0 && $checkmodus < 10))
  {
    $langpath = $REX['INCLUDE_PATH'].'/lang';
    foreach($REX['LANGUAGES'] as $l)
    {
      $I18N_T = rex_create_lang($l,$langpath,FALSE);
      $label = $I18N_T->msg('lang');
      $label .= ' (utf-8)';
      $langs[$l] = '<li><a href="index.php?checkmodus=0.5&amp;lang='.$l.'"'. rex_tabindex() .'>'.$label.'</a></li>';

    }
    unset($I18N_T);

    // wenn nur eine Sprache -> direkte weiterleitung
    if (count($REX['LANGUAGES'])==1)
    {
      header('Location: index.php?checkmodus=0.5&lang='.key($langs));
      exit();
    }

    rex_setup_title('SETUP: SELECT LANGUAGE');

    echo '<h2 class="rex-hl2">Please choose a language!</h2>
        <div class="rex-area-content">
          <ul class="rex-setup-language">'. implode('', $langs) .'</ul>
        </div>';
  }

  // ---------------------------------- MODUS 0 | Start

  if ($checkmodus == '0.5')
  {
    rex_setup_title('SETUP: START');

    $REX['LANG'] = $lang;

    echo $I18N->msg('setup_005', '<h2 class="rex-hl2">', '</h2>');
    echo '<div class="rex-area-content">';

    echo $I18N->msg('setup_005_1', '<h3 class="rex-hl3">', '</h3>', ' class="rex-ul1"');
    echo '<div class="rex-area-scroll">';

    $Basedir = dirname(__FILE__);
    $license_file = $Basedir.'/../../../_lizenz.txt';
    $license = '<p class="rex-tx1">'.nl2br(rex_get_file_contents($license_file)).'</p>';

    echo $license;

    echo '</div>
        </div>
        <div class="rex-area-footer">
          <p class="rex-algn-rght"><a href="index.php?page=setup&amp;checkmodus=1&amp;lang='.urlencode($lang).'"'. rex_tabindex() .'>&raquo; '.$I18N->msg("setup_006").'</a></p>
        </div>';

    $checkmodus = 0;
  }

  // ---------------------------------- MODUS 1 | Versionscheck - Rechtecheck

  if ($checkmodus == 1)
  {
    // -------------------------- VERSIONSCHECK
    if (version_compare(phpversion(), REX_MIN_PHP_VERSION, '<'))
    {
      $MSG['err'] .= '<li>'. $I18N->msg('setup_010', phpversion()).'</li>';
    }

    // -------------------------- EXTENSION CHECK
    foreach(array('session', 'mysql', 'pcre') as $extension)
    {
      if(!extension_loaded($extension))
      $MSG['err'] .= '<li>'. $I18N->msg('setup_010_1', $extension).'</li>';
    }

    // -------------------------- SCHREIBRECHTE
    $WRITEABLES = array (
    $REX['INCLUDE_PATH'].DIRECTORY_SEPARATOR.'master.inc.php',
    $REX['INCLUDE_PATH'].DIRECTORY_SEPARATOR.'addons.inc.php',
    $REX['INCLUDE_PATH'].DIRECTORY_SEPARATOR.'plugins.inc.php',
    $REX['INCLUDE_PATH'].DIRECTORY_SEPARATOR.'clang.inc.php',
    $REX['GENERATED_PATH'],
    $REX['GENERATED_PATH'].DIRECTORY_SEPARATOR.'articles',
    $REX['GENERATED_PATH'].DIRECTORY_SEPARATOR.'templates',
    $REX['GENERATED_PATH'].DIRECTORY_SEPARATOR.'files',
    $REX['MEDIAFOLDER'],
    $REX['MEDIAFOLDER'] .DIRECTORY_SEPARATOR.'.redaxo',
    getImportDir()
    );

    foreach($REX['SYSTEM_ADDONS'] as $system_addon)
    $WRITEABLES[] = $REX['INCLUDE_PATH'].DIRECTORY_SEPARATOR.'addons'.DIRECTORY_SEPARATOR. $system_addon;

    $res = rex_setup_is_writable($WRITEABLES);
    if(count($res) > 0)
    {
      $MSG['err'] .= '<li>';
      foreach($res as $type => $messages)
      {
        if(count($messages) > 0)
        {
          $MSG['err'] .= '<h3 class="rex-hl3">'. _rex_is_writable_info($type) .'</h3>';
          $MSG['err'] .= '<ul>';
          foreach($messages as $message)
          {
            $MSG['err'] .= '<li>'. $message .'</li>';
          }
          $MSG['err'] .= '</ul>';
        }
      }
      $MSG['err'] .= '</li>';
    }
  }

  if ($MSG['err'] == '' && $checkmodus == 1)
  {
    rex_setup_title($I18N->msg('setup_step1'));

    echo $I18N->msg('setup_016', '<h2 class="rex-hl2">', '</h2>');
    echo '<div class="rex-area-content">';

    echo $I18N->msg('setup_016_1', ' class="rex-ul1"', '<span class="rex-ok">', '</span>');
    echo '<div class="rex-message"><p class="rex-warning" id="security_warning" style="display: none;"><span>'. $I18N->msg('setup_security_msg') .'</span></p></div>
          <noscript><div class="rex-message"><p class="rex-warning"><span>'. $I18N->msg('setup_no_js_security_msg') .'</span></p></div></noscript>
          <iframe src="include/pages/setup.inc.php?page=setup&amp;checkmodus=1.5&amp;lang='.urlencode($lang).'" style="display: none;"></iframe>
       </div>
       <div class="rex-area-footer">
         <p id="nextstep" class="rex-algn-rght">
           <a href="index.php?page=setup&amp;checkmodus=2&amp;lang='.urlencode($lang).'"'. rex_tabindex() .'>&raquo; '.$I18N->msg('setup_017').'</a>
         </p>
       </div>';

  }
  elseif ($MSG['err'] != "")
  {

    rex_setup_title($I18N->msg('setup_step1'));

    echo '<h2 class="rex-hl2">'.$I18N->msg('setup_headline1').'</h2>
        <div class="rex-area-content">
          <ul class="rex-ul1">'.$MSG['err'].'</ul>
          <p class="rex-tx1">'.$I18N->msg('setup_018').'</p>
        </div>
        <div class="rex-area-footer">
          <p class="rex-algn-rght">
            <a href="index.php?page=setup&amp;checkmodus=1&amp;lang='.urlencode($lang).'"'. rex_tabindex() .'>&raquo; '.$I18N->msg('setup_017').'</a>
          </p>
        </div>';
  }

  // ---------------------------------- MODUS 2 | master.inc.php - Datenbankcheck

  if ($checkmodus == 2 && $send == 1)
  {
    $master_file = $REX['INCLUDE_PATH'].'/master.inc.php';
    $cont = rex_get_file_contents($master_file);

    // slashes entfernen
    $serveraddress             = stripslashes(rex_post('serveraddress', 'string'));
    $serverbezeichnung         = stripslashes(rex_post('serverbezeichnung', 'string'));
    $error_email               = stripslashes(rex_post('error_email', 'string'));
    $psw_func                  = stripslashes(rex_post('psw_func', 'string'));
    $mysql_host                = stripslashes(rex_post('mysql_host', 'string'));
    $redaxo_db_user_login      = stripslashes(rex_post('redaxo_db_user_login', 'string'));
    $redaxo_db_user_pass       = stripslashes(rex_post('redaxo_db_user_pass', 'string'));
    $dbname                    = stripslashes(rex_post('dbname', 'string'));
    $redaxo_db_create          = rex_post('redaxo_db_create', 'boolean');

    // einfache anführungszeichen und dollarzeichen maskieren
    $search = array("'", '$');
    $destroy = array("\\'", '\\$');

    $replace = array(
      'search' => array(
        "@(REX\['SERVER'\].?\=.?).*$@m",
        "@(REX\['SERVERNAME'\].?\=.?).*$@m",
        "@(REX\['LANG'\].?\=.?).*$@m",
        "@(REX\['INSTNAME'\].?\=.?).*$@m",
        "@(REX\['ERROR_EMAIL'\].?\=.?).*$@m",
        "@(REX\['PSWFUNC'\].?\=.?).*$@m",
        "@(REX\['DB'\]\['1'\]\['HOST'\].?\=.?).*$@m",
        "@(REX\['DB'\]\['1'\]\['LOGIN'\].?\=.?).*$@m",
        "@(REX\['DB'\]\['1'\]\['PSW'\].?\=.?).*$@m",
        "@(REX\['DB'\]\['1'\]\['NAME'\].?\=.?).*$@m"
      ),
      'replace' => array(
        "$1'".str_replace($search, $destroy, $serveraddress)."';",
        "$1'".str_replace($search, $destroy, $serverbezeichnung)."';",
        "$1'".str_replace($search, $destroy, $lang)."';",
        "$1'rex".date('YmdHis')."';",
        "$1'".str_replace($search, $destroy, $error_email)."';",
        "$1'".str_replace($search, $destroy, $psw_func)."';",
        "$1'".str_replace($search, $destroy, $mysql_host)."';",
        "$1'".str_replace($search, $destroy, $redaxo_db_user_login)."';",
        "$1'".str_replace($search, $destroy, $redaxo_db_user_pass)."';",
        "$1'".str_replace($search, $destroy, $dbname)."';"
      )
    );

    $cont = preg_replace($replace['search'], $replace['replace'], $cont);

    if(rex_put_file_contents($master_file, $cont) === false)
    {
      $err_msg = $I18N->msg('setup_020', '<b>', '</b>');
    }

    // -------------------------- DATENBANKZUGRIFF
    $err = rex_sql::checkDbConnection($mysql_host, $redaxo_db_user_login, $redaxo_db_user_pass, $dbname, $redaxo_db_create);
    if($err !== true)
    {
      $err_msg = $err;
    }
    else
    {
      $REX['DB']['1']['NAME'] = $dbname;
      $REX['DB']['1']['LOGIN'] = $redaxo_db_user_login;
      $REX['DB']['1']['PSW'] = $redaxo_db_user_pass;
      $REX['DB']['1']['HOST'] = $mysql_host;

      $err_msg = "";
      $checkmodus = 3;
      $send = "";
    }
  }
  else
  {
    // Allgemeine Infos
    $serveraddress         = $REX['SERVER'];
    $serverbezeichnung     = $REX['SERVERNAME'];
    $error_email           = $REX['ERROR_EMAIL'];
    $psw_func              = $REX['PSWFUNC'];

    // DB Infos
    $dbname                = $REX['DB']['1']['NAME'];
    $redaxo_db_user_login  = $REX['DB']['1']['LOGIN'];
    $redaxo_db_user_pass   = $REX['DB']['1']['PSW'];
    $mysql_host            = $REX['DB']['1']['HOST'];
  }

  if ($checkmodus == 2)
  {
    rex_setup_title($I18N->msg('setup_step2'));

    echo '<h2 class="rex-hl2">'.$I18N->msg('setup_023').'</h2>
        <div class="rex-form" id="rex-form-setup-step-2">
        <form action="index.php" method="post">
        <fieldset class="rex-form-col-1">
          <input type="hidden" name="page" value="setup" />
          <input type="hidden" name="checkmodus" value="2" />
          <input type="hidden" name="send" value="1" />
          <input type="hidden" name="lang" value="'.htmlspecialchars($lang).'" />';

    if (isset ($err_msg) and $err_msg != '') {
      echo rex_warning($err_msg);
    }

    $psw_functions = '';
    foreach(array('', 'sha1') as $key => $algo)
    {
      $key = $algo;
      if($algo == '') $algo = $I18N->msg('setup_no_encryption');
      if($algo == 'sha1') $algo = $I18N->msg('setup_psw_encryption');  // ' ('. $I18N->msg('recommended') .')'
      $selected = $key == $psw_func ? ' selected="selected"' : '';

      $psw_functions .= '<option value="'. $key .'"'. $selected .'>'. $algo .'</option>';
    }

    echo '
            <legend>'.$I18N->msg("setup_0201").'</legend>

            <div class="rex-form-wrapper">
              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-text">
                  <label for="serverbezeichnung" title="$REX[\'SERVERNAME\']">'.$I18N->msg("setup_025").'</label>
                  <input class="rex-form-text" type="text" id="serverbezeichnung" name="serverbezeichnung" value="'.htmlspecialchars($serverbezeichnung).'"'. rex_tabindex() .' />
                </p>
              </div>

              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-text">
                  <label for="serveraddress" title="$REX[\'SERVER\']">'.$I18N->msg("setup_024").'</label>
                  <input class="rex-form-text" type="text" id="serveraddress" name="serveraddress" value="'.htmlspecialchars($serveraddress).'"'. rex_tabindex() .' />
                </p>
              </div>

              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-text">
                  <label for="error_email" title="$REX[\'ERROR_EMAIL\']">'.$I18N->msg("setup_026").'</label>
                  <input class="rex-form-text" type="text" id="error_email" name="error_email" value="'.htmlspecialchars($error_email).'"'. rex_tabindex() .' />
                </p>
              </div>

              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-select">
                  <label for="psw_func">'.$I18N->msg("setup_encryption").'</label>
                  <select class="rex-form-select" id="psw_func" name="psw_func"'. rex_tabindex() .'>
                    '. $psw_functions .'
                  </select>
                </p>
              </div>
          </div>
          </fieldset>

          <fieldset class="rex-form-col-1">
            <legend>'.$I18N->msg("setup_0202").'</legend>
            <div class="rex-form-wrapper">
              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-text">
                  <label for="dbname">'.$I18N->msg("setup_027").'</label>
                  <input class="rex-form-text" type="text" value="'.htmlspecialchars($dbname).'" id="dbname" name="dbname"'. rex_tabindex() .' />
                </p>
              </div>

              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-text">
                  <label for="mysql_host">MySQL Host</label>
                  <input class="rex-form-text" type="text" id="mysql_host" name="mysql_host" value="'.htmlspecialchars($mysql_host).'"'. rex_tabindex() .' />
                </p>
              </div>

              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-text">
                  <label for="redaxo_db_user_login">Login</label>
                  <input class="rex-form-text" type="text" id="redaxo_db_user_login" name="redaxo_db_user_login" value="'.htmlspecialchars($redaxo_db_user_login).'"'. rex_tabindex() .' />
                </p>
              </div>

              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-text">
                  <label for="redaxo_db_user_pass">'.$I18N->msg("setup_028").'</label>
                  <input class="rex-form-text" type="text" id="redaxo_db_user_pass" name="redaxo_db_user_pass" value="'.htmlspecialchars($redaxo_db_user_pass).'"'. rex_tabindex() .' />
                </p>
              </div>

              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-checkbox">
                  <label for="redaxo_db_create">'.$I18N->msg("setup_create_db").'</label>
                  <input class="rex-form-checkbox" type="checkbox" id="redaxo_db_create" name="redaxo_db_create" value="1"'. rex_tabindex() .' />
                </p>
              </div>
            </div>
          </fieldset>

          <fieldset class="rex-form-col-1">
            <div class="rex-form-wrapper">
              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-submit">
                  <input class="rex-form-submit" type="submit" value="'.$I18N->msg("setup_029").'"'. rex_tabindex() .' />
                </p>
              </div>

            </div>
          </fieldset>
        </form>
        </div>
        <script type="text/javascript">
           <!--
          jQuery(function($) {
            $("#serverbezeichnung").focus();
          });
           //-->
        </script>';
  }

  // ---------------------------------- MODUS 3 | Datenbank anlegen ...

  if ($checkmodus == 3 && $send == 1)
  {
    $err_msg = '';
    $dbanlegen = rex_post('dbanlegen', 'int', '');

    // -------------------------- Benötigte Tabellen prüfen
    $requiredTables = array (
    $REX['TABLE_PREFIX'] .'action',
    $REX['TABLE_PREFIX'] .'article',
    $REX['TABLE_PREFIX'] .'article_slice',
    $REX['TABLE_PREFIX'] .'clang',
    $REX['TABLE_PREFIX'] .'file',
    $REX['TABLE_PREFIX'] .'file_category',
    $REX['TABLE_PREFIX'] .'module_action',
    $REX['TABLE_PREFIX'] .'module',
    $REX['TABLE_PREFIX'] .'template',
    $REX['TABLE_PREFIX'] .'user',
    );

    if ($dbanlegen == 4)
    {
      // ----- vorhandenen seite updaten
        $info_msg = '';

        // import uploaded 4.x dump
        if (isset ($_FILES['FORM']) && $_FILES['FORM']['size']['importfile'] < 1)
        {
          // no upload file error
          $err_msg = $I18N->msg("setup_03801")."<br>";
        }
        else
        {
          // drop rex_* tables
          $info_msg .= rex_setup_dropREXtables();

          // set database default charset
          $info_msg .= rex_setup_setDBcharset('utf8 COLLATE utf8_general_ci');

          // create temp sql dump from upload
          $tmp_sql = getImportDir().'/temp.sql';
          if (@ move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'], $tmp_sql))
          {
            // import sql dump
            if($err_msg == '')
            {
              $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/import_export/lang/');
              rex_a1_import_db($tmp_sql);
              @ unlink($tmp_sql);
            }
            else
            {
              // no permissions error
              $err_msg = $I18N->msg("setup_03802")." ".$I18N->msg("setup_03803", "addons/import_export/backup/")." <br>";
            }

            // notify without setup abort
            if($info_msg != '')
              echo rex_info($info_msg);
          }

          // setup addons
          if($err_msg == '')
          $err_msg .= rex_setup_addons();

          // apply 4.3 patches to database
          if($err_msg == '')
          $err_msg .= rex_setup_import($REX['INCLUDE_PATH'].'/install/update4_x_to_4_5.sql');
        }
      }
    elseif ($dbanlegen == 3)
    {
      // ----- vorhandenen Export importieren

      // set database default charset
      $err_msg .= rex_setup_setDBcharset('utf8 COLLATE utf8_general_ci');

      $import_name = rex_post('import_name', 'string');

      if($import_name == '')
      {
        $err_msg .= '<p>'.$I18N->msg('setup_03701').'</p>';
      }
      else
      {
        $import_sql = getImportDir().'/'.$import_name.'.sql';
        $import_archiv = getImportDir().'/'.$import_name.'.tar.gz';

        // Nur hier zuerst die Addons installieren
        // Da sonst Daten aus dem eingespielten Export
        // ‹berschrieben würden
        if($err_msg == '')
        $err_msg .= rex_setup_addons(true, false);
        if($err_msg == '')
        $err_msg .= rex_setup_import($import_sql, $import_archiv);
      }
    }
    elseif ($dbanlegen == 2)
    {
      // ----- db schon vorhanden, nichts tun
      $err_msg .= rex_setup_addons(false, false);
    }
    elseif ($dbanlegen == 1)
    {
      // ----- volle Datenbank, alte DB löschen / drop
      rex_setup_setDBcharset('utf8 COLLATE utf8_general_ci');

      $import_sql = $REX['INCLUDE_PATH'].'/install/redaxo4_5.sql';

      $db = rex_sql::factory();
      foreach($requiredTables as $table)
      $db->setQuery('DROP TABLE IF EXISTS `'. $table .'`');

      if($err_msg == '')
      $err_msg .= rex_setup_import($import_sql);

      if($err_msg == '')
      $err_msg .= rex_setup_addons(true);
    }
    elseif ($dbanlegen == 0)
    {
      // ----- leere Datenbank neu einrichten
      rex_setup_setDBcharset('utf8 COLLATE utf8_general_ci');

      $import_sql = $REX['INCLUDE_PATH'].'/install/redaxo4_5.sql';

      if($err_msg == '')
      $err_msg .= rex_setup_import($import_sql);

      $err_msg .= rex_setup_addons();
    }

    if($err_msg == "" && $dbanlegen !== '')
    {
      // Prüfen, welche Tabellen bereits vorhanden sind
      $existingTables = array();
      foreach(rex_sql::showTables() as $tblname)
      {
        if (substr($tblname, 0, strlen($REX['TABLE_PREFIX'])) == $REX['TABLE_PREFIX'])
        {
          $existingTables[] = $tblname;
        }
      }

      foreach(array_diff($requiredTables, $existingTables) as $missingTable)
      {
        $err_msg .= $I18N->msg('setup_031', $missingTable)."<br />";
      }
    }

    if ($err_msg == "")
    {
      $send = "";
      $checkmodus = 4;
    }
  }

  if ($checkmodus == 3)
  {
    $dbanlegen = rex_post('dbanlegen', 'int', '');
    rex_setup_title($I18N->msg('setup_step3'));

    echo '<div class="rex-form rex-form-setup-step-database">
        <form action="index.php" enctype="multipart/form-data" method="post">
        <fieldset class="rex-form-col-1">
          <input type="hidden" name="page" value="setup" />
          <input type="hidden" name="checkmodus" value="3" />
          <input type="hidden" name="send" value="1" />
          <input type="hidden" name="lang" value="'.htmlspecialchars($lang).'" />

          <legend>'.$I18N->msg('setup_030_headline').'</legend>
            <div class="rex-form-wrapper">
        ';

    if (isset ($err_msg) and $err_msg != '')
    echo rex_warning($err_msg.'<br />'.$I18N->msg('setup_033'));

    $dbchecked = array_fill(0, 6, '');
    switch ($dbanlegen)
    {
      case 1 :
      case 2 :
      case 3 :
      case 4 :
        $dbchecked[$dbanlegen] = ' checked="checked"';
        break;
      default :
        $dbchecked[0] = ' checked="checked"';
    }

    // Vorhandene Exporte auslesen
    $sel_export = new rex_select();
    $sel_export->setName('import_name');
    $sel_export->setId('import_name');
    $sel_export->setStyle('class="rex-form-select"');
    $sel_export->setAttribute('onclick', 'checkInput(\'dbanlegen_3\')');
    $export_dir = getImportDir();
    $exports_found = false;

    if (is_dir($export_dir))
    {
      if ($handle = opendir($export_dir))
      {
        $export_archives = array ();
        $export_sqls = array ();

        while (($file = readdir($handle)) !== false)
        {
          if ($file == '.' || $file == '..')
          {
            continue;
          }

          $isSql = (substr($file, strlen($file) - 4) == '.sql');
          $isArchive = (substr($file, strlen($file) - 7) == '.tar.gz');

          if ($isSql)
          {
            // endung .sql abschneiden
            $export_sqls[] = substr($file, 0, -4);
            $exports_found = true;
          }
          elseif ($isArchive)
          {
            // endung .tar.gz abschneiden
            $export_archives[] = substr($file, 0, -7);
            $exports_found = true;
          }
        }
        closedir($handle);
      }

      foreach ($export_sqls as $sql_export)
      {
        // Es ist ein Export Archiv + SQL File vorhanden
        if (in_array($sql_export, $export_archives))
        {
          $sel_export->addOption($sql_export, $sql_export);
        }
      }
    }

    echo '

    <div class="rex-form-row">
      <p class="rex-form-col-a rex-form-radio rex-form-label-right">
        <input class="rex-form-radio" type="radio" id="dbanlegen_0" name="dbanlegen" value="0"'.$dbchecked[0]. rex_tabindex() .' />
        <label for="dbanlegen_0">'.$I18N->msg('setup_034').'</label>
      </p>
    </div>

    <div class="rex-form-row">
      <p class="rex-form-col-a rex-form-radio rex-form-label-right">
        <input class="rex-form-radio" type="radio" id="dbanlegen_1" name="dbanlegen" value="1"'.$dbchecked[1] .' />
        <label for="dbanlegen_1">'.$I18N->msg('setup_035', '<b>', '</b>').'</label>
      </p>
    </div>

    <div class="rex-form-row">
      <p class="rex-form-col-a rex-form-radio rex-form-label-right">
        <input class="rex-form-radio" type="radio" id="dbanlegen_2" name="dbanlegen" value="2"'.$dbchecked[2] .' />
        <label for="dbanlegen_2">'.$I18N->msg('setup_036').'</label>
      </p>
    </div>

    <div class="rex-form-row">
      <p class="rex-form-col-a rex-form-radio rex-form-label-right">
        <input class="rex-form-radio" type="radio" id="dbanlegen_4" name="dbanlegen" value="4"'.$dbchecked[4] .' />
        <label for="dbanlegen_4">'.$I18N->msg('setup_038').'</label>
      </p>

      <p class="rex-form-col-a rex-form-select rex-form-radio-select">
        <input style="margin-left:25px;" onclick="checkInput(\'dbanlegen_4\')" class="rex-form-file" type="file" id="importdbfile" name="FORM[importfile]" size="18" />
      </p>
    </div>';

    if($exports_found)
    {
      echo '
    <div class="rex-form-row">
      <p class="rex-form-col-a rex-form-radio rex-form-label-right">
        <input class="rex-form-radio" type="radio" id="dbanlegen_3" name="dbanlegen" value="3"'.$dbchecked[3] .' />
        <label for="dbanlegen_3">'.$I18N->msg('setup_037').'</label>
      </p>
      <p class="rex-form-col-a rex-form-select rex-form-radio-select">'. $sel_export->get() .'</p>
    </div>';
    }

    echo '
      </div>
      </fieldset>
      <fieldset class="rex-form-col-1">
        <div class="rex-form-wrapper">
          <div class="rex-form-row">
            <p class="rex-form-col-a rex-form-submit">
              <input class="rex-form-submit" type="submit" value="'.$I18N->msg('setup_039').'"'. rex_tabindex() .' />
            </p>
          </div>
        </div>
      </fieldset>
  </form>
  </div>
  ';
  }

  // ---------------------------------- MODUS 4 | User anlegen ...

  if ($checkmodus == 4 && $send == 1)
  {
    $noadmin           = rex_post('noadmin', 'int');
    $redaxo_user_login = rex_post('redaxo_user_login', 'string');
    $redaxo_user_pass  = rex_post('redaxo_user_pass', 'string');

    $err_msg = "";
    if ($noadmin != 1)
    {
      if ($redaxo_user_login == '')
      {
        $err_msg .= $I18N->msg('setup_040');
      }

      if ($redaxo_user_pass == '')
      {
        // Falls auch kein Login eingegeben wurde, die Fehlermeldungen mit " " trennen
        if($err_msg != '') $err_msg .= ' ';

        $err_msg .= $I18N->msg('setup_041');
      }

      if ($err_msg == "")
      {
        $ga = rex_sql::factory();
        $ga->setQuery("select * from ".$REX['TABLE_PREFIX']."user where login='$redaxo_user_login'");

        if ($ga->getRows() > 0)
        {
          $err_msg .= $I18N->msg('setup_042');
        }
        else
        {
          if ($REX['PSWFUNC'] != '')
          $redaxo_user_pass = call_user_func($REX['PSWFUNC'], $redaxo_user_pass);

          $user = rex_sql::factory();
          // $user->debugsql = true;
          $user->setTable($REX['TABLE_PREFIX'].'user');
          $user->setValue('name', 'Administrator');
          $user->setValue('login', $redaxo_user_login);
          $user->setValue('psw', $redaxo_user_pass);
          $user->setValue('rights', '#admin[]#');
          $user->addGlobalCreateFields('setup');
          $user->setValue('status', '1');
          if (!$user->insert())
          {
            $err_msg .= $I18N->msg("setup_043");
          }
        }
      }
    }
    else
    {
      $gu = rex_sql::factory();
      $gu->setQuery("select * from ".$REX['TABLE_PREFIX']."user LIMIT 1");
      if ($gu->getRows() == 0)
      $err_msg .= $I18N->msg('setup_044');
    }

    if ($err_msg == '')
    {
      $checkmodus = 5;
      $send = '';
    }
  }

  if ($checkmodus == 4)
  {
    $user_sql = rex_sql::factory();
    $user_sql->setQuery("select * from ".$REX['TABLE_PREFIX']."user LIMIT 1");

    rex_setup_title($I18N->msg("setup_step4"));

    echo '
    <div class="rex-form rex-form-setup-admin">
    <form action="index.php" method="post" autocomplete="off">
      <fieldset class="rex-form-col-1">
        <input type="hidden" name="page" value="setup" />
        <input type="hidden" name="checkmodus" value="4" />
        <input type="hidden" name="send" value="1" />
        <input type="hidden" name="lang" value="'.htmlspecialchars($lang).'" />
        <legend>'.$I18N->msg("setup_045").'</legend>
        <div class="rex-form-wrapper">
        ';

    if ($err_msg != "")
    echo rex_warning($err_msg);

    $redaxo_user_login = rex_post('redaxo_user_login', 'string');
    $redaxo_user_pass  = rex_post('redaxo_user_pass', 'string');

    echo '
      <div class="rex-form-row">
        <p class="rex-form-col-a rex-form-text">
          <label for="redaxo_user_login">'.$I18N->msg("setup_046").':</label>
          <input class="rex-form-text" type="text" value="'.htmlspecialchars($redaxo_user_login).'" id="redaxo_user_login" name="redaxo_user_login"'. rex_tabindex() .'/>
        </p>
      </div>
      <div class="rex-form-row">
        <p class="rex-form-col-a rex-form-text">
          <label for="redaxo_user_pass">'.$I18N->msg("setup_047").':</label>
          <input class="rex-form-text" type="password" value="'.htmlspecialchars($redaxo_user_pass).'" id="redaxo_user_pass" name="redaxo_user_pass"'. rex_tabindex() .'/>
        </p>
      </div>';

    if($user_sql->getRows() > 0)
    {
      echo '
      <div class="rex-form-row">
        <p class="rex-form-col-a rex-form-checkbox rex-form-label-right">
          <input class="rex-form-checkbox" type="checkbox" id="noadmin" name="noadmin" value="1"'. rex_tabindex() .'/>
          <label for="noadmin">'.$I18N->msg("setup_048").'</label>
        </p>
      </div>';
    }

    echo '
      </div>
      </fieldset>
      <fieldset class="rex-form-col-1">
        <div class="rex-form-wrapper">
          <div class="rex-form-row">
            <p class="rex-form-col-a rex-form-submit">
              <input class="rex-form-submit" type="submit" value="'.$I18N->msg("setup_049").'"'. rex_tabindex() .' />
            </p>
          </div>
        </div>
      </fieldset>
    </form>
    </div>
    <script type="text/javascript">
       <!--
      jQuery(function($) {
        $("#redaxo_user_login").focus();
      });
       //-->
    </script>';

  }

  // ---------------------------------- MODUS 5 | Setup verschieben ...

  if ($checkmodus == 5)
  {
    $master_file = $REX['INCLUDE_PATH'].'/master.inc.php';
    $cont = rex_get_file_contents($master_file);
    $cont = preg_replace("@(REX\['SETUP'\].?\=.?)[^;]*@", '$1false', $cont);

    if(rex_put_file_contents($master_file, $cont))
    {
      $errmsg = "";
    }
    else
    {
      $errmsg = $I18N->msg('setup_050');
    }

    rex_setup_title($I18N->msg('setup_step5'));
    echo $I18N->msg('setup_051', '<h2 class="rex-hl2">', '</h2>');
    echo '<div class="rex-area-content">';
    echo $I18N->msg('setup_052', '<h3 class="rex-hl3">', '</h3>', ' class="rex-ul1"', '<a href="index.php">', '</a>');
    echo '<p class="rex-tx1">'.$I18N->msg('setup_053').'</p>';
    echo '</div>';

  }
  echo '</div>';
