<?php


/**
 *
 * @package redaxo4
 * @version $Id: index.inc.php,v 1.10 2008/04/02 15:49:37 kills Exp $
 */

// Für größere Exports den Speicher für PHP erhöhen.

@ini_set('memory_limit', '32M');

// ------- Addon Includes
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/classes/class.tar.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/classes/class.rex_tar.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_import_export.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_folder.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_import_folder.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_string.inc.php';

// ------------------------------ FUNC
$msg = "";

$impname = rex_request('impname', 'string');

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

  if (isset($_FILES['FORM']) && $_FILES['FORM']['size']['importfile'] < 1 && $impname == "")
  {
    $msg = $I18N_IM_EXPORT->msg("no_import_file_chosen")."<br>";
  }
  else
  {
    if ($impname == "")
    {
      $file_temp = $REX['INCLUDE_PATH']."/addons/$page/files/tar.temp";
    }
    else
    {
      $file_temp = $REX['INCLUDE_PATH']."/addons/$page/files/$impname";
    }
    if ($impname != "" || @move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'], $file_temp))
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
elseif (isset ($function) && $function == 'export')
{

  // ------------------------------ FUNC EXPORT

  $exportfilename = strtolower($exportfilename);
  $exportfilename = stripslashes($exportfilename);
  $filename = ereg_replace('[^\.a-z0-9_\-]', '', $exportfilename);

  if ($filename != $exportfilename)
  {
    $msg = $I18N_IM_EXPORT->msg('filename_updated');
    $exportfilename = $filename;
  }
  else
  {
    $content = '';
    $header = '';
    $ext = '';
    if ($exporttype == 'sql')
    {
      // ------------------------------ FUNC EXPORT SQL
      $header = 'plain/text';
      $ext = '.sql';

      $content = rex_a1_export_db();
      // ------------------------------ /FUNC EXPORT SQL
    }
    elseif ($exporttype == 'files')
    {
      // ------------------------------ FUNC EXPORT FILES
      $header = 'tar/gzip';
      $ext = '.tar.gz';

      if (!isset($EXPDIR) || $EXPDIR == '')
      {
        $msg = $I18N_IM_EXPORT->msg('please_choose_folder');
      }
      else
      {
        $content = rex_a1_export_files($EXPDIR, $filename);
      }
      // ------------------------------ /FUNC EXPORT FILES
    }

    if ($content != '')
    {
      if($exportdl == 1)
      {
        $filename = $filename.$ext;
        header("Content-type: $header");
        header("Content-Disposition: attachment; filename=$filename");
        echo $content;
        exit;

      }
      else
      {
        // check filename ob vorhanden
        // aendern filename
        // speicher content in files

        $export_path = $REX['INCLUDE_PATH']."/addons/$page/files/";

        if (file_exists($export_path . $filename . $ext))
        {
          $i = 1;
          while(file_exists($export_path . $filename .'_'. $i . $ext))
            $i++;

          $filename = $filename .'_'. $i;
        }

        if (rex_put_file_contents($export_path . $filename . $ext, $content) !== false)
        {
          $msg = $I18N_IM_EXPORT->msg('file_generated_in').' '.strtr($filename . $ext, '\\', '/');
        }
        else
        {
          $msg = $I18N_IM_EXPORT->msg('file_could_not_be_generated').' '.$I18N->msg('check_rights_in_directory').' '.$export_path;
        }
      }
    }
  }
}

require $REX['INCLUDE_PATH']."/layout/top.php";

rex_title($I18N_IM_EXPORT->msg("importexport"), "");

if ($msg != '')
{
  echo rex_warning($msg);
}

?>
<div class="rex-cnt-cols">

  <!-- Linker Abschnitt -->
  <div class="rex-cnt-col2">
    <p class="rex-hdl"><?php echo $I18N_IM_EXPORT->msg('import'); ?></p>
    <div class="rex-cnt">
      <p><?php echo $I18N_IM_EXPORT->msg('intro_import') ?></p>

      <!-- DB IMPORT LIST -->
      <div class="rex-addon-editmode">
      <form action="index.php" enctype="multipart/form-data" method="post" onsubmit="return confirm('<?php echo $I18N_IM_EXPORT->msg('proceed_db_import') ?>')">
        <fieldset>
          <legend class="rex-lgnd"><?php echo $I18N_IM_EXPORT->msg('database'); ?></legend>
          <input type="hidden" name="page" value="<?php echo $page ?>" />
          <input type="hidden" name="function" value="dbimport" />
          <p class="rex-ftxt">
            <label for="importdbfile"><?php echo $I18N_IM_EXPORT->msg('database'); ?></label>
            <input type="file" class="rex-ffile" id="importdbfile" name="FORM[importfile]" />
          </p>
          <p>
            <input type="submit" class="rex-sbmt" value="<?php echo $I18N_IM_EXPORT->msg('db_import') ?>" />
          </p>
        </fieldset>
      </form>
      </div>

      <table class="rex-table" summary="<?php echo $I18N_IM_EXPORT->msg('export_db_summary'); ?>">
        <caption><?php echo $I18N_IM_EXPORT->msg('export_db_caption'); ?></caption>
        <colgroup>
          <col width="*" />
          <col width="15%" span="3"/>
        </colgroup>
        <thead>
          <tr>
            <th><?php echo $I18N_IM_EXPORT->msg('filename'); ?></th>
            <th><?php echo $I18N_IM_EXPORT->msg('createdate'); ?></th>
            <th colspan="2"><?php echo $I18N_IM_EXPORT->msg('function'); ?></th>
          </tr>
        </thead>
        <tbody>
<?php
  $dir = getImportDir();
  $folder = readImportFolder('.sql');

  foreach ($folder as $file)
  {
    $filepath = $dir.'/'.$file;
    $filec = date('d.m.Y H:i', filemtime($filepath));
    $filesize = OOMedia::_getFormattedSize(filesize($filepath));

    echo '<tr>
            <td>'. $file .' <br />['.$filesize.']</td>
            <td>'. $filec .'</td>
            <td><a href="index.php?page='. $page .'&amp;function=dbimport&amp;impname='. $file .'" title="'. $I18N_IM_EXPORT->msg('import_file') .'" onclick="return confirm(\''. $I18N_IM_EXPORT->msg('proceed_db_import') .'\')">'. $I18N_IM_EXPORT->msg('import') .'</a></td>
            <td><a href="index.php?page='. $page .'&amp;function=delete&amp;impname='. $file .'" title="'. $I18N_IM_EXPORT->msg('delete_file') .'" onclick="return confirm(\''. $I18N->msg('delete') .' ?\')">'. $I18N_IM_EXPORT->msg('delete') .'</a></td>
          </tr>
  ';
  }
?>
        </tbody>
      </table>

      <!-- FILE IMPORT -->
      <div class="rex-addon-editmode">
      <form action="index.php" enctype="multipart/form-data" method="post" onsubmit="return confirm('<?php echo $I18N_IM_EXPORT->msg('proceed_file_import') ?>')" >
        <fieldset>
          <legend class="rex-lgnd"><?php echo $I18N_IM_EXPORT->msg('files'); ?></legend>
          <input type="hidden" name="page" value="<?php echo $page ?>" />
          <input type="hidden" name="function" value="fileimport" />
          <p class="rex-ftxt">
            <label for="importtarfile"><?php echo $I18N_IM_EXPORT->msg('files'); ?></label>
            <input type="file" class="rex-ffile" id="importtarfile" name="FORM[importfile]" />
          </p>
          <p>
            <input type="submit" class="rex-sbmt" value="<?php echo $I18N_IM_EXPORT->msg('db_import') ?>" />
          </p>
        </fieldset>
      </form>
      </div>

      <table class="rex-table" summary="<?php echo $I18N_IM_EXPORT->msg('export_file_summary'); ?>">
        <caption><?php echo $I18N_IM_EXPORT->msg('export_file_caption'); ?></caption>
        <colgroup>
          <col width="*" />
          <col width="15%" span="3"/>
        </colgroup>
        <thead>
          <tr>
            <th><?php echo $I18N_IM_EXPORT->msg('filename'); ?></th>
            <th><?php echo $I18N_IM_EXPORT->msg('createdate'); ?></th>
            <th colspan="2"><?php echo $I18N_IM_EXPORT->msg('function'); ?></th>
          </tr>
        </thead>
        <tbody>
<?php
  $dir = getImportDir();
  $folder = readImportFolder('.tar.gz');

  foreach ($folder as $file)
  {
    $filepath = $dir.'/'.$file;
    $filec = date('d.m.Y H:i', filemtime($filepath));
    $filesize = OOMedia::_getFormattedSize(filesize($filepath));

    echo '<tr>
            <td>'. $file .'<br />['.$filesize.']</td>
            <td>'. $filec .'</td>
            <td><a href="index.php?page='. $page .'&amp;function=fileimport&amp;impname='. $file .'" title="'. $I18N_IM_EXPORT->msg('import_file') .'" onclick="return confirm(\''. $I18N_IM_EXPORT->msg('proceed_file_import') .'\')">'. $I18N_IM_EXPORT->msg('import') .'</a></td>
            <td><a href="index.php?page='. $page .'&amp;function=delete&amp;impname='. $file .'" title="'. $I18N_IM_EXPORT->msg('delete_file') .'" onclick="return confirm(\''. $I18N->msg('delete') .' ?\')">'. $I18N_IM_EXPORT->msg('delete') .'</a></td>
          </tr>';
  }
?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Rechter Abschnitt -->
  <div class="rex-cnt-col2">
    <p class="rex-hdl"><?php echo $I18N_IM_EXPORT->msg('export'); ?></p>
    <div class="rex-cnt">
      <p><?php echo $I18N_IM_EXPORT->msg('intro_export') ?></p>

      <div class="rex-addon-editmode">
      <form action="index.php" enctype="multipart/form-data" method="post" >
        <fieldset>
          <legend class="rex-lgnd"><?php echo $I18N_IM_EXPORT->msg('export'); ?></legend>
          <input type="hidden" name="page" value="<?php echo $page ?>" />
          <input type="hidden" name="function" value="export" />
<?php
$checkedsql = '';
$checkedfiles = '';

if (isset ($exporttype) and $exporttype == 'files')
{
  $checkedfiles = ' checked="checked"';
}
else
{
  $checkedsql = ' checked="checked"';
}
?>
            <p class="rex-rdo">
              <input type="radio" id="exporttype_sql" name="exporttype" value="sql"<?php echo $checkedsql ?> />
              <label class="rex-lbl-rght" for="exporttype_sql"><?php echo $I18N_IM_EXPORT->msg('database_export'); ?></label>
            </p>
            <p class="rex-rdo">
              <input type="radio" id="exporttype_files" name="exporttype" value="files"<?php echo $checkedfiles ?> />
              <label class="rex-lbl-rght" for="exporttype_files"><?php echo $I18N_IM_EXPORT->msg('file_export'); ?></label>
            </p>
            <!-- FILE EXPORT LIST -->
            <div class="rex-export-list">
<?php
  $dir = $REX['INCLUDE_PATH'] .'/../../';
  $folders = readSubFolders($dir);

  foreach ($folders as $file)
  {
    if ($file == 'redaxo')
    {
      continue;
    }

    $checked = '';
    if (isset($EXPDIR) && is_array($EXPDIR) && array_key_exists($file, $EXPDIR) !== false)
    {
      $checked = ' checked="checked"';
    }

    echo '<p class="rex-chckbx">
            <input type="checkbox" onchange="checkInput(\'exporttype_files\');" id="EXPDIR_'. $file .'" name="EXPDIR['. $file .']" value="true"'. $checked .' />
            <label class="rex-lbl-rght" for="EXPDIR_'. $file .'">'. $file .'</label>
          </p>
    ';
  }
?>
				<div class="rex-clear"></div>
              </div>
<?php
$checked0 = '';
$checked1 = '';

if (isset ($exportdl) and $exportdl == 1)
{
  $checked1 = ' checked="checked"';
}
else
{
  $checked0 = ' checked="checked"';
}
?>
            <p class="rex-rdo">
              <input type="radio" id="exportdl_server" name="exportdl" value="0"<?php echo $checked0; ?> />
              <label class="rex-lbl-rght" for="exportdl_server"><?php echo $I18N_IM_EXPORT->msg('save_on_server'); ?></label>
            </p>
            <p class="rex-rdo">
              <input type="radio" id="exportdl_download" name="exportdl" value="1"<?php echo $checked1; ?> />
              <label class="rex-lbl-rght" for="exportdl_download"><?php echo $I18N_IM_EXPORT->msg('download_as_file'); ?></label>
            </p>
            <p class="rex-ftxt">
              <label for="exportfilename"><?php echo $I18N_IM_EXPORT->msg('filename'); ?></label>
              <input type="text" id="exportfilename" name="exportfilename" value="<?php echo $exportfilename; ?>" />
            </p>
            <p>
              <input type="submit" class="rex-sbmt" value="<?php echo $I18N_IM_EXPORT->msg('db_export'); ?>" />
            </p>
        </fieldset>
      </form>
      </div>
    </div>
  </div>

</div>

<?php require $REX['INCLUDE_PATH']."/layout/bottom.php"; ?>