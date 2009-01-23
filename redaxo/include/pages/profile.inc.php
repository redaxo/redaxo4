<?php
/**
 *
 * @package redaxo4
 * @version $Id: profile.inc.php,v 1.2 2008/04/15 15:50:28 kills Exp $
 */

$info = '';
$warning = '';
$user_id = $REX_USER->getValue('user_id');
// Allgemeine Infos
$userpsw = rex_request('userpsw', 'string');
$username = rex_request('username', 'string');
$userdesc = rex_request('userdesc', 'string');

// --------------------------------- Title

rex_title($I18N->msg('profile_title'),'');

// --------------------------------- BE LANG

// backend sprache
$sel_be_sprache = new rex_select;
$sel_be_sprache->setStyle('class="rex-form-select"');
$sel_be_sprache->setSize(1);
$sel_be_sprache->setName("userperm_be_sprache");
$sel_be_sprache->setId("userperm-mylang");
$sel_be_sprache->addOption("default","");
$cur_htmlcharset = $I18N->msg('htmlcharset');
$langpath = $REX['INCLUDE_PATH'].'/lang';
$langs = array();
if ($handle = opendir($langpath))
{
	while (false !== ($file = readdir($handle)))
  {
		if (substr($file,-5) == '.lang')
    {
			$locale = substr($file,0,strlen($file)-strlen(substr($file,-5)));
			$I18N_T = rex_create_lang($locale,$langpath,FALSE); // Locale nicht neu setzen
      $i_htmlcharset = $I18N_T->msg('htmlcharset');
      if ($cur_htmlcharset == $i_htmlcharset)
      {
      	$sel_be_sprache->addOption($I18N_T->msg('lang'),$locale);
      	$langs[$locale] = $I18N_T->msg('lang');
      }
		}
	}
	closedir($handle);
	unset($I18N_T);
}
$userperm_be_sprache = rex_request('userperm_be_sprache', 'string');
$userperm_be_sprache_selected = '';
foreach($langs as $k => $v)
{
	if ($REX_LOGIN->USER->hasPerm('be_lang['.$k.']'))
	{
	  $userperm_be_sprache_selected = $k;
	}
}


// --------------------------------- FUNCTIONS

if (rex_post('upd_profile_button', 'string'))
{
  $updateuser = new rex_sql;
  $updateuser->setTable($REX['TABLE_PREFIX'].'user');
  $updateuser->setWhere('user_id='. $user_id);
  $updateuser->setValue('name',$username);
  if ($REX['PSWFUNC']!='' && $userpsw != $sql->getValue($REX['TABLE_PREFIX'].'user.psw')) $userpsw = call_user_func($REX['PSWFUNC'],$userpsw);
  $updateuser->setValue('psw',$userpsw);
  $updateuser->setValue('description',$userdesc);

	// set be langauage
	$userperm_be_sprache = rex_request("userperm_be_sprache","string");
	if(!isset($langs[$userperm_be_sprache])) $userperm_be_sprache = "default";
	$userperm_be_sprache_selected = $userperm_be_sprache;
	
	$rights = preg_replace('@#be_lang\[([^\]]*)\]@' , '', $REX_LOGIN->getValue("rights"));
	$rights .= 'be_lang['.$userperm_be_sprache.']#';
  $updateuser->setValue('rights',$rights);
	
  $updateuser->addGlobalUpdateFields();

  if($updateuser->update())
    $info = $I18N->msg('user_data_updated');
  else
    $warning = $updateuser->getError();
}

$sel_be_sprache->setSelected($userperm_be_sprache_selected);



// ---------------------------------- ERR MSG

if ($info != '')
  echo rex_info($info);

if ($warning != '')
  echo rex_warning($warning);

// --------------------------------- FORMS

$sql = new rex_login_sql;
$sql->setQuery('select * from '. $REX['TABLE_PREFIX'] .'user where user_id='. $user_id);
if ($sql->getRows()!=1)
{
  echo rex_warning('You have no permission to this area!');
}
else
{
  $userpsw = $sql->getValue($REX['TABLE_PREFIX'].'user.psw');
  $username = $sql->getValue($REX['TABLE_PREFIX'].'user.name');
  $userdesc = $sql->getValue($REX['TABLE_PREFIX'].'user.description');

  echo '
    <div class="rex-form rex-form-profile">
    <form action="index.php" method="post">
      <fieldset class="rex-form-col-2">
        <legend>'.$I18N->msg('profile_myprofile').'</legend>

        <div class="rex-form-wrapper">
          <input type="hidden" name="page" value="profile" />

					<div class="rex-form-row">
						<p class="rex-form-col-a rex-form-read">
              <label for="userlogin">'. htmlspecialchars($I18N->msg('login_name')).'</label>
              <span class="rex-form-read" id="userlogin">'. htmlspecialchars($sql->getValue($REX['TABLE_PREFIX'].'user.login')) .'</span>
						</p>
						<p class="rex-form-col-b rex-form-text">
              <label for="userpsw">'.$I18N->msg('password').'</label>
							<input class="rex-form-text" type="text" id="userpsw" name="userpsw" value="'.htmlspecialchars($userpsw).'" />
							'. ($REX['PSWFUNC']!='' ? '<span class="rex-form-notice">'. $I18N->msg('psw_encrypted') .'</span>' : '') .'
						</p>
					</div>

					<div class="rex-form-row">
						<p class="rex-form-col-a rex-form-text">
              <label for="username">'.$I18N->msg('name').'</label>
              <input class="rex-form-text" type="text" id="username" name="username" value="'.htmlspecialchars($username).'" />
            </p>
						<p class="rex-form-col-b rex-form-text">
              <label for="userdesc">'.$I18N->msg('description').'</label>
              <input class="rex-form-text" type="text" id="userdesc" name="userdesc" value="'.htmlspecialchars($userdesc).'" />
            </p>
      		</div>

					<div class="rex-form-row">
          <p class="rex-form-col-a rex-form-select">
            <label for="userperm-mylang">'.$I18N->msg('backend_language').'</label>
            '.$sel_be_sprache->get().'
          </p>
			    </div>

      	</div>
      </fieldset>

      <fieldset class="rex-form-col-1">
        <div class="rex-form-wrapper">
          <div class="rex-form-row">
						<p class="rex-form-col-a rex-form-submit">
            	<input class="rex-form-submit" type="submit" name="upd_profile_button" value="'.$I18N->msg('profile_save').'" '. rex_accesskey($I18N->msg('user_save'), $REX['ACKEY']['SAVE']) .' />
            </p>
          </div>
        </div>
      </fieldset>
    </form>
    </div>
  ';
}