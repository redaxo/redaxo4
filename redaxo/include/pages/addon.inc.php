<?php

/**
 *
 * @package redaxo4
 * @version $Id: addon.inc.php,v 1.5 2008/03/25 10:42:51 kills Exp $
 */

include_once $REX['INCLUDE_PATH'].'/functions/function_rex_other.inc.php';
include_once $REX['INCLUDE_PATH'].'/functions/function_rex_addons.inc.php';

rex_title($I18N->msg('addon'), '');

// -------------- Defaults

$addonname = rex_request('addonname', 'string');
$subpage   = rex_request('subpage', 'string');

$ADDONS    = rex_read_addons_folder();
$addonname = array_search($addonname, $ADDONS) !== false ? $addonname : '';

$warning = '';
$info = '';

// ----------------- HELPPAGE
if ($subpage == 'help' && $addonname != '')
{
  $credits = '';
  $version  = OOAddon::getVersion($addonname);
  $author   =  OOAddon::getAuthor($addonname);
  $supportPage = OOAddon::getSupportPage($addonname);
  $helpfile = $REX['INCLUDE_PATH'].'/addons/'.$addonname.'/help.inc.php';
  
  $credits .= $I18N->msg("credits_name") .': <span>'. $addonname .'</span><br />';
  if($version) $credits .= $I18N->msg("credits_version") .': <span>'. $version .'</span><br />';
  if($author) $credits .= $I18N->msg("credits_author") .': <span>'. $author .'</span><br />';
  if($supportPage) $credits .= $I18N->msg("credits_supportpage") .': <span><a href="http://'.$supportPage.'" onclick="window.open(this.href); return false;">'. $supportPage .'</a></span><br />';
  
  echo '<div class="rex-area">
  			<h3 class="rex-hl2">'.$I18N->msg("addon_help").' '.$addonname.'</h3>
	  		<div class="rex-area-content">';
  if (!is_file($helpfile))
  {
    echo '<p>'. $I18N->msg("addon_no_help_file") .'</p>';
  }
  else
  {
    include $helpfile;
  }
  echo '<br />
        <p id="rex-addon-credits">'. $credits .'</p>
        </div>
  			<div class="rex-area-footer">
  				<p><a href="index.php?page=addon">'.$I18N->msg("addon_back").'</a></p>
  			</div>
  		</div>';
}

// ----------------- FUNCTIONS
if ($addonname != '')
{
  $install  = rex_get('install', 'int', -1);
  $activate = rex_get('activate', 'int', -1);
  $uninstall = rex_get('uninstall', 'int', -1);
  $delete = rex_get('delete', 'int', -1);

  // ----------------- ADDON INSTALL
  if ($install == 1)
  {
    if (($warning = rex_install_addon($ADDONS, $addonname)) === true)
    {
      $info = $I18N->msg("addon_installed", $addonname);
    }
  }
  // ----------------- ADDON ACTIVATE
  elseif ($activate == 1)
  {
    if (($warning = rex_activate_addon($ADDONS, $addonname)) === true)
    {
      $info = $I18N->msg("addon_activated", $addonname);
    }
  }
  // ----------------- ADDON DEACTIVATE
  elseif ($activate == 0)
  {
    if (($warning = rex_deactivate_addon($ADDONS, $addonname)) === true)
    {
      $info = $I18N->msg("addon_deactivated", $addonname);
    }
  }
  // ----------------- ADDON UNINSTALL
  elseif ($uninstall == 1)
  {
    if (($warning = rex_uninstall_addon($ADDONS, $addonname)) === true)
    {
      $info = $I18N->msg("addon_uninstalled", $addonname);
    }
  }
  // ----------------- ADDON DELETE
  elseif ($delete == 1)
  {
    if (($warning = rex_delete_addon($ADDONS, $addonname)) === true)
    {
      $info = $I18N->msg("addon_deleted", $addonname);
      $addonkey = array_search( $addonname, $ADDONS);
      unset($ADDONS[$addonkey]);
    }
  }
}

// ----------------- OUT
if ($subpage == '')
{
  // Vergleiche Addons aus dem Verzeichnis addons/ mit den Eintraegen in include/addons.inc.php
  // Wenn ein Addon in der Datei fehlt oder nicht mehr vorhanden ist, aendere den Dateiinhalt.
  if (count(array_diff(array_keys(array_flip($ADDONS)), array_keys($REX['ADDON']['install']))) > 0 ||
      count(array_diff(array_keys($REX['ADDON']['install']), array_keys(array_flip($ADDONS)))) > 0)
  {

    if (($state = rex_generateAddons($ADDONS)) !== true)
    {
      $warning = $state;
    }
  }

  if ($info != '')
    echo rex_info($info);

  if ($warning != '' && $warning !== true)
    echo rex_warning($warning);

  if (!isset ($user_id))
  {
    $user_id = '';
  }
  echo '
      <table class="rex-table" summary="'.$I18N->msg("addon_summary").'">
      <caption>'.$I18N->msg("addon_caption").'</caption>
      <colgroup>
      	<col width="40" />
        <col width="*"/>
        <col width="130" />
        <col width="130" />
        <col width="130" />
        <col width="153" />
      </colgroup>
  	  <thead>
        <tr>
          <th class="rex-icon">&nbsp;</th>
          <th>'.$I18N->msg("addon_hname").'</th>
          <th>'.$I18N->msg("addon_hinstall").'</th>
          <th>'.$I18N->msg("addon_hactive").'</th>
          <th colspan="2">'.$I18N->msg("addon_hdelete").'</th>
        </tr>
  	  </thead>
  	  <tbody>';

  foreach ($ADDONS as $cur)
  {
  	if (OOAddon::isSystemAddon($cur))
  	{
  		$delete = $I18N->msg("addon_systemaddon");
  	}
    else
  	{
  		$delete = '<a href="index.php?page=addon&amp;addonname='.$cur.'&amp;delete=1" onclick="return confirm(\''.htmlspecialchars($I18N->msg('addon_delete_question', $cur)).'\');">'.$I18N->msg("addon_delete").'</a>';
  	}

    if (OOAddon::isInstalled($cur))
    {
      $install = $I18N->msg("addon_yes").' - <a href="index.php?page=addon&amp;addonname='.$cur.'&amp;install=1">'.$I18N->msg("addon_reinstall").'</a>';
      $uninstall = '<a href="index.php?page=addon&amp;addonname='.$cur.'&amp;uninstall=1" onclick="return confirm(\''.htmlspecialchars($I18N->msg("addon_uninstall_question", $cur)).'\');">'.$I18N->msg("addon_uninstall").'</a>';
    }
    else
    {
      $install = $I18N->msg("addon_no").' - <a href="index.php?page=addon&amp;addonname='.$cur.'&amp;install=1">'.$I18N->msg("addon_install").'</a>';
      $uninstall = $I18N->msg("addon_notinstalled");
    }

    if (OOAddon::isActivated($cur))
    {
      $status = $I18N->msg("addon_yes").' - <a href="index.php?page=addon&amp;addonname='.$cur.'&amp;activate=0">'.$I18N->msg("addon_deactivate").'</a>';
    }
    elseif (OOAddon::isInstalled($cur))
    {
      $status = $I18N->msg("addon_no").' - <a href="index.php?page=addon&amp;addonname='.$cur.'&amp;activate=1">'.$I18N->msg("addon_activate").'</a>';
    }
    else
    {
      $status = $I18N->msg("addon_notinstalled");
    }

    echo '
        <tr>
          <td class="rex-icon"><img src="media/addon.gif" alt="'. htmlspecialchars($cur) .'" title="'. htmlspecialchars($cur) .'"/></td>
          <td>'.htmlspecialchars($cur).' [<a href="index.php?page=addon&amp;subpage=help&amp;addonname='.$cur.'">?</a>]</td>
          <td>'.$install.'</td>
          <td>'.$status.'</td>
          <td>'.$uninstall.'</td>
          <td>'.$delete.'</td>
        </tr>'."\n   ";
  }

  echo '</tbody>
  		</table>';
}