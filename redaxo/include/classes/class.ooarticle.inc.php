<?

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
	var $_re_id;
	var $_name;
	var $_catname;
	var $_cattype;
	var $_alias;
	var $_description;
	var $_attribute;
	var $_file;
	var $_type_id;
	var $_teaser;
	var $_startpage;
	var $_prior;
	var $_path;
	var $_status;
	var $_online_from;
	var $_online_to;
	var $_createdate;
	var $_updatedate;
	var $_keywords;
	var $_template_id;
	var $_clang;
	var $_createuser;
	var $_updateuser;

	/*
	 * Constructor
	 */
	function OOArticle($params = false) {
		if($params){
		    foreach($this->getClassVars() as $key=>$var){
		        $this->$key = $params[$var];
		    }
		}
	}

	/*
	 * CLASS Function:
	 * Returns an Array containing article db field names
	 */
	function getClassVars(){
			$class_vars = get_class_vars("OOArticle");
			foreach($class_vars as $key=>$var){
			    $class_vars[$key] = substr($key,1);
			}
			return $class_vars;
	}

	/*
	 * CLASS Function:
	 * Return an OOArticle object based on an id
	 */
	function getArticleById($an_id, $clang = false) {
		if($clang === false) $clang = $REX[CUR_CLANG];
		$sql = new sql;
		//$sql->debugsql = true;
		$sql->setQuery("select ".implode(',',OOArticle::getClassVars())." from rex_article where id = '$an_id' and clang = '$clang'");
		if ($sql->getRows() == 1) {
            foreach(OOArticle::getClassVars() as $var){
                $article_data[$var] = $sql->getValue($var);
            }
			return new OOArticle($article_data);
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
	function searchArticlesByName($a_name, $clang = false) {
		if($clang === false) $clang = $REX[CUR_CLANG];
		$artlist = array();
		$sql = new sql;
		$sql->debugsql = true;
		$sql->setQuery("select ".implode(',',OOArticle::getClassVars())." from rex_article where name like '$a_name'");
		for ($i = 0; $i < $sql->getRows(); $i++) {
            foreach(OOArticle::getClassVars() as $var){
                $article_data[$var] = $sql->getValue($var);
            }
            $artlist[] = new OOArticle($article_data);
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
	function getArticlesByType($a_type_id, $clang = false) {
		if($clang === false) $clang = $REX[CUR_CLANG];
		$artlist = array();
		$sql = new sql;
		$sql->debugsql = true;
		$sql->setQuery("select ".implode(',',OOArticle::getClassVars())." from rex_article where type_id = '$a_type_id'");
		for ($i = 0; $i < $sql->getRows(); $i++) {
            foreach(OOArticle::getClassVars() as $var){
                $article_data[$var] = $sql->getValue($var);
            }
            $artlist[] = new OOArticle($article_data);
			$sql->next();
		}
		return $artlist;
	}


	/*
	 * CLASS Function:
	 * Return the site wide start article
	 */
	function getSiteStartArticle() {
		global $REX;
		if($clang === false) $clang = $REX[CUR_CLANG];
		$sql = new sql;
		//$sql->debugsql = true;
		$sql->setQuery("select ".implode(',',OOArticle::getClassVars())." from rex_article where id = '$REX[STARTARTIKEL_ID]' and clang='$clang'");
		if ($sql->getRows() == 1) {
            foreach(OOArticle::getClassVars() as $var){
                $article_data[$var] = $sql->getValue($var);
            }
			return new OOArticle($article_data);
		}
		return null;
	}

	/*
	 * CLASS Function:
	 * Return start article for a certain category
	 */
	function getCategoryStartArticle($a_category_id, $clang = false) {
		global $REX;
		if($clang === false) $clang = $REX[CUR_CLANG];
		$sql = new sql;
		$sql->setQuery("select ".implode(',',OOArticle::getClassVars())." from rex_article where id = '$a_category_id' and clang='$clang'");
		if ($sql->getRows() == 1) {
            foreach(OOArticle::getClassVars() as $var){
                $article_data[$var] = $sql->getValue($var);
            }
			return new OOArticle($article_data);
		}
		return null;
	}

	/*
	 * CLASS Function:
	 * Return a list of articles for a certain category
	 */
	function getArticlesOfCategory($a_category_id, $ignore_offlines = false, $clang = false) {
		global $REX;
		if($clang === false) $clang = $REX[CUR_CLANG];
		$off = $ignore_offlines ? " and status = 1 " : "" ;
		$artlist = array();
		$sql = new sql;
		$sql->setQuery("select ".implode(',',OOArticle::getClassVars())." from rex_article where re_id = '$a_category_id' and clang='$clang' $off order by prior");
		for ($i = 0; $i < $sql->getRows(); $i++) {
            foreach(OOArticle::getClassVars() as $var){
                $article_data[$var] = $sql->getValue($var);
            }
            $artlist[] = new OOArticle($article_data);
			$sql->next();
		}
		return $artlist;
	}

	/*
	 * CLASS Function:
	 * Return a list of top-level articles
	 */
	function getRootArticles($ignore_offlines = false, $clang = false) {
		global $REX;
		if($clang === false) $clang = $REX[CUR_CLANG];
		$off = $ignore_offlines ? " and status = 1 " : "" ;
		$artlist = array();
		$sql = new sql;
		$sql->setQuery("select ".implode(',',OOArticle::getClassVars())." from rex_article where re_id = '0' and clang='$clang' $off order by prior");
		for ($i = 0; $i < $sql->getRows(); $i++) {
            foreach(OOArticle::getClassVars() as $var){
                $article_data[$var] = $sql->getValue($var);
            }
            $artlist[] = new OOArticle($article_data);
			$sql->next();
		}
		return $artlist;
	}


	function getArticlesOfCategoryByDate($a_category_id, $day_from, $month_from, $year_from, $day_to, $month_to, $year_to) {
		// TO BE DONE, 26.05.04
		return null;
	}

	/*
	 * CLASS function
	 * Returns a list of articles of any category that have been
	 * newly created.
	 * $number_of_articles = how far to go back in history
	 */
	function getNewArticles($number_of_articles = 0, $ignore_startpages = true, $ignore_offlines = true, $category_id = 0, $clang = false) {
		global $REX;
		$number_of_articles = intval($number_of_articles);
		if($clang === false) $clang = $REX[CUR_CLANG];
		$category = $category_id ? " and re_id = '{$category_id}' " : "";
		$off = $ignore_offlines ? " and status = '1' " : "" ;
		$nostart = $ignore_startpages ? " and startpage = '0' and id != '$REX[STARTARTIKEL_ID]'" : "";
		$limit = $number_of_articles ? " LIMIT 0, {$number_of_articles} " : "";
		$artlist = array();
		$sql = new sql;
		$sql->debugsql = true;
		$sql->setQuery("select ".implode(',',OOArticle::getClassVars())." from rex_article where clang='$clang' $off $nostart $category order by createdate desc $limit");
		for ($i = 0; $i < $sql->getRows(); $i++) {
            foreach(OOArticle::getClassVars() as $var){
                $article_data[$var] = $sql->getValue($var);
            }
            $artlist[] = new OOArticle($article_data);
			$sql->next();
		}
		return $artlist;
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
		return $this->_description;
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
			$param_string = $REX['MOD_REWRITE'] ? "?" : "&amp;";
			foreach ($params as $key => $val) {
				$param_string .= "{$key}={$val}&amp;";
			}
		}
		$param_string = substr($param_string,0,strlen($param_string)-5); // cut off the last '&'
		$mr_name = $this->getModRewriteName();
		$url = $REX['MOD_REWRITE'] ? "/{$this->_id}-{$mr_name}"
		                           : "index.php?article_id={$this->_id}";
	  	return $REX['WWW_PATH']."{$url}{$param_string}";
	}

	/*
	*  Accessor Method:
	 * returns true if this Article is the Startpage for the category.
	 */
	function isStartPage() {
	    return $this->_startpage;
	}


	/*
	*  Accessor Method:
	 * returns true if this Article is the Startpage for the entire site.
	 */
	function isSiteStartArticle() {
	 global $REX;
	 return $this->_id == $REX[STARTARTIKEL_ID];
	}
}
?>