<?php

/**
 * Creditsseite. Auflistung der Credits an die Entwickler von REDAXO und den AddOns.
 * @package redaxo4
 * @version $Id: credits.inc.php,v 1.6 2008/03/25 10:13:16 kills Exp $
 */

rex_title($I18N->msg("credits"), "");

include_once $REX['INCLUDE_PATH']."/functions/function_rex_other.inc.php";
include_once $REX['INCLUDE_PATH']."/functions/function_rex_addons.inc.php";

?>
<div class="rex-area rex-mab-10">
	<h3 class="rex-hl2">REDAXO <?php echo $REX['VERSION'].'.'.$REX['SUBVERSION'].'.'.$REX['MINORVERSION'] ?></h3>

	<div class="rex-area-content">

	<p class="rex-tx1">
		<b>Jan Kristinus</b>, jan.kristinus@redaxo.de<br />
		Erfinder und Kernentwickler<br />
		Yakamara Media GmbH &amp; Co KG, <a href="http://www.yakamara.de" onclick="window.open(this.href); return false;">www.yakamara.de</a>
	</p>

	<p class="rex-tx1">
		<b>Markus Staab</b>, markus.staab@redaxo.de<br />
		Kernentwickler<br />
    REDAXO, <a href="http://www.redaxo.de" onclick="window.open(this.href); return false;">www.redaxo.de</a>
	</p>

	<p class="rex-tx1">
		<b>Thomas Blum</b>, thomas.blum@redaxo.de<br />
		Layout/Design Entwickler<br />
		blumbeet - web.studio, <a href="http://www.blumbeet.com" onclick="window.open(this.href); return false;">www.blumbeet.com</a>
	</p>
	</div>
</div>


<div class="rex-area">

		<table class="rex-table">
			<thead>
			<tr>
				<th><?php echo $I18N->msg("credits_name"); ?></th>
				<th><?php echo $I18N->msg("credits_version"); ?></th>
				<th><?php echo $I18N->msg("credits_author"); ?></th>
				<th><?php echo $I18N->msg("credits_supportpage"); ?></th>
			</tr>
			</thead>

			<tbody>

		<?php

		$ADDONS = rex_read_addons_folder();

    foreach ($ADDONS as $cur)
    {
      $isActive = OOAddon::isActivated($cur);
      $version  = OOAddon::getVersion($cur);
      $author   =  OOAddon::getAuthor($cur);
      $supportPage = OOAddon::getSupportPage($cur);

    	if ($isActive) $cl = 'rex-clr-grn';
    	else $cl = 'rex-clr-red';
    	echo '<tr><td><span class="'.$cl.'">'.htmlspecialchars($cur).'</span> [<a href="index.php?page=addon&amp;subpage=help&amp;addonname='.$cur.'">?</a>]</td><td class="'.$cl.'">';


    	if ($version) echo '['.$version.']';
    	echo '</td><td class="'.$cl.'">';

    	if ($author) echo htmlspecialchars($author);
    	if (!$isActive) echo $I18N->msg('credits_addon_inactive');
    	echo '</td><td class="'.$cl.'">';
    	if ($supportPage) echo '<a href="http://'.$supportPage.'" onclick="window.open(this.href); return false;">'. $supportPage .'</a>';
    	echo '</td></tr>';
    }
    	?>
    		</tbody>
    	</table>
</div>