<?php

/**
 * Community Install 
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

function rex_com_setup_is_writable($items)
{
  global $REX;
  $res = array();

  foreach($items as $item)
  {
    $is_writable = _rex_is_writable($item);

    // 0 => kein Fehler
    if($is_writable != 0)
    {
      $res[$is_writable][] = $item;
    }
  }

  return $res;
}

$WRITEABLES = array (
    $REX['INCLUDE_PATH'].'/addons/community/plugins.inc.php',
    $REX['INCLUDE_PATH'].'/addons/community/plugins/',
  );

$res = rex_com_setup_is_writable($WRITEABLES);
$writable_msg = "";
if(count($res) > 0)
{
  foreach($res as $type => $messages)
  {
    if(count($messages) > 0)
    {
      $writable_msg .= '<ul>';
      foreach($messages as $message)
      {
        $writable_msg .= '<li>'. $message .'</li>';
      }
      $writable_msg .= '</ul>';
    }
  }
}




if (OOAddon::isAvailable('xform') != 1 || OOAddon::isAvailable('phpmailer') != 1)
{

	// Installation erfolgreich
	$REX['ADDON']['install']['community'] = 0;
	$REX['ADDON']['installmsg']['community'] = 'AddOn "XForm" und/oder "PHPMailer" ist nicht installiert und aktiviert.';

}elseif($writable_msg != ""){

	// Schreibrechte prŸfen
	$REX['ADDON']['install']['community'] = 0;
	$REX['ADDON']['installmsg']['community'] = 'Folgende Ordner brauchen noch Schreibrechte:'.$writable_msg;

}else
{

	// Metainfo erweitern
	$a = new rex_sql;
	$a->setTable("rex_62_params");
	$a->setValue("title","Zugriffsrechte");
	$a->setValue("prior","1");
	$a->setValue("type","3");
	$a->setValue("params","0:Alle|-1:Nur nicht Eingeloggte|1:Nur Eingeloggte|2:Nur Moderatoren und Admins|3:Nur Admins");
	$a->setValue("validate",NULL);
	$a->addGlobalCreateFields();

	$g = new rex_sql;
	$g->setQuery('select * from rex_62_params where name="art_com_perm"');
	
	if ($g->getRows()==1)
	{
		$a->setWhere('name="art_com_perm"');
		$a->update();
	}else
	{
		$a->insert();
	}
	
	$g = new rex_sql;
	$g->setQuery('show columns from rex_article Like "art_com_perm"');

	if ($g->getRows()==0) $a->setQuery("ALTER TABLE `rex_article` ADD `art_com_perm` VARCHAR( 255 ) NOT NULL"); 
	
	$REX['ADDON']['install']['community'] = 1;

	// XForm vorhanden -> install.sql wird automatisch ausgeführt.

}

?>