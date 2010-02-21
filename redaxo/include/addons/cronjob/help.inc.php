<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

?>
<h3>Cronjob Addon</h3>

<p>
Dieses Addon ermoeglicht es, Cronjobs in einem jeweils festgelegten Intervall ausfuehren zu lassen. Es wird zwischen vier Typen unterschieden:
<ul style="margin-left: 1.5em">
  <li>PHP-Code</li>
  <li>PHP-Callback</li>
  <li>URL-Aufruf</li>
  <li>Extension</li>
</ul>
Andere Addons (Plugins) koennen sich als Extension registrieren (Beispiel: Im-/Export-Addon fuer automatische Datenbank-Sicherungen).
</p>