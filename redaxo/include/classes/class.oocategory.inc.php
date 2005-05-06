<?

/*
 * The OOCategory class is an object wrapper over the database table rex_category.
 * Together with OOArticle and OOArticleSlice it provides an object oriented
 * Framework for accessing vital parts of your website.
 * This framework can be used in Modules, Templates and PHP-Slices!
 *
 * Carsten Eckelmann <carsten@circle42.com>, May 2004^
 *
 *
 * Jan: muss noch umgebaut werden auf generated und clang
 */
class OOCategory {

  var $_id = 0;
  var $_clang = 0;

	/*
	 * Constructor
	 */
	function OOCategory($id=0,$clang="") {

		global $REX;

		$this->_id = $id;
		if ($clang != "" ) $this->_clang = $clang;
		else $this->_clang = $REX[CUR_CLANG];
		include_once($REX[INCLUDE_PATH]."/generated/articles/".$this->_id.".".$this->_clang.".article");

	}

	/*
	 * Class Function:
	 * Returns Value of Category
	 */

	function getValue($value) {

		global $REX;
		return $REX[ART][$this->_id][$value][$this->_clang];

	}

	/*
	 * CLASS Function:
	 * Return an OOCategory object based on an id
	 */
	function getCategoryById($an_id,$clang="") {

		global $REX;

		if ($clang == "") $clang = $this->_clang;

		if (include_once($REX[INCLUDE_PATH]."/generated/articles/$an_id.$clang.article"))
		{
			return new OOCategory($an_id,$clang);
		}else
		{
			return null;
		}

	}

	/*
	 * CLASS Function:
	 * Return a list of categories which names match the
	 * search string. For now the search string can be either
	 * a simple name or a string containing SQL search placeholders
	 * that you would insert into a 'LIKE '%...%' statement.
	 *
	 * Returns an array of OOCategory objects.
	 */
	function searchCategoriesByName($a_name, $ignore_offlines = false) {
		$off = $ignore_offlines ? " and status = 1 " : "" ;
		$catlist = array();
		$sql = new sql;
		$sql->setQuery("select id, name, description, func, re_category_id, prior, path, status from rex_category where name like '$a_name' $off order by name");
		for ($i = 0; $i < $sql->getRows(); $i++) {
			$catlist[] = new OOCategory($sql->getValue("id"),$sql->getValue("name"),
										$sql->getValue("description"), $sql->getValue("func"),
										$sql->getValue("re_category_id"), $sql->getValue("prior"),
										$sql->getValue("path"), $sql->getValue("status"));
			$sql->next();
		}
		return $catlist;
	}

	/*
	 * CLASS Function:
	 * Return a list of top level categories, ie.
	 * categories that have no parent.
	 * Returns an array of OOCategory objects sorted by $prior.
	 *
	 * If $ignore_offlines is set to TRUE,
	 * all categories with status 0 will be
	 * excempt from this list!
	 */
	//function getRootCategories($ignore_offlines = false) {

	function getRootCategories($ignore_offlines = false, $clang = "") {

		global $REX;

		if ($clang == "" ) $clang = $REX[CUR_CLANG];

		include_once($REX[INCLUDE_PATH]."/generated/articles/0.".$clang.".clist");
		$CL = $REX[RE_CAT_ID][0];
		for ($i = 0; $i < count($CL); $i++)
		{

			$temp = new OOCategory(current($CL),$clang);
			if ($temp->isOnline() || !$ignore_offlines) $catlist[] = $temp;
			next($CL);
		}
		return $catlist;
	}

	/*
	 * Object Function:
	 * Return a list of all subcategories.
	 * Returns an array of OOCategory objects sorted by $prior.
	 *
	 * If $ignore_offlines is set to TRUE,
	 * all categories with status 0 will be
	 * excempt from this list!
	 */
	function getChildren($ignore_offlines = false) {

		global $REX;

		include_once($REX[INCLUDE_PATH]."/generated/articles/".$this->_id.".".$this->_clang.".clist");
		$CL = $REX[RE_CAT_ID][$this->_id];
		for ($i = 0; $i < count($CL); $i++)
		{
			$temp = new OOCategory(current($CL),$this->_clang);
			if ($temp->isOnline() || !$ignore_offlines) $catlist[] = $temp;
			next($CL);
		}
		return $catlist;
	}

	/*
	 * Object Function:
	 * Returns the parent category
	 */
	function getParent() {
		$re_id = $REX[ART][$this->_id][re_id][$this->_clang];
		return $re_id > 0 ? OOCategory::getCategoryById($re_id,$this->_clang) : null;
	}

	/*
	 * Object Function:
	 * Returns TRUE if this category is the direct
	 * parent of the other category.
	 */
	function isParent($other_cat) {

		// return $this->_id == $other_cat->_re_category_id;
	}

	/*
	 * Object Function:
	 * Returns TRUE if this category is an ancestor
	 * (parent, grandparent, greatgrandparent, etc)
	 * of the other category.
	 */
	function isAncestor($other_cat) {
		// TODO!
		return false;
	}

	/*
	 * Object Function:
	 * Return a list of articles in this category
	 * Returns an array of OOArticle objects sorted by $prior.
	 *
	 * If $ignore_offlines is set to TRUE,
	 * all articles with status 0 will be
	 * excempt from this list!
	 */
	function getArticles($ignore_offlines = true) {
		// return OOArticle::getArticlesOfCategory($this->_id, $ignore_offlines);
	}

	/*
	 * OBJECT Function:
	 * Returns the number of articles in this Category
	 *
	 * $ignore_offlines = count only Articles that are online
	 * $ignore_startpage = do not count the startpage
	 */
	function countArticles($ignore_offlines = true, $ignore_startpage = true) {
		// TODO
		return 0;
	}

	/*
	 * OBJECT function
	 * Returns a list of articles of this category that have been
	 * newly created.
	 * $number_of_articles = how far to go back in history
	 * $ignore_startpage = ignore the category startpage
	 * $ignore_offlines = ignore any articles that are offline
	 */
	function getNewArticles($number_of_articles = 0, $ignore_startpage = true, $ignore_offlines = true) {
		// return OOArticle::getNewArticles($number_of_articles, $ignore_startpage, $ignore_offlines, $this->_id);
	}


	/*
	 * Object Function:
	 * Return a list of articles that are online
	 * only in a certain time frame.
	 * Returns an array of OOArticle objects sorted by $prior.
	 *
	 * Day format: 01 - 31
	 * Month format: 01 - 12
	 * Year format: e.g. 2004
	 */
	function getArticlesByDate($day_from, $month_from, $year_from, $day_to, $month_to, $year_to) {
		// return OOArticle::getArticlesOfCategoryByDate($this->_id, $day_from, $month_from, $year_from, $day_to, $month_to, $year_to);
	}

	/*
	 * Object Function:
	 * Return the start article for this category
	 */
	function getStartArticle() {
		// return OOArticle::getCategoryStartArticle($this->_id);
	}

	/*
	 * Object Function:
	 * Return a list of Ancestor Categories forming the path
	 * from the topmost to this category. The last element
	 * would then be the direct parent of this category and the
	 * first element would be a root category.
	 * Returns an array of OOCategory objects.
	 */
	function getPathList() {
		// TO BE DONE!! 26.05.04
		return null;
	}

	/*
	 * Accessor Method:
	 * returns the id of the category
	 */
	function getId() {
		return $this->_id;
	}

	/*
	 * Accessor Method:
	 * returns the clang of the category
	 */
	function getClang() {
		return $this->_clang;
	}

	/*
	 * Accessor Method:
	 * set the id of the category
	 */
	function setClang($clang="") {
		global $REX;
		$this->_clang = $clang;
		include_once($REX[INCLUDE_PATH]."/generated/articles/".$this->_id.".".$this->_clang.".article");
	}


	/*
	 * Accessor Method:
	 * returns the name of the category
	 */
	function getName() {
		global $REX;
		return $REX[ART][$this->_id][catname][$this->_clang];
	}

	/*
	 * Accessor Method:
	 * returns true if category is online.
	 */
	function isOnline() {
		global $REX;
		return $REX[ART][$this->_id][status][$this->_clang];
	}

	/*
	 * Accessor Method:
	 * returns the category description.
	 */
	function getDescription() {
		global $REX;
		return $REX[ART][$this->_id][description][$this->_clang];
	}

	/*
	 * Accessor Method:
	 * returns the prioity of the category
	 */
	function getPriority() {
		global $REX;
		return $REX[ART][$this->_id][prior][$this->_clang];
	}

	/*
	 * Object Helper Function:
	 * Returns a String representation of this object
	 * for debugging purposes.
	 */
	function toString() {
		return "Category: ".$this->_id.", ".$this->_name.", ".($this->isOnline() ? "online" : "offline");
	}

	/*
	 * Object Helper Function:
	 * Returns a url for linking to this category
	 */
	function getUrl() {
		return rex_getUrl($this->getId(),$this->getClang());
	}
}
?>