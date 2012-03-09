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

?>

<div class="rex-addon-output">

  <h2 class="rex-hl2"><?php echo $I18N->msg('tinymce_title'); ?></h2>

  <div class="rex-addon-content">
    <p class="rex-tx1">
    <?php echo $I18N->msg('tinymce_tiny_versinfo'); ?>
    </p>
    <p class="rex-tx1">
    <?php echo $I18N->msg('tinymce_shorthelp'); ?>
    </p>
    <p class="rex-tx1">
    <?php echo $I18N->msg('tinymce_longhelp'); ?>
    </p>
    <p class="rex-tx1">
    <?php echo $I18N->msg('tinymce_nodel_notice'); ?>
    </p>

  </div>

</div>

<div class="rex-addon-output">

  <h2 class="rex-hl2"><?php echo $I18N->msg('tinymce_title_module_input'); ?></h2>

  <div class="rex-addon-content">
    <p class="rex-tx1">
    <?php echo $I18N->msg('tinymce_help_module_input'); ?>
    </p>
    <?php rex_highlight_string(rex_get_file_contents($REX['INCLUDE_PATH'].'/addons/tinymce/modul_input.txt')); ?>
  </div>

</div>

<div class="rex-addon-output">

  <h2 class="rex-hl2"><?php echo $I18N->msg('tinymce_title_module_output'); ?></h2>

  <div class="rex-addon-content">
    <p class="rex-tx1">
    <?php echo $I18N->msg('tinymce_help_module_output'); ?>
    </p>
    <?php rex_highlight_string(rex_get_file_contents($REX['INCLUDE_PATH'].'/addons/tinymce/modul_output.txt')); ?>
  </div>

</div>
