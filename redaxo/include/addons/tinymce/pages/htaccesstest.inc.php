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

	$address = dirname(dirname($_SERVER['PHP_SELF']));
	$splitURL = split('/redaxo/', $address);
	$address = $splitURL[0];
	if($address != '/' && $address != '\\')
		$address .= '/';
		$address = str_replace("\\",'/',$address);

	$path1 = 'http://' . $_SERVER['HTTP_HOST'] . $address . 'redaxo/include/addons/tinymce/img/';
	$path2 = 'http://' . $_SERVER['HTTP_HOST'] . $address . 'redaxo/include/addons/tinymce/tinymce/jscripts/';

	$testimg1 = $path1 . '/emoticons.gif';
	$testimg2 = $path2 . '/emoticons.gif';

	$info_htaccess = $I18N_A52->msg('info_htaccess', $path1, $path2)
?>
<div id="tinyMCEhtaccessInfo" style="display:none;background:#FAE9E5;border:solid 2px #c00;padding:20px;font-weight:bold;color:#c00;margin-bottom:10px;">
<?php echo $info_htaccess; ?>
</div>

<script type="text/javascript">
<!--
function showHtaccessInfo() {
	document.getElementById('tinyMCEhtaccessInfo').style.display = 'block';
}
var tinyImgPreloader1 = new Image();
var tinyImgPreloader2 = new Image();

tinyImgPreloader1.onerror = (function(){
	showHtaccessInfo();
})
tinyImgPreloader2.onerror = (function(){
	showHtaccessInfo();
})
tinyImgPreloader1.src = '<?php echo $testimg1; ?>';
tinyImgPreloader2.src = '<?php echo $testimg2; ?>';
//-->
</script>