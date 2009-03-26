<?php

/**
 *
 * @package redaxo4
 * @version svn:$Id$
 */

// Für größere Exports den Speicher für PHP erhöhen.
@ini_set('memory_limit', '64M');

// ------- Addon Includes
include_once $REX['INCLUDE_PATH'].'/addons/import_export/classes/class.tar.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/import_export/classes/class.rex_tar.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/import_export/functions/function_import_export.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/import_export/functions/function_folder.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/import_export/functions/function_import_folder.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/import_export/functions/function_string.inc.php';

$info = '';
$warning = '';

// ------------------------------ Requestvars
$function       = rex_request('function', 'string');
$impname        = rex_request('impname', 'string');
$exportfilename = rex_post('exportfilename', 'string');
$exporttype     = rex_post('exporttype', 'string');
$exportdl       = rex_post('exportdl', 'boolean');
$EXPDIR         = rex_post('EXPDIR', 'array');

if ($impname != '')
{
  $impname = str_replace("/", "", $impname);

  if ($function == "dbimport" && substr($impname, -4, 4) != ".sql")
    $impname = "";
  elseif ($function == "fileimport" && substr($impname, -7, 7) != ".tar.gz")
    $impname = "";
}

if ($exportfilename == '')
  $exportfilename = 'rex_'.$REX['VERSION'].'_'.date("Ymd");

if ($function == 'export')
{
  // ------------------------------ FUNC EXPORT

  $exportfilename = strtolower($exportfilename);
  $exportfilename = stripslashes($exportfilename);
  $filename       = ereg_replace('[^\.a-z0-9_\-]', '', $exportfilename);

  if ($filename != $exportfilename)
  {
    $info = $I18N->msg('im_export_filename_updated');
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

      if (empty($EXPDIR))
      {
        $warning = $I18N->msg('im_export_please_choose_folder');
      }
      else
      {
        $content = rex_a1_export_files($EXPDIR);
      }
      // ------------------------------ /FUNC EXPORT FILES
    }

    if ($content != '')
    {
      if($exportdl)
      {
      	ob_end_clean();
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

        $export_path = getImportDir().'/';

        if (file_exists($export_path . $filename . $ext))
        {
          $i = 1;
          while(file_exists($export_path . $filename .'_'. $i . $ext))
            $i++;

          $filename = $filename .'_'. $i;
        }

        if (rex_put_file_contents($export_path . $filename . $ext, $content) !== false)
        {
          $info = $I18N->msg('im_export_file_generated_in').' '.strtr($filename . $ext, '\\', '/');
        }
        else
        {
          $warning = $I18N->msg('im_export_file_could_not_be_generated').' '.$I18N->msg('im_export_check_rights_in_directory').' '.$export_path;
        }
      }
    }
  }
}

if ($info != '')
{
  echo rex_info($info);
}
if ($warning != '')
{
  echo rex_warning($warning);
}

?>

<div class="rex-area rex-area-col-2">
  
  <div class="rex-area-col-b">
    <h3 class="rex-hl2"><?php echo $I18N->msg('im_export_export'); ?></h3>
  
    <div class="rex-area-content">
      <p class="rex-tx1"><?php echo $I18N->msg('im_export_intro_export') ?></p>
      
      <div class="rex-form" id="rex-form-export">
      <form action="index.php" enctype="multipart/form-data" method="post" >
        <fieldset class="rex-form-col-1">
          <legend><?php echo $I18N->msg('im_export_export'); ?></legend>
          
          <div class="rex-form-wrapper">
            <input type="hidden" name="page" value="import_export" />
            <input type="hidden" name="subpage" value="export" /> 
            <input type="hidden" name="function" value="export" />
<?php
$checkedsql = '';
$checkedfiles = '';

if ($exporttype == 'files')
{
  $checkedfiles = ' checked="checked"';
}
else
{
  $checkedsql = ' checked="checked"';
}
?>
            <div class="rex-form-row">
              <p class="rex-form-radio rex-form-label-right">
                <input class="rex-form-radio" type="radio" id="exporttype_sql" name="exporttype" value="sql"<?php echo $checkedsql ?> />
                <label for="exporttype_sql"><?php echo $I18N->msg('im_export_database_export'); ?></label>
              </p>
            </div>
            <div class="rex-form-row">
              <p class="rex-form-radio rex-form-label-right">
                <input class="rex-form-radio" type="radio" id="exporttype_files" name="exporttype" value="files"<?php echo $checkedfiles ?> />
                <label for="exporttype_files"><?php echo $I18N->msg('im_export_file_export'); ?></label>
              </p>
              
              <div class="rex-form-checkboxes">
                <div class="rex-form-checkboxes-wrapper">
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
    if (array_key_exists($file, $EXPDIR) !== false)
    {
      $checked = ' checked="checked"';
    }

    echo '<p class="rex-form-checkbox rex-form-label-right">
            <input class="rex-form-checkbox" type="checkbox" onchange="checkInput(\'exporttype_files\');" id="EXPDIR_'. $file .'" name="EXPDIR['. $file .']" value="true"'. $checked .' />
            <label for="EXPDIR_'. $file .'">'. $file .'</label>
          </p>
    ';
  }
?>
    </div><!-- END rex-form-checkboxes-wrapper -->
  </div><!-- END rex-form-checkboxes -->
</div><!-- END rex-form-row -->
<?php
$checked0 = '';
$checked1 = '';

if ($exportdl)
{
  $checked1 = ' checked="checked"';
}
else
{
  $checked0 = ' checked="checked"';
}
?>
            <div class="rex-form-row">
              <p class="rex-form-radio rex-form-label-right">
                <input class="rex-form-radio" type="radio" id="exportdl_server" name="exportdl" value="0"<?php echo $checked0; ?> />
                <label for="exportdl_server"><?php echo $I18N->msg('im_export_save_on_server'); ?></label>
              </p>
            </div>
            <div class="rex-form-row">
              <p class="rex-form-radio rex-form-label-right">
                <input class="rex-form-radio" type="radio" id="exportdl_download" name="exportdl" value="1"<?php echo $checked1; ?> />
                <label for="exportdl_download"><?php echo $I18N->msg('im_export_download_as_file'); ?></label>
              </p>
            </div>
            <div class="rex-form-row">
              <p class="rex-form-text">
                <label for="exportfilename"><?php echo $I18N->msg('im_export_filename'); ?></label>
                <input class="rex-form-text" type="text" id="exportfilename" name="exportfilename" value="<?php echo $exportfilename; ?>" />
              </p>
            </div>
            <div class="rex-form-row">
              <p class="rex-form-submit">
                <input class="rex-form-submit" type="submit" value="<?php echo $I18N->msg('im_export_db_export'); ?>" />
              </p>
            </div>
          </div>
        </fieldset>
      </form>
      </div><!-- END rex-form -->
    </div><!-- END rex-area-content -->
  </div><!-- END rex-area-col-b -->
  <div class="rex-clearer"></div>
</div><!-- END rex-area -->