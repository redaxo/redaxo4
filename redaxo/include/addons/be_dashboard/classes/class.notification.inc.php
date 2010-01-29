<?php

/*abstract*/ class rex_dashboard_notification
{
  var $message;
  
  function rex_dashboard_notification()
  {
    $this->message = '';
  }
  
  /*protected*/ function prepare()
  {
    // override in subclasses to retrieve and set message
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
    $this->prepare();
    
    $message = $this->getMessage();
    
    if($message)
    {
      return '<li>'. $message .'</li>';
    }
    return '';
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