<?php
/**
 *
 * @package redaxo4
 * @version $Id: profile.inc.php,v 1.2 2008/04/15 15:50:28 kills Exp $
 */

$info = '';
$warning = '';
$user_id = $REX_USER->getValue('user_id');

// --------------------------------- Title

rex_title($I18N->msg('profile_title'),'');

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
  $updateuser->addGlobalUpdateFields();

  if($updateuser->update())
    $info = $I18N->msg('user_data_updated');
  else
    $warning = $updateuser->getError();
}


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
    <div class="rex-usr-editmode">
    <form action="index.php" method="post">
      <fieldset>
        <legend class="rex-lgnd">'.$I18N->msg('profile_myprofile').'</legend>

        <div class="rex-fldst-wrppr">
          <input type="hidden" name="page" value="profile" />

          <div>
            <p class="rex-cnt-col2">
              <label for="userlogin">'. htmlspecialchars($I18N->msg('login_name')).'</label>
              <span id="userlogin">'. htmlspecialchars($sql->getValue($REX['TABLE_PREFIX'].'user.login')) .'</span>
            </p>
            <p class="rex-cnt-col2">
              <label for="userpsw">'.$I18N->msg('password').'</label>
              <input type="text" id="userpsw" name="userpsw" value="'.htmlspecialchars($userpsw).'" />
              '. ($REX['PSWFUNC']!='' ? '<span>'. $I18N->msg('psw_encrypted') .'</span>' : '') .'
            </p>
  		    </div>

          <div>
            <p class="rex-cnt-col2">
              <label for="username">'.$I18N->msg('name').'</label>
              <input type="text" id="username" name="username" value="'.htmlspecialchars($username).'" />
            </p>
            <p class="rex-cnt-col2">
              <label for="userdesc">'.$I18N->msg('description').'</label>
              <input type="text" id="userdesc" name="userdesc" value="'.htmlspecialchars($userdesc).'" />
            </p>
      		</div>

          <div>
            <p class="rex-cnt-col2"><input type="submit" class="rex-sbmt" name="upd_profile_button" value="'.$I18N->msg('profile_save').'" '. rex_accesskey($I18N->msg('user_save'), $REX['ACKEY']['SAVE']) .' /></p>
          </div>
        </div>
      </fieldset>
    </form>
    </div>
  ';
}

?>