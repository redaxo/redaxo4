<?php

// -------------------------------------------------------------- USER AUTH


$url_params = array();

unset($REX['COM_USER']);
unset($jump_id);

$rex_com_auth_use_jump_url = FALSE;
$rex_com_auth_info = 0;	// 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in / 4 - session timeout
$rex_com_auth_session_duration = 7200; // 3000s Session timeout

// rex_com_auth_stay
// rex_com_auth_jump

$rex_com_auth_login_query = array();
$rex_com_auth_login_request = array();
$rex_com_auth_login = TRUE;

foreach($REX['ADDON']['community']['plugin_auth']['rex_com_auth_login_definition'] as $v)
{
  $replacekey = '###REPLACE_'.$v['field'].'###';
  $rex_com_auth_login_request[$replacekey] = rex_request($v['request'],$v['type']);
  $rex_com_auth_login_query[] = '`'.$v['field'].'`'.$v['compare'].'"'.$replacekey.'"';
  if(!isset($_REQUEST[$v['request']])) $rex_com_auth_login = FALSE;
}
$rex_com_auth_login_query = 'select * from rex_com_user where '.implode(' and ', $rex_com_auth_login_query).' and status>0';

// ----------- COM_USER init

$REX['COM_USER'] = new rex_com_user();


// ---------------------------------------------- LOGOUT

if (rex_request("rex_com_auth_logout","int") == 1) 
{ 

	$REX['COM_USER']->logout(true); // kills session and cookies
	unset($REX['COM_USER']);
	// $url_params['rex_com_auth_logout'] = 1;
	$jump_aid = $REX['ADDON']['community']['plugin_auth']['article_login_failed'];
	$rex_com_auth_use_jump_url = TRUE;
	$rex_com_auth_info = 1;	// 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in / 4 - session timeout


// ---------------------------------------------- SESSION

}elseif (	
			$REX['COM_USER']->getSessionVar('UID') != "" 
			&& 
			$REX['COM_USER']->getSessionVar('SID') != "" 
			&& 
			$REX['COM_USER']->checkQuery(
				'select * from rex_com_user where id="USER_ID" and session_key="USER_SESSION_KEY" and status>0',
				array(
					'USER_ID' => $REX['COM_USER']->getSessionVar('UID'),
					'USER_SESSION_KEY' => $REX['COM_USER']->getSessionVar('SID')
				)
			)
			&& 
			$REX['COM_USER']->getSessionVar('STIME') > ( time() - $rex_com_auth_session_duration)
		) 
{

	// echo "bin eingeloggt";

	$REX['COM_USER']->setSessionVar('STIME',time());


// ---------------------------------------------- LOGIN

	}elseif (
      $rex_com_auth_login 
      && 
      $REX['COM_USER']->checkQuery(
        $rex_com_auth_login_query, 
        $rex_com_auth_login_request
      )
    )
{

	// echo "login";

	$REX['COM_USER']->sessionFixation();
	$jump_aid = $REX['ADDON']['community']['plugin_auth']['article_login_ok'];
	$rex_com_auth_info = 3;	// 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in
   	$rex_com_auth_use_jump_url = TRUE;

	$session_key = $REX['COM_USER']->createSessionKey();
	$uu = rex_sql::factory();
	$uu->setQuery('update rex_com_user set session_key="'.$session_key.'" where id='.$REX['COM_USER']->getValue("id"));

	$REX['COM_USER']->setSessionVar('UID',$REX['COM_USER']->getValue('id'));
	$REX['COM_USER']->setSessionVar('SID', $session_key);
	$REX['COM_USER']->setSessionVar('STIME',time());
	if($REX['ADDON']['community']['plugin_auth']['stay_active'] == "1" && rex_request("rex_com_auth_stay_active") == 1)
	{
		$REX['COM_USER']->setCookieVar("session_key", $session_key);
	}else
	{
		$REX['COM_USER']->setCookieVar("session_key", "");
	}


// ---------------------------------------------- COOKIE

}elseif (
			$REX['ADDON']['community']['plugin_auth']['stay_active'] == "1" 
			&& 
			$REX['COM_USER']->getCookieVar('session_key') != '' 
			&& 
			$REX['COM_USER']->checkQuery(
				'select * from rex_com_user where session_key="USER_SESSION_KEY" and status>0',
				array(
					'USER_SESSION_KEY' => $REX['COM_USER']->getCookieVar('session_key')
				)
			) 
		)
{

	// echo "cookie";

	$REX['COM_USER']->sessionFixation();
	// $jump_aid = $REX['ADDON']['community']['plugin_auth']['article_login_ok'];
	// $rex_com_auth_info = 3;	// 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in
   	// $rex_com_auth_use_jump_url = TRUE;

	$session_key = $REX['COM_USER']->createSessionKey();
	$uu = rex_sql::factory();
	$uu->setQuery('update rex_com_user set session_key="'.$session_key.'" where id='.$REX['COM_USER']->getValue("id"));

	$REX['COM_USER']->setSessionVar('UID',$REX['COM_USER']->getValue('id'));
	$REX['COM_USER']->setSessionVar('SID', $session_key);
	$REX['COM_USER']->setSessionVar('STIME',time());
	$REX['COM_USER']->setCookieVar("session_key", $session_key);


// ---------------------------------------------- SESSON TIMEOUT

}elseif (
			$REX['COM_USER']->getSessionVar('STIME') != "" 
			&& 
			$REX['COM_USER']->getSessionVar('STIME') < ( time() - $rex_com_auth_session_duration)
	)
{

	// echo "session timeout";

	$jump_aid = $REX['ADDON']['community']['plugin_auth']['article_login_failed'];
	$rex_com_auth_info = 4;	// 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in / 4 - session timeout
	$REX['COM_USER']->deleteCookieVars();
	$REX['COM_USER']->deleteSessionVars();

	

// ---------------------------------------------- LOGIN FAILED

}elseif (
			(rex_request("rex_com_auth_name","string") != "" &&  isset($_REQUEST['rex_com_auth_password']) )
			|| 
			$REX['COM_USER']->getCookieVar('session_key') != '' 
			|| 
			$REX['COM_USER']->getSessionVar('UID') != "" 
	)
{

	// echo "login failed";

	$jump_aid = $REX['ADDON']['community']['plugin_auth']['article_login_failed'];
	$rex_com_auth_info = 2;	// 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in
	$REX['COM_USER']->deleteCookieVars();
	$REX['COM_USER']->deleteSessionVars();
	unset($REX['COM_USER']);

	$url_params['rex_com_auth_name'] = rex_request("rex_com_auth_name","string");
	if($REX['ADDON']['community']['plugin_auth']['stay_active'] == "1")
	{
		$url_params['rex_com_auth_stay_active'] = rex_request("rex_com_auth_stay_active","int");;
	}	


// ---------------------------------------------- NOTHING

}else
{
	
	// echo "nichts";

	$rex_com_auth_info = 0;	// 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in
	unset($REX['COM_USER']);

}





$rex_com_auth_jump = rex_request('rex_com_auth_jump','string');

if (
      (isset($jump_aid) && $article = OOArticle::getArticleById($jump_aid))
      ||
      ($rex_com_auth_use_jump_url && $rex_com_auth_jump != "")
   )
{
	ob_end_clean();
	
	$url_params['rex_com_auth_info'] = $rex_com_auth_info;
	
	if($rex_com_auth_use_jump_url && $rex_com_auth_jump != "")
	{
		header('Location: http://'.$REX["SERVER"].'/'.rex_com_auth_urldecode($rex_com_auth_jump));
	}else
	{
		if($rex_com_auth_jump != "")
			$url_params[$REX['ADDON']['community']['plugin_auth']['request']['jump']] = $rex_com_auth_jump;
		header('Location:'.rex_getUrl($jump_aid,'',$url_params,'&'));
	}
	exit;
}



// ---------- page_permissions
if($article = OOArticle::getArticleById($REX["ARTICLE_ID"]))
{
	if(!rex_com_checkperm($article))
	{
		ob_end_clean();
		header('Location:'.rex_getUrl($REX['ADDON']['community']['plugin_auth']['article_withoutperm'],'',$url_params,'&'));
		exit;
	}
}else
{
	// Wenn Article nicht vorhanden - nichts machen -> wird dann von der index.php geregelt sodass eine fehlerseite auftaucht
	// $jump_aid = $REX['ADDON']['community']['plugin_auth']['article_withoutperm'];
}

?>