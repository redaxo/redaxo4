<?php

/*abstract*/ class rex_installManager
{
  var $i18nPrefix;
  
  function rex_installManager($i18nPrefix)
  {
    $this->i18nPrefix = $i18nPrefix;
  }
  
  /**
   * 
   */
  /*public*/ function install($addonName, $installDump = true)
  {
    $state = true;
  
    $install_dir  = $this->baseFolder($addonName);
    $install_file = $install_dir.'install.inc.php';
    $install_sql  = $install_dir.'install.sql';
    $config_file  = $install_dir.'config.inc.php';
    
    // Prüfen des Addon Ornders auf Schreibrechte,
    // damit das Addon später wieder gelöscht werden kann
    $state = rex_is_writable($install_dir);
  
    if ($state === true)
    {
      if (is_readable($install_file))
      {
        $this->includeInstaller($install_file);
  
        // Wurde das "install" Flag gesetzt?
        // Fehlermeldung ausgegeben? Wenn ja, Abbruch
        $instmsg = $this->apiCall('getProperty', array($addonName, 'installmsg', ''));
        
        if (!$this->apiCall('isInstalled', array($addonName)) || $instmsg)
        {
          $state = $this->I18N('no_install', $addonName).'<br />';
          if ($instmsg == '')
          {
            $state .= $this->I18N('no_reason');
          }
          else
          {
            $state .= $instmsg;
          }
        }
        else
        {
          // check if config file exists
          if (is_readable($config_file))
          {
            if (!$this->apiCall('isActivated', array($addonName)))
            {
              $this->includeConfig($config_file);
            }
          }
          else
          {
            $state = $this->I18N('config_not_found');
          }
  
          if($installDump === true && $state === true && is_readable($install_sql))
          {
            $state = rex_install_dump($install_sql);
  
            if($state !== true)
              $state = 'Error found in install.sql:<br />'. $state;
          }
  
          // Installation ok
          if ($state === true)
          {
            // regenerate Addons file
            $state = $this->generateConfig();
          }
        }
      }
      else
      {
        $state = $this->I18N('install_not_found');
      }
    }
  
    if($state !== true)
      $this->apiCall('setProperty', array($addonName, 'install', 0));
  
    return $state;
  }
  
  /**
   * 
   */
  /*public*/ function uninstall($addonName)
  {
    $state = true;
    
    $install_dir    = $this->baseFolder($addonName);
    $uninstall_file = $install_dir.'uninstall.inc.php';
    $uninstall_sql  = $install_dir.'uninstall.sql';
  
    if (is_readable($uninstall_file))
    {
      $this->includeUninstaller($uninstall_file);
  
      // Wurde das "install" Flag gesetzt?
      // Fehlermeldung ausgegeben? Wenn ja, Abbruch
      $instmsg = $this->apiCall('getProperty', array($addonName, 'installmsg', ''));
      
      if ($this->apiCall('isInstalled', array($addonName)) || $instmsg)
      {
        $state = $this->I18N('no_uninstall', $addonName).'<br />';
        if ($instmsg == '')
        {
          $state .= $this->I18N('no_reason');
        }
        else
        {
          $state .= $instmsg;
        }
      }
      else
      {
        $state = $this->deactivate($addonName);
  
        if($state === true && is_readable($uninstall_sql))
        {
          $state = rex_install_dump($uninstall_sql);
  
          if($state !== true)
            $state = 'Error found in uninstall.sql:<br />'. $state;
        }
  
        if ($state === true)
        {
          // regenerate Addons file
          $state = $this->generateConfig();
        }
      }
    }
    else
    {
      $state = $this->I18N('uninstall_not_found');
    }
  
    // Fehler beim uninstall -> Addon bleibt installiert
    if($state !== true)
      $this->apiCall('setProperty', array($addonName, 'install', 1));
  
    return $state;
  }
  
  /**
   * 
   */
  /*public*/ function activate($addonName)
  {
    if ($this->apiCall('isInstalled', array($addonName)))
    {
      $this->apiCall('setProperty', array($addonName, 'status', 1));
      $state = $this->generateConfig();
    }
    else
    {
      $state = $this->I18N('no_activation', $addonName);
    }
  
    if($state !== true)
      $this->apiCall('setProperty', array($addonName, 'status', 0));
  
    return $state;
  }
  
  /**
   * 
   */
  /*public*/ function deactivate($addonName)
  {
    $this->apiCall('setProperty', array($addonName, 'status', 0));
    $state = $this->generateConfig();
  
    if($state !== true)
      $this->apiCall('setProperty', array($addonName, 'status', 1));
      
    return $state;
  }
  
  /**
   * 
   */
  /*public*/ function delete($addonName)
  {
    // zuerst deinstallieren
    // bei erfolg, komplett löschen
    $state = $this->uninstall($addonName);
    $state = $state && rex_deleteDir($this->baseFolder($addonName), true);
    $state = $state && $this->generateConfig();
  
    return $state;
  }
  
  /*protected*/ function I18N()
  {
    global $I18N;
    
    debug_print_backtrace();
    
    $args = func_get_args();
    $args[0] = $this->i18nPrefix. $args[0];
    
    return rex_call_func(array($I18N, 'msg'), $args, false);
  }
  
  /*protected*/ function includeConfig($configFile)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
  
  /*protected*/ function includeInstaller($installFile)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
  
  /*protected*/ function includeUninstaller($uninstallFile)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
  
  /*protected*/ function generateConfig()
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
  
  /*protected*/ function apiCall($method, $arguments)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
      
  /*protected*/ function baseFolder($addonName)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
}

class rex_addonManager extends rex_installManager
{
  var $configArray;
  
  function rex_addonManager($configArray)
  {
    $this->configArray = $configArray;
    parent::rex_installManager('addon_');
  }
  
  /*public*/ function delete($addonName)
  {
    global $REX, $I18N;
    
    // System AddOns dürfen nicht gelöscht werden!
    if(in_array($addonName, $REX['SYSTEM_ADDONS']))
      return $I18N->msg('addon_systemaddon_delete_not_allowed');
      
    parent::delete($addonName);
  }
  
  /*protected*/ function includeConfig($configFile)
  {
    global $REX;
    require $configFile;
  }
  
  
  /*protected*/ function includeInstaller($installFile)
  {
    global $REX;
    require $installFile;
  }
  
  /*protected*/ function includeUninstaller($uninstallFile)
  {
    global $REX;
    require $uninstallFile;
  }
  
  /*protected*/ function generateConfig()
  {
    return rex_generateAddons($this->configArray);
  }
  
  /*protected*/ function apiCall($method, $arguments)
  {
    if(!is_array($arguments))
      trigger_error('Expecting $arguments to be an array!', E_USER_ERROR);
      
    return rex_call_func(array('OOAddon', $method), $arguments, false);
  }
  
  /*protected*/ function baseFolder($addonName)
  {
    return rex_addons_folder($addonName);
  }
}

class rex_pluginManager extends rex_installManager
{
  var $configArray;
  var $addonName;
  
  function rex_pluginManager($configArray, $addonName)
  {
    $this->configArray =& $configArray;
    $this->addonName = $addonName;
    parent::rex_installManager('plugin_');
  }
  
  /*protected*/ function includeConfig($configFile)
  {
    global $REX;
    require $configFile;
  }
  
  /*protected*/ function includeInstaller($installFile)
  {
    global $REX;
    $ADDONSsic = $REX['ADDON'];
    $REX['ADDON'] = array();
    
    require $installFile;
    
    $ADDONSsic['plugins'][$this->addonName] = $REX['ADDON'];
    $REX['ADDON'] = $ADDONSsic;
  }
  
  /*protected*/ function includeUninstaller($uninstallFile)
  {
    global $REX;
    $ADDONSsic = $REX['ADDON'];
    $REX['ADDON'] = array();
    
    require $uninstallFile;
    
    $ADDONSsic['plugins'][$this->addonName] = $REX['ADDON'];
    $REX['ADDON'] = $ADDONSsic;
  }
  
  /*protected*/ function generateConfig()
  {
    return rex_generatePlugins($this->configArray);
  }
  
  /*protected*/ function apiCall($method, $arguments)
  {
    if(!is_array($arguments))
      trigger_error('Expecting $arguments to be an array!', E_USER_ERROR);
      
    // addonName als 1. Parameter einfügen
    array_unshift($arguments, $this->addonName);
      
    return rex_call_func(array('OOPlugin', $method), $arguments, false);
  }
  
  /*protected*/ function baseFolder($addonName)
  {
    return rex_plugins_folder($this->addonName, $addonName);
  }
}