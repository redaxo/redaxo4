<?
include_once $REX[INCLUDE_PATH]."/classes/class.ooarticle.inc.php";

/*
 * The OOCategory class is an object wrapper over the database table rex_category.
 * Together with OOArticle and OOArticleSlice it provides an object oriented
 * Framework for accessing vital parts of your website.
 * This framework can be used in Modules, Templates and PHP-Slices!
 *
 * Carsten Eckelmann <carsten@circle42.com>, May 2004
 */
class OOCategory {
  var $_id;
  var $_name;
  var $_description;
  var $_func;
  var $_re_category_id;
  var $_prior;
  var $_path;
  var $_status; // online=1, offline=0
	
	/*
	 * Constructor
	 */
	function OOCategory($id, $name, $description, $func, $re_category_id, $prior, $path, $status) {
		$this->_id = $id;
		$this->_name = $name; 
		$this->_description = $description; 
		$this->_func = $func;
		$this->_re_category_id = $re_category_id; 
		$this->_prior = $prior;
		$this->_path = $path;
		$this->_status = $status;
	}
	
	/*
	 * CLASS Function:
	 * Return an OOCategory object based on an id
	 */
	function getCategoryById($an_id) {
		$sql = new sql;
		$sql->setQuery("select id, name, description, func, re_category_id, prior, path, status from rex_category where id = $an_id");
		if ($sql->getRows() == 1) {
			return new OOCategory($sql->getValue("id"),$sql->getValue("name"),
										$sql->getValue("description"), $sql->getValue("func"),
										$sql->getValue("re_category_id"), $sql->getValue("prior"),
										$sql->getValue("path"), $sql->getValue("status"));
			
		}
		return null;
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
		$off = $ignore_offlines ? "" : " and status = 1 ";
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
	function getRootCategories($ignore_offlines = false) {
		$off = $ignore_offlines ? "" : " and status = 1 ";
		$catlist = array();
		$sql = new sql;
		$sql->setQuery("select id, name, description, func, re_category_id, prior, path, status from rex_category where re_category_id = 0 $off order by prior");
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
	 * Object Function:
	 * Return a list of all subcategories.
	 * Returns an array of OOCategory objects sorted by $prior.
	 * 
	 * If $ignore_offlines is set to TRUE, 
	 * all categories with status 0 will be
	 * excempt from this list!
	 */
	function getChildren($ignore_offlines = false) {
		$off = $ignore_offlines ? "" : " and status = 1 ";
		$catlist = array();
		$sql = new sql;
		$sql->setQuery("select id, name, description, func, re_category_id, prior, path, status from rex_category where re_category_id = {$this->_id} $off order by prior");
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
	 * Object Function:
	 * Returns the parent category
	 */
	function getParent() {
		return $_re_category_id > 0 ? OOCategory::getCategoryById($this->_re_category_id) : null;
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
	function getArticles($ignore_offlines = false) {
		return OOArticle::getArticlesOfCategory($this->_id, $ignore_offlines);
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
		return OOArticle::getArticlesOfCategoryByDate($this->_id, $day_from, $month_from, $year_from, $day_to, $month_to, $year_to);
	}
	
	/*
	 * Object Function:
	 * Return the start article for this category
	 */
	function getStartArticle() {
		return OOArticle::getCategoryStartArticle($this->_id);
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
	 * returns the name of the category
	 */
	function getName() {
		return $this->_name;
	}
	
	/*
	 * Accessor Method:
	 * returns true if category is online.
	 */
	function isOnline() {
		return $this->_status == 1 ? true : false;
	}
	
	/*
	 * Accessor Method:
	 * returns the category description.
	 */
	function getDescription() {
		return $this->_description;
	}
	
	/*
	 * Accessor Method:
	 * returns the prioity of the category
	 */
	function getPriority() {
		return $this->_prior;
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
	function getUrl($params = null) {
		$start = $this->getStartArticle();
		return $start ? $start->getUrl($params) : "";
	}
}
?>
