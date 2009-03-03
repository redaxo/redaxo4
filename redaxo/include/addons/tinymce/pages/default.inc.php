<?php
/**
 * TinyMCE Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @author andreas[dot]eberhard[at]redaxo[dot]de Andreas Eberhard
 * @author <a href="http://rex.andreaseberhard.de">rex.andreaseberhad.de</a>
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>
 *
 * @package redaxo4
 * @version $Id: config.inc.php,v 1.5 2008/03/11 16:04:53 kills Exp $
 */

	// Tabelle bei REDAXO 3.2.x ausgeben
	if ($rxa_tinymce['rexversion'] == '32')
	{
		echo '<table border="0" cellpadding="5" cellspacing="1" width="770">';
		echo '<tr>';
		echo '<td class="grey">';
	}
?>

<div class="rex-addon-output">

  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_simple_sample'); ?></h2>
  <div class="rex-addon-content">

  <?php echo $I18N_A52->msg('txt_simple_sample'); ?>
  <br /><br />
<?php
  echo $I18N_A52->msg('title_input');
  $file = dirname( __FILE__) .'/../examples/simple-input.txt';
  if(is_readable($file))
  {
    echo '<textarea style="width:99%;height:120px;" onfocus="this.select();">';
    if (strstr($REX['LANG'],'utf8'))
    {
      echo utf8_encode(htmlspecialchars(file_get_contents($file)));
    }
    else
    {
      echo htmlspecialchars(file_get_contents($file));
    }
    echo '</textarea>';
  }
  echo "<br /><br />";
  echo $I18N_A52->msg('title_output');
  $file = dirname( __FILE__) .'/../examples/output.txt';
  if(is_readable($file))
  {
    echo '<textarea style="width:99%;height:120px;" onfocus="this.select();">';
    if (strstr($REX['LANG'],'utf8'))
    {
      echo utf8_encode(htmlspecialchars(file_get_contents($file)));
    }
    else
    {
      echo htmlspecialchars(file_get_contents($file));
    }
    echo '</textarea>';
  }
?>
<br /><br />
  </div>

  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_extended_sample'); ?></h2>
  <div class="rex-addon-content">

  <?php echo $I18N_A52->msg('txt_extended_sample'); ?>
  <br /><br />
<?php
  echo $I18N_A52->msg('title_input');
  $file = dirname( __FILE__) .'/../examples/extended-input.txt';
  if(is_readable($file))
  {
    echo '<textarea style="width:99%;height:300px;" onfocus="this.select();">';
    if (strstr($REX['LANG'],'utf8'))
    {
      echo utf8_encode(htmlspecialchars(file_get_contents($file)));
    }
    else
    {
      echo htmlspecialchars(file_get_contents($file));
    }
    echo '</textarea>';
  }
?>

  </div>

  <br /><br />

</div>


<?php
	// Tabelle bei REDAXO 3.2.x ausgeben
	if ($rxa_tinymce['rexversion'] == '32')
	{
		echo '</td>';
		echo '</tr>';
		echo '</table>';
	}
?>