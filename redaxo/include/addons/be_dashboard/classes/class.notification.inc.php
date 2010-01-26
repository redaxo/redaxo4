<?php

/*abstract*/ class rex_dashboard_notification
{
  var $message;
  
  function rex_dashboard_notification()
  {
    $this->message = '';
  }
  
  /*public*/ function setMessage($message)
  {
    $this->message = $message;
  }
  
  /*public*/ function getMessage()
  {
    return $this->message;
  } 
  
  /*public*/ function get()
  {
    return '<li>'. $this->getMessage() .'</li>';
  }
  
  /*public*/ function registerAsExtension($params)
  {
    $params['subject'][] = $this;
    return $params['subject'];
  }
   
  /*
   * Static Method: Returns boolean if is notification
   */
  /*public static*/ function isValid($notification)
  {
    return is_object($notification) && is_a($notification, 'rex_dashboard_notification');
  }
}