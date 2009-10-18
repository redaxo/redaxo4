<?php

$msg = "Meine Meldung";

if ($msg != '')
	echo rex_warning($msg);

?>


<div class="rex-addon-output">
	<h2 class="rex-hl2"><?php echo $I18N->msg('dummyaddon'); ?></h2>

	<div class="rex-addon-content">
		<p class="rex-tx1">abc</p>
		<p class="rex-tx1">def</p>
	</div>

</div>


<div class="rex-addon-output">
	<div class="rex-area-col-2">
		<div class="rex-area-col-a">

			<h3 class="rex-hl2">Linke Seite</h3>
			
			<div class="rex-area-content">
				<h4 class="rex-hl3">Headline 1</h4>
				<p class="rex-tx1">Text</p>

			</div>
		
		</div>
		<div class="rex-area-col-b">

			<h3 class="rex-hl2">Rechte Seite</h3>
			
			<div class="rex-area-content">
				<h4 class="rex-hl3">Headline 2</h4>
				<p class="rex-tx1">Text</p>

			</div>
		
		</div>
	</div>
</div>

					