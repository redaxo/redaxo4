<?php

/**
 * Textile Addon
 *  
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
require $REX['INCLUDE_PATH'].'/layout/top.php';

rex_title('Textile');

$mdl_ex =<<<EOD
if (REX_IS_VALUE[1]) 
{
  \$textile =<<<EOD
  REX_HTML_VALUE[1]
  EOD;
  echo rex_a79_textile(\$textile);
}
EOD;

$mdl_help = '<?php rex_a79_help_overview(); ?>';

?>

<p>
Einfach mit folgendem Code in die Ausgabe eines beliebigen Moduls einbinden
</p>

<h2>Beispiel für REX_VALUE[1]</h2>
<code><?php echo nl2br(htmlspecialchars($mdl_ex)) ?></code>

<p>
Um eine Tabelle mit einer Anleitung und Hinweisen in ein Modul einzubinden, 
einfach folgenden Funktionsaufruf einfügen:
</p>

<code><?php echo htmlspecialchars( $mdl_help) ?></code>
<?php

require $REX['INCLUDE_PATH'].'/layout/bottom.php';
?>