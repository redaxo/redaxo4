<?php
include_once $REX[INCLUDE_PATH]."/classes/class.oomediacategory.inc.php";

class OOMedia {
    // id
    var $_id;
    // reid
    var $_re_id;
    // categoryid
    var $_cat_id;
    // categoryname
    var $_cat_name;
    // oomediacategory
    var $_cat;
    
    // filename
    var $_name;
    // originalname
    var $_orgname;
    // filetype
    var $_type;
    // filesize
    var $_size;
    
    // filewidth
    var $_width;
    // fileheight
    var $_height;
    
    // filetitle
    var $_title;
    // filedescription
    var $_description;
    // copyright
    var $_copyright;
    
    // createstamp
    var $_createstamp;
    // creator
    var $_creator;
    
    /**
     * @access protected
     */
    function OOMedia( $id) {
        $query = 'SELECT * FROM '. OOMedia::getTableJoin() .' WHERE file_id = '. $id;
        $sql = new sql();
//        $sql->debugsql = true;
        $result = $sql->get_array( $query);
        if ( count( $result) == 0 ) {
            trigger_error( 'No OOMediaCategory found with id "'. $id .'"', E_USER_ERROR);
        }
        $result = $result[0];
//        var_dump( $result);
        
        $this->_id            = $result['file_id'];
        $this->_re_id         = $result['re_file_id'];
        $this->_cat_id        = $result['category_id'];
        $this->_cat_name      = $result['name'];
        
        $this->_name          = $result['filename'];
        $this->_orgname       = $result['originalname'];
        $this->_type          = $result['filetype'];
        $this->_size          = $result['filesize'];
        
        $this->_width         = $result['width'];
        $this->_height        = $result['height'];

        $this->_title         = $result['title'];
        $this->_description   = $result['description'];
        $this->_copyright     = $result['copyright'];
        
        $this->_createstamp   = $result['stamp'];
        $this->_creator       = $result['user_login'];
    }
    
    /**
     * @access protected
     */
    function getTableName() {
        return 'rex_file';
    }
    
    /**
     * @access protected
     */
    function getTableJoin() {
        $mediatable = OOMedia::getTableName();
        $cattable = OOMediaCategory::getTableName();
        return  $mediatable .' LEFT JOIN '. $cattable .' ON '. $mediatable . '.category_id = '. $cattable .'.id';
    }
    
    /**
     * @access public
     */
    function getMediaById( $id) {
        return new OOMedia( $id);
    }
    
    /**
     * @access public
     */
    function searchMediaByFileName( $name) {
        $query = 'SELECT file_id FROM '. OOMedia::getTableName() .' WHERE filename = "'. addslashes( $name) .'"';
        $sql = new sql();
        $result = $sql->get_array( $query);
        
        $media = array();
        foreach ( $result as $line) {
            $media[] = OOMedia::getMediaById( $line['file_id']);
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
    function getTitle() {
        return $this->_title;
    }
    
    /**
     * @access public
     */
    function getCategory() {
        if ( $this->_cat === null) {
            $this->_cat = & OOMediaCategory::getCategoryById( $this->getCategoryId()); 
        }
        return $this->_cat;
    }
    
    /**
     * @access public
     */
    function getCategoryName() {
        return $this->_cat_name;
    }
    
    /**
     * @access public
     */
    function getCategoryId() {
        return $this->_cat_id;
    }
    
    /**
     * @access public
     */
    function getDescription() {
        return $this->_description;
    }
    
    /**
     * @access public
     */
    function getCopyright() {
        return $this->_copyright;
    }
    
    /**
     * @access public
     */
    function getFileName() {
        return $this->_name;
    }
    
    /**
     * @access public
     */
    function getOrgFileName() {
        return $this->_orgname;
    }
    /**
     * @access public
     */
    function getWidth() {
        return $this->_width;
    }
    
    /**
     * @access public
     */
    function getHeight() {
        return $this->_height;
    }
    
    /**
     * @access public
     */
    function toHTML() {
        global $REX;
        
        $file = $REX['HTDOCS_PATH'] .'files/'. $this->getFileName();
        $filetype = strrchr( $this->getFileName(), '.');
        
        switch( $filetype) {
            case '.jpg'  :
            case '.jpeg' :
            case '.png'  :
            case '.gif'  :
            case '.bmp'  :
                        {
                            return '<img src="'. $file .'" alt="'. htmlentities( $this->getDescription()) .'" width="'. $this->getWidth() .'px" height="'. $this->getHeight() .'px"/>';
                        }
            case '.js'   :
                        {
                            return '<script type="text/javascript" src="'. $file .'"></script>';
                        }
            case '.css'     :
                        {
                            return '<link href="'. $file .'" rel="stylesheet" type="text/css">';
                        }
            default     :   return 'No html-equivalent available for type "'. $filetype .'"';
        }
    }
    
    /**
     * @access public
     */
    function toString() {
        return 'OOMedia, "'. $this->getId() .'", "'. $this->getName() .'"'. "<br/>\n";
    }
}

?>