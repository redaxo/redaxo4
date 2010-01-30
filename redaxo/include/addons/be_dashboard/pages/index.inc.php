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
$dashboard_notifications = array();
$dashboard_notifications = rex_register_extension_point('DASHBOARD_NOTIFICATION', $dashboard_notifications);

if(count($dashboard_notifications) > 0)
{
  $content = '';
  foreach($dashboard_notifications as $notification)
  {
    if(rex_dashboard_notification::isValid($notification))
    {
      $content .= $notification->get();
    }
  }
  unset($dashboard_notifications);
  
  if($content != '')
  {
    $component = new rex_dashboard_component($I18N->msg('dashboard_notifications'));
    $component->setContent('<ul>'. $content .'</ul>');
    echo $component->get();
  }
}

// ----- EXTENSION POINT
$dashboard_components = array();
$dashboard_components = rex_register_extension_point('DASHBOARD_COMPONENT', $dashboard_components);
foreach($dashboard_components as $component)
{
  if(rex_dashboard_component::isValid($component))
  {
    echo $component->get();
  }
}
unset($dashboard_components);
			
echo '</div>';

include $REX["INCLUDE_PATH"]."/layout/bottom.php";