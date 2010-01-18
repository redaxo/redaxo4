<?php

/**
 * Wrapper Funktion um eine Componente ins Dashboard zu integrieren 
 * 
 * @param $componentTitle
 * @param $componentBody
 */
function rex_a655_component_wrapper($componentTitle, $componentBody)
{
  return '<div class="rex-dashboard-component">
            <h3>'. $componentTitle .'</h3>
            '. $componentBody .'
          </div>';
}

/**
 * Wrapper Funktion um eine Benachrichtigung in die Hauptkomponente einzubinden
 * @param $message
 * @return unknown_type
 */
function rex_a655_notification_wrapper($message)
{
  return '<li>'. $message .'</li>';
}