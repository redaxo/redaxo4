<?
include_once $REX[INCLUDE_PATH]."/classes/class.ooarticleslice.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.category.inc.php";

/*
 * The OOArticle class is an object wrapper over the database table rex_article.
 * Together with OOCategory and OOArticleSlice it provides an object oriented
 * Framework for accessing vital parts of your website.
 * This framework can be used in Modules, Templates and PHP-Slices!
 *
 * Carsten Eckelmann <carsten@circle42.com>, May 2004
 */
class OOArticle {
  var $id;
  var $name;
  var $beschreibung;
  var $attribute;
  var $text;
  var $category_id;
  var $type_id;
  var $startpage;
  var $prior;
  var $path;
  var $status;
  var $online_von;
  var $online_bis;
  var $erstelldatum;
  var $suchbegriffe;
  var $template_id;
  var $checkbox01;
  var $checkbox02;
  var $checkbox03;
  var $checkbox04;

	/*
	 * CLASS Function:
	 * Return an OOArticle object based on an id
	 */
	function getArticleById($an_id) {
	}

	/*
	 * CLASS Function:
	 * Return a list of articles which names match the
	 * search string. For now the search string can be either
	 * a simple name or a string containing SQL search placeholders
	 * that you would insert into a 'LIKE '%...%' statement.
	 *
	 * Returns an array of OOArticle objects.
	 */
	function searchArticlesByName($a_name) {
	}
	
	/*
	 * CLASS Function:
	 * Return the site wide start article
	 */
	function getSiteStartArticle() {
	}
	 
	/*
	 * Object Function:
	 * Return the category of this article
	 * Returns an OOCategory object.
	 */
	function getCategory() {
	}
	
	/*
	 * Object Function:
	 * Return a list of all the slices of this article
	 * in the order as they appear.
	 * Returns an array of OOArticleSlice objects
	 */
	function getSlices() {
	}
}
?>
