<?php

/**
 * REDAXO Version Checker Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

function rex_a657_get_latest_version()
{
  $updateUrl = 'http://www.redaxo.de/517-0-latest-redaxo-version-de.html';
  
  return preg_replace('/[^0-9\.]/', '', rex_get_file_contents($updateUrl));
}

function rex_a657_check_version()
{
  global $I18N, $REX;
  
  $latestVersion = rex_a657_get_latest_version();
  $rexVersion = $REX['VERSION'].'.'.$REX['SUBVERSION'].'.'.$REX['MINORVERSION'];
  
  if(version_compare($rexVersion, $latestVersion, '>'))
  {
    // Dev version
    $notice = rex_warning($I18N->msg('vchecker_dev_version'));
  }
  else if (version_compare($rexVersion, $latestVersion, '<'))
  {
    // update required
    $notice = rex_warning($I18N->msg('vchecker_old_version', $rexVersion, $latestVersion));
  }
  else
  {
    // current version
    $notice = rex_info($I18N->msg('vchecker_current_version', $rexVersion));
  }
  
  return $notice;
}
