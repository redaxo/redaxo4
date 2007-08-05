<?php

/**
 * Layout Fuß des Backends
 * @package redaxo3
 * @version $Id$
 */

?>

	</div>
<!-- *** OUTPUT OF CONTENT - END *** -->

</div><!-- END #rex-wrapper -->

	<div id="rex-ftr">
		<ul>
			<li><a href="http://www.pergopa.de" target="_blank" class="black"<?php echo rex_tabindex() ?>>pergopa kristinus gbr</a> | </li>
      <li><a href="http://www.redaxo.de" target="_blank" class="black"<?php echo rex_tabindex() ?>>redaxo.de</a> | </li>
      <li><a href="http://forum.redaxo.de"<?php echo rex_tabindex() ?>>?</a></li>
		</ul>
		<p><?php echo showScripttime() ?> sec | <?php echo rex_formatter :: format(time(), 'strftime', 'date'); ?></p>
	</div>
   </body>
</html>