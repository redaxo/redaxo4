<?php

class OOMediaCategory {
    // id
    var $_id;
    // re_id
    var $re_id;
    // name
    var $_name;
    // path
    var $_path;
    
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
        $this->_re_id = $result['re_id'];
        $this->_name  = $result['name'];
        $this->_path  = $result['path'];
    }
    
    
    /**
     * @access protected
     */
    function getTableName() {
        return 'rex_file_category';
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
    function toString() {
        return 'OOMediaCategory, "'. $this->getId() .'", "'. $this->getName() .'"'. "<br/>\n";
    }
}
?>