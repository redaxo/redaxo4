<?php

/**
 * image_manager Addon
 *
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$mypage = 'image_manager';

$REX['ADDON']['name'][$mypage] = 'Image Manager';
$REX['ADDON']['perm'][$mypage] = 'image_manager[]';
$REX['ADDON']['version'][$mypage] = '1.2.1';
$REX['ADDON']['author'][$mypage] = 'Markus Staab, Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
$REX['PERM'][] = 'image_manager[]';

$REX['ADDON']['image_manager']['jpg_quality'] = 85;
$settings = rex_path::addonData($mypage, 'settings.inc.php');
if (file_exists($settings)) {
    include $settings;
}

$REX['ADDON']['image_manager']['classpaths']['effects'] = array();
$REX['ADDON']['image_manager']['classpaths']['effects'][] = dirname(__FILE__) . '/classes/effects/';

require_once dirname(__FILE__) . '/classes/class.rex_image.inc.php';
require_once dirname(__FILE__) . '/classes/class.rex_image_cacher.inc.php';
require_once dirname(__FILE__) . '/classes/class.rex_image_manager.inc.php';
require_once dirname(__FILE__) . '/classes/class.rex_effect_abstract.inc.php';




// RUN ON EP ADDONS_INCLUDED
////////////////////////////////////////////////////////////////////////////////
if (!$REX['SETUP']) {
    rex_register_extension('ADDONS_INCLUDED', 'image_manager_init', array(), REX_EXTENSION_EARLY);
}

if (!function_exists('image_manager_init')) {
    function image_manager_init()
    {
        global $REX;

        //--- handle image request
        $rex_img_file = rex_get('rex_img_file', 'string');
        $rex_img_type = rex_get('rex_img_type', 'string');
        $rex_img_init = false;
        if ($rex_img_file != '' && $rex_img_type != '') {
            $rex_img_init = true;
        }

        $imagepath = $REX['HTDOCS_PATH'] . $REX['MEDIA_DIR'] . '/' . $rex_img_file;
        $cachepath = $REX['GENERATED_PATH'] . '/files/';

        // REGISTER EXTENSION POINT
        $subject = array(
            'rex_img_type' => $rex_img_type,
            'rex_img_file' => $rex_img_file,
            'rex_img_init' => $rex_img_init,
            'imagepath'    => $imagepath,
            'cachepath'    => $cachepath
        );
        $subject   = rex_register_extension_point('IMAGE_MANAGER_INIT', $subject);

        if (isset($subject['rex_img_file'])) {
            $rex_img_file = $subject['rex_img_file'];
        }
        if (isset($subject['rex_img_type'])) {
            $rex_img_type = $subject['rex_img_type'];
        }
        if (isset($subject['imagepath'])) {
            $imagepath    = $subject['imagepath'];
        }
        if (isset($subject['cachepath'])) {
            $cachepath    = $subject['cachepath'];
        }

        if ($subject['rex_img_init']) {
            $image         = new rex_image($imagepath);
            $image_cacher  = new rex_image_cacher($cachepath);
            $image_manager = new rex_image_manager($image_cacher);

            $image = $image_manager->applyEffects($image, $rex_img_type);
            $image_manager->sendImage($image, $rex_img_type);
            exit();
        }
    }
}


if ($REX['REDAXO']) {
    // delete thumbnails on mediapool changes
    if (!function_exists('rex_image_manager_ep_mediaupdated')) {
        rex_register_extension('MEDIA_UPDATED', 'rex_image_manager_ep_mediaupdated');
        rex_register_extension('MEDIA_DELETED', 'rex_image_manager_ep_mediaupdated');
        function rex_image_manager_ep_mediaupdated($params)
        {
            rex_image_cacher::deleteCache($params['filename']);
        }
    }

    // handle backend pages
    $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang/');

    $descPage = new rex_be_page($I18N->msg('imanager_subpage_desc'), array(
        'page' => 'image_manager',
        'subpage' => ''
    )
    );
    $descPage->setHref('index.php?page=image_manager');

    $confPage = new rex_be_page($I18N->msg('imanager_subpage_types'), array(
        'page' => 'image_manager',
        'subpage' => array('types', 'effects')
    )
    );
    $confPage->setHref('index.php?page=image_manager&subpage=types');

    $settingsPage = new rex_be_page($I18N->msg('imanager_subpage_config'), array(
        'page' => 'image_manager',
        'subpage' => 'settings'
    )
    );
    $settingsPage->setHref('index.php?page=image_manager&subpage=settings');

    $ccPage = new rex_be_page($I18N->msg('imanager_subpage_clear_cache'), array(
        'page' => 'image_manager',
        'subpage' => 'clear_cache'
    )
    );
    $ccPage->setHref('index.php?page=image_manager&subpage=clear_cache');
    $ccPage->setLinkAttr('onclick', 'return confirm(\'' . $I18N->msg('imanager_type_cache_delete') . ' ?\')');

    $REX['ADDON']['pages'][$mypage] = array (
        $descPage, $confPage, $settingsPage, $ccPage
    );
}
