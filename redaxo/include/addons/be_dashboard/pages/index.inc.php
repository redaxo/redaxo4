<?php

/**
 *
 * @package redaxo4
 * @version svn:$Id$
 */


include $REX["INCLUDE_PATH"]."/layout/top.php";

rex_title($I18N->msg('dashboard'), '');

echo '<div id="rex-form-dashboard">';

// ----- EXTENSION POINT
$dashboard_content = '';
$dashboard_content = rex_register_extension_point('DASHBOARD_CONTENT', $dashboard_content, array () );
echo $dashboard_content;
			
echo '</div>';

include $REX["INCLUDE_PATH"]."/layout/bottom.php";