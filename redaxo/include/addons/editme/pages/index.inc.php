<?php

/**
 * Version editme
 *
 * @author jan.kristinus@redaxo.de Jan Kristinus
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

require $REX['INCLUDE_PATH'].'/layout/top.php';

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');

rex_title($I18N->msg("editme"));

?>
<style>
body#rex-page-editme div.rex-form, 
body#rex-page-editme table.rex-table {
	border: 0;
	-moz-outline-radius-bottomleft: 4px;
	-moz-outline-radius-bottomright: 4px;
	-moz-outline-radius-topleft: 4px;
	-moz-outline-radius-topright: 4px;
	outline: 1px solid #CBCBCB;
}

#rex-page-editme input.submit {
    background-image: url(../be_style/plugins/agk_skin/button.gif);
    background-repeat: repeat-x;
    padding: 2px 6px;
    border: 0;
    -moz-outline-radius-bottomleft: 4px;
    -moz-outline-radius-bottomright: 4px;
    -moz-outline-radius-topleft: 4px;
    -moz-outline-radius-topright: 4px;
    outline: 1px solid #CBCBCB;
}
</style>
<?php


switch($subpage)
{
  case 'field':
    break;

  default:
  {
	  $subpage = 'tables';
  }
}

require $REX['INCLUDE_PATH'] . '/addons/'.$page.'/pages/'.$subpage.'.inc.php';


?>
<!--

-->
<?php
require $REX['INCLUDE_PATH'].'/layout/bottom.php';