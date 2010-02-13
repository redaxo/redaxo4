<?php

/**
 * Backenddashboard Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

include $REX["INCLUDE_PATH"]."/layout/top.php";

rex_title($I18N->msg('dashboard'), '');

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
    $component = new rex_dashboard_component('notifications');
    $component->setTitle($I18N->msg('dashboard_notifications'));
    $component->setContent($content);
    
  	echo '<div class="rex-dashboard-section rex-dashboard-1col rex-dashboard-notifications">
  	        <div class="rex-dashboard-column rex-dashboard-column-first">
              '.$component->get().'
            </div>
          </div>';
  }
}

// ----- EXTENSION POINT
$dashboard_components = array();
$dashboard_components = rex_register_extension_point('DASHBOARD_COMPONENT', $dashboard_components);

// ------------ sort components by block and format
$components = array();
foreach($dashboard_components as $index => $component)
{
  if(rex_dashboard_component::isValid($component))
  {
    $block  = $component->getBlock();
    $format = $component->getFormat();
    
    if(!isset($components[$format]))
    {
      $components[$format] = array();
    }
    if(!isset($components[$format][$block]))
    {
      $components[$format][$block] = array();
    }
    
    $components[$format][$block][] = $component;
  }
  unset($dashboard_components[$index]);
}

// ------------ show components
foreach($components as $format => $componentBlocks)
{
  foreach($componentBlocks as $block => $componentBlock)
  {
    if($format == 'full')
    {
      echo '<div class="rex-dashboard-section rex-dashboard-1col rex-dashboard-components">
              <div class="rex-dashboard-column rex-dashboard-column-first">';
      
      if($block != '')
      {
          echo '<h2 class="rex-dashboard-hl1">'. $block .'</h2>';
      }
      
      foreach($componentBlock as $component)
      {
        echo $component->get();
      }
      
      echo '  </div>
            </div>';
    }
    else if ($format == 'half')
    {
      $numComponents = count($componentBlock);
      $componentsPerCol = ceil($numComponents / 2);
      
      echo '<div class="rex-dashboard-section rex-dashboard-2col rex-dashboard-components">';
      
      if($block != '')
      {
          echo '<h2 class="rex-dashboard-hl1">'. $block .'</h2>';
      }
      
      // show first column
      $i = 0;
      echo '  <div class="rex-dashboard-column rex-dashboard-column-first">';
      foreach($componentBlock as $index => $component)
      {
        echo $component->get();
        unset($componentBlock[$index]);
        
        $i++;
        if($i == $componentsPerCol) break;
      }
      echo '</div>';
      // /show first column
      
      // show second column
      echo '<div class="rex-dashboard-column">';
      foreach($componentBlock as $index => $component)
      {
        echo $component->get();
        unset($componentBlock[$index]);
      }
      echo '</div>';
      // /show second column
      
      echo '</div>';
    }
  }
  unset($components[$format][$block]);
}
//
//// ------------ show fullsize components
//echo '<div class="rex-dashboard-section rex-dashboard-1col rex-dashboard-components">
//        <div class="rex-dashboard-column rex-dashboard-column-first">';
//
//if(isset($components['full']) && count($components['full']) > 0)
//{
//  foreach($blocks as $block)
//  {
//    foreach($components['full'] as $index => $component)
//    {
//      if($block == $component->getBlock())
//      {
//        echo $component->get();
//        unset($components['full'][$index]);
//      }
//    }
//  }
//}
//			
//echo '  </div>
//      </div>';
// /----------- show fullsize components

//$numComponents = count($components['half']);
//$componentsPerCol = ceil($numComponents / 2);
//
//echo '<div class="rex-dashboard-section rex-dashboard-2col rex-dashboard-components">';
//
//// show first column
//$i = 0;
//echo '  <div class="rex-dashboard-column rex-dashboard-column-first">';
//foreach($dashboard_components as $index => $component)
//{
//  if(rex_dashboard_component::isValid($component))
//  {
//    echo $component->get();
//    unset($dashboard_components[$index]);
//    
//    $i++;
//    if($i == $componentsPerCol) break;
//  }
//}
//echo '</div>';
//// /show first column
//
//// show second column
//echo '<div class="rex-dashboard-column">';
//foreach($dashboard_components as $index => $component)
//{
//  if(rex_dashboard_component::isValid($component))
//  {
//    echo $component->get();
//    unset($dashboard_components[$index]);
//  }
//}
//echo '</div>';
//// /show second column
//
//echo '</div>';
// ----------- /show halfsize components (remaing components)

include $REX["INCLUDE_PATH"]."/layout/bottom.php";