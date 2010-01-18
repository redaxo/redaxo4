<?php

function rex_a657_dashboard_notification($params)
{
  $notice = rex_a657_check_version();
  return $params['subject']. rex_a655_notification_wrapper($notice); 
}

