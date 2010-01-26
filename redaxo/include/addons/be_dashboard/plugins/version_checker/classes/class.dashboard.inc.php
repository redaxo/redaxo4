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

class rex_version_checker_notification extends rex_dashboard_notification
{
  function rex_version_checker_notification()
  {
    parent::rex_dashboard_notification();
    $this->setMessage(rex_a657_check_version());
  }
}
