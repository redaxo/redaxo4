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
  $updateUrl = 'http://www.redaxo.de/de/latestversion';
  
  $latestVersion = @rex_get_file_contents($updateUrl);
  
  if($latestVersion != '')
  {
    return preg_replace('/[^0-9\.]/', '', $latestVersion);
  }
  return false;
}

function rex_a657_check_version()
{
  global $I18N, $REX;
  
  $latestVersion = rex_a657_get_latest_version();
  if(!$latestVersion) return false;
  
  $rexVersion = $REX['VERSION'].'.'.$REX['SUBVERSION'].'.'.$REX['MINORVERSION'];
  if(version_compare($rexVersion, $latestVersion, '>'))
  {
    // Dev version
    $notice = rex_warning($I18N->msg('vchecker_dev_version', $rexVersion));
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

function rex_a657_check_connectivity($url, $port = 80, $timeout = 3)
{
  $fp = @fsockopen($url, $port);
  if (!$fp) {
    // unable to open socket
    return false;
  }
  else
  {
    fwrite($fp, "GET / HTTP/1.0\r\n\r\n");
    stream_set_timeout($fp, $timeout);
    $res = fread($fp, 100);

    $info = stream_get_meta_data($fp);
    fclose($fp);

    if ($info['timed_out']) {
      // connection timeout
      return false;
    }
  }
  
  return true;
}