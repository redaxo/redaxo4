<?
class OORedaxo {

	/*
	 *  class vars
	 */
	var $clang;

	/*
	 * this vars get read out
	 */
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
	function OORedaxo($params = false,$clang=false) {
		//var_dump($params);
		if($params){
		    foreach(OORedaxo::getClassVars() as $key=>$var){
		        $this->$key = $params[$key];
		    }
		}
	}

	/*
	 * Nothing but a bugfix ;)
	 *
	 */
	function setClang($clang) {
	    $this->clang = $clang;
	}

	/*
	 * Class Function:
	 * Returns Object Value
	 */
	function getValue($value) {

		return $this->$$value;

	}

	/*
	 * CLASS Function:
	 * Returns an Array containing article field names
	 */
	function getClassVars(){
			$class_vars = get_class_vars('OORedaxo');
			foreach($class_vars as $key=>$var){
			    if(substr($key,1)=='_'){
			    	$class_vars[$key] = substr($key,1);
			    }
			}
			return $class_vars;
	}

    /*
    * CLASS Function:
    * Converts Genernated Array to OOBase Format Array
    */
    function convertGeneratedArray($generatedArray,$clang){
		$OORedaxoArray['_id'] = $generatedArray['article_id'][$clang];
		$OORedaxoArray['_clang'] = $clang;
        foreach($generatedArray as $key=>$var){
            $OORedaxoArray['_'.$key]=$var[$clang];
        }
		unset($OORedaxoArray['_article_id']);
        return $OORedaxoArray;
    }

	/*
	 * Accessor Method:
	 * returns the clang of the category
	 */
	function getClang() {
		return $this->_clang;
	}

	/*
	 * Object Helper Function:
	 * Returns a url for linking to this article
	 */
	function getUrl() {
		return rex_getUrl($this->getId(),$this->getClang());
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
	 * returns the Type ID of the article
	 */
	function getTypeId() {
		return $this->_type_id;
	}

	/*
	 * Accessor Method:
	 * returns the article description.
	 */
	function getDescription() {
		return $this->_description;
	}

	/*
	 * Accessor Method:
	 * returns true if article is online.
	 */
	function isOnline() {
		return $this->_status == 1 ? true : false;
	}

	/*
	 * Object Helper Function:
	 * Returns a String representation of this object
	 * for debugging purposes.
	 */
	function toString() {
		return $this->_id.", ".$this->_name.", ".($this->isOnline() ? "online" : "offline");
	}
}
?>
