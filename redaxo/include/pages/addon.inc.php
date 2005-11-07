<?php
/** 
 *  
 * @package redaxo3 
 * @version $Id$ 
 */ 

title($I18N->msg("addon"),"");

$dir = $REX['INCLUDE_PATH']."/addons/";
chdir($dir);
$hdl = opendir(".");
while (false !== ($file = readdir($hdl)))
{
  if($file != ".." AND $file != "." && is_dir($file))
  {
    $ADDONS[] = $file;
  }
}
natsort($ADDONS); // Sortiere Array
chdir("../..");

$SP = true; // SHOW PAGE ADDON LIST
$WA = false;  // WRITE ADDONS TO FILE: include/addons.inc.php

// ----------------- HELPPAGE
if (isset($spage) and $spage == "help" && array_search($addonname,$ADDONS) !== false)
{
  echo '<table class="rex" style="table-layout:auto;" cellpadding="5" cellspacing="1">';
  echo '<tr><th>'.$I18N->msg("addon_help").' '.$addonname.'</th></tr>';
  echo '<tr><td>';
  if (!is_file($REX['INCLUDE_PATH']."/addons/$addonname/help.inc.php")) {
    echo $I18N->msg("addon_no_help_file");
  } else {
    include $REX['INCLUDE_PATH']."/addons/$addonname/help.inc.php";
  }
  echo '&nbsp;</td></tr>';
  echo '<tr><td><a href="index.php?page=addon">'.$I18N->msg("addon_back").'</a></td></tr>';
  echo '</table>';
  $SP = false;
}


// ----------------- FUNCTIONS
if (isset($addonname) and array_search($addonname,$ADDONS) !== false)
{
  // $addonname ist vorhanden
  if (isset($install) and $install == 1)
  {
    //if (!@include $REX['INCLUDE_PATH']."/addons/$addonname/install.inc.php")
    if (!is_readable ($REX['INCLUDE_PATH']."/addons/$addonname/install.inc.php"))
    {
      $errmsg = $I18N->msg("addon_install_not_found");
    } else
    {
      include $REX['INCLUDE_PATH']."/addons/$addonname/install.inc.php";
      if ($REX['ADDON']['install'][$addonname] != 1 || (isset($REX['ADDON']['installmsg'][$addonname]) and $REX['ADDON']['installmsg'][$addonname] != ""))
      {
        $errmsg = "'$addonname' ".$I18N->msg("addon_no_install")."<br/>";
        if ($REX['ADDON']['installmsg'][$addonname] == "") $errmsg .= $I18N->msg("addon_no_reason");
        else $errmsg .= $REX['ADDON']['installmsg'][$addonname];
      } else
      {
        // include config.
        // if config is broken installation prozess will be terminated -> no install -> no errors in redaxo
        
        // skip config if it is a reinstall !
        if ($REX['ADDON']['status'][$addonname] != 1) {
          include $REX['INCLUDE_PATH']."/addons/$addonname/config.inc.php";
        }
        $errmsg = $addonname." ".$I18N->msg("addon_installed");
        $REX['ADDON']['install'][$addonname] = 1;
        $errmsg = $I18N->msg("addon_installed");
        $WA = true;
      }
    }
  } elseif (isset($activate) and $activate == 1)
  {
    if ($REX['ADDON']['install'][$addonname] != 1)
    {
      $errmsg = $I18N->msg("addon_no_activation");
    } else
    {
      $REX['ADDON']['status'][$addonname] = 1;
      $errmsg = $I18N->msg("addon_activated");
      $WA = true;
    }
  } elseif (isset($activate) and $activate == 0)
  {
    $REX['ADDON']['status'][$addonname] = 0;
    $errmsg = $I18N->msg("addon_deactivated");
    $WA = true;
  }
  
}

// ----------------- WRITE INCLUDE/ADDONS FILE
  if ($WA) {
    $wAF_msg = write_AddonFile($ADDONS);
    if ($wAF_msg !== true) {
      echo $I18N->msg($wAF_msg);
    };
  }


// ----------------- OUT
if ($SP)
{

  // Vergleiche Addons aus dem Verzeichnis addons/ mit den Eintraegen in include/addons.inc.php
  // Wenn ein Addon in der Datei fehlt, ergaenze es.
  foreach ($ADDONS as $value) {
    if (!in_array ($value, $REX['ADDON']['install']) or $REX['ADDON']['install'][$value] === '') {
      $WA = true;
    }
  }
  if ($WA) {
    $wAF_msg = write_AddonFile($ADDONS);
    if ($wAF_msg !== true) {
      echo $I18N->msg($wAF_msg);
    };
  }

  if (isset($errmsg) and $errmsg != "") echo '<table border="0" cellpadding="5" cellspacing="1" width="770"><tr><td class="warning">'.$errmsg.'</td></tr></table><br />';
  
  if (!isset($user_id)) { $user_id = ''; }
  echo '<table class="rex" style="table-layout:auto;" cellpadding="5" cellspacing="1">
    <form action="index.php" method="post">
    <input type="hidden" name="page" value="user">
    <input type="hidden" name="user_id" value="'.$user_id.'">
    <tr>
      <th>'.$I18N->msg("addon_hname").'</th>
      <th>'.$I18N->msg("addon_hinstall").'</th>
      <th>'.$I18N->msg("addon_hactive").'</th>
      <th><b>&#160;</b></th>
    </tr>'."\n";
  
  reset ($ADDONS);
  for ($i=0; $i<count($ADDONS); $i++)
  {
    $cur = current($ADDONS);
    if ($REX['ADDON']['install'][$cur] == 1) {
      $install = $I18N->msg("addon_yes").' - <a href="index.php?page=addon&amp;addonname='.$cur.'&amp;install=1">'.$I18N->msg("addon_reinstall").'</a>';
    } else {
      $install = $I18N->msg("addon_no").' - <a href="index.php?page=addon&amp;addonname='.$cur.'&amp;install=1">'.$I18N->msg("addon_install").'</a>';
      $status = $I18N->msg("addon_no");
    }
    if ($REX['ADDON']['status'][$cur] == 1) {
      $status = $I18N->msg("addon_yes").' - <a href="index.php?page=addon&amp;addonname='.$cur.'&amp;activate=0">'.$I18N->msg("addon_deactivate").'</a>';
    } elseif ($REX['ADDON']['install'][$cur] == 1) {
      $status = $I18N->msg("addon_no").' - <a href="index.php?page=addon&amp;addonname='.$cur.'&amp;activate=1">'.$I18N->msg("addon_activate").'</a>';
    }
  
    echo '    <tr>
      <td width="100">'.$cur.' [<a href="index.php?page=addon&amp;spage=help&amp;addonname='.$cur.'">?</a>]</td>
      <td width="100">'.$install.'</td>
      <td width="100">'.$status.'</td>
      <td width="100">&#160;</td>
    </tr>'."\n";
    
    next ($ADDONS);
  }
  echo '</table>';
}


/**
* Schreibt Addoneigenschaften in die Datei include/addons.inc.php
* @param array Array mit den Namen der Addons aus dem Verzeichnis addons/
*/
function write_AddonFile($ADDONS) {
  global $REX;
  
  $fehler_check = false; // (boolean) falls mal etwas zu ueberpruefen ist
  $content = "// --- DYN\n\r";
  natsort($ADDONS);
  reset($ADDONS);
  for ($i=0; $i<count($ADDONS); $i++)
  {
    $cur = current($ADDONS);
    if (!isset ($REX['ADDON']['install'][$cur]) or $REX['ADDON']['install'][$cur] != 1 ) $REX['ADDON']['install'][$cur] = 0;
    if (!isset ($REX['ADDON']['status'][$cur]) or $REX['ADDON']['status'][$cur] != 1 )  $REX['ADDON']['status'][$cur] = 0;
    
    $content .= "
\$REX['ADDON']['install']['$cur'] = ".$REX['ADDON']['install'][$cur].";
\$REX['ADDON']['status']['$cur'] = ".$REX['ADDON']['status'][$cur].";
";
    next($ADDONS);  
  }
  $content .= "\n\r// --- /DYN";

  $file = $REX['INCLUDE_PATH']."/addons.inc.php";
  // Sichergehen, dass die Datei existiert und beschreibbar ist
  if (is_writable($file)) {
  
    if (!$h = fopen($file, "r")) {
      if ($fehler_check) { echo 'Konnte Datei nicht lesen.'."\n"; }
      return 'Konnte Datei nicht lesen.';
    }
    $fcontent = fread($h,filesize($file));
    $fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", $content, $fcontent);
    fclose($h);
  
    if (!$h = fopen($file, "w+")) {
      if ($fehler_check) { echo 'Konnte Datei nicht zum schreiben oeffnen.'."\n"; }
      return 'addon_schreibrecht';
    }
    //if (!fwrite($h, $fcontent, strlen($fcontent))) {
    if (!fwrite($h, trim($fcontent))) {
      if ($fehler_check) { echo 'Konnte Inhalt nicht in Datei schreiben.'."\n"; }
      return 'addon_schreibrecht';
    }
    fclose($h);
    
    // alles ist gut gegangen
    return true;
  } else {
    if ($fehler_check) { echo 'Datei hat keine Schreibrechte.'."\n"; }
    return 'addon_schreibrecht';
  }
  if ($fehler_check) { echo 'Diese Zeile sollte nie erscheinen.'."\n"; }
  return false;
}


?>