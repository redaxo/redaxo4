<?php

// <div>


/**
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$info = '';
$warning = '';

if(rex_request("func","string")=="update")
{

	$REX['ADDON']['editme']['plugin_auth']['auth_active'] = rex_request("auth_active","int");
	$REX['ADDON']['editme']['plugin_auth']['stay_active'] = rex_request("stay_active","int");
	$REX['ADDON']['editme']['plugin_auth']['article_login_ok'] = rex_request("article_login_ok","int");;
	$REX['ADDON']['editme']['plugin_auth']['article_login_failed'] = rex_request("article_login_failed","int");;
	$REX['ADDON']['editme']['plugin_auth']['article_logout'] = rex_request("article_logout","int");;
	$REX['ADDON']['editme']['plugin_auth']['article_withoutperm'] = rex_request("article_withoutperm","int");;

	$config_file = $REX['INCLUDE_PATH'].'/addons/community/plugins/auth/config.inc.php';

	$content = '
$REX[\'ADDON\'][\'editme\'][\'plugin_auth\'][\'auth_active\'] = "'.$REX['ADDON']['editme']['plugin_auth']['auth_active'].'";
$REX[\'ADDON\'][\'editme\'][\'plugin_auth\'][\'stay_active\'] = "'.$REX['ADDON']['editme']['plugin_auth']['stay_active'].'";
$REX[\'ADDON\'][\'editme\'][\'plugin_auth\'][\'article_login_ok\'] = '.$REX['ADDON']['editme']['plugin_auth']['article_login_ok'].';
$REX[\'ADDON\'][\'editme\'][\'plugin_auth\'][\'article_login_failed\'] = '.$REX['ADDON']['editme']['plugin_auth']['article_login_failed'].';
$REX[\'ADDON\'][\'editme\'][\'plugin_auth\'][\'article_logout\'] = '.$REX['ADDON']['editme']['plugin_auth']['article_logout'].';
$REX[\'ADDON\'][\'editme\'][\'plugin_auth\'][\'article_withoutperm\'] = '.$REX['ADDON']['editme']['plugin_auth']['article_withoutperm'].';
';

	if(rex_replace_dynamic_contents($config_file, $content) !== false)
		echo rex_info('Daten wurden aktualisiert');
	else
		echo rex_warning('Fehler beim Schreiben der Configdatei '.$config_file);

	if(!is_writable($config_file))
	  echo rex_warning($I18N->msg('imanager_config_not_writable', $config_file));

}

echo '
	<div class="rex-form" id="rex-form-system-setup">
  	<form action="index.php" method="post">
    	<input type="hidden" name="page" value="community" />
    	<input type="hidden" name="subpage" value="plugin.auth" />
    	<input type="hidden" name="func" value="update" />
		
			<div class="rex-area-col-2">
				<div class="rex-area-col-a">
	
					<h3 class="rex-hl2">Beschreibung</h3>
	
					<div class="rex-area-content">

<p class="rex-tx1">Bei der Installation von "Auth" wurden 3 Felder in der Metainfo hinzugefügt, welche
direkt in den Metadaten eines jeden Artikels vorhanden sind.</p>

<h4 class="rex-hl3">1) MetaInfo: Art der Gruppenrechte<br /> Nur für Gruppen, Für alle etc. [art_com_grouptype]</h4>
<h4 class="rex-hl3">2) MetaInfo: Zugriffsrechte<br />ob man eingeloggt sein muss oder nicht. [art_com_permtype]</h4>
<h4 class="rex-hl3">3) MetaInfo: Gruppen<br />
welche gruppen sind betroffen. [art_com_groups]</h4>

<p class="rex-tx1">Mit diesen 3 Felden kann man nun die Rechte zu Artikeln zuweisen</p>

<p class="rex-tx1">Um sich einzuloggen muss ein Formular erstellt werden, welches diese Feldnamen
hat (<b>rex_com_lname, rex_com_lpsw</b>). Werden diese übergeben (als einfach abgeschickt und an irgendeinen Artikel geschickt) wird automatisch eine 
Authentifizierung durchgeführt und auf die entsprechenden Artikel verwiesen. Zusätzlich muss der User mindestens auf status=1 stehen,
damit ein erfolgreicher Login möglich ist</p>

<p class="rex-tx1">Will man sich nun wieder ausloggen, kann jeder Link verwendet werden, welcher auf einen REDAXO-Artikel
verweist und man den Parameter und Wert <b>?rex_com_logout=1</b> mitgibt.</p>

<p class="rex-tx1">Ist man erfolgreich eingeloggt, hat man über PHP das Object <b>$REX[\'COM_USER\']</b> zur Verfügung. Über 
<b>$REX[\'COM_USER\']->getValue(\'name\'), wie \'user_id\' etc,</b> kann man die Werte eines Users auslesen. Ist man nicht
eingeloggt, dann ist das <b>$REX[\'COM_USER\']</b>-Objekt nicht gesetzt. Formulare um das Profil
zu pflegen, die Registrierung durchzuführen oder ähnliches, wird nicht über das Auth-Plugin geloest. <br /></p>


<p class="rex-tx1">Um die Authentifizierung in der Navigation zu nutzen, sprich, dafür zu Sorgen, dass nur die richtigen Navigationspunkte auftauchen,
am besten die <b>rex_navigation</b>-Funktion von REDAXO verwenden. Wenn man eigene Navigationen gebaut hat, dann kann man das prüfen indem man das entsprechende Artikel-Objekt 
an die Funktion übergibt <b>rex_com_checkperm(&$obj (OOArticle-Objekt))</b></p>


<p class="rex-tx1">Angemeldet bleiben lässt auch aktivieren, wobei hier beim Formular nur die entsprechende Checkbox hinzugefügrt werden muss. Weiterhin ist/muss in der 
Userverwaltung das Feld <b>session_key</b> vorhanden sein, in welchem die entsprechende Session gespeichert wird, welche dann in den lokalen Cookies der Browser
gespeichert werden.</p>

<p class="rex-tx1">Über <b>rex_com_jurl</b> kann man übergeben, wohin man nach einem erfolgreichen Login springen soll.</p>


<!-- <p class="rex-button"><a class="rex-button" href="index.php?page=specials&amp;func=generate"><span><span>Logintemplate einspielen / Todo</span></span></a></p> -->
		
					</div>
				</div>
			
				<div class="rex-area-col-b">
					
					<h3 class="rex-hl2">'.$I18N->msg("specials_settings").'</h3>
					
					<div class="rex-area-content">
					
						<fieldset class="rex-form-col-1">
							<legend>'.$I18N->msg("status").'</legend>
							
							<div class="rex-form-wrapper">
							
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-auth">Authentifizierung aktiviert</label>
										<input class="rex-form-text" type="checkbox" id="rex-form-auth" name="auth_active" value="1" ';
								if($REX['ADDON']['editme']['plugin_auth']['auth_active']=="1") echo 'checked="checked"';
								echo ' />
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-stay">"Angemeldet bleiben" aktiviert</label>
										<input class="rex-form-text" type="checkbox" id="rex-form-stay" name="stay_active" value="1" ';
								if($REX['ADDON']['editme']['plugin_auth']['stay_active']=="1") echo 'checked="checked"';
								echo ' />
									</p>
								</div>

							</div>
						</fieldset>


						<fieldset class="rex-form-col-1">
							<legend>'.$I18N->msg("forwarder").'</legend>
							
							<div class="rex-form-wrapper">

            					
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-widget">
										<label for="rex-form-article_login_ok">Sprung zu diesem Artikel wenn erfolgreich eingeloggt</label>
										'. rex_var_link::_getLinkButton('article_login_ok', 1, stripslashes($REX['ADDON']['editme']['plugin_auth']['article_login_ok'])) .'
									</p>
								</div>
							
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-widget">
										<label for="rex-form-article_login_failed">Sprung zu diesem Artikel wenn nicht erfolgreich eingeloggt</label>
                    					'. rex_var_link::_getLinkButton('article_login_failed', 2, stripslashes($REX['ADDON']['editme']['plugin_auth']['article_login_failed'])) .'
									</p>
								</div>
								
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-widget">
										<label for="rex-form-article_logout">Sprung zu diesem Artikel nach dem Ausloggen</label>
                    					'. rex_var_link::_getLinkButton('article_logout', 3, stripslashes($REX['ADDON']['editme']['plugin_auth']['article_logout'])) .'
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-widget">
										<label for="rex-form-article_logout">Sprung zu diesem Artikel wenn auf gesperrtem Artikel zugriffen wird</label>
                    					'. rex_var_link::_getLinkButton('article_withoutperm', 4, stripslashes($REX['ADDON']['editme']['plugin_auth']['article_withoutperm'])) .'
									</p>
								</div>
							
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-submit">
										<input type="submit" class="rex-form-submit" name="sendit" value="'.$I18N->msg("specials_update").'"'. rex_accesskey($I18N->msg('specials_update'), $REX['ACKEY']['SAVE']) .' />
									</p>
								</div>
								
						</fieldset>
					</div> <!-- Ende rex-area-content //-->
					
				</div> <!-- Ende rex-area-col-b //-->
			</div> <!-- Ende rex-area-col-2 //-->
			
		</form>
	</div>
  ';


