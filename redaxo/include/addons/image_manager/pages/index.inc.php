<?php

/**
 * Image-Resize Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]yakmara[dot]de Jan Kristinus
 * @author dh[at]daveholloway[dot]co[dot]uk Dave Holloway
 *
 * @package redaxo4
 * @version svn:$Id$
 */

require_once dirname(__FILE__) . '/../functions/function_rex_effects.inc.php';
require_once dirname(__FILE__) . '/../functions/function_rex_extensions.inc.php';

require $REX['INCLUDE_PATH'] . '/layout/top.php';

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string', 'types');
$func = rex_request('func', 'string');
$msg = '';

if ($subpage == 'clear_cache') {
    $c = rex_image_cacher::deleteCache();
    $msg = $I18N->msg('imanager_cache_files_removed', $c);
}

rex_title('Image Manager', $REX['ADDON']['pages']['image_manager']);

if ($msg != '') {
    echo rex_info($msg);
}

// Include Current Page
switch ($subpage) {
    case 'overview' :
    case 'effects' :
    case 'settings' :
        break;

    default:
        $subpage = 'types';

}

require dirname(__FILE__) . '/' . $subpage . '.inc.php';
require $REX['INCLUDE_PATH'] . '/layout/bottom.php';
