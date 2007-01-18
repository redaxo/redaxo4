<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */

$mypage = 'metaform';

if ($REX['REDAXO']) $I18N_META_FORM = new i18n($REX['LANG'],$REX['INCLUDE_PATH'].'/addons/'. $mypage .'/lang');

$REX['ADDON']['rxid'][$mypage] = '62';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'Meta Form';
$REX['ADDON']['perm'][$mypage] = 'metaform[]';

$REX['PERM'][] = 'metaform[]';

if($REX['REDAXO'])
{
	if($page == 'content' && $mode =='meta')
	{
	  include($REX['INCLUDE_PATH']. '/addons/'. $mypage .'/extensions/extension_meta_form.inc.php');
	}
	
  include($REX['INCLUDE_PATH']. '/addons/'. $mypage .'/extensions/extension_meta_params.inc.php');
}
?>