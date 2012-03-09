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

$mypage = 'tinymce';

if (isset($I18N) && is_object($I18N))
{
  if ($REX['VERSION'] . $REX['SUBVERSION'] < '42')
  {
    $I18N->locale = $REX['LANG'];
    $I18N->filename = $REX['INCLUDE_PATH'] . '/addons/tinymce/lang/'. $REX['LANG'] . ".lang";
    $I18N->loadTexts();
  }
  else
  {
    $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/tinymce/lang/');
  }
}
?>

<h3><?php echo $I18N->msg('tinymce_title'); ?></h3>
<br />
<p>
<?php echo $I18N->msg('tinymce_tiny_versinfo'); ?>
<br /><br />
<?php echo $I18N->msg('tinymce_shorthelp'); ?>
</p>
