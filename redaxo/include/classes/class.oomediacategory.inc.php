<?php

class OOMediaCategory {
    // id
    var $_id;
    // re_id
    var $_parent_id;
    
    // name
    var $_name;
    // path
    var $_path;
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
    
    // child categories
    var $_children;
    // files (media)
    var $_files;
    
     /**
     * @access protected
     */
    function OOMediaCategory( $id) {
        $query = 'SELECT * FROM '. OOMediaCategory::_getTableName() .' WHERE id = '. $id;
        
        $sql = new sql();
//        $sql->debugsql = true;
        $result = $sql->get_array( $query);
        $result = $result[0];
        
        if ( count( $result) == 0 ) {
            trigger_error( 'No OOMediaCategory found with id "'. $id .'"', E_USER_ERROR);
        }
        
        $this->_id    = $result['id'];
        $this->_parent_id = $result['re_id'];
        
        $this->_name  = $result['name'];
        $this->_path  = $result['path'];
        $this->_hide  = $result['hide'];
        
        $this->_createdate  = $result['createdate'];
        $this->_updatedate  = $result['updatedate'];
        
        $this->_createuser  = $result['createuser'];
        $this->_updateuser  = $result['updateuser'];
        
        $this->_children = null;
        $this->_files = null;
    }
    
    
    /**
     * @access protected
     */
    function _getTableName() {
        global $REX;
        return $REX['TABLE_PREFIX'].'file_category';
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
    function getRootCategories( $ignore_offlines = true) {
        $qry = 'SELECT id FROM '. OOMediaCategory::_getTableName() . ' WHERE re_id = 0';
        $sql = new sql();
        $sql->setQuery( $qry);
        $result = $sql->get_array();
        
        $rootCats = array();
        foreach ( $result as $row) {
            $id = $row['id'];
            $rootCats[] = OOMediaCategory::getCategoryById($id);
        }
        
        return $rootCats;
    } 
     
    /**
     * @access public
     */
    function searchCategoryByName( $name) {
        $query = 'SELECT id FROM '. OOMedia::_getTableName() .' WHERE name = "'. addslashes( $name) .'"';
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
        return $this->_parent_id;
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
    function getChildren() {
        if( $this->_children === null) {
            $this->_children = array();
            
            $qry = 'SELECT id FROM '. OOMediaCategory::_getTableName() .' WHERE re_id = '. $this->getId();
            
            $sql = new sql();
            $sql->setQuery( $qry);
            $result = $sql->get_array();
            
            if ( is_array( $result)) {
                foreach ( $result as $row ) {
                    $id = $row['id'];
                    $this->_children[] = &OOMediaCateogry::getCategoryById( $id);
                }
            } 
        }
        
        return $this->_children;
    }
    
    /**
     * @access public
     */
    function countChildren() {
        return count( $this->getChildren());
    }
    
    /**
     * @access public
     */
    function getFiles() {
        if( $this->_files === null) {
            $this->_files = array();
            
            $qry = 'SELECT file_id FROM '. OOMedia::_getTableName() .' WHERE category_id = '. $this->getId();
            
            $sql = new sql();
            $sql->setQuery( $qry);
            $result = $sql->get_array();
            
            if ( is_array( $result)) {
                foreach ( $result as $row ) {
                    $id = $row['file_id'];
                    $this->_files[] = &OOMedia::getMediaById( $id);
                }
            } 
        }
        
        return $this->_files;
    }
    
    /**
     * @access public
     */
    function countFiles() {
        return count( $this->getFiles());
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
        } else if ( OOMediaCategory::isValid( $mediaCat)) {
            return $this->getParentId() == $mediaCat->getId();
        }
        return null;
    }
    
    /**
     * @access public
     */
    function isValid( $mediaCat) {
        return is_object( $mediaCat) && is_a( $mediaCat, 'oomediacategory');
    }
    
    /**
     * @access protected
     */
    function _getSQLSetString() {
        $set = ' SET'.
               '  re_id = '. $this->getParentId() .
               ', name = "'. $this->getName() .'"'.
               ', path = "'. $this->getPath() .'"'.
               ', hide = '. $this->isHidden() .
               ', updatedate = '. $this->getUpdateDate() .
               ', createdate = '. $this->getCreateDate() .
               ', updateuser = "'. $this->getUpdateUser() .'"'.
               ', createuser = "'. $this->getCreateUser() .'"';
               
        return $set;
    }
    
    /**
     * @access protected
     * @return Returns <code>true</code> on success or <code>false</code> on error
     */
    function _insert() {
        $qry = 'INSERT INTO '. $this->_getTableName();
        $qry .= $this->_getSQLSetString();
        
        $sql = new sql();
        $sql->debugsql = true;
        $sql->query( $qry);
        
        return $sql->getError();
    }
    
    /**
     * @access protected
     * @return Returns <code>true</code> on success or <code>false</code> on error
     */
    function _update() {
        $qry = 'UPDATE '. $this->_getTableName();
        $qry .= $this->_getSQLSetString();
        $qry .= ' LIMIT 1';
        
        $sql = new sql();
        $sql->debugsql = true;
        $sql->query( $qry);
        
        return $sql->getError();
    }
    
    /**
     * @access protected
     * @return Returns <code>true</code> on success or <code>false</code> on error
     */
    function _save() {
        if ( $this->getId() !== null ) {
            return $this->_update();
        } else {
            return $this->insert();
        }
    }
    
    /**
     * @access protected
     * @return Returns <code>true</code> on success or <code>false</code> on error
     */
    function _delete() {
        $qry = 'DELETE FROM '. $this->_getTableName() . ' WHERE id = '. $this->getId() . ' LIMIT 1';
        $sql = new sql();
        $sql->debugsql = true;
        $sql->query( $qry);
        return $sql->getError();
    }
}
?>