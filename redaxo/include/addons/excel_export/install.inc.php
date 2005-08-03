<?php

/*
    excel_export Addon by <a href="mailto:staab@public-4u.de">Markus Staab</a>
    <a href="http://www.public-4u.de">www.public-4u.de</a>
    20.06.2005
    Version RC1
*/
 
$Basedir = dirname( __FILE__);
require_once $Basedir .'/include/functions/compat.inc.php';
 
// CREATE/UPDATE DATABASE AND CREATE/UPDATE MODULES
$sql = new CompatSql();
$error = '';
foreach ( readSqlDump( dirname( __FILE__). '/install.sql') as $query) {
    $sql->query( $query);
    $error .= $sql->getError();
}
unset( $sql);

// CREATE/UPDATE PAGES


// CREATE/UPDATE FILES


// REGENERATE SITE

if ( $error != ''){
    $REX['ADDON']['installmsg']['excel_export'] = $error;
} else {
    $REX['ADDON']['install']['excel_export'] = 1;
}

// ERRMSG IN CASE: $REX[ADDON][installmsg]["guestbook"] = "Leider konnte nichts installiert werden da.";
?>