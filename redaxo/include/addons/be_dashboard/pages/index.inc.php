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
$dashboard_notifications = '';
$dashboard_notifications = rex_register_extension_point('DASHBOARD_NOTIFICATION', $dashboard_notifications);

if($dashboard_notifications != '')
{
  $componentTitle = $I18N->msg('dashboard_notifications');
  echo rex_a655_component_wrapper($componentTitle, '<ul>'. $dashboard_notifications .'</ul>');
}

// ----- EXTENSION POINT
$dashboard_content = '';
$dashboard_content = rex_register_extension_point('DASHBOARD_COMPONENT', $dashboard_content);
echo $dashboard_content;
			
echo '</div>';

include $REX["INCLUDE_PATH"]."/layout/bottom.php";