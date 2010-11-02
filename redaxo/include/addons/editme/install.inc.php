<?php

/**
 * editme
 *
 * @author jan@kristinus.de
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$error = '';
if (!OOAddon::isAvailable('xform'))
{
  $error = 'AddOn "XForm" ist nicht installiert und aktiviert.';
}elseif(OOAddon::getVersion('xform') < '2.2')
{
  $error = 'Das AddOn "XForm" muss mindestens in der Version 2.2 vorhanden sein.';
}elseif(!rex_xform_manager::createBasicSet('em'))
{
  $error = 'Der XForm Manager hat das "em" BasicSet nicht installieren können.';
}

if($error == '')
{
  $REX['ADDON']['install']['editme'] = 1;
}
else
{
   $REX['ADDON']['installmsg']['editme'] = $error;
}