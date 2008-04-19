<?php

/**
 * Image-Resize Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]yakmara[dot]de Jan Kristinus
 *
 * @package redaxo4
 * @version $Id: index.inc.php,v 1.7 2008/04/12 08:04:07 kills Exp $
 */

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');
$msg = '';

require $REX['INCLUDE_PATH'] . '/layout/top.php';


if ($subpage == 'clear_cache')
{
  $c = rex_thumbnail::deleteCache();
  $msg = $I18N_IMG_RES->msg('cache_files_removed', $c);
}

// Build Subnavigation
$subpages = array (
  	array ('', $I18N_IMG_RES->msg('subpage_desc')),
  	array ('settings', $I18N_IMG_RES->msg('subpage_config')),
  	array ('clear_cache', $I18N_IMG_RES->msg('subpage_clear_cache')),
	);

rex_title('Image Resize', $subpages);

// Include Current Page
switch($subpage)
{
  case 'settings' :
  {
    break;
  }

  default:
  {
  	if ($msg != '')
		  echo rex_info($msg);

	  $subpage = 'overview';
  }
}

require $REX['INCLUDE_PATH'] . '/addons/image_resize/pages/'.$subpage.'.inc.php';
require $REX['INCLUDE_PATH'] . '/layout/bottom.php';

?>