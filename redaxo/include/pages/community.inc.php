<?
// changed 02.04.04 Carsten Eckelman <careck@circle42.com>
//   * i18n

// $subnavi = "&nbsp;&nbsp;&nbsp;<a href=index.php?page=community&subpage=users>Userverwaltung</a> | <a href=index.php?page=community&subpage=comments>Artikelkommentarverwaltung</a>";



if ($subpage == "newsletter")
{
	// ----------------- ARTIKELKOMMENTARE

	include $REX[INCLUDE_PATH]."/pages/community/newsletter.inc.php";
	
}elseif ($subpage == "comment")
{
	// ----------------- ARTIKELKOMMENTARE

	include $REX[INCLUDE_PATH]."/pages/community/article_comment.inc.php";
	
}elseif ($subpage == "user")
{
	// ----------------- USERVERWALTUNG

	include $REX[INCLUDE_PATH]."/pages/community/user.inc.php";
	
}elseif ($subpage == "board")
{
	// ----------------- BOARDVERWALTUNG

	include $REX[INCLUDE_PATH]."/pages/community/board.inc.php";
	
}else
{
	// ----------------- ÜBERSICHT

	title("Community",$subnavi,"blue");

	echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>";
	echo "	<tr>
			<th align=left colspan=2 class=dblue>".$I18N->msg('overview')."</th>
		</tr>
		<tr>
			<td class=blue width=50% valign=top>
			
			<br><a href=index.php?page=community&subpage=user><b>".$I18N->msg('user_management')."</b></a>
			<br>".$I18N->msg('user_management_info')."
			
			<br><br><a href=index.php?page=community&subpage=board><b>".$I18N->msg('board_management')."</b></a>
			<br>".$I18N->msg('board_management_info')."
			
			<br><br><a href=index.php?page=community&subpage=comment><b>".$I18N->msg('article_management')."</b></a>
			<br>".$I18N->msg('article_management_info')."
			
			<br><br><a href=index.php?page=community&subpage=newsletter><b>".$I18N->msg('newsletter')."</b></a>
			<br>".$I18N->msg('newsletter_info')."
			
			<!-- 
			<br><br><a href=index.php?page=community&subpage=count><b>".$I18N->msg('evaluation')."</b></a>
			<br>".$I18N->msg('evaluation_info')."
			//-->
						
			<br><br><br><br></td>
			<td class=blue valign=top></td>
		</tr>";	
	echo "</table>";
}


?>