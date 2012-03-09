<?php

/**
 * TinyMCE Addon
 *
 * @author andreaseberhard[at]gmail[dot]com Andreas Eberhard
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

unset($_SESSION['tinymce']);

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');
$active_be = rex_request('active_be', 'string');
$active_fe = rex_request('active_fe', 'string');
$excludecats = rex_request('excludecats', 'string');
$excludeids = rex_request('excludeids', 'string');

$config_file = $REX['INCLUDE_PATH'] . '/addons/tinymce/config.inc.php';

if ($func == 'update')
{
  $REX['ADDON']['tinymce']['backend'] = $active_be;
  $REX['ADDON']['tinymce']['frontend'] = $active_fe;
  $REX['ADDON']['tinymce']['excludecats'] = $excludecats;
  $REX['ADDON']['tinymce']['excludeids'] = $excludeids;
  $content = '
$REX[\'ADDON\'][\'tinymce\'][\'backend\'] = \''.$active_be.'\';
$REX[\'ADDON\'][\'tinymce\'][\'frontend\'] = \''.$active_fe.'\';
$REX[\'ADDON\'][\'tinymce\'][\'excludecats\'] = \''.$excludecats.'\';
$REX[\'ADDON\'][\'tinymce\'][\'excludeids\'] = \''.$excludeids.'\';
';
  if(rex_replace_dynamic_contents($config_file, $content) !== false)
    echo rex_info($I18N->msg('tinymce_config_saved'));
  else
    echo rex_warning($I18N->msg('tinymce_config_not_saved'));
}
if(!tinymce_is_writable($config_file))
  echo rex_warning($I18N->msg('tinymce_error_notwriteable', $config_file));

$active_be_check = '';
if ($REX['ADDON']['tinymce']['backend'] == '1')
{
  $active_be_check = 'checked="checked"';
}
$active_fe_check = '';
if ($REX['ADDON']['tinymce']['frontend'] == '1')
{
  $active_fe_check = 'checked="checked"';
}
?>

<div class="rex-addon-output">
<div class="rex-form">
  <h2 class="rex-hl2"><?php echo $I18N->msg('tinymce_config_title'); ?></h2>

  <form action="index.php" method="post">
  <fieldset class="rex-form-col-1">

  <div class="rex-form-wrapper">
    <input type="hidden" name="page" value="<?php echo $page; ?>" />
    <input type="hidden" name="subpage" value="<?php echo $subpage; ?>" />
    <input type="hidden" name="func" value="update" />

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-checkbox"style="display:inline !important;">
        <label for="active" style="width:145px !important;"><?php echo $I18N->msg('tinymce_active_be'); ?></label>
        <input type="checkbox" <?php echo $active_be_check; ?> value="1" id="active" name="active_be" />
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-checkbox"style="display:inline !important;">
        <label for="runtimeinfo" style="width:145px !important;"><?php echo $I18N->msg('tinymce_active_fe'); ?></label>
        <input type="checkbox" <?php echo $active_fe_check; ?> value="1" id="runtimeinfo" name="active_fe" />
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-text">
        <label for="excludecats"><?php echo $I18N->msg('tinymce_config_excludecats'); ?></label>
        <input class="rex-form-text" type="text" id="excludecats" name="excludecats" value="<?php echo $REX['ADDON']['tinymce']['excludecats']; ?>" />
        <br /><?php echo $I18N->msg('tinymce_config_csv'); ?>
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-text">
        <label for="excludeids"><?php echo $I18N->msg('tinymce_config_excludeids'); ?></label>
        <input class="rex-form-text" type="text" id="excludeids" name="excludeids" value="<?php echo $REX['ADDON']['tinymce']['excludeids']; ?>" />
        <br /><?php echo $I18N->msg('tinymce_config_csv'); ?>
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-submit">
        <input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $I18N->msg('update'); ?>" />
      </p>
    </div>

  </div>

  </fieldset>
  </form>

</div>
</div>
