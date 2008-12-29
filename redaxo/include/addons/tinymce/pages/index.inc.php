<?php

/**
 * TinyMCE Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>s
 *
 * @package redaxo4
 * @version $Id: index.inc.php,v 1.4 2008/03/11 16:05:55 kills Exp $
 */

require $REX['INCLUDE_PATH']."/layout/top.php";

$subline = '
<ul>
  <li><a href="http://tinymce.moxiecode.com" onclick="window.open(this.href); return false;">'.$I18N_A52->msg('website').'</a> | </li>
  <li><a href="http://tinymce.moxiecode.com/tinymce/docs/index.html" onclick="window.open(this.href); return false;">'.$I18N_A52->msg('documentation').'</a> | </li>
  <li><a href="http://tinymce.moxiecode.com/tinymce/docs/reference_plugins.html" onclick="window.open(this.href); return false;">'.$I18N_A52->msg('list_of_plugins').'</a></li>
</ul>
';

rex_title($I18N_A52->msg('title'), $subline);

$install = rex_get('install', 'string');
if($install != '')
{
	include_once $REX['INCLUDE_PATH'] . '/addons/tinymce/functions/function_pclzip.inc.php';


	switch ($install) {
  	case 'compressor':
  	{
  		rex_a52_extract_archive('include/addons/tinymce/js/tinymce_compressor.zip');
  		break;
  	}
  	case 'spellchecker':
  	{
  		rex_a52_extract_archive('include/addons/tinymce/js/tinymce_spellchecker.zip');
  		break;
  	}
  }
}


$mdl_1 =<<<EOD
<?php
// Diese 3 Zeilen dürfen keine führenden Leerzeichen besitzen!
\$value1 =<<<TEXT
REX_VALUE[1]
TEXT;

\$editor=new rexTiny2Editor();
\$editor->id=1;
\$editor->content=\$value1;
\$editor->show();
?>
EOD;



$mdl_2 =<<<EOD
<?php
// Diese 3 Zeilen dürfen keine führenden Leerzeichen besitzen!
\$value1 =<<<TEXT
REX_VALUE[1]
TEXT;

\$editor1=new rexTiny2Editor();
\$editor1->id=1;
\$editor1->content=\$value1;
\$editor1->editorCSS = '../files/tmp_/tinymce/content.css';
\$editor1->disable='justifyleft,justifycenter,justifyright,justifyfull';
\$editor1->buttons3='tablecontrols,separator,search,replace,separator,print';
//\$editor1->add_validhtml='img[myspecialtag]';
\$editor1->show();

// Diese 3 Zeilen dürfen keine führenden Leerzeichen besitzen!
\$value2 =<<<TEXT
REX_VALUE[2]
TEXT;

\$editor2=new rexTiny2Editor();
\$editor2->id=2;
\$editor2->content=\$value2;
\$editor2->show();
?>
EOD;



$mdl_3 =<<<EOD
<?php
if (REX_IS_VALUE[1])
{
// Diese 3 Zeilen dürfen keine führenden Leerzeichen besitzen!
\$content =<<<TEXT
REX_HTML_VALUE[1]
TEXT;

  echo '<div class="section">';
  echo \$content;
  echo '</div>';
}
?>
EOD;

$mdl_css = <<<EOD
<head>
  ...
  <link rel="stylesheet" type="text/css"
   href="files/tmp_/tinymce/tinymce.css" media="screen" />
  ...
</head>
EOD;

?>

<div class="rex-addon-output">
	<h2><?php echo $I18N_A52->msg('install_extensions'); ?></h2>

	<div class="rex-addon-content">

		<p>
			<a href="?page=tinymce&amp;install=compressor">GZip Compressor</a>
			<br />
			<a href="?page=tinymce&amp;install=spellchecker">Spellchecker</a>
		</p>
	</div>

  <h2><?php echo $I18N_A52->msg('modulecss'); ?></h2>

  <div class="rex-addon-content">
    <?php rex_highlight_string($mdl_css); ?>
  </div>

	<h2><?php echo $I18N_A52->msg('moduleinput_simple'); ?></h2>

	<div class="rex-addon-content">
		<?php rex_highlight_string($mdl_1); ?>
	</div>

<h2><?php echo $I18N_A52->msg('moduleinput_extends'); ?></h2>

	<div class="rex-addon-content">
		<?php rex_highlight_string($mdl_2); ?>
	</div>

<h2><?php echo $I18N_A52->msg('moduleoutput'); ?></h2>

	<div class="rex-addon-content">
		<?php rex_highlight_string($mdl_3); ?>
	</div>

	<div class="rex-addon-content">
		<p>
			<a href="http://www.gn2-netwerk.de">GN2-Netwerk</a>
			<br />
			<a href="http://www.public-4u.de">Public-4u e.K.</a>
		</p>
	</div>
</div>
<?php

require $REX['INCLUDE_PATH']."/layout/bottom.php";

?>