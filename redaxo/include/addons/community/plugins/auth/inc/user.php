<?php

$login = "";

if (isset($REX['COM_USER']) && is_object($REX['COM_USER']))
{
	$login .= '<div id="com-user-box" class="com-user-loggedin">';
	$login .= '<h4>Benutzer</h4>';
	$login .= '<p><strong><a href="'.rex_getUrl(1).'">'.$REX["COM_USER"]->getValue('login').'</a></strong></p>';
	$login .= '<ul>
					<li><a class="icon icon-myprfl" href="'.rex_getUrl(27).'"><span>Mein Profil</span></a></li>
					<li><a class="icon icon-lgt" href="'.rex_getUrl('','',array('rex_com_logout'=>1)).'"><span>Abmelden</span></a></li>
				</ul>
			';
	$login .= '</div>';

}else
{
	$login .= '<div id="com-user-box">';
	$login .= '<form action="'.rex_getUrl('').'" method="post">
					<fieldset>
					<p class="formtext">
						<label for="name" class="hidden">Benutzername:</label>
						<input type="text" id="name" name="rex_com_lname" value="Benutzername..." onblur="if(this.value == \'\') this.value=\'Benutzername...\'" onfocus="if(this.value == \'Benutzername...\') this.value=\'\'" />
					</p>
					<p class="formtext">
						<label for="password" class="hidden">Passwort:</label>
						<input type="password" id="password" name="rex_com_lpsw" value="Passwort..." onblur="if(this.value == \'\') this.value=\'Passwort...\'" onfocus="if(this.value == \'Passwort...\') this.value=\'\'" /><input class="f-submit-img" type="image" value="Login" title="Anmeldung durchführen" src="/layout/css/icon_submit_ok.gif" name="search"/>
					</p>
					</fieldset>
				</form>
				<ul>
					<li><a class="icon-arrow" href="'.rex_getUrl(33).'"><span>Registrieren ?</span></a></li>
					<li><a class="icon-arrow" href="'.rex_getUrl(34).'"><span>Passwort ?</span></a></li>
				</ul>
			';
	$login .= '</div>';

}

echo '<div class="bx-v1"><div class="bx-v1-2"><div class="bx-v1-cntnt"><h3>myRedaxo</h3>'.$login.'</div></div></div>';

?>