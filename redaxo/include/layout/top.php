<?php

/** 
 *  
 * @package redaxo3
 * @version $Id$
 */

if (!isset ($page_name))
  $page_name = '';

$page_title = $REX['SERVERNAME'];
if ($page_name != '')
{
  $page_title .= ' - ' . $page_name;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $I18N->msg("htmllang"); ?>" lang="<?php echo $I18N->msg("htmllang"); ?>">
<head>
  <title><?php echo $page_title ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $I18N->msg("htmlcharset"); ?>" />
  <meta http-equiv="Content-Language" content="<?php echo $I18N->msg("htmllang"); ?>" />
  <meta http-equiv="Pragma" content="no-cache" />
  <link rel="stylesheet" type="text/css" href="css/backend.css" media="screen, projection, print" />
  <link rel="stylesheet" type="text/css" href="css/table-old.css" media="screen, projection, print" />
  <link rel="stylesheet" type="text/css" href="css/aural.css" media="handheld, aural, braille" />
  <script src="js/standard.js" type="text/javascript"></script>
  <script type="text/javascript">
  <!--
  var redaxo = true;
  //-->
  </script>
</head>
<?php

if (isset ($open_header_only) && $open_header_only == true)
{
  return;
}
?>
<body onunload="closeAll();">
<div id="rex-hdr">

	<p class="rex-hdr-top"><?php echo $REX['SERVERNAME']; ?></p>
	
	<div>
	<?php

if (isset ($LOGIN) AND $LOGIN)
{
  $user_name = $REX_USER->getValue('name') != '' ? $REX_USER->getValue('name') : $REX_USER->getValue('login');
  echo '<p>' . $I18N->msg('name') . ' : <strong>' . $user_name . '</strong> [<a href="index.php?FORM[logout]=1">' . $I18N->msg('logout') . '</a>]</p>' . "\n";
  echo '<ul>';
  echo '<li><a href="index.php?page=structure">' . $I18N->msg("structure") . '</a></li>' . "\n";

  if ($REX_USER->hasPerm("mediapool[]") || $REX_USER->hasPerm("admin[]") || ($REX_USER->hasPerm("clang[") AND ($REX_USER->hasPerm("csw[") || $REX_USER->hasPerm("csr["))))
  {
    echo '<li> | <a href="#" onclick="openMediaPool();">' . $I18N->msg("pool_name") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm("template[]") || $REX_USER->hasPerm("admin[]"))
  {
    echo '<li> | <a href="index.php?page=template">' . $I18N->msg("template") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm("module[]") || $REX_USER->hasPerm("admin[]"))
  {
    echo '<li> | <a href="index.php?page=module">' . $I18N->msg("modules") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm("user[]") || $REX_USER->hasPerm("admin[]"))
  {
    echo '<li> | <a href="index.php?page=user">' . $I18N->msg("user") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm("addon[]") || $REX_USER->hasPerm("admin[]"))
  {
    echo '<li> | <a href="index.php?page=addon">' . $I18N->msg("addon") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm("specials[]") || $REX_USER->hasPerm("admin[]"))
  {
    echo '<li> | <a href="index.php?page=specials">' . $I18N->msg("specials") . '</a></li>' . "\n";
  }

  if (is_array($REX['ADDON']['status']))
  {
    reset($REX['ADDON']['status']);
  }

  echo '</ul>' . "\n";

  echo '<ul>' . "\n";
  $border = '';
  for ($i = 0; $i < count($REX['ADDON']['status']); $i++)
  {
    if ($i != 0)
    {
      $border = ' | ';
    }
    $apage = key($REX['ADDON']['status']);

    if (isset ($REX['ADDON']['perm'][$apage]))
    {
      $perm = $REX['ADDON']['perm'][$apage];

    }
    else
    {
      $perm = '';
    }

    if (isset ($REX['ADDON']['name'][$apage]))
    {
      $name = $REX['ADDON']['name'][$apage];

    }
    else
    {
      $name = '';
    }

    if (isset ($REX['ADDON']['popup'][$apage]))
    {
      $popup = $REX['ADDON']['popup'][$apage];

    }
    else
    {
      $popup = '';
    }

    // Leerzeichen durch &nbsp; ersetzen, damit Addonnamen immer in einer Zeile stehen
    $name = str_replace(' ', '&nbsp;', $name);

    if (current($REX['ADDON']['status']) == 1 && $name != '' && ($perm == '' || $REX_USER->hasPerm($perm) || $REX_USER->hasPerm("admin[]")))
    {
      if ($popup == 1)
      {
        echo '<li>' . $border . '<a href="javascript:newPoolWindow(\'index.php?page=' . $apage . '\');">' . $name . '</a></li>' . "\n";

      }
      elseif ($popup == "" or $popup == 0)
      {
        echo '<li>' . $border . '<a href="index.php?page=' . $apage . '">' . $name . '</a></li>' . "\n";

      }
      else
      {
        echo '<li>' . $border . '<a href="javascript:' . $popup . '">' . $name . '</a></li>' . "\n";
      }
    }
    next($REX['ADDON']['status']);
  }
  echo '</ul>' . "\n";

}
else
{
  echo '<p>' . $I18N->msg('logged_out') . '</p>';
}
?>
	</div>

</div>

<div id="rex-wrapper">