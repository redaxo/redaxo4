<?php

/**
 * @2005 by vscope new media design
 * @email office@vscope.at
 * @web http://www.vscope.at
 **/

/*
* function checks if online/offline status is correct
*/
function CHECKONOFFSTATUS(){

	$db = new sql;
	$today = date("Ymd");
	$sql = "
	SELECT id,status
	FROM rex_article WHERE
	(online_von <= '$today' AND online_von != '' AND online_bis = '' AND status='0')
	OR
	(online_von <= '$today' AND online_von != '' AND online_bis >= '$today' AND status='0')
	OR
	(online_von = '' AND online_bis >= '$today' AND status='0')
	OR
	(online_von < '$today' AND online_bis < '$today' AND online_von != '' AND online_bis != '' AND status='0')
	OR
	(online_bis < '$today' AND online_bis != '' AND online_von = '' AND status='1')
	OR
	(online_bis < '$today' AND online_bis != '' AND online_von > '$today' AND status='1')
	OR
	(online_bis < '$today' AND online_von < '$today' AND online_von != '' AND online_bis != '' AND status='1')
	OR
	(online_bis > '$today' AND online_von > '$today' AND online_von != '' AND online_bis != '' AND status='1')
	";
	$result = $db->get_array($sql);
	if(is_array($result)){
	    foreach($result as $var){
	        $status = $var[status] == 0 ? 1 : 0;
	        $sql = "UPDATE rex_article SET status = '$status' WHERE id='$var[id]'";
	        $db->setQuery($sql);
	    }
	}

}

?>
