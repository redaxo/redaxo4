<?
include_once $REX[INCLUDE_PATH]."/classes/class.ooarticleslice.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.oocategory.inc.php";

/*
 * The OOArticle class is an object wrapper over the database table rex_article.
 * Together with OOCategory and OOArticleSlice it provides an object oriented
 * Framework for accessing vital parts of your website.
 * This framework can be used in Modules, Templates and PHP-Slices!
 *
 * Carsten Eckelmann <carsten@circle42.com>, May 2004
 */
class OOArticle {
  var $_id;
  var $_name;
  var $_beschreibung;
  var $_attribute;
  var $_file;
  var $_category_id;
  var $_type_id;
  var $_startpage;
  var $_prior;
  var $_path;
  var $_status;
  var $_online_von;
  var $_online_bis;
  var $_erstelldatum;
  var $_suchbegriffe;
  var $_template_id;
  var $_checkbox01;
  var $_checkbox02;
  var $_checkbox03;
  var $_checkbox04;

	/*
	 * Constructor
	 */
	function OOArticle($id,$name,$beschreibung,$attribute,$file,$category_id,$type_id,$startpage,$prior,$path,$status,$online_von,$online_bis,$erstelldatum,$suchbegriffe,$template_id,$checkbox01,$checkbox02,$checkbox03,$checkbox04) {
		$this->_id = $id;
		$this->_name = $name;
		$this->_beschreibung = $beschreibung;
		$this->_attribute = $attribute;
		$this->_file = $file;
		$this->_category_id = $category_id;
		$this->_type_id = $type_id;
		$this->_startpage = $startpage;
		$this->_prior = $prior;
		$this->_path = $path;
		$this->_status = $status;
		$this->_online_von = $online_von;
		$this->_online_bis = $online_bis;
		$this->_erstelldatum = $erstelldatum;
		$this->_suchbegriffe = $suchbegriffe;
		$this->_template_id = $template_id;
		$this->_checkbox01 = $checkbox01;
		$this->_checkbox02 = $checkbox02;
		$this->_checkbox03 = $checkbox03;
		$this->_checkbox04 = $checkbox04;
	}

	/*
	 * CLASS Function:
	 * Return an OOArticle object based on an id
	 */
	function getArticleById($an_id) {
		$sql = new sql;
		$sql->setQuery("select id,name,beschreibung,attribute,file,category_id,type_id,startpage,prior,path,status,online_von,online_bis,erstelldatum,suchbegriffe,template_id,checkbox01,checkbox02,checkbox03,checkbox04 from rex_article where id = $an_id");
		if ($sql->getRows() == 1) {
			return new OOArticle($sql->getValue("id"),$sql->getValue("name"),
								$sql->getValue("beschreibung"),$sql->getValue("attribute"),
								$sql->getValue("file"),$sql->getValue("category_id"),
								$sql->getValue("type_id"),$sql->getValue("startpage"),
								$sql->getValue("prior"),$sql->getValue("path"),
								$sql->getValue("status"),$sql->getValue("online_von"),
								$sql->getValue("online_bis"),$sql->getValue("erstelldatum"),
								$sql->getValue("suchbegriffe"),$sql->getValue("template_id"),
								$sql->getValue("checkbox01"),$sql->getValue("checkbox02"),
								$sql->getValue("checkbox03"),$sql->getValue("checkbox04"));
		}
		return null;
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
		$artlist = array();
		$sql = new sql;
		$sql->setQuery("select id,name,beschreibung,attribute,file,category_id,type_id,startpage,prior,path,status,online_von,online_bis,erstelldatum,suchbegriffe,template_id,checkbox01,checkbox02,checkbox03,checkbox04 from rex_article where name like '$a_name'");
		for ($i = 0; $i < $sql->getRows(); $i++) {
			$artlist[] = new OOArticle($sql->getValue("id"),$sql->getValue("name"),
								$sql->getValue("beschreibung"),$sql->getValue("attribute"),
								$sql->getValue("file"),$sql->getValue("category_id"),
								$sql->getValue("type_id"),$sql->getValue("startpage"),
								$sql->getValue("prior"),$sql->getValue("path"),
								$sql->getValue("status"),$sql->getValue("online_von"),
								$sql->getValue("online_bis"),$sql->getValue("erstelldatum"),
								$sql->getValue("suchbegriffe"),$sql->getValue("template_id"),
								$sql->getValue("checkbox01"),$sql->getValue("checkbox02"),
								$sql->getValue("checkbox03"),$sql->getValue("checkbox04"));
			$sql->next();
		}
		return $artlist;
	}

	/*
	 * CLASS Function:
	 * Return a list of articles which have a certain type.
	 *
	 * Returns an array of OOArticle objects.
	 */
	function getArticlesByType($a_type_id) {
		$artlist = array();
		$sql = new sql;
		$sql->setQuery("select id,name,beschreibung,attribute,file,category_id,type_id,startpage,prior,path,status,online_von,online_bis,erstelldatum,suchbegriffe,template_id,checkbox01,checkbox02,checkbox03,checkbox04 from rex_article where type_id = $a_type_id");
		for ($i = 0; $i < $sql->getRows(); $i++) {
			$artlist[] = new OOArticle($sql->getValue("id"),$sql->getValue("name"),
								$sql->getValue("beschreibung"),$sql->getValue("attribute"),
								$sql->getValue("file"),$sql->getValue("category_id"),
								$sql->getValue("type_id"),$sql->getValue("startpage"),
								$sql->getValue("prior"),$sql->getValue("path"),
								$sql->getValue("status"),$sql->getValue("online_von"),
								$sql->getValue("online_bis"),$sql->getValue("erstelldatum"),
								$sql->getValue("suchbegriffe"),$sql->getValue("template_id"),
								$sql->getValue("checkbox01"),$sql->getValue("checkbox02"),
								$sql->getValue("checkbox03"),$sql->getValue("checkbox04"));
			$sql->next();
		}
		return $artlist;
	}


	/*
	 * CLASS Function:
	 * Return the site wide start article
	 */
	function getSiteStartArticle() {
		$sql = new sql;
		$sql->setQuery("select id,name,beschreibung,attribute,file,category_id,type_id,startpage,prior,path,status,online_von,online_bis,erstelldatum,suchbegriffe,template_id,checkbox01,checkbox02,checkbox03,checkbox04 from rex_article where startpage = {$REX[STARTARTIKEL_ID]}");
		if ($sql->getRows() == 1) {
			return new OOArticle($sql->getValue("id"),$sql->getValue("name"),
								$sql->getValue("beschreibung"),$sql->getValue("attribute"),
								$sql->getValue("file"),$sql->getValue("category_id"),
								$sql->getValue("type_id"),$sql->getValue("startpage"),
								$sql->getValue("prior"),$sql->getValue("path"),
								$sql->getValue("status"),$sql->getValue("online_von"),
								$sql->getValue("online_bis"),$sql->getValue("erstelldatum"),
								$sql->getValue("suchbegriffe"),$sql->getValue("template_id"),
								$sql->getValue("checkbox01"),$sql->getValue("checkbox02"),
								$sql->getValue("checkbox03"),$sql->getValue("checkbox04"));
		}
		return null;
	}

	/*
	 * CLASS Function:
	 * Return start article for a certain category
	 */
	function getCategoryStartArticle($a_category_id) {
		$sql = new sql;
		$sql->setQuery("select id,name,beschreibung,attribute,file,category_id,type_id,startpage,prior,path,status,online_von,online_bis,erstelldatum,suchbegriffe,template_id,checkbox01,checkbox02,checkbox03,checkbox04 from rex_article where startpage = 1 and category_id = $a_category_id");
		if ($sql->getRows() == 1) {
			return new OOArticle($sql->getValue("id"),$sql->getValue("name"),
								$sql->getValue("beschreibung"),$sql->getValue("attribute"),
								$sql->getValue("file"),$sql->getValue("category_id"),
								$sql->getValue("type_id"),$sql->getValue("startpage"),
								$sql->getValue("prior"),$sql->getValue("path"),
								$sql->getValue("status"),$sql->getValue("online_von"),
								$sql->getValue("online_bis"),$sql->getValue("erstelldatum"),
								$sql->getValue("suchbegriffe"),$sql->getValue("template_id"),
								$sql->getValue("checkbox01"),$sql->getValue("checkbox02"),
								$sql->getValue("checkbox03"),$sql->getValue("checkbox04"));
		}
		return null;
	}

	/*
	 * CLASS Function:
	 * Return a list of articles for a certain category
	 */
	function getArticlesOfCategory($a_category_id, $ignore_offlines = false) {
		$off = $ignore_offlines ? " and status = 1 " : "" ;
		$artlist = array();
		$sql = new sql;
		$sql->setQuery("select id,name,beschreibung,attribute,file,category_id,type_id,startpage,prior,path,status,online_von,online_bis,erstelldatum,suchbegriffe,template_id,checkbox01,checkbox02,checkbox03,checkbox04 from rex_article where category_id = $a_category_id $off order by prior");
		for ($i = 0; $i < $sql->getRows(); $i++) {
			$artlist[] = new OOArticle($sql->getValue("id"),$sql->getValue("name"),
								$sql->getValue("beschreibung"),$sql->getValue("attribute"),
								$sql->getValue("file"),$sql->getValue("category_id"),
								$sql->getValue("type_id"),$sql->getValue("startpage"),
								$sql->getValue("prior"),$sql->getValue("path"),
								$sql->getValue("status"),$sql->getValue("online_von"),
								$sql->getValue("online_bis"),$sql->getValue("erstelldatum"),
								$sql->getValue("suchbegriffe"),$sql->getValue("template_id"),
								$sql->getValue("checkbox01"),$sql->getValue("checkbox02"),
								$sql->getValue("checkbox03"),$sql->getValue("checkbox04"));
			$sql->next();
		}
		return $artlist;
	}

	/*
	 * CLASS Function:
	 * Return a list of top-level articles
	 */
	function getRootArticles($ignore_offlines = false) {
		$off = $ignore_offlines ? " and status = 1 " : "" ;
		$artlist = array();
		$sql = new sql;
		$sql->setQuery("select id,name,beschreibung,attribute,file,category_id,type_id,startpage,prior,path,status,online_von,online_bis,erstelldatum,suchbegriffe,template_id,checkbox01,checkbox02,checkbox03,checkbox04 from rex_article where category_id = 0 $off order by prior");
		for ($i = 0; $i < $sql->getRows(); $i++) {
			$artlist[] = new OOArticle($sql->getValue("id"),$sql->getValue("name"),
								$sql->getValue("beschreibung"),$sql->getValue("attribute"),
								$sql->getValue("file"),$sql->getValue("category_id"),
								$sql->getValue("type_id"),$sql->getValue("startpage"),
								$sql->getValue("prior"),$sql->getValue("path"),
								$sql->getValue("status"),$sql->getValue("online_von"),
								$sql->getValue("online_bis"),$sql->getValue("erstelldatum"),
								$sql->getValue("suchbegriffe"),$sql->getValue("template_id"),
								$sql->getValue("checkbox01"),$sql->getValue("checkbox02"),
								$sql->getValue("checkbox03"),$sql->getValue("checkbox04"));
			$sql->next();
		}
		return $artlist;
	}


	function getArticlesOfCategoryByDate($a_category_id, $day_from, $month_from, $year_from, $day_to, $month_to, $year_to) {
		// TO BE DONE, 26.05.04
		return null;
	}

	/*
	 * CLASS function:
	 * Return a list of articles which slices contain the search string.
	 * Returns an array of OOArticle objects.
	 */
	function fullTextSearch($searchstring) {
		$slices = OOArticleSlice::fullTextSearch($searchstring);
		$artlist = array();
		$ret = array();
		foreach ($slices as $slice) {
			if (! isset($artlist[$slice->_article_id])){
				$ret[]=$slice->getArticle();
				$artlist[$slice->_article_id]=1;
			}
		}
		return $ret;
	}

	/*
	 * Object Function:
	 * Return the category of this article
	 * Returns an OOCategory object.
	 */
	function getCategory() {
		return OOCategory::getCategoryById($this->_category_id);
	}

	/*
	 * Object Function:
	 * Return the first slice of this article
	 * Returns an OOArticleSlice objects
	 */
	function getFirstSlice() {
		return OOArticleSlice::getFirstSliceForArticle($this->_id);
	}

	/*
	 * Object Function:
	 * Return a list of all the slices of this article
	 * in the order as they appear, but only those with a certain type!
	 * Returns an array of OOArticleSlice objects
	 */
	function getSlicesOfType($a_type_id) {
		return OOArticleSlice::getSlicesForArticleOfType($this->_id, $a_type_id);
	}

	/*
	 * Accessor Method:
	 * returns the id of the article
	 */
	function getId() {
		return $this->_id;
	}

	/*
	 * Accessor Method:
	 * returns the name of the article
	 */
	function getName() {
		return $this->_name;
	}

	/*
	 * Accessor Method:
	 * returns the name transformed for
	 * use with mod_rewrite in an url.
	 */
	function getModRewriteName() {
		$url = ModRewriteName($this->_name);
		return $url;
	}

	/*
	 * Accessor Method:
	 * returns true if article is online.
	 */
	function isOnline() {
		return $this->_status == 1 ? true : false;
	}

	/*
	 * Accessor Method:
	 * returns the article description.
	 */
	function getDescription() {
		return $this->_beschreibung;
	}

	/*
	 * Object Helper Function:
	 * Returns a String representation of this object
	 * for debugging purposes.
	 */
	function toString() {
		return "Article: ".$this->_id.", ".$this->_name.", ".($this->isOnline() ? "online" : "offline");
	}

	/*
	 * Object Helper Function:
	 * Returns a url for linking to this article
	 * This url respects the setting for mod_rewrite
	 * support!
	 *
	 * If you pass an associative array for $params,
	 * then these parameters will be attached to the URL.
	 * e.g.:
	 *   $param = array("order" => "123", "name" => "horst");
	 *   $article->getUrl($param);
	 * will return:
	 *   index.php?article_id=1&order=123&name=horst
	 * or if mod_rewrite support is activated:
	 *   /1-The_Article_Name?order=123&name=horst
	 */
	function getUrl($params = null) {
		global $REX;
		$param_string = "";
		if ($params && sizeof($params) > 0) {
			$param_string = $REX['MOD_REWRITE'] ? "?" : "&";
			foreach ($params as $key => $val) {
				$param_string .= "{$key}={$val}&";
			}
		}
		$param_string = substr($param_string,0,strlen($param_string)-1); // cut off the last '&'
		$mr_name = $this->getModRewriteName();
		$url = $REX['MOD_REWRITE'] ? "/{$this->_id}-{$mr_name}"
		                           : "index.php?article_id={$this->_id}";
	  return $REX['WWW_PATH']."{$url}{$param_string}";
	}

	/*
	 * returns true if this Article is the Startpage for the category.
	 */
	function isStartPage() {
		return $this->_startpage == 1;
	}
	
}
?>
