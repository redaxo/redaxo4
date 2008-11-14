<?php

/**
 * Textile Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 * @package redaxo4
 * @version $Id: index.inc.php,v 1.6 2008/03/11 16:04:53 kills Exp $
 */

require $REX['INCLUDE_PATH'].'/layout/top.php';

rex_title('Textile');


$mdl_help = '<?php rex_a79_help_overview(); ?>';


$mdl_ex ='<?php
if(OOAddon::isAvailable("textile"))
{
  if(REX_IS_VALUE[1])
  {
    $textile = htmlspecialchars_decode("REX_VALUE[1]");
    $textile = str_replace("<br />","",$textile);
    echo rex_a79_textile($textile);
  }
}
else
{
  echo rex_warning(\'Dieses Modul ben&ouml;tigt das "textile" Addon!\');
}
?>';

?>

<div class="rex-addon-output">
	<h2 class="rex-hl2"><?php echo $I18N_A79->msg('code_for_module_input'); ?></h2>

	<div class="rex-addon-content">
		<p class="rex-tx1"><?php echo $I18N_A79->msg('module_intro_help'); ?></p>

		<p class="rex-code"><code><?php echo htmlspecialchars($mdl_help); ?></code></p>

		<p class="rex-tx1"><?php echo $I18N_A79->msg('module_rights'); ?></p>
	</div>

	<h2 class="rex-hl2"><?php echo $I18N_A79->msg('code_for_module_output'); ?></h2>

	<div class="rex-addon-content">
		<p class="rex-tx1"><?php echo $I18N_A79->msg('module_intro_moduleoutput'); ?></p>

		<h3><?php echo $I18N_A79->msg('example_for'); ?> REX_VALUE[1]</h3>
		<p class="rex-code"><?php highlight_string($mdl_ex); ?></p>
	</div>
</div>

<?php
require $REX['INCLUDE_PATH'].'/layout/bottom.php';
?>