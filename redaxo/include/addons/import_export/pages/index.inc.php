<?php


/** 
 *  
 * @package redaxo3 
 * @version $Id$ 
 */

// Für größere Exports den Speicher für PHP erhöhen.

@ ini_set('memory_limit', '32M');

// ------- Addon Includes
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/classes/class.tar.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_import_export.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_folder.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_import_folder.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_string.inc.php';

// ------------------------------ FUNC
$msg = "";

if (isset ($impname) && $impname != '')
{
  $impname = str_replace("/", "", $impname);

  if ($function == "dbimport" && substr($impname, -4, 4) != ".sql")
    $impname = "";
  elseif ($function == "fileimport" && substr($impname, -7, 7) != ".tar.gz") $impname = "";

}

if (!isset ($exportfilename) || $exportfilename == '')
  $exportfilename = 'rex_'.$REX['VERSION'].'_'.date("Ymd");

if (isset ($function) && $function == "delete")
{

  // ------------------------------ FUNC DELETE

  if (unlink($REX['INCLUDE_PATH']."/addons/$page/files/$impname"));
  $msg = $I18N_IM_EXPORT->msg("file_deleted");

}
elseif (isset ($function) && $function == "dbimport")
{

  // ------------------------------ FUNC DBIMPORT

  // noch checken das nicht alle tabellen geloescht werden
  // install/temp.sql aendern

  if (isset ($_FILES['FORM']) && $_FILES['FORM']['size']['importfile'] < 1 && $impname == "")
  {
    $msg = $I18N_IM_EXPORT->msg("no_import_file_chosen_or_wrong_version")."<br>";
  }
  else
  {
    if ($impname != "")
    {
      $file_temp = $REX['INCLUDE_PATH']."/addons/$page/files/$impname";
    }
    else
    {
      $file_temp = $REX['INCLUDE_PATH']."/addons/$page/files/sql.temp";
    }

    if ($impname != "" || @ move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'], $file_temp))
    {
      $state = rex_a1_import_db($file_temp);
      $msg = $state['message'];

      // temp datei löschen
      if ($impname == "")
      {
        @ unlink($file_temp);
      }
    }
    else
    {
      $msg = $I18N_IM_EXPORT->msg("file_could_not_be_uploaded")." ".$I18N_IM_EXPORT->msg("you_have_no_write_permission_in", "addons/$page/files/")." <br>";
    }
  }

}
elseif (isset ($function) && $function == "fileimport")
{

  // ------------------------------ FUNC FILEIMPORT

  if (isset ($_FILES['FORM']) && $_FILES['FORM']['size']['importfile'] < 1 && $impname == "")
  {
    $msg = $I18N_IM_EXPORT->msg("no_import_file_chosen")."<br>";
  }
  else
  {
    if ($impname != "")
    {
      $file_temp = $REX['INCLUDE_PATH']."/addons/$page/files/$impname";
    }
    else
    {
      $file_temp = $REX['INCLUDE_PATH']."/addons/$page/files/tar.temp";
    }

    if ($impname != "" || @ move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'], $file_temp))
    {
      $state = rex_a1_import_files($file_temp);
      $msg = $state['message'];

      // temp datei löschen
      if ($impname == "")
      {
        @ unlink($file_temp);
      }
    }
    else
    {
      $msg = $I18N_IM_EXPORT->msg("file_could_not_be_uploaded")." ".$I18N_IM_EXPORT->msg("you_have_no_write_permission_in", "addons/$page/files/")." <br>";
    }
  }

}
elseif (isset ($function) && $function == "export")
{

  // ------------------------------ FUNC EXPORT 

  $exportfilename = strtolower($exportfilename);
  $exportfilename = stripslashes($exportfilename);
  $filename = ereg_replace("[^\.a-z0-9_\-]", "", $exportfilename);

  if ($filename != $exportfilename)
  {
    $msg = $I18N_IM_EXPORT->msg("filename_updated");
    $exportfilename = $filename;
  }
  else
  {
    $content = "";
    if ($exporttype == "sql")
    {
      // ------------------------------ FUNC EXPORT SQL    
      $header = "plain/text";
      $ext = ".sql";

      $content = rex_a1_export_db();
      // ------------------------------ /FUNC EXPORT SQL    
    }
    elseif ($exporttype == "files")
    {
      // ------------------------------ FUNC EXPORT FILES
      $header = "tar/gzip";
      $ext = ".tar.gz";

      if ($EXPDIR == "")
      {
        $msg = $I18N_IM_EXPORT->msg("please_choose_folder");
      }
      else
      {
        $content = rex_a1_export_files($EXPDIR, $filename);
      }
      // ------------------------------ /FUNC EXPORT FILES
    }

    if ($content != "" && $exportdl == 1)
    {
      $filename = $filename.$ext;
      header("Content-type: $header");
      header("Content-Disposition: attachment; filename=$filename");
      echo $content;
      exit;

    }
    elseif ($content != "")
    {
      // check filename ob vorhanden
      // aendern filename
      // speicher content in files

      $dir_filename = $REX['INCLUDE_PATH']."/addons/$page/files/";
      $filename = $dir_filename.$filename;

      if (file_exists($filename.$ext))
      {
        for ($i = 0; $i < 1000; $i++)
        {
          if (!file_exists($filename."_$i".$ext))
          {
            $filename = $filename."_$i".$ext;
            break;
          }
        }
      }
      else
      {
        $filename .= $ext;
      }

      if (is_writable($dir_filename) && $fp = fopen($filename, "w"))
      {
        fputs($fp, $content);
        fclose($fp);
        $msg = $I18N_IM_EXPORT->msg('file_generated_in').' '.$filename;
      }
      else
      {
        $msg = $I18N_IM_EXPORT->msg('file_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').' '.$REX['INCLUDE_PATH']."/addons/$page/files";
      }
      // echo $content;
    }
  }
}

include $REX['INCLUDE_PATH']."/layout/top.php";
rex_title($I18N_IM_EXPORT->msg("importexport"), "");
if ($msg != '')
  echo "<table border=0 cellpadding=5 cellspacing=1 width=770><tr><td class=warning>$msg</td></tr></table><br>";
?>

<table width="770" cellspacing="1" border="0" cellpadding="5">

<tr>
  <th width="50%" align="left"><?php echo $I18N_IM_EXPORT->msg('import'); ?></th>
  <th align="left"><?php echo $I18N_IM_EXPORT->msg('export'); ?></th>
</tr>


<tr>
  <td valign="top" class="dgrey"><?php


// ----------------------------------------------------------------- IMPORT

// DB IMPORT
echo "<br />".$I18N_IM_EXPORT->msg("intro_import")."  
  
  <br><br><table width=100% border=0 cellspacing=1 cellpadding=4 bgcolor=#ffffff>
  <tr><td align=left colspan=2 class=lgrey>".$I18N_IM_EXPORT->msg("database")."</td>
  <form action=index.php method=post enctype='multipart/form-data'>
  <input type=hidden name=page value=$page>
  <input type=hidden name=function value=dbimport>
  <tr>
    <td class=lgrey><input type=file name=FORM[importfile]></td>
    <td class=lgrey width=129><input type=submit value='".$I18N_IM_EXPORT->msg("db_import")."'></td>
  </tr>
  </form>
  </table>";

echo "<br><table width=100% border=0 cellspacing=1 cellpadding=4 bgcolor=#ffffff>";
echo "<tr>
      <td align=left class=lgrey>".$I18N_IM_EXPORT->msg("filename")."</td>
      <td width=60 class=lgrey>".$I18N_IM_EXPORT->msg("createdate")."</td>
      <td width=60 class=lgrey>&nbsp;</td>
      <td width=60 class=lgrey>&nbsp;</td>";

// DB IMPORT LIST
// all files in files with .sql als endung
$dir = getImportDir();
$folder = readImportFolder(".sql");

foreach ($folder as $file)
{
  $filepath = $dir.'/'.$file;
  $filec = date("d.m.Y H:i", filemtime($filepath));
  echo "<tr>
                <td class=lgrey><b>$file</b></td>
                <td class=lgrey>$filec</td>
                <td class=lgrey><a href=index.php?page=$page&amp;function=dbimport&amp;impname=$file title='".$I18N_IM_EXPORT->msg('import_file')."' onclick=\"return confirm('".$I18N_IM_EXPORT->msg('proceed_db_import')."')\">".$I18N_IM_EXPORT->msg("import")."</a></td>
                <td class=lgrey><a href=index.php?page=$page&amp;function=delete&amp;impname=$file title='".$I18N_IM_EXPORT->msg('delete_file')."' onclick=\"return confirm('".$I18N->msg('delete')." ?')\">".$I18N_IM_EXPORT->msg("delete")."</a></td></tr>";
}
echo "</table>";

// FILE IMPORT
echo "<br><table width=100% border=0 cellspacing=1 cellpadding=4 bgcolor=#ffffff>
  <tr><td align=left colspan=2 class=lgrey>".$I18N_IM_EXPORT->msg("files")."</td>
  <form action=index.php method=post enctype='multipart/form-data'>
  <input type=hidden name=page value=$page>
  <input type=hidden name=function value=fileimport>
  <tr>
    <td class=lgrey><input type=file name=FORM[importfile]></td>
    <td class=lgrey width=130><input type=submit value='".$I18N_IM_EXPORT->msg("db_import")."'></td>
  </tr>
  </form>
  </table>";

echo "<br><table width=100% border=0 cellspacing=1 cellpadding=4 bgcolor=#ffffff>"."\n";
echo "<tr>
      <td align=left class=lgrey>".$I18N_IM_EXPORT->msg("filename")."</td>
      <td width=60 class=lgrey>".$I18N_IM_EXPORT->msg("createdate")."</td>
      <td width=60 class=lgrey>&nbsp;</td>
      <td width=60 class=lgrey>&nbsp;</td>"."\n";

// FILE IMPORT LIST
// all files in files with .tar.gz als endung

$dir = getImportDir();
$folder = readImportFolder(".tar.gz");

foreach ($folder as $file)
{
  $filepath = $dir.'/'.$file;
  $filec = date("d.m.Y H:i", filemtime($filepath));
  echo "<tr>
                <td class=lgrey><b>$file</b></td>
                <td class=lgrey>$filec</td>
                <td class=lgrey><a href=index.php?page=$page&function=fileimport&impname=$file title='".$I18N_IM_EXPORT->msg('import_file')."' onclick=\"return confirm('".$I18N_IM_EXPORT->msg('proceed_file_import')."')\">".$I18N_IM_EXPORT->msg("import")."</a></td>
                <td class=lgrey><a href=index.php?page=$page&function=delete&impname=$file title='".$I18N_IM_EXPORT->msg('delete_file')."' onclick=\"return confirm('".$I18N->msg('delete')." ?')\">".$I18N_IM_EXPORT->msg("delete")."</a></td></tr>";
}

echo "</table><br />"."\n";

// ----------------------------------------------------------------- /IMPORT
?></td><td valign="top" class="dgrey"><?php


// ----------------------------------------------------------------- EXPORT

echo "<br />".$I18N_IM_EXPORT->msg("intro_export")."<br /><br />"."\n";

echo "<table width=100% border=0 cellspacing=1 cellpadding=4 bgcolor=#ffffff>
  
  <form action=index.php method=post enctype='multipart/form-data'>
  <input type=hidden name=page value=$page>
  <input type=hidden name=function value=export>  
  "."\n";

$checkedsql = "";
$checkedfiles = "";

if (isset ($exporttype) and $exporttype == "files")
  $checkedfiles = " checked";
else
  $checkedsql = " checked";

echo "<tr>"."\n";
echo "  <td class=lgrey width=30><input type=radio id=exporttype[sql] name=exporttype value=sql $checkedsql></td>"."\n";
echo "  <td class=lgrey><label for=exporttype[sql]>".$I18N_IM_EXPORT->msg("database_export")."</label></td>"."\n";
echo "</tr>"."\n";

echo "<tr>"."\n";
echo "  <td class=lgrey><input type=radio id=exporttype[files] name=exporttype value=files $checkedfiles></td>"."\n";
echo "  <td class=lgrey><label for=exporttype[files]>".$I18N_IM_EXPORT->msg("file_export")."</label></td>"."\n";
echo "</tr>"."\n";

echo "<tr><td class=grey>&nbsp;</td><td class=lgrey><table width=100%>"."\n";
// FILE EXPORT LIST
// all folders of the webpage except the cms dir

$dir = $REX['INCLUDE_PATH']."/../..";
$folders = readSubFolders($dir);

foreach ($folders as $file)
{
  if ($file == 'redaxo')
  {
    continue;
  }

  $checked = "";
  if (isset ($EXPDIR) and is_Array($EXPDIR))
    if (array_key_exists($file, $EXPDIR) !== false)
      $checked = " checked";
  echo "<tr>"."\n";
  echo "  <td class=lgrey width=30><input type=checkbox onchange=\"checkInput('exporttype[files]');\" id=EXPDIR[$file] name=EXPDIR[$file] value=true $checked></td>"."\n";
  echo "  <td class=lgrey><label for=EXPDIR[$file]>$file</label></td>"."\n";
  echo "</tr>"."\n";
}

echo "</table></td></tr>";

$checked0 = "";
$checked1 = "";

if (isset ($exportdl) and $exportdl == 1)
  $checked1 = " checked";
else
  $checked0 = " checked";

echo "<tr>"."\n";
echo "  <td class=lgrey><input type=radio id=exportdl[server] name=exportdl value=0 $checked0></td>"."\n";
echo "  <td class=lgrey><label for=exportdl[server]>".$I18N_IM_EXPORT->msg("save_on_server")."</label></td>"."\n";
echo "</tr>"."\n";
echo "<tr>"."\n";
echo "  <td class=lgrey><input type=radio id=exportdl[download] name=exportdl value=1 $checked1></td>"."\n";
echo "  <td class=lgrey><label for=exportdl[download]>".$I18N_IM_EXPORT->msg("download_as_file")."</label></td>"."\n";
echo "</tr>"."\n";
echo "<tr>"."\n";
echo "  <td class=lgrey></td>"."\n";
echo "  <td class=lgrey><input type=text size=20 name=exportfilename class=inp100 value='$exportfilename'></td>"."\n";
echo "</tr>"."\n";

echo "<tr>"."\n";
echo "  <td class=lgrey></td>"."\n";
echo "  <td class=lgrey><input type=submit value='".$I18N_IM_EXPORT->msg("db_export")."'></td>"."\n";
echo "</tr>"."\n";

echo "</form>"."\n";
echo "</table><br />"."\n";

// ----------------------------------------------------------------- /EXPORT
?></td>
</tr>
</table>




<?php include $REX['INCLUDE_PATH']."/layout/bottom.php"; ?>