<?php

// Anpassen des Include-Path, da innerhalt der Pear-Klassen mit relativen Pfaden zum Pear-Root gearbeitet wird
ini_set('include_path', ini_get('include_path') .PATH_SEPARATOR. $REX['INCLUDE_PATH'].'/classes/');

include_once $REX['INCLUDE_PATH'].'/classes/MIME/Type.php';
include_once $REX['INCLUDE_PATH'].'/classes/File/Archive.php';

?>