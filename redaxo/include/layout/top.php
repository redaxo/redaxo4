<?php

/**
 * Layout Kopf des Backends
 * @package redaxo3
 * @version $Id$
 */

if (!isset ($page_name))
  $page_name = '';

$page_title = $REX['SERVERNAME'];

if ($page_name != '')
  $page_title .= ' - ' . $page_name;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $I18N->msg("htmllang"); ?>" lang="<?php echo $I18N->msg("htmllang"); ?>">
<head>
  <title><?php echo $page_title ?></title>
<?php
  // ----- EXTENSION POINT
  echo rex_register_extension_point('PAGE_HEADER', '');
?>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $I18N->msg("htmlcharset"); ?>" />
  <meta http-equiv="Content-Language" content="<?php echo $I18N->msg("htmllang"); ?>" />
  <meta http-equiv="Cache-Control" content="no-cache" />
  <meta http-equiv="Pragma" content="no-cache" />
  <link rel="stylesheet" type="text/css" href="css/backend.css" media="screen, projection, print" />
  <link rel="stylesheet" type="text/css" href="css/aural.css" media="handheld, aural, braille" />
  <script src="js/standard.js" type="text/javascript"></script>
  <script type="text/javascript">
  <!--
  var redaxo = true;
  //-->
  </script>
</head>
<?php

$body_id = str_replace('_', '-', $page);
?>
<body id="rex-page-<?php echo $body_id; ?>" <?php

if (!isset($open_header_only)) echo 'onunload="closeAll();"';

?>>

<div id="rex-hdr">

	<p class="rex-hdr-top"><?php echo $REX['SERVERNAME']; ?></p>

	<div>
<?php

if (isset ($LOGIN) AND $LOGIN AND !isset($open_header_only))
{
  $accesskey = 1;

  $user_name = $REX_USER->getValue('name') != '' ? $REX_USER->getValue('name') : $REX_USER->getValue('login');
  echo '<p>' . $I18N->msg('name') . ' : <strong>' . $user_name . '</strong> [<a href="index.php?FORM[logout]=1" accesskey="'. $REX['ACKEY']['LOGOUT'] .'">' . $I18N->msg('logout') . '</a>]</p>' . "\n";
  echo '<ul>';
  echo '<li><a href="index.php?page=structure"'. rex_tabindex() .' accesskey="'. $accesskey++ .'">' . $I18N->msg("structure") . '</a></li>' . "\n";

  if ($REX_USER->hasPerm("mediapool[]") || $REX_USER->hasPerm("admin[]") || ($REX_USER->hasPerm("clang[") AND ($REX_USER->hasPerm("csw[") || $REX_USER->hasPerm("csr["))))
  {
    echo '<li> | <a href="#" onclick="openMediaPool();"'. rex_tabindex() . 'accesskey="'. $accesskey++ .'">' . $I18N->msg("pool_name") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm("template[]") || $REX_USER->hasPerm("admin[]"))
  {
    echo '<li> | <a href="index.php?page=template"'. rex_tabindex() .' accesskey="'. $accesskey++ .'">' . $I18N->msg("template") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm("module[]") || $REX_USER->hasPerm("admin[]"))
  {
    echo '<li> | <a href="index.php?page=module"'. rex_tabindex() .' accesskey="'. $accesskey++ .'">' . $I18N->msg("modules") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm("user[]") || $REX_USER->hasPerm("admin[]"))
  {
    echo '<li> | <a href="index.php?page=user"'. rex_tabindex() .' accesskey="'. $accesskey++ .'">' . $I18N->msg("user") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm("addon[]") || $REX_USER->hasPerm("admin[]"))
  {
    echo '<li> | <a href="index.php?page=addon"'. rex_tabindex() .' accesskey="'. $accesskey++ .'">' . $I18N->msg("addon") . '</a></li>' . "\n";
  }

  if ($REX_USER->hasPerm("specials[]") || $REX_USER->hasPerm("admin[]"))
  {
    echo '<li> | <a href="index.php?page=specials"'. rex_tabindex() .' accesskey="'. $accesskey++ .'">' . $I18N->msg("specials") . '</a></li>' . "\n";
  }

  if (is_array($REX['ADDON']['status']))
  {
    reset($REX['ADDON']['status']);
  }

  echo '</ul>' . "\n";

  $first = true;
  echo '<ul>' . "\n";
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
      $accesskey = ' accesskey="'. $REX['ACKEY']['ADDON'][$apage] .'"';

    // Leerzeichen durch &nbsp; ersetzen, damit Addonnamen immer in einer Zeile stehen
    $name = str_replace(' ', '&nbsp;', $name);

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
        echo '<li>' . $separator . '<a href="javascript:newPoolWindow(\'index.php?page=' . $apage . '\');"'. rex_tabindex() . $accesskey .'>' . $name . '</a></li>' . "\n";
      }
      elseif ($popup == "" or $popup == 0)
      {
        echo '<li>' . $separator . '<a href="index.php?page=' . $apage . '"'. rex_tabindex() . $accesskey .'>' . $name . '</a></li>' . "\n";
      }
      else
      {
        echo '<li>' . $separator . '<a href="' . $popup . '"'. rex_tabindex() . $accesskey .'>' . $name . '</a></li>' . "\n";
      }
    }
    next($REX['ADDON']['status']);
  }

  echo '</ul>' . "\n";
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
<?php



?>
<div id="rex-wrapper">