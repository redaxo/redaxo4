<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
rex_register_extension('A1_BEFORE_DB_IMPORT', 'rex_a62_metainfo_cleanup');

/**
 * Erweitert das Meta-Formular um die neuen Meta-Felder	
 */
function rex_a62_metainfo_cleanup($params)
{
	global $REX;
	
	// Alle Metafelder löschen, nicht das nach einem Import in der Parameter Tabelle
	// noch Datensätze zu Feldern stehen, welche nicht als Spalten in der rex_article angelegt wurden!
	$sql = new rex_sql();
	$sql->setQuery('DELETE FROM '. $REX['TABLE_PREFIX'] .'62_params');
}

?>