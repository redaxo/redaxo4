<?
include_once $REX[INCLUDE_PATH]."/classes/class.ooarticle.inc.php";

/*
 * The OOArticleSlice class is an object wrapper over the database table rex_articel_slice.
 * Together with OOArticle and OOCategory it provides an object oriented
 * Framework for accessing vital parts of your website.
 * This framework can be used in Modules, Templates and PHP-Slices!
 *
 * Carsten Eckelmann <carsten@circle42.com>, May 2004
 */
class OOArticleSlice {
  var $id;
  var $re_article_slice_id;
  var $value;
  var $file;
  var $link;
  var $php;
  var $html;
  var $article_id;
  var $modultyp_id;

	/*
	 * Constructor
	 */
	function OOArticleSlice() {
		$this->_id = $id;
		$this->_re_article_slice_id = $re_article_slice_id;
		$this->_value = &$value;
		$this->_file = &$file;
		$this->_link = &$link;
		$this->_php = $php;
		$this->_html = $html;
		$this->_article_id = $article_id;
		$this->_modultyp_id = $modultyp_id;
	}
	
	/*
	 * CLASS Function:
	 */
	function getSliceById($an_id) {
	}
	
	/*
	 * CLASS Function:
	 */
	function getSlicesForArticle($an_article_id) {
	}
	
	/*
	 * CLASS Function:
	 */
	function getSlicesForArticleOfType($an_article_id, $a_type_id) {
	}
	
	/*
	 * Object Function:
	 */
	function getNextSlice() {
	}
	
	/*
	 * Object Function:
	 */
	function getPreviousSlice() {
	}
}

?>
