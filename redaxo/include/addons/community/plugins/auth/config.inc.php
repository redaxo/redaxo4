<?php

/**
 * Plugin Auth
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/functions/function.rex_com_checkperm.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/functions/function.rex_com_auth_urlendecode.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/classes/class.rex_com_navigation.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/classes/class.rex_com_user.inc.php";

rex_register_extension('REX_NAVI_CLASSNAME', create_function('','return "rex_com_navigation";'));

// --- DYN
$REX['ADDON']['community']['plugin_auth']['auth_active'] = "1";
$REX['ADDON']['community']['plugin_auth']['stay_active'] = "1";
$REX['ADDON']['community']['plugin_auth']['article_login_ok'] = 1;
$REX['ADDON']['community']['plugin_auth']['article_login_failed'] = 1;
$REX['ADDON']['community']['plugin_auth']['article_logout'] = 1;
$REX['ADDON']['community']['plugin_auth']['article_withoutperm'] = 67;
// --- /DYN

$REX['ADDON']['community']['xform_path']['value'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/xform/value/";
$REX['ADDON']['community']['xform_path']['validate'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/xform/validate/";
$REX['ADDON']['community']['xform_path']['action'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/xform/action/";

$REX['ADDON']['community']['plugin_auth']['rex_com_auth_login_definition'] = array();
$REX['ADDON']['community']['plugin_auth']['rex_com_auth_login_definition'][] = array('field' => 'login', 'compare' => '=', 'request' => 'rex_com_auth_name', 'type' => 'string');
// $REX['ADDON']['community']['plugin_auth']['rex_com_auth_login_definition'][] = array('field' => 'email', 'compare' => '=', 'request' => 'rex_com_auth_name', 'type' => 'string');
$REX['ADDON']['community']['plugin_auth']['rex_com_auth_login_definition'][] = array('field' => 'password', 'compare' => '=', 'request' => 'rex_com_auth_password', 'type' => 'string');

if ($REX["REDAXO"])
{
	if ($REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[auth]")))
		$REX['ADDON']['community']['SUBPAGES'][] = array('plugin.auth','Authentifizierung');

}elseif($REX['ADDON']['community']['plugin_auth']['auth_active'] == 1)
{

	// nur im Frontend..
	rex_register_extension('ADDONS_INCLUDED', create_function('','
	
		global $REX,$I18N;
		include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/inc/auth.php";
	
	'));
}

?>