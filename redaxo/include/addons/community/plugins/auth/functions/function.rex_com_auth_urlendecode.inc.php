<?php

function rex_com_auth_urlencode($url)
{
  $url = base64_encode($url);
  $url = str_replace("/","_",$url);
  $url = str_replace("+","-",$url);
  return $url;
}

function rex_com_auth_urldecode($url)
{
  $url = str_replace("_","/",$url);
  $url = str_replace("-","+",$url);
  $url = base64_decode($url);
  return $url;
}

?>