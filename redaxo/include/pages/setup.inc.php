<?php

/** 
 *  
 * @package redaxo3
 * @version $Id$
 */ 

// --------------------------------------------- SETUP FUNCTIONS

/**
 * Ausgabe des Setup spezifischen Titels 
 */
function rex_setuptitle($title)
{
  rex_title($title,"");

  echo "
  <table border=0 cellpadding=5 cellspacing=1 width=770>
  <tr><td class=lgrey><font class=content>";

}

/**
 * Prüfen ob ein/e Datei/Ordner beschreibbar ist 
 */
function rex_is_writable($item)
{
  global $I18N;
  
  $state = true;
  
  // Fehler unterdrücken, falls keine Berechtigung
  if (@is_dir($item))
  {
    if (!@ is_writable($item."/."))
    {
      $state = $I18N->msg("setup_012", rex_absPath($item));
    }
  }
  // Fehler unterdrücken, falls keine Berechtigung
  elseif (@is_file($item))
  {
    if (!@ is_writable($item))
    {
      $state = $I18N->msg("setup_014", rex_absPath($item));
    }
  }
  else
  {
    $state = $I18N->msg("setup_015", rex_absPath($item));
  }
  
  return $state;
}

// --------------------------------------------- END: SETUP FUNCTIONS

echo "
<style type=text/css>
  .ok   { color:#3EC94A; }
  .nice   { color:#33aa33; }
  .error    { color:#cc3333; }
</style>";

$MSG['err'] = "";
$MSG['good'] = "";

if (!isset ($checkmodus)) $checkmodus = '';
if (!isset ($send)) $send = '';
if (!isset ($dbanlegen)) $dbanlegen = '';
if (!isset ($noadmin)) $noadmin = '';


// ---------------------------------- MODUS 0 | Start
if (!($checkmodus>0 && $checkmodus<10))
{
  rex_setuptitle("SETUP: SELECT LANGUAGE");

  echo "<center><table><tr><td>";
  echo "<br><br><b><a href=index.php?checkmodus=0.5&lang=de_de class=head>&gt;&nbsp;DEUTSCH</a></b>";
  echo "<br><br><b><a href=index.php?checkmodus=0.5&lang=en_gb class=head>&gt;&nbsp;ENGLISH</a></b>";
  echo "<br><br><b><a href=index.php?checkmodus=0.5&lang=es_es class=head>&gt;&nbsp;ESPANIOL</a></b>";
  echo "<br><br><b><a href=index.php?checkmodus=0.5&lang=pl_pl class=head>&gt;&nbsp;POLSKI</a></b>";
  echo "<br><br><b><a href=index.php?checkmodus=0.5&lang=tr_tr class=head>&gt;&nbsp;TURKYE</a></b>";
  echo "<br><br>";
  echo "</td></tr></table></center>";
}


// ---------------------------------- MODUS 0 | Start

if ($checkmodus=="0.5")
{
  rex_setuptitle("SETUP: START");

  echo $I18N->msg("setup_005");

  echo "<br><br>";
  echo "<div id=lizenz style='width:500px; height:200px; overflow:auto; background-color:#ffffff; text-align:left; font-size:9px;'>";
  
  $Basedir = dirname( __FILE__);
  $license_file = $Basedir .'/../../../_lizenz.txt';
  $hdl = fopen( $license_file, 'r');
  $license = nl2br( fread ( $hdl, filesize( $license_file)));
  fclose( $hdl);
  echo $license;

  echo "</div>";

  echo "<br><br><a href=index.php?page=setup&checkmodus=1&lang=$lang>&raquo; ".$I18N->msg("setup_006")."</a><br><br>";

  $checkmodus = 0;
}


// ---------------------------------- MODUS 1 | Versionscheck - Rechtecheck

if($checkmodus == 1)
{

  // -------------------------- VERSIONSCHECK

  if(version_compare(phpversion(), "4.2.0", "<") == 1)
  {
    $MSG['err'] .= $I18N->msg("setup_010")."<br>";
  }

  // -------------------------- SCHREIBRECHTE

  $WRITEABLE = array($REX['INCLUDE_PATH']."/master.inc.php",
         $REX['INCLUDE_PATH']."/addons.inc.php",
         $REX['INCLUDE_PATH']."/clang.inc.php",
         $REX['INCLUDE_PATH']."/ctype.inc.php",
         $REX['INCLUDE_PATH']."/generated",
         $REX['INCLUDE_PATH']."/generated/articles",
         $REX['INCLUDE_PATH']."/generated/templates",
         $REX['INCLUDE_PATH']."/addons/stats/logs",
         $REX['INCLUDE_PATH']."/generated/files",
         $REX['INCLUDE_PATH']."/addons/import_export/files",
         $REX['INCLUDE_PATH']."/../../files");
               
  foreach($WRITEABLE as $item)
  {
    if (($_msg = rex_is_writable( $item)) !== true)
    {
      $MSG['err'] .= $_msg ."<br>";
    }
  }
}

if ($MSG['err']=="" && $checkmodus == 1)
{
  rex_setuptitle($I18N->msg("setup_step1"));
  
  echo $I18N->msg("setup_016");
  echo "<br><br><a href=index.php?page=setup&checkmodus=2&lang=$lang>&raquo; ".$I18N->msg("setup_017")."</a><br><br>";

}elseif($MSG['err']!="")
{

  rex_setuptitle($I18N->msg("setup_step1"));

  echo "<b>".$I18N->msg("setup_headline1")."</b><br><br>".$MSG['err']."

  <br>".$I18N->msg("setup_018")."<br><br>
  <a href=index.php?page=setup&checkmodus=1&lang=$lang>&raquo; ".$I18N->msg("setup_017")."</a><br><br>";
}


// ---------------------------------- MODUS 2 | master.inc.php - Datenbankcheck

if ($checkmodus == 2 && $send == 1)
{
  $h = @fopen($REX['INCLUDE_PATH']."/master.inc.php","r");
  $cont = fread($h,filesize("include/master.inc.php"));
  $cont = ereg_replace("(REX\['SERVER'\].?\=.?\")[^\"]*","\\1".$serveraddress,$cont);
  $cont = ereg_replace("(REX\['SERVERNAME'\].?\=.?\")[^\"]*","\\1".$serverbezeichnung,$cont);
  $cont = ereg_replace("(REX\['LANG'\].?\=.?\")[^\"]*","\\1".$lang,$cont);
  $cont = ereg_replace("(REX\['INSTNAME'\].?\=.?\")[^\"]*","\\1"."rex".date("YmdHis"),$cont);
  $cont = ereg_replace("(REX\['error_emailaddress'\].?\=.?\")[^\"]*","\\1".$error_email,$cont);
  $cont = ereg_replace("(DB\['1'\]\['HOST'\].?\=.?\")[^\"]*","\\1".$mysql_host,$cont);
  $cont = ereg_replace("(DB\['1'\]\['LOGIN'\].?\=.?\")[^\"]*","\\1".$redaxo_db_user_login,$cont);
  $cont = ereg_replace("(DB\['1'\]\['PSW'\].?\=.?\")[^\"]*","\\1".$redaxo_db_user_pass,$cont);
  $cont = ereg_replace("(DB\['1'\]\['NAME'\].?\=.?\")[^\"]*","\\1".$dbname,$cont);
  
  fclose($h);

  $h = @fopen($REX['INCLUDE_PATH']."/master.inc.php","w+");
  if(fwrite($h,$cont,strlen($cont)) > 0)
  {
  }else
  {
    $err_msg = $I18N->msg("setup_020");
  }

  // -------------------------- DATENBANKZUGRIFF
  $link = @mysql_connect($mysql_host, $redaxo_db_user_login, $redaxo_db_user_pass);
  if(!$link)
  {
    $err_msg = $I18N->msg("setup_021")."<br>";
  }elseif(!@mysql_select_db($dbname, $link))
  {
    $err_msg = $I18N->msg("setup_022")."<br>";
  }elseif($link)
  {
    $DB[1]['NAME'] = $dbname;
    $DB[1]['LOGIN'] = $redaxo_db_user_login;
    $DB[1]['PSW'] = $redaxo_db_user_pass;
    $DB[1]['HOST'] = $mysql_host;

    $err_msg = "";
    $checkmodus = 3;
    $send = "";
  }
  @mysql_close($link);

}else
{
  $serveraddress = $REX['SERVER'];
  $serverbezeichnung = $REX['SERVERNAME'];
  $error_email = $REX['error_emailaddress'];
  $dbname = $DB[1]['NAME'];
  $redaxo_db_user_login = $DB[1]['LOGIN'];
  $redaxo_db_user_pass = $DB[1]['PSW'];
  $mysql_host = $DB[1]['HOST'];
}

if ($checkmodus == 2 )
{

  rex_setuptitle($I18N->msg("setup_step2"));

  echo "<b>".$I18N->msg("setup_023")."</b><br><br>
    <table border=0 cellpadding=5 cellspacing=0 width=500>
    <form action=index.php method=post>
    <input type=hidden name=page value=setup>
    <input type=hidden name=checkmodus value=2>
    <input type=hidden name=send value=1>
    <input type=hidden name=lang value=$lang>
    ";

  if(isset($err_msg) and $err_msg != '') echo "<tr><td class=warning colspan=2>$err_msg</td></tr><tr><td></td></tr>";

  echo "
    <tr><td colspan=2>// ---- ".$I18N->msg("setup_0201")."</td></tr>
    <tr><td width=200><label for='serveraddress'>".$I18N->msg("setup_024")."</label></td><td><input type=text id=serveraddress name=serveraddress value='$serveraddress' class=inp100></td></tr>
    <tr><td><label for='serverbezeichnung'>".$I18N->msg("setup_025")."</label></td><td><input type=text id=serverbezeichnung name=serverbezeichnung value='$serverbezeichnung' class=inp100></td></tr>
    <tr><td><label for='error_email'>".$I18N->msg("setup_026")."</label></td><td><input type=text id=error_email name=error_email value='$error_email' class=inp100></td></tr>
    <tr><td colspan=2><br>// ---- ".$I18N->msg("setup_0202")."</td></tr>
    <tr><td><label for='dbname'>".$I18N->msg("setup_027")."</label></td><td><input type=text class=inp100 value='$dbname' id=dbname name=dbname></td></tr>
    <tr><td><label for='mysql_host'>MySQL Host</label></td><td><input type=text id=mysql_host name=mysql_host value='$mysql_host' class=inp100></td></tr>
    <tr><td><label for='redaxo_db_user_login'>Login</label></td><td><input type=text id=redaxo_db_user_login name=redaxo_db_user_login value='$redaxo_db_user_login' class=inp100></td></tr>
    <tr><td><label for='redaxo_db_user_pass'>".$I18N->msg("setup_028")."</label></td><td><input type=text id=redaxo_db_user_pass name=redaxo_db_user_pass value='$redaxo_db_user_pass' class=inp100></td></tr>
    <tr><td>&nbsp;</td><td valign=middle><input type=submit value='".$I18N->msg("setup_029")."'></td></tr>
    </table>";
  echo "<br>";
}


// ---------------------------------- MODUS 3 | Datenbank anlegen ...

if ($checkmodus == 3 && $send == 1)
{
  $err_msg = '';
  if ($dbanlegen == 3)
  {
    // ----- Demo installieren
    // ----- db
    $file_temp = $REX['INCLUDE_PATH']."/install/rex_3.1_simple_demo.sql";
    $h = fopen($file_temp,"r");
    $conts = fread($h,filesize($file_temp));
    $conts = str_replace("## Redaxo Database Dump Version ".$REX['version']." \n","",$conts);
    $all = explode("\n",$conts);
    $add = new sql;
    foreach($all as $hier){
      $add->setquery(Trim(str_replace("||||||+N+||||||","\n",$hier),";"));
      $add->flush();
    }
    $msg = $I18N->msg("database_imported").". ".$I18N->msg("entry_count",count($all))."<br>";
    unset($REX['CLANG']);
    $gl = new sql;
    $gl->setQuery("select * from rex_clang");
    for ($i=0;$i<$gl->getRows();$i++)
    {
      $id = $gl->getValue("id");
      $name = $gl->getValue("name");
      $REX['CLANG'][$id] = $name;
      $gl->next();
    }
    $msg .= rex_generateAll();
    // ----- files
    include_once $REX['INCLUDE_PATH']. '/addons/import_export/classes/class.tar.inc.php';
    include_once $REX['INCLUDE_PATH']. '/addons/import_export/functions/function_folder.inc.php';
    $tar = new tar;
    $file_temp = $REX['INCLUDE_PATH']."/install/rex_3.1_simple_demo.tar.gz";
    $tar->openTAR($file_temp);
    if(!$tar->extractTar())
    {
      $err_msg .= "error";
    }

  }else if ($dbanlegen == 2)
  {
    // ----- Keine Datenbank anlegen
    $TBLS = array(
    "rex_action" => 0,
    "rex_article" => 0,
    "rex_article_slice" => 0,
    "rex_article_type" => 0,
    "rex_clang" => 0,
    "rex_file" => 0,
    "rex_file_category" => 0,
    "rex_help" => 0,
    "rex_module_action" => 0,
    "rex_modultyp" => 0,
    "rex_template" => 0,
    "rex_user" => 0);

    $gt = new sql;
    // $gt->debugsql = 1;
    $gt->setQuery("show tables");
    for ($i=0;$i<$gt->getRows();$i++,$gt->next())
    {
      $tblname = $gt->getValue("Tables_in_".$DB[1]['NAME']);
      if (substr($tblname,0,4)=="rex_")
      {
        // echo $tblname."<br>";
        if (array_key_exists("$tblname",$TBLS))
        {
          $TBLS["$tblname"] = 1;
        }
      }
    }

    for ($i=0;$i<count($TBLS);$i++)
    {
      if (current($TBLS)!=1) $err_msg .= $I18N->msg("setup_031",key($TBLS))."<br>";
      next($TBLS);
    }

  }elseif($dbanlegen == 1)
  {

    // ----- leere Datenbank und alte DB löschen / drop
    $fname = $REX['INCLUDE_PATH']."/install/redaxo3_0_with_drop.sql";
    $h = fopen($fname,"r");
    $create = fread($h,filesize($fname));
    $link = mysql_connect($DB[1]['HOST'],$DB[1]['LOGIN'],$DB[1]['PSW']);
    $lines = explode(";",$create);
    array_pop($lines);
    foreach($lines as $line)
    if(!mysql_db_query($DB[1]['NAME'],$line,$link))
    {
      $err_msg .= $I18N->msg("setup_032")." MySQL: ".mysql_error()."<br>";
    }
  }elseif($dbanlegen == 0)
  {
    // ----- leere Datenbank und alte DB lassen
    $fname = $REX['INCLUDE_PATH']."/install/redaxo3_0_without_drop.sql";
    $h = fopen($fname,"r");
    $create = fread($h,filesize($fname));
    $link = mysql_connect($DB[1]['HOST'],$DB[1]['LOGIN'],$DB[1]['PSW']);
    $lines = explode(";",$create);
    array_pop($lines);
    foreach($lines as $line)
    if(!mysql_db_query($DB[1]['NAME'],$line,$link))
    {
      $err_msg .= $I18N->msg("setup_032")." MySQL: ".mysql_error()."<br>";
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

  rex_setuptitle($I18N->msg("setup_step3"));

  echo "<b>Datenbank anlegen</b><br><br>
    <table border=0 cellpadding=5 cellspacing=0 width=100%>
    <form action=index.php method=post>
    <input type=hidden name=page value=setup>
    <input type=hidden name=checkmodus value=3>
    <input type=hidden name=send value=1>
    <input type=hidden name=lang value=$lang>
    ";

  if (isset($err_msg) and $err_msg != '') echo "<tr><td class=warning colspan=2>$err_msg<br>".$I18N->msg("setup_033")."</td></tr><tr><td></td></tr>";

  if (!isset ($dbchecked0)) $dbchecked0 = '';
  if (!isset ($dbchecked1)) $dbchecked1 = '';
  if (!isset ($dbchecked2)) $dbchecked2 = '';
  if (!isset ($dbchecked3)) $dbchecked3 = '';
  switch ($dbanlegen) {
    case 1: $dbchecked1 = " checked"; break;
    case 2: $dbchecked2 = " checked"; break;
    case 3: $dbchecked3 = " checked"; break;
    default: $dbchecked0 = " checked";
  }
/*  if ($dbanlegen == 1) $dbchecked1 = " checked";
  elseif ($dbanlegen == 2) $dbchecked2 = " checked";
  elseif ($dbanlegen == 3) $dbchecked3 = " checked";
  else $dbchecked0 = " checked"; */

  echo "
    <tr>
      <td width=50 align=right><input type=radio id=dbanlegen[0] name=dbanlegen value=0 $dbchecked0></td>
      <td><label for='dbanlegen[0]'>".$I18N->msg("setup_034")."</label></td>
    </tr>
    <tr>  <td align=right><input type=radio id=dbanlegen[1] name=dbanlegen value=1 $dbchecked1></td>
      <td><label for='dbanlegen[1]'>".$I18N->msg("setup_035")."</label></td>
    </tr>
    <tr>
      <td align=right><input type=radio id=dbanlegen[2] name=dbanlegen value=2 $dbchecked2></td>
      <td><label for='dbanlegen[2]'>".$I18N->msg("setup_036")."</label></td>
    </tr>
    <tr>
      <td align=right><input type=radio id=dbanlegen[3] name=dbanlegen value=3 $dbchecked3></td>
      <td><label for='dbanlegen[3]'>".$I18N->msg("setup_037")."</label></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign=middle><input type=submit value='".$I18N->msg("setup_038")."'></td>
    </tr>
    </table><br>";

}


// ---------------------------------- MODUS 4 | User anlegen ...

if ($checkmodus == 4 && $send == 1)
{
  $err_msg = "";
  if ($noadmin != 1)
  {
    if ($redaxo_user_login == '')
    {
      $err_msg .= $I18N->msg("setup_040")."<br>";
    }
    if ($redaxo_user_pass == '')
    {
      $err_msg .= $I18N->msg("setup_041")."<br>";
    }

    if($err_msg == "")
    {
      $ga = new sql;
      $ga->setQuery("select * from rex_user where login='$redaxo_user_login'");

      if ($ga->getRows()>0)
      {
        $err_msg = $I18N->msg("setup_042");
      }else
      {
        $insert = "INSERT INTO rex_user (name,login,psw,rights) VALUES ('Administrator','$redaxo_user_login','$redaxo_user_pass','admin[]dev[]import[]stats[]moveSlice[]')";
        $link = @mysql_connect($DB[1]['HOST'],$DB[1]['LOGIN'],$DB[1]['PSW']);
        if(!@mysql_db_query($DB[1]['NAME'],$insert,$link))
        {
          $err_msg .= $I18N->msg("setup_043")."<br>";
        }
      }
    }
  }else
  {
    $gu = new sql;
    $gu->setQuery("select * from rex_user LIMIT 1");
    if ($gu->getRows()==0) $err_msg .= $I18N->msg("setup_044")."<br>";
    
  }

  if ($err_msg == "")
  {
    $checkmodus = 5;
    $send = "";
  }

}

if ($checkmodus == 4)
{

  rex_setuptitle($I18N->msg("setup_step4"));

  echo "<b>".$I18N->msg("setup_045")."</b><br><br>
    <table border=0 cellpadding=5 cellspacing=0 width=500>
    <form action=index.php method=post>
    <input type=hidden name=page value=setup>
    <input type=hidden name=checkmodus value=4>
    <input type=hidden name=send value=1>
    <input type=hidden name=lang value=$lang>
    ";

  if($err_msg!="") echo "<tr><td class=warning colspan=2>$err_msg</td></tr><tr><td></td></tr>";

  if ($dbanlegen == 1) $dbchecked1 = " checked";
  elseif ($dbanlegen == 2) $dbchecked2 = " checked";
  else $dbchecked0 = " checked";

  if (!isset ($redaxo_user_login)) $redaxo_user_login = '';
  if (!isset ($redaxo_user_pass)) $redaxo_user_pass = '';
  echo "

    <tr><td><label for='redaxo_user_login'>".$I18N->msg("setup_046").":</label></td><td><input type=text class=inp100 value=\"$redaxo_user_login\" id=redaxo_user_login name=redaxo_user_login></td></tr>
    <tr><td><label for='redaxo_user_pass'>".$I18N->msg("setup_047").":</label></td><td><input type=text class=inp100 value=\"$redaxo_user_pass\" id=redaxo_user_pass name=redaxo_user_pass></td></tr>
    <tr><td align=right><input type=checkbox id=noadmin name=noadmin value=1></td><td><label for='noadmin'>".$I18N->msg("setup_048")."</label></td></tr>
    <tr><td>&nbsp;</td><td valign=middle><input type=submit value='".$I18N->msg("setup_049")."'></td></tr>
    </table>";

  echo "<br>";

}


// ---------------------------------- MODUS 5 | Setup verschieben ...

if ($checkmodus == 5)
{

  $h = @fopen($REX['INCLUDE_PATH']."/master.inc.php","r");
  $cont = fread($h,filesize($REX['INCLUDE_PATH']."/master.inc.php"));
  $cont = ereg_replace("(REX\['SETUP'\].?\=.?)[^;]*","\\1"."false",$cont);
  fclose($h);
  $h = @fopen($REX['INCLUDE_PATH']."/master.inc.php","w+");
  if(fwrite($h,$cont,strlen($cont)) > 0)
  {
    $errmsg = "";
  }else
  {
    $errmsg = $I18N->msg("setup_050");
  }

  // generate all articles,cats,templates,caches
  // generateAll();
  rex_setuptitle($I18N->msg("setup_step5"));
  echo "".$I18N->msg("setup_051")."";

}

echo "</font></td></tr></table>";

?>