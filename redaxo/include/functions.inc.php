<?php

include_once $REX[INCLUDE_PATH]."/functions/function_showmicrotime.inc.php";

// ------------------------------------------------------------ wenn magic quotes off

if (!get_magic_quotes_gpc()) include $REX[INCLUDE_PATH]."/functions/function_rex_mquotes.inc.php";

// ------------------------------------------------------------ includes

// hier wird die i18n sprachklasse erzeugt
include_once $REX[INCLUDE_PATH]."/classes/class.i18n.inc.php";
$I18N = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/lang/");
$REX[LOCALES] = i18n::getLocales($REX[INCLUDE_PATH]."/lang/");

// klassen
// include $REX[INCLUDE_PATH]."/classes/class.ftp.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.sql.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.select.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.article.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.login.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.form.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.list.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.board.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.mime_mail.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.mail_decode.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.tar.inc.php";
include $REX[INCLUDE_PATH]."/classes/class.stat.inc.php";

// OO Classes
include_once $REX[INCLUDE_PATH]."/classes/class.oocategory.inc.php";

// Textile class
include_once $REX[INCLUDE_PATH]."/classes/class.textile.inc.php";

// Advanced Caching class
include_once $REX[INCLUDE_PATH]."/classes/class.cache.inc.php";

// functions
include $REX[INCLUDE_PATH]."/functions/function_datefrommydate.inc.php";
include $REX[INCLUDE_PATH]."/functions/function_selectdate.inc.php";
include $REX[INCLUDE_PATH]."/functions/function_mail.inc.php";
include $REX[INCLUDE_PATH]."/functions/function_createimage.inc.php";
include $REX[INCLUDE_PATH]."/functions/function_rex_mediapool.inc.php";
include $REX[INCLUDE_PATH]."/functions/function_rex_modrewrite.inc.php";

// ------------------------------------------------------------ redaxo includes

include $REX[INCLUDE_PATH]."/functions/function_rex_title.inc.php";
include $REX[INCLUDE_PATH]."/functions/function_rex_generate.inc.php";

?>