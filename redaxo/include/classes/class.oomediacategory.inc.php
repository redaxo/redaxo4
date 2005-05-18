<?php

class OOMediaCategory {
    // id
    var $_id;
    // re_id
    var $_parent;
    
    // name
    var $_name;
    // path
    var $_path;
    
    // clang
    var $_clang;
    // hide
    var $_hide;
    
    // createdate
    var $_createdate;
    // updatedate
    var $_updatedate;
    
    // createuser
    var $_createuser;
    // updateuser
    var $_updateuser;
    
    /**
     * @access protected
     */
    function OOMediaCategory( $id) {
        $query = 'SELECT * FROM '. OOMediaCategory::getTableName() .' WHERE id = '. $id;
        
        $sql = new sql();
//        $sql->debugsql = true;
        $result = $sql->get_array( $query);
        $result = $result[0];
        
        if ( count( $result) == 0 ) {
            trigger_error( 'No OOMediaCategory found with id "'. $id .'"', E_USER_ERROR);
        }
//        var_dump( $result);
        
        $this->_id    = $result['id'];
        $this->_parent = $result['re_id'];
        
        $this->_name  = $result['name'];
        $this->_path  = $result['path'];
        
        $this->_clang  = $result['clang'];
        $this->_hide  = $result['hide'];
        
        $this->_createdate  = $result['createdate'];
        $this->_updatedate  = $result['updatedate'];
        
        $this->_createuser  = $result['createuser'];
        $this->_updateuser  = $result['updateuser'];
    }
    
    
    /**
     * @access protected
     */
    function getTableName() {
        global $REX;
        return $REX[TABLE_PREFIX].'file_category';
    }
    
    /**
     * @access public
     */
    function getCategoryById( $id) {
        return new OOMediaCategory( $id);
    }
    
    /**
     * @access public
     */
    function searchCategoryByName( $name) {
        $query = 'SELECT id FROM '. OOMedia::getTableName() .' WHERE name = "'. addslashes( $name) .'"';
        $sql = new sql();
        $result = $sql->get_array( $query);
        
        $media = array();
        foreach ( $result as $line) {
            $media[] = OOMediaCategory::getCategoryById( $line['id']);
        }
        
        return $media;
    }
    
    /**
     * @access public
     */
    function toString() {
        return 'OOMediaCategory, "'. $this->getId() .'", "'. $this->getName() .'"'. "<br/>\n";
    }
    
    /**
     * @access public
     */
    function getId() {
        return $this->_id;
    }
    
    /**
     * @access public
     */
    function getName() {
        return $this->_name;
    }
    
    /**
     * @access public
     */
    function getPath() {
        return $this->_path;
    }
    
    /**
     * @access public
     */
    function getUpdateUser() {
        return $this->_updateuser;
    }
    
    /**
     * @access public
     */
    function getUpdateDate() {
        return $this->_updatedate;
    }
     
    /**
     * @access public
     */
    function getCreateUser() {
        return $this->_createuser;
    }
    
    /**
     * @access public
     */
    function getCreateDate() {
        return $this->_createdate;
    }
    
    /**
     * @access public
     */
    function getParentId() {
        return $this->_parent;
    }
    
    /**
     * @access public
     */
    function getParent() {
        return OOMediaCateogry::getCategoryById( $this->getParentId());
    }
    
    /**
     * @access public
     */
    function isHidden() {
        return $this->_hide;
    }
    
    /**
     * @access public
     */
    function isParent( $mediaCat) {
        if ( is_int( $mediaCat)) {
            return $mediaCatId == $this->getParentId(); 
        } else if ( is_object( $mediaCat) && 
                    is_a( $mediaCat, 'oomediacategory')) {
            return $this->getParentId() == $mediaCat->getId();
        }
        return null;
    }
}
?>