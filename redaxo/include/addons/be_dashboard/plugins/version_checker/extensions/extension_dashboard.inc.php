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

function rex_a657_dashboard_notification($params)
{
  $notice = rex_a657_check_version();
  return $params['subject']. rex_a655_notification_wrapper($notice); 
}

