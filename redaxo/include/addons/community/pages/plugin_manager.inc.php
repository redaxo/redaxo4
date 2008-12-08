<?php

/**
 *
 * @package redaxo4
 * @version $Id: addon.inc.php,v 1.5 2008/03/25 10:42:51 kills Exp $
 */

include_once $REX['INCLUDE_PATH'].'/addons/community/functions/functions.plugin.inc.php';

// -------------- Defaults

$pluginname = rex_request('pluginname', 'string');
$function = rex_request('function', 'string');

$plugins = rex_read_plugins_folder();
$pluginname = array_search($pluginname, $plugins) !== false ? $pluginname : '';

$warning = '';
$info = '';

// ----------------- FUNCTIONS
if ($pluginname != '')
{
  $install  = rex_get('install', 'int', -1);
  $activate = rex_get('activate', 'int', -1);
  $uninstall = rex_get('uninstall', 'int', -1);

  // ----------------- plugin INSTALL
  if ($install == 1)
  {
    if (($warning = rex_install_plugin($plugins, $pluginname)) === true)
    {
      $info = $I18N_COM->msg("plugin_installed", $pluginname);
    }
  }
  // ----------------- plugin ACTIVATE
  elseif ($activate == 1)
  {
    if (($warning = rex_activate_plugin($plugins, $pluginname)) === true)
    {
      $info = $I18N_COM->msg("plugin_activated", $pluginname);
    }
  }
  // ----------------- plugin DEACTIVATE
  elseif ($activate == 0)
  {
    if (($warning = rex_deactivate_plugin($plugins, $pluginname)) === true)
    {
      $info = $I18N_COM->msg("plugin_deactivated", $pluginname);
    }
  }
  // ----------------- plugin UNINSTALL
  elseif ($uninstall == 1)
  {
    if (($warning = rex_uninstall_plugin($plugins, $pluginname)) === true)
    {
      $info = $I18N_COM->msg("plugin_uninstalled", $pluginname);
    }
  }
}

// ----------------- OUT
// Vergleiche plugins aus dem Verzeichnis plugins/ mit den Eintraegen in include/plugins.inc.php
// Wenn ein plugin in der Datei fehlt oder nicht mehr vorhanden ist, aendere den Dateiinhalt.
if (count(array_diff($plugins, OOPlugin::getRegisteredPlugins())) > 0 ||
    count(array_diff(OOPlugin::getRegisteredPlugins(), $plugins)) > 0)
{
  if (($state = rex_generateplugins($plugins)) !== true)
  {
    $warning = $state;
  }
}

if ($info != '')
  echo rex_info($info);

if ($warning != '' && $warning !== true)
  echo rex_warning($warning);

// ----------------- HELPPAGE
if ($function == 'help' && $pluginname != '')
{
  $helpfile = rex_plugins_dir($pluginname) .'help.inc.php';

  echo '<p class="rex-hdl">'.$I18N_COM->msg("plugin_help").' '.$pluginname.'</p>
      <div class="rex-adn-hlp">';
  if (!is_file($helpfile))
  {
    echo $I18N_COM->msg("plugin_no_help_file");
  }
  else
  {
    include $helpfile;
  }
  echo '</div>
      <p class="rex-hdl"><a href="index.php?page=community&subpage=plugin_manager">'.$I18N_COM->msg("plugin_back").'</a></p>';
}
else
{
  echo '
      <table class="rex-table" summary="'.$I18N_COM->msg("plugin_summary").'">
      <caption class="rex-hide">'.$I18N_COM->msg("plugin_caption").'</caption>
      <colgroup>
        <col width="40" />
        <col width="*"/>
        <col width="130" />
        <col width="130" />
        <col width="130" />
      </colgroup>
      <thead>
        <tr>
          <th class="rex-icon">&nbsp;</th>
          <th>'.$I18N_COM->msg("plugin_hname").'</th>
          <th>'.$I18N_COM->msg("plugin_hinstall").'</th>
          <th>'.$I18N_COM->msg("plugin_hactive").'</th>
          <th>'.$I18N_COM->msg("plugin_hdelete").'</th>
        </tr>
      </thead>
      <tbody>';

  foreach ($plugins as $cur)
  {
    if (OOPlugin::isInstalled($cur))
    {
      $install = $I18N_COM->msg("plugin_yes").' - <a href="index.php?page=community&subpage=plugin_manager&amp;pluginname='.$cur.'&amp;install=1">'.$I18N_COM->msg("plugin_reinstall").'</a>';
      $uninstall = '<a href="index.php?page=community&subpage=plugin_manager&amp;pluginname='.$cur.'&amp;uninstall=1" onclick="return confirm(\''.htmlspecialchars($I18N_COM->msg("plugin_uninstall_question", $cur)).'\');">'.$I18N_COM->msg("plugin_uninstall").'</a>';
    }
    else
    {
      $install = $I18N_COM->msg("plugin_no").' - <a href="index.php?page=community&subpage=plugin_manager&amp;pluginname='.$cur.'&amp;install=1">'.$I18N_COM->msg("plugin_install").'</a>';
      $uninstall = $I18N_COM->msg("plugin_notinstalled");
    }

    if (OOPlugin::isActivated($cur))
    {
      $status = $I18N_COM->msg("plugin_yes").' - <a href="index.php?page=community&subpage=plugin_manager&amp;pluginname='.$cur.'&amp;activate=0">'.$I18N_COM->msg("plugin_deactivate").'</a>';
    }
    elseif (OOPlugin::isInstalled($cur))
    {
      $status = $I18N_COM->msg("plugin_no").' - <a href="index.php?page=community&subpage=plugin_manager&amp;pluginname='.$cur.'&amp;activate=1">'.$I18N_COM->msg("plugin_activate").'</a>';
    }
    else
    {
      $status = $I18N_COM->msg("plugin_notinstalled");
    }

    echo '
        <tr>
          <td class="rex-icon"><img src="media/addon.gif" alt="'. htmlspecialchars($cur) .'" title="'. htmlspecialchars($cur) .'"/></td>
          <td>'.htmlspecialchars($cur).' [<a href="index.php?page=community&subpage=plugin_manager&amp;function=help&amp;pluginname='.$cur.'">?</a>]</td>
          <td>'.$install.'</td>
          <td>'.$status.'</td>
          <td>'.$uninstall.'</td>
        </tr>'."\n   ";
  }

  echo '</tbody>
      </table>';
}
?>