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
<div class="rex-page-output rex-mrgn">
	<h2>REDAXO</h2>

	<div class="rex-page-content">

	<p>
		<b>Jan Kristinus</b>, jan.kristinus@redaxo.de<br />
		Erfinder und Kernentwickler<br />
		Yakamara Media GmbH &amp; Co KG, <a href="http://www.yakamara.de" onclick="window.open(this.href); return false;">www.yakamara.de</a>
	</p>

	<p>
		<b>Markus Staab</b>, markus.staab@redaxo.de<br />
		Kernentwickler
	</p>

	<p>
		<b>Wolfgang Huttegger</b>, wolfgang.huttegger@redaxo.de<br />
		Kernentwickler<br />
		vscope new media, <a href="http://www.vscope.at" onclick="window.open(this.href); return false;">www.vscope.at</a>
	</p>

	<p>
		<b>Thomas Blum</b>, thomas.blum@redaxo.de<br />
		Layout/Design Entwickler<br />
		blumbeet - web.studio, <a href="http://www.blumbeet.com" onclick="window.open(this.href); return false;">www.blumbeet.com</a>
	</p>
	</div>
</div>


<div class="rex-page-output">

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
    	echo '<tr><td><span class="'.$cl.'">'.$cur.'</span> [<a href="index.php?page=addon&amp;subpage=help&amp;addonname='.$cur.'">?</a>]</td><td class="'.$cl.'">';


    	if ($version) echo '['.$version.']';
    	echo '</td><td class="'.$cl.'">';

    	if ($author) echo $author;
    	if (!$isActive) echo $I18N->msg('credits_addon_inactive');
    	echo '</td><td class="'.$cl.'">';
    	if ($supportPage) echo '<a href="http://'.$supportPage.'" onclick="window.open(this.href); return false;">'. $supportPage .'</a>';
    	echo '</td></tr>';
    }
    	?>
    		</tbody>
    	</table>
</div>