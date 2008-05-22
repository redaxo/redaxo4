<?php

/**
 * Layout Kopf des Backends
 * @package redaxo4
 * @version $Id: top.php,v 1.7 2008/04/02 19:58:00 kills Exp $
 */

if (!isset ($page_name))
  $page_name = '';

$page_title = $REX['SERVERNAME'];

if ($page_name != '')
  $page_title .= ' - ' . $page_name;

$body_id = str_replace('_', '-', $page);
$bodyAttr = 'id="rex-page-'. $body_id .'"';

if (!isset($open_header_only)) $bodyAttr .= ' onunload="closeAll();"';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $I18N->msg('htmllang'); ?>" lang="<?php echo $I18N->msg('htmllang'); ?>">
<head>
  <title><?php echo $page_title ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $I18N->msg('htmlcharset'); ?>" />
  <meta http-equiv="Content-Language" content="<?php echo $I18N->msg('htmllang'); ?>" />
  <meta http-equiv="Cache-Control" content="no-cache" />
  <meta http-equiv="Pragma" content="no-cache" />
  <link rel="stylesheet" type="text/css" href="media/css_backend.css" media="screen, projection, print" />
  <link rel="stylesheet" type="text/css" href="media/css_aural.css" media="handheld, aural, braille" />
  <script src="media/jquery.pack.js" type="text/javascript"></script>
  <script src="media/standard.js" type="text/javascript"></script>
  <script type="text/javascript">
  <!--
  var redaxo = true;

  // jQuery is now removed from the $ namespace
  // to use the $ shorthand, use (function($){ ... })(jQuery);
  // and for the onload handler: jQuery(function($){ ... });
  jQuery.noConflict();
  //-->
  </script>
<?php
  // ----- EXTENSION POINT
  echo rex_register_extension_point('PAGE_HEADER', '');
?>
</head>
<body <?php echo $bodyAttr; ?>>

<div id="rex-hdr">

  <p class="rex-hdr-top"><?php echo $REX['SERVERNAME']; ?></p>

  <div>
<?php

if (isset ($LOGIN) AND $LOGIN AND !isset($open_header_only))
{
  $accesskey = 1;

  $user_name = $REX_USER->getValue('name') != '' ? $REX_USER->getValue('name') : $REX_USER->getValue('login');
  echo '<p>' . $I18N->msg('name') . ' : <strong><a href="index.php?page=profile">' . $user_name . '</a></strong> [<a href="index.php?FORM[logout]=1"'. rex_accesskey($I18N->msg('logout'), $REX['ACKEY']['LOGOUT']) .'>' . $I18N->msg('logout') . '</a>]</p>' . "\n";
  echo '<ul id="rex-main-mnu">';

  $activeClass = ' class="rex-active"';
  $liClass = $page == 'structure' || $page == 'content' ? $activeClass : '';

  echo '<li'. $liClass .' id="rex-mainnavi-structure"><a href="index.php?page=structure"'. rex_tabindex() . rex_accesskey($I18N->msg('structure'), $accesskey++) .'>' . $I18N->msg("structure") . '</a></li>' . "\n";

  if ($REX_USER->hasPerm('mediapool[]') || $REX_USER->hasPerm('admin[]') || ($REX_USER->hasPerm('clang[') AND ($REX_USER->hasPerm('csw[') || $REX_USER->hasPerm('csr['))))
  {
    $liClass = $page == 'medienpool' ? $activeClass : '';
    echo '<li'. $liClass .' id="rex-mainnavi-mediapool"> | <a href="#" onclick="openMediaPool();"'. rex_tabindex() . rex_accesskey($I18N->msg('pool_name'), $accesskey++) .'>' . $I18N->msg("pool_name") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm('template[]') || $REX_USER->hasPerm('admin[]'))
  {
    $liClass = $page == 'template' ? $activeClass : '';
    echo '<li'. $liClass .' id="rex-mainnavi-template"> | <a href="index.php?page=template"'. rex_tabindex() . rex_accesskey($I18N->msg('template'), $accesskey++) .'>' . $I18N->msg("template") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm('module[]') || $REX_USER->hasPerm('admin[]'))
  {
    $liClass = $page == 'module' ? $activeClass : '';
    echo '<li'. $liClass .' id="rex-mainnavi-module"> | <a href="index.php?page=module"'. rex_tabindex() . rex_accesskey($I18N->msg('modules'), $accesskey++) .'>' . $I18N->msg("modules") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm('user[]') || $REX_USER->hasPerm('admin[]'))
  {
    $liClass = $page == 'user' ? $activeClass : '';
    echo '<li'. $liClass .' id="rex-mainnavi-user"> | <a href="index.php?page=user"'. rex_tabindex() . rex_accesskey($I18N->msg('user'), $accesskey++) .'>' . $I18N->msg("user") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm('addon[]') || $REX_USER->hasPerm('admin[]'))
  {
    $liClass = $page == 'addon' ? $activeClass : '';
    echo '<li'. $liClass .' id="rex-mainnavi-addon"> | <a href="index.php?page=addon"'. rex_tabindex() . rex_accesskey($I18N->msg('addon'), $accesskey++) .'>' . $I18N->msg("addon") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm('specials[]') || $REX_USER->hasPerm('admin[]'))
  {
    $liClass = $page == 'specials' ? $activeClass : '';
    echo '<li'. $liClass .' id="rex-mainnavi-specials"> | <a href="index.php?page=specials"'. rex_tabindex() . rex_accesskey($I18N->msg('specials'), $accesskey++) .'>' . $I18N->msg("specials") . '</a></li>' . "\n";
  }

  if (is_array($REX['ADDON']['status']))
  {
    reset($REX['ADDON']['status']);
  }

  echo '</ul>' . "\n";

  $onlineAddons = array_filter(array_values($REX['ADDON']['status']));
  if(count($onlineAddons) > 0)
  {
    $first = true;
    echo '<ul id="rex-addon-mnu">' . "\n";

    for ($i = 0; $i < count($REX['ADDON']['status']); $i++)
    {
      $apage = key($REX['ADDON']['status']);

      $perm = '';
      if(isset ($REX['ADDON']['perm'][$apage]))
        $perm = $REX['ADDON']['perm'][$apage];

      $name = '';
      if(isset ($REX['ADDON']['name'][$apage]))
        $name = $REX['ADDON']['name'][$apage];

      $popup = '';
      if(isset ($REX['ADDON']['popup'][$apage]))
        $popup = $REX['ADDON']['popup'][$apage];

      $accesskey = '';
      if(isset ($REX['ACKEY']['ADDON'][$apage]))
        $accesskey = rex_accesskey($name, $REX['ACKEY']['ADDON'][$apage]);

      // Leerzeichen durch &nbsp; ersetzen, damit Addonnamen immer in einer Zeile stehen
      $name = str_replace(' ', '&nbsp;', $name);

      $liClass = $page == $apage ? $activeClass : '';
      if (current($REX['ADDON']['status']) == 1 && $name != '' && ($perm == '' || $REX_USER->hasPerm($perm) || $REX_USER->hasPerm("admin[]")))
      {
        $separator = ' | ';
        if($first)
        {
          $separator = '';
          $first = false;
        }
        if ($popup == 1)
        {
          echo '<li'. $liClass .' id="rex-mainnavi-' . $apage . '">' . $separator . '<a href="javascript:newPoolWindow(\'index.php?page=' . $apage . '\');"'. rex_tabindex() . $accesskey .'>' . $name . '</a></li>' . "\n";
        }
        elseif ($popup == '' or $popup == 0)
        {
          echo '<li'. $liClass .' id="rex-mainnavi-' . $apage . '">' . $separator . '<a href="index.php?page=' . $apage . '"'. rex_tabindex() . $accesskey .'>' . $name . '</a></li>' . "\n";
        }
        else
        {
          echo '<li'. $liClass .' id="rex-mainnavi-' . $apage . '">' . $separator . '<a href="' . $popup . '"'. rex_tabindex() . $accesskey .'>' . $name . '</a></li>' . "\n";
        }
      }
      next($REX['ADDON']['status']);
    }

    echo '</ul>' . "\n";
  }
}
else if(!isset($open_header_only))
{
  echo '<p>' . $I18N->msg('logged_out') . '</p>';
}else
{
  echo '<p>&nbsp;</p>';
}
?>
  </div>

</div>

<div id="rex-wrapper">