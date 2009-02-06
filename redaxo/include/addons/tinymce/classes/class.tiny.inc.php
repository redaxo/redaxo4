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
 * @version $Id: class.tiny.inc.php,v 1.8 2008/03/11 16:05:55 kills Exp $
 */

global $TINY2;
$TINY2['counter'] = 0;
$TINY2['script'] = 0;
$TINY2['boxes'] = 0;
$TINY2['address'] = $REX['HTDOCS_PATH'];

class rexTiny2Editor
{


  var $editorCSS = '../files/tmp_/tinymce/tinymce.css';
  var $advimageCSS = '';
  var $disable = '';
  var $plugins = 'advlink,advimage,emotions,iespell,table,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu,paste,directionality,fullscreen,redaxo';
  var $validhtml =
    '"a[accesskey|charset|class|coords|dir<ltr?rtl|href|hreflang|id|lang|name"
      +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rel|rev"
      +"|shape<circle?default?poly?rect|style|tabindex|title|target|type],"
    +"abbr[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"acronym[class|dir<ltr?rtl|id|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"address[class|align|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|style|title],"
    +"applet[align<bottom?left?middle?right?top|alt|archive|class|code|codebase"
      +"|height|hspace|id|name|object|style|title|vspace|width],"
    +"area[accesskey|alt|class|coords|dir<ltr?rtl|href|id|lang|nohref<nohref"
      +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup"
      +"|shape<circle?default?poly?rect|style|tabindex|title|target],"
    +"base[href|target],"
    +"basefont[color|face|id|size],"
    +"bdo[class|dir<ltr?rtl|id|lang|style|title],"
    +"big[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"blockquote[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
      +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
      +"|onmouseover|onmouseup|style|title],"
    +"body[alink|background|bgcolor|class|dir<ltr?rtl|id|lang|link|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|onunload|style|title|text|vlink],"
    +"br[class|clear<all?left?none?right|id|style|title],"
    +"button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur"
      +"|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown"
      +"|onmousemove|onmouseout|onmouseover|onmouseup|style|tabindex|title|type"
      +"|value],"
    +"caption[align<bottom?left?right?top|class|dir<ltr?rtl|id|lang|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"center[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"cite[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"code[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"col[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
      +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
      +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
      +"|valign<baseline?bottom?middle?top|width],"
    +"colgroup[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl"
      +"|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
      +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
      +"|valign<baseline?bottom?middle?top|width],"
    +"dd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
    +"del[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|style|title],"
    +"dfn[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"dir[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|style|title],"
    +"div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"dl[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|style|title],"
    +"dt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
    +"em/i[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"fieldset[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"font[class|color|dir<ltr?rtl|face|id|lang|size|style|title],"
    +"form[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang"
      +"|method<get?post|name|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onsubmit"
      +"|style|title|target],"
    +"frame[class|frameborder|id|longdesc|marginheight|marginwidth|name"
      +"|noresize<noresize|scrolling<auto?no?yes|src|style|title],"
    +"frameset[class|cols|id|onload|onunload|rows|style|title],"
    +"h1[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"h2[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"h3[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"h4[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"h5[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"h6[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"head[dir<ltr?rtl|lang|profile],"
    +"hr[align<center?left?right|class|dir<ltr?rtl|id|lang|noshade<noshade|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|size|style|title|width],"
    +"html[dir<ltr?rtl|lang|version],"
    +"iframe[align<bottom?left?middle?right?top|class|frameborder|height|id"
      +"|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style"
      +"|title|width],"
    +"img[align<bottom?left?middle?right?top|alt|border|class|dir<ltr?rtl|height"
      +"|hspace|id|ismap<ismap|lang|longdesc|name|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|src|style|title|usemap|vspace|width],"
    +"input[accept|accesskey|align<bottom?left?middle?right?top|alt"
      +"|checked<checked|class|dir<ltr?rtl|disabled<disabled|id|ismap<ismap|lang"
      +"|maxlength|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
      +"|readonly<readonly|size|src|style|tabindex|title"
      +"|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text"
      +"|usemap|value],"
    +"ins[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|style|title],"
    +"isindex[class|dir<ltr?rtl|id|lang|prompt|style|title],"
    +"kbd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"label[accesskey|class|dir<ltr?rtl|for|id|lang|onblur|onclick|ondblclick"
      +"|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
      +"|onmouseover|onmouseup|style|title],"
    +"legend[align<bottom?left?right?top|accesskey|class|dir<ltr?rtl|id|lang"
      +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"li[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title|type"
      +"|value],"
    +"link[charset|class|dir<ltr?rtl|href|hreflang|id|lang|media|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|rel|rev|style|title|target|type],"
    +"map[class|dir<ltr?rtl|id|lang|name|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"menu[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|style|title],"
    +"meta[content|dir<ltr?rtl|http-equiv|lang|name|scheme],"
    +"noframes[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"noscript[class|dir<ltr?rtl|id|lang|style|title],"
    +"object[align<bottom?left?middle?right?top|archive|border|class|classid"
      +"|codebase|codetype|data|declare|dir<ltr?rtl|height|hspace|id|lang|name"
      +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|standby|style|tabindex|title|type|usemap"
      +"|vspace|width],"
    +"ol[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|start|style|title|type],"
    +"optgroup[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick|ondblclick"
      +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
      +"|onmouseover|onmouseup|selected<selected|style|title|value],"
    +"p[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|style|title],"
    +"param[id|name|type|value|valuetype<DATA?OBJECT?REF],"
    +"pre/listing/plaintext/xmp[align|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
      +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
      +"|onmouseover|onmouseup|style|title|width],"
    +"q[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"s[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
    +"samp[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"script[charset|defer|language|src|type],"
    +"select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name"
      +"|onblur|onchange|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|size|style"
      +"|tabindex|title],"
    +"small[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"span[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|style|title],"
    +"strike[class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|style|title],"
    +"strong/b[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"style[dir<ltr?rtl|lang|media|title|type],"
    +"sub[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"sup[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title],"
    +"table[align<center?left?right|bgcolor|border|cellpadding|cellspacing|class"
      +"|dir<ltr?rtl|frame|height|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rules"
      +"|style|summary|title|width],"
    +"tbody[align<center?char?justify?left?right|char|class|charoff|dir<ltr?rtl|id"
      +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
      +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
      +"|valign<baseline?bottom?middle?top],"
    +"td[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
      +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
      +"|style|title|valign<baseline?bottom?middle?top|width],"
    +"textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name"
      +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
      +"|readonly<readonly|rows|style|tabindex|title],"
    +"tfoot[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
      +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
      +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
      +"|valign<baseline?bottom?middle?top],"
    +"th[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
      +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
      +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
      +"|style|title|valign<baseline?bottom?middle?top|width],"
    +"thead[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
      +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
      +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
      +"|valign<baseline?bottom?middle?top],"
    +"title[dir<ltr?rtl|lang],"
    +"tr[abbr|align<center?char?justify?left?right|bgcolor|char|charoff|class"
      +"|rowspan|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title|valign<baseline?bottom?middle?top],"
    +"tt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
    +"u[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
    +"ul[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
      +"|onmouseup|style|title|type],"
    +"var[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
      +"|title]"
    ';
  var $add_validhtml = '';
  var $buttons1 = 'styleselect,separator,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,outdent,indent';
  var $buttons2 = 'link,unlink,insertEmail,separator,image,insertMedia,separator,removeformat,paste,pastetext,pasteword,code';
  var $buttons3 = '';
  var $buttons4 = '';
  var $buttons1_add = '';
  var $buttons2_add = '';
  var $lang = 'de';

  var $address = '';

  var $content;
  var $id;

  function get()
  {
    ob_start();
    $this->show();
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  function show()
  {
    global $REX;

    $this->address = dirname(dirname($_SERVER['PHP_SELF']));
    $splitURL = split('/redaxo/', $this->address);
    $this->address = $splitURL[0];

    if($this->address != '/' && $this->address != '\\')
      $this->address .= '/';

    if ($GLOBALS['TINY2']['script'] != 1)
    {
      $script = '../files/tmp_/tinymce/jscripts/tiny_mce/tiny_mce_gzip.js';
      $useGzip = file_exists($script);

      if (!$useGzip)
        $script = '../files/tmp_/tinymce/jscripts/tiny_mce/tiny_mce.js';

      $init = "\n" . '<script language="javascript" type="text/javascript" src="' . $script . '"></script>';

      if ($useGzip)
      {
        include_once $REX['INCLUDE_PATH'] . '/addons/tinymce/functions/function_rex_folder.inc.php';
        $plugins = readFolder('../files/tmp_/tinymce/jscripts/tiny_mce/plugins');
        unset ($plugins[0]); // Lösche .
        unset ($plugins[1]); // Lösche ..
        $init .= "\n" .
        "<script type=\"text/javascript\">
        				tinyMCE_GZ.init({
        					plugins : '" . implode(',', $plugins) . "',
        					themes : 'advanced',
        					languages : 'de',
        					disk_cache : true,
        					debug : false
        				});
        				</script>";
      }
      echo $init;

      $GLOBALS['TINY2']['script'] = "1";
    }

    if ($GLOBALS['TINY2']['boxes'] != 1)
    {
      $GLOBALS['TINY2']['boxes'] = "1";
    }

    echo "\n" . '<script language="javascript" type="text/javascript">' . "\n";
    echo 'var sDocumentBase = "' . $this->address . '"' . ';' . "\n";

    echo 'tinyMCE.init({' . "\n";
    echo 'document_base_url: sDocumentBase,' . "\n";
    echo 'relative_urls: true,' . "\n";
    echo 'advimage_styles : "' . $this->advimageCSS . '",' . "\n";
    echo 'content_css : "' . $this->editorCSS . '",' . "\n";
    echo 'mode : "exact",' . "\n";
    echo 'elements : "tiny2e' . $this->id . '",' . "\n";
    echo 'theme : "advanced",' . "\n";
    echo 'advimage_image_browser_callback : "fileBrowserCallBack",' . "\n";
    echo 'advlink_file_browser_callback:"linkBrowserCallBack",' . "\n";
    echo 'plugins : "' . $this->plugins . '",' . "\n";
    echo 'theme_advanced_disable : "' . $this->disable . '",' . "\n";
    echo 'theme_advanced_buttons1 : "' . $this->buttons1 . '",' . "\n";
    echo 'theme_advanced_buttons2 : "' . $this->buttons2 . '",' . "\n";
    echo 'theme_advanced_buttons3 : "' . $this->buttons3 . '",' . "\n";
    echo 'theme_advanced_buttons4 : "' . $this->buttons4 . '",' . "\n";
    echo 'theme_advanced_buttons1_add : "' . $this->buttons1_add . '",' . "\n";
    echo 'theme_advanced_buttons2_add : "' . $this->buttons2_add . '",' . "\n";
    echo 'theme_advanced_toolbar_location : "top",' . "\n";
    echo 'theme_advanced_toolbar_align : "left",' . "\n";
    echo 'plugin_insertdate_dateFormat : "%Y-%m-%d",' . "\n";
    echo 'plugin_insertdate_timeFormat : "%H:%M:%S",' . "\n";
    echo 'inline_styles: true,' . "\n";
    echo 'valid_elements : ' . $this->validhtml . ",\n";
    echo 'extended_valid_elements : "' . $this->add_validhtml . '",' . "\n";
    echo 'paste_auto_cleanup_on_paste : true,' . "\n";
    echo 'paste_convert_headers_to_strong : true,' . "\n";
    echo 'convert_fonts_to_spans : true,' . "\n";
    echo 'cleanup_on_startup : true,' . "\n";
    echo 'remove_linebreaks : true,' . "\n";
    echo 'language: "' . $this->lang . '",' . "\n";
    echo 'apply_source_formatting : false,' . "\n";
    echo 'accessibility_warnings : false' . "\n";
    echo '});' . "\n";

    echo 'function fileBrowserCallBack(field_name, url, type, win) {' . "\n";
    echo 'newPoolWindow( sDocumentBase+"redaxo/index.php?page=mediapool&opener_input_field=TINYIMG");' . "\n";
    echo '}' . "\n";

    echo 'function linkBrowserCallBack(href, target){' . "\n";
    echo 'newLinkMapWindow(sDocumentBase+"redaxo/index.php?page=linkmap&opener_input_field=TINY");' . "\n";
    echo '}' . "\n";

    echo '//redaxo default callback functions' . "\n";

    echo 'function insertLink(link,name){' . "\n";
    echo ' var win=tinyMCE.getWindowArg("window");'."\n";
    echo ' win.document.forms[0].href.value=link;'."\n";
    echo ' win.document.forms[0].title.value=name;'."\n";
    echo '}' . "\n";

    echo 'function insertImage(imageUrl,title){' . "\n";
    echo ' var win=tinyMCE.getWindowArg("window");'."\n";
    echo ' win.document.forms[0].src.value=imageUrl;'."\n";
    echo ' win.document.forms[0].title.value=title;'."\n";
    // Hier Fehler behandeln
    // siehe http://trac.symfony-project.com/ticket/2625
    echo ' try {'."\n";
    echo '   win.resetImageData();'."\n";
    echo '   win.showPreviewImage(imageUrl, false);'."\n";
    echo ' } catch (ex) {}'."\n";
    echo '}' . "\n";

    echo 'function insertFileLink(fileUrl,title){' . "\n";
    echo '  tinyMCE.themes["advanced"]._insertLink(fileUrl,"_self");' . "\n";
    echo '}' . "\n";

    echo 'function insertHtml(htmlCode){' . "\n";
    echo ' tinyMCE.execCommand("mceInsertContent", false, htmlCode);' . "\n";
    echo '}' . "\n";

    echo '</script>';

    echo '<textarea name="VALUE[' . $this->id . ']" class="tiny2" id="tiny2e' . $this->id . '" style="width:100%;" cols="50" rows="15">' . $this->content . '</textarea>';
  }
}
?>