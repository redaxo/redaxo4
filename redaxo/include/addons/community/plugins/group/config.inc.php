<?php

/**
 * Plugin Group
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

// echo "Dummy Config ist geladen";



// xform addfield "group" einbauen


if ($REX["REDAXO"] && $REX['USER'])
{
	if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[group]"))
		$REX['ADDON']['community']['SUBPAGES'][] = array('plugin.group','Gruppen');
}


?>