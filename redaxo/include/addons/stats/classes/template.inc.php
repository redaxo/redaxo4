
<?php


if ( $show == "day" ) $maincontent = "REX_EVAL_DAY";
if ( $show == "allarticle" ) $maincontent = "REX_EVAL_ALLARTICLE";
if ( $show == "top10article" ) $maincontent = "REX_EVAL_TOP10ARTICLE";
if ( $show == "worst10article" ) $maincontent = "REX_EVAL_WORST10ARTICLE";
if ( $show == "laender" ) $maincontent = "REX_EVAL_LAENDER";
if ( $show == "suchmaschinen" ) $maincontent = "REX_EVAL_SUCHMASCHINEN";
if ( $show == "referer" ) $maincontent = "REX_EVAL_REFERER";
if ( $show == "browser" ) $maincontent = "REX_EVAL_BROWSER";
if ( $show == "operatingsystem" ) $maincontent = "REX_EVAL_OPERATINGSYSTEM";
if ( $show == "keywords" ) $maincontent = "REX_EVAL_SEARCHWORDS";



if ( $show == "month" )
{
	$pfad = "REX_EVAL_LOGPATH";
	
	$maincontent = "<table border=0 cellpadding=5 cellspacing=1 width=100%>
					<tr><th>".$I18N->msg("month")."</th><th>".$I18N_STATS->msg("page_views")."</th><th>".$I18N_STATS->msg("visits")."</th><th>".$I18N_STATS->msg("page_views_per_visit")."</th></tr>";

	if (is_dir($pfad)) 
	{
		if ($dh = opendir($pfad) )
		{
			while (($file = readdir($dh)) !== false)
			{
				if ( substr($file, 7, 4) == "_mon" ) 
					if ( substr($file,0,4) == "REX_EVAL_YEAR" )
						if ( strstr($file,".php") == ".php" ) 
							include($file);
			}  	
		}
		closedir($dh);
	} else 
		echo $I18N_STATS->msg("error_no_dir",$pfad);

	$maincontent .= "</table>";

}


function isactive($what)
{
	global $show;
	
	if ( $show == $what ) return "dgrey";
	else return "grey";
}
echo "
<table border=0 cellpadding=0 cellspacing=0 width=770>
<tr><td colspan=2>
<table border=0 cellpadding=5 cellspacing=0 width=100%>
	<tr><th>".$I18N_STATS->msg("evaluation_for")." REX_EVAL_DATE</th></tr>
</table>
</td></tr>

<tr><td valign=top class=dgrey>
	<table border=0 cellpadding=5 cellspacing=1 width=200 >
		<tr>
			<td class=grey><b>".$I18N_STATS->msg("time")."</b></td>
		</tr>
		
		<tr>
			<td class=".isactive("day")."><a href=index.php?page=stats&sub=stats&show=day&year=$year&month=$month>".$I18N_STATS->msg("days")."</a></td>
		</tr>
		<tr>
			<td class=".isactive("month")."><a href=index.php?page=stats&sub=stats&show=month&year=$year&month=$month>".$I18N_STATS->msg("months")."</a></td>
		</tr>
		<tr>
			<td class=grey><b>".$I18N->msg("article")."</b></td>
		</tr>
		
		<tr>
			<td class=".isactive("allarticle")."><a href=index.php?page=stats&sub=stats&show=allarticle&year=$year&month=$month>".$I18N_STATS->msg("all_articles")."</a></td>
		</tr>
		<tr>
			<td class=".isactive("top10article")."><a href=index.php?page=stats&sub=stats&show=top10article&year=$year&month=$month>".$I18N_STATS->msg("top_ten")."</a></td>
		</tr>
		<tr>
			<td class=".isactive("worst10article")."><a href=index.php?page=stats&sub=stats&show=worst10article&year=$year&month=$month>".$I18N_STATS->msg("worst_ten")."</a></td>
		</tr>
		<tr>
			<td class=grey><b>".$I18N_STATS->msg("visitors")."</b></td>
		</tr>
		<tr>
			<td class=".isactive("laender")."><a href=index.php?page=stats&sub=stats&show=laender&year=$year&month=$month>".$I18N_STATS->msg("countries")."</a></td>
		</tr>
		<tr>
			<td class=".isactive("suchmaschinen")."><a href=index.php?page=stats&sub=stats&show=suchmaschinen&year=$year&month=$month>".$I18N_STATS->msg("search_engines")."</a></td>
		</tr>
		<tr>
			<td class=".isactive("keywords")."><a href=index.php?page=stats&sub=stats&show=keywords&year=$year&month=$month>".$I18N_STATS->msg("search_words")."</a></td>
		</tr>
		<tr>
			<td class=".isactive("referer")."><a href=index.php?page=stats&sub=stats&show=referer&year=$year&month=$month>".$I18N_STATS->msg("referer")."</a></td>
		</tr>
		<tr>
			<td class=grey><b>".$I18N_STATS->msg("browser")."</b></td>
		</tr>
		<tr>
			<td class=".isactive("browser")."><a href=index.php?page=stats&sub=stats&show=browser&year=$year&month=$month>".$I18N_STATS->msg("all_browsers")."</a></td>
		</tr>
		<tr>
			<td class=".isactive("operatingsystem")."><a href=index.php?page=stats&sub=stats&show=operatingsystem&year=$year&month=$month>".$I18N_STATS->msg("operating_systems")."</a></td>
		</tr>
		
	</table>
</td>

<td valign=top width=570 class=dgrey>$maincontent</td>

</tr></table>";

	 
?>
