<?php

/**
 * Managerklasse zum handeln von rexAddons
 */
/*abstract*/ class rex_installManager
{
  var $i18nPrefix;
  
  /**
   * Konstruktor
   * 
   * @param $i18nPrefix Sprachprefix aller I18N Sprachschlüssel
   */
  function rex_installManager($i18nPrefix)
  {
    $this->i18nPrefix = $i18nPrefix;
  }
  
  /**
   * Installiert ein Addon
   * 
   * @param $addonName Name des Addons
   * @param $installDump Flag, ob die Datei install.sql importiert werden soll
   */
  /*public*/ function install($addonName, $installDump = TRUE)
  {
    $state = TRUE;
  
    $install_dir  = $this->baseFolder($addonName);
    $install_file = $install_dir.'install.inc.php';
    $install_sql  = $install_dir.'install.sql';
    $config_file  = $install_dir.'config.inc.php';
    $files_dir    = $install_dir.'files';
    
    // Prüfen des Addon Ornders auf Schreibrechte,
    // damit das Addon später wieder gelöscht werden kann
    $state = rex_is_writable($install_dir);
    
    if ($state === TRUE)
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
  
          if($installDump === TRUE && $state === TRUE && is_readable($install_sql))
          {
            $state = rex_install_dump($install_sql);
  
            if($state !== TRUE)
              $state = 'Error found in install.sql:<br />'. $state;
          }
  
          // Installation ok
          if ($state === TRUE)
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
  
    // Dateien kopieren
    if($state === TRUE && is_dir($files_dir))
    {
      if(!rex_copyDir($files_dir, $this->mediaFolder($addonName)))
      {
        $state = $this->I18N('install_cant_copy_files');
      }
    }
    
    if($state !== TRUE)
      $this->apiCall('setProperty', array($addonName, 'install', 0));
  
    return $state;
  }
  
  /**
   * De-installiert ein Addon
   * 
   * @param $addonName Name des Addons
   */
  /*public*/ function uninstall($addonName)
  {
    $state = TRUE;
    
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
  
        if($state === TRUE && is_readable($uninstall_sql))
        {
          $state = rex_install_dump($uninstall_sql);
  
          if($state !== TRUE)
            $state = 'Error found in uninstall.sql:<br />'. $state;
        }
  
        if ($state === TRUE)
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
    
    if($state === TRUE)
    {
      if(!rex_deleteDir($this->mediaFolder($addonName), TRUE))
      {
        $state = $this->I18N('install_cant_delete_files');
      }
    }
  
    // Fehler beim uninstall -> Addon bleibt installiert
    if($state !== TRUE)
      $this->apiCall('setProperty', array($addonName, 'install', 1));
  
    return $state;
  }
  
  /**
   * Aktiviert ein Addon
   * 
   * @param $addonName Name des Addons
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
  
    if($state !== TRUE)
      $this->apiCall('setProperty', array($addonName, 'status', 0));
  
    return $state;
  }
  
  /**
   * Deaktiviert ein Addon
   * 
   * @param $addonName Name des Addons
   */
  /*public*/ function deactivate($addonName)
  {
    $this->apiCall('setProperty', array($addonName, 'status', 0));
    $state = $this->generateConfig();
  
    if($state !== TRUE)
      $this->apiCall('setProperty', array($addonName, 'status', 1));
      
    return $state;
  }
  
  /**
   * Löscht ein Addon im Filesystem
   * 
   * @param $addonName Name des Addons
   */
  /*public*/ function delete($addonName)
  {
    // zuerst deinstallieren
    // bei erfolg, komplett löschen
    $state = TRUE;
    $state = $state && $this->uninstall($addonName);
    $state = $state && rex_deleteDir($this->baseFolder($addonName), TRUE);
    $state = $state && $this->generateConfig();
  
    return $state;
  }
  
  /**
   * Übersetzen eines Sprachschlüssels unter Verwendung des Prefixes 
   */
  /*protected*/ function I18N()
  {
    global $I18N;
    
    $args = func_get_args();
    $args[0] = $this->i18nPrefix. $args[0];
    
    return rex_call_func(array($I18N, 'msg'), $args, false);
  }

  /**
   * Bindet die config-Datei eines Addons ein
   */
  /*protected*/ function includeConfig($configFile)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
  
  /**
   * Bindet die installations-Datei eines Addons ein
   */
  /*protected*/ function includeInstaller($installFile)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
  
  /**
   * Bindet die deinstallations-Datei eines Addons ein
   */
  /*protected*/ function includeUninstaller($uninstallFile)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
  
  /**
   * Speichert den aktuellen Zustand
   */
  /*protected*/ function generateConfig()
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
  
  /**
   * Ansprechen einer API funktion
   * 
   * @param $method Name der Funktion
   * @param $arguments Array von Parametern/Argumenten
   */
  /*protected*/ function apiCall($method, $arguments)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
      
  /**
   * Findet den Basispfad eines Addons
   */
  /*protected*/ function baseFolder($addonName)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
  
  /**
   * Findet den Basispfad für Media-Dateien
   */
  /*protected*/ function mediaFolder($addonName)
  {
    trigger_error('Method has to be overridden by subclass!', E_USER_ERROR);
  }
}

/**
 * Manager zum installieren von OOAddons
 */
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
  
  /*protected*/ function mediaFolder($addonName)
  {
    global $REX;
    return $REX['MEDIAFOLDER'] .DIRECTORY_SEPARATOR .'addons'. DIRECTORY_SEPARATOR .$addonName;
  }
}

/**
 * Manager zum intallieren von OOPlugins
 */
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
    // TODO ordentliche formatumwandlung
    require $configFile;
  }
  
  /*protected*/ function includeInstaller($installFile)
  {
    global $REX;
    $ADDONSsic = $REX['ADDON'];
    $REX['ADDON'] = array();
    
    // TODO ordentliche formatumwandlung
    require $installFile;
    
    $ADDONSsic['plugins'][$this->addonName] = $REX['ADDON'];
    $REX['ADDON'] = $ADDONSsic;
  }
  
  /*protected*/ function includeUninstaller($uninstallFile)
  {
    global $REX;
    $ADDONSsic = $REX['ADDON'];
    $REX['ADDON'] = array();
    
    // TODO ordentliche formatumwandlung
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
  
  /*protected*/ function baseFolder($pluginName)
  {
    return rex_plugins_folder($this->addonName, $pluginName);
  }
  
  /*protected*/ function mediaFolder($pluginName)
  {
    global $REX;
    return $REX['MEDIAFOLDER'] .DIRECTORY_SEPARATOR .'addons'. DIRECTORY_SEPARATOR. $this->addonName .DIRECTORY_SEPARATOR .'plugins'. DIRECTORY_SEPARATOR. $pluginName;
  }
}