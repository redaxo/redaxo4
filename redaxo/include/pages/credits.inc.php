<?php

/**
 * Creditsseite. Auflistung der Credits an die Entwickler von REDAXO und den AddOns.
 * @package redaxo4
 * @version $Id$
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
		Yakamara Media GmbH &amp; Co KG, <a href="http://www.yakamara.de">www.yakamara.de</a>
	</p>

	<p>
		<b>Markus Staab</b>, markus.staab@redaxo.de<br />
		Kernentwickler
	</p>
	
	<p>
		<b>Wolfgang Huttegger</b>, wolfgang.huttegger@redaxo.de<br />
		Kernentwickler<br />
		vscope new media, <a href="http://www.vscope.at">www.vscope.at</a>
	</p>
	
	<p>
		<b>Thomas Blum</b>, thomas.blum@redaxo.de<br />
		Layout/Design Entwickler<br />
		blumbeet - web.studio, <a href="http://www.blumbeet.com">www.blumbeet.com</a>
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
    	if (isset($REX['ADDON']['page'][$cur])) $cl = 'rex-clr-grn';
    	else $cl = 'rex-clr-red';
    	echo '<tr><td class="'.$cl.'">'.$cur.'</td><td class="'.$cl.'">';

    	if (isset($REX['ADDON']['version'][$cur])) echo '['.$REX['ADDON']['version'][$cur].']';
    	echo '</td><td class="'.$cl.'">';

    	if (isset($REX['ADDON']['author'][$cur])) echo $REX['ADDON']['author'][$cur];
    	if (!isset($REX['ADDON']['page'][$cur])) echo 'AddOn inaktiv';
    	echo '</td><td class="'.$cl.'">';
    	if (isset($REX['ADDON']['supportpage'][$cur])) echo '<a href="'.$REX['ADDON']['supportpage'][$cur].'">'.$REX['ADDON']['supportpage'][$cur].'</a>';
    	echo '</td></tr>';
    }
    	?>
    		</tbody>
    	</table>
</div>