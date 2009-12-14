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

rex_title($I18N->msg("editme"),$REX['ADDON'][$page]['SUBPAGES']);

$tables = rex_em_getTables();

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
  	require $REX['INCLUDE_PATH'] . '/addons/'.$page.'/pages/'.$subpage.'.inc.php';
    break;
	case '':
	  $subpage = 'tables';
	  require $REX['INCLUDE_PATH'] . '/addons/'.$page.'/pages/'.$subpage.'.inc.php';
		break;
  default:
  {
  	$table = "";
  	foreach($tables as $t)
  		if($t["label"] == $subpage)
  		  $table = $subpage;
  	
		if($table == "")
		{
			$subpage = "tables";
			require $REX['INCLUDE_PATH'] . '/addons/'.$page.'/pages/'.$subpage.'.inc.php';
	  }
		else
			require $REX['INCLUDE_PATH'] . '/addons/'.$page.'/pages/edit.inc.php'; 			

			// $tables = rex_em_getTables();
  		// echo '<pre>';var_dump($tables);echo '</pre>';
  	

  }
}




?>
<!--

-->
<?php
require $REX['INCLUDE_PATH'].'/layout/bottom.php';