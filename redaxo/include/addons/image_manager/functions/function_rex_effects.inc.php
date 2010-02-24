<?php

function rex_imanager_supportedEffects()
{
  global $REX;
  
  $dirs = $REX['ADDON']['image_manager']['classpaths']['effects'];
  
  $effects = array();
  foreach($dirs as $dir)
  {
    $files = glob($dir . 'class.rex_effect_*.inc.php');
    if($files)
    {
      foreach($files as $file)
      {
        $effects[rex_imanager_effectClass($file)] = $file;
      }
    }
  }
  return $effects;
}

function rex_imanager_supportedEffectNames()
{
  $effectNames = array();
  foreach(rex_imanager_supportedEffects() as $effectClass => $effectFile)
  {
    $effectNames[] = rex_imanager_effectName($effectFile);
  }
  return $effectNames;
}

function rex_imanager_effectName($effectFile)
{
  return str_replace(
      array('class.rex_effect_', '.inc.php'),
      '',
      basename($effectFile)
    );
}

function rex_imanager_effectClass($effectFile)
{
  return str_replace(
      array('class.', '.inc.php'),
      '',
      basename($effectFile)
    );
}