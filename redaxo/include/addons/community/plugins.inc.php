<?php

$REX['ADDON']['plugins']['community'] = array();

// ----------------- DONT EDIT BELOW THIS
// --- DYN
$REX['ADDON']['plugins']['community']['articlecomment']['install'] = 1;
$REX['ADDON']['plugins']['community']['articlecomment']['status'] = 1;

$REX['ADDON']['plugins']['community']['board']['install'] = 1;
$REX['ADDON']['plugins']['community']['board']['status'] = 1;

$REX['ADDON']['plugins']['community']['contacts']['install'] = 1;
$REX['ADDON']['plugins']['community']['contacts']['status'] = 1;

$REX['ADDON']['plugins']['community']['dummy']['install'] = 0;
$REX['ADDON']['plugins']['community']['dummy']['status'] = 0;

$REX['ADDON']['plugins']['community']['guestbook']['install'] = 1;
$REX['ADDON']['plugins']['community']['guestbook']['status'] = 1;

$REX['ADDON']['plugins']['community']['messages']['install'] = 1;
$REX['ADDON']['plugins']['community']['messages']['status'] = 1;

$REX['ADDON']['plugins']['community']['setup']['install'] = 1;
$REX['ADDON']['plugins']['community']['setup']['status'] = 1;

$REX['ADDON']['pluginlist']['community'] = "articlecomment,board,contacts,group,mytest,newsletter,setup,messages,activity,guestbook,group_xxx,activity_xxx,mytest_xxx,newsletter_xxx,rundmail,mitmachwerkstatt,dummy,xxx_activity,xxx_group,xxx_newsletter";
// --- /DYN
// ----------------- /DONT EDIT BELOW THIS

// ----- all addons configs included
rex_register_extension_point('COM_PLUGINS_INCLUDED');

?>