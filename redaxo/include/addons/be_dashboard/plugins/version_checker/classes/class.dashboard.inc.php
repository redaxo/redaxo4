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
    // this is the new style constructor used by newer php versions.
    // important: if you change the signatur of this method, change also the signature of rex_version_checker_notification()
    function __construct()
    {
        $this->rex_version_checker_notification();
    }

     // this is the deprecated old style constructor kept for compat reasons. 
    // important: if you change the signatur of this method, change also the signature of __construct()
    function rex_version_checker_notification()
    {
        // default cache lifetime in seconds
        $cache_options['lifetime'] = 3600;

        parent::rex_dashboard_notification('version-checker', $cache_options);
    }

    /*public*/ function checkPermission()
    {
        global $REX;

        return $REX['USER']->isAdmin();
    }

    /*protected*/ function prepare()
    {
        global $I18N;

        $versionCheck = rex_a657_check_version();

        if ($versionCheck) {
            $this->setMessage($versionCheck);
        } else {
            $this->setMessage(rex_warning('Version-Checker: ' . $I18N->msg('vchecker_connection_error')));
        }
    }
}
