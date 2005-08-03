<?php

class ExportTable {
    /* Name der Tabelle */
    var $name;
    
    var $sql;
    
    var $numAll;
    var $numOld;
    var $numNew;
    
    var $dataAll;
    var $dataNew;
    
    var $lastExport;
    
    function ExportTable( $name) {
        $this->name = $name;
        
        $this->sql = new CompatSql();
//        $this->sql->debugsql = true;
        
        $this->numAll = null; 
        $this->numOld = null;
        $this->numNew = null; 
        
        $this->dataAll = null; 
        $this->dataNew = null;
         
        $this->lastExport = null; 
    }  
    
    function numAll() {
       if ( $this->numAll === null) {
           $qry = 'SELECT COUNT(*) as COUNT FROM '. $this->name;
           $this->sql->setQuery( $qry);
           $result = $this->sql->get_array();
           $this->numAll = $result[0]['COUNT'];
       }
       return $this->numAll;
    }
    
    function numOld() {
        if ( $this->numOld === null) {
           $qry = 'SELECT rows FROM '. TBL_EXCEL_EXPORT .' WHERE tbl_name = "'. $this->name .'" ORDER BY id DESC';
           $this->sql->setQuery( $qry);
           $result = $this->sql->get_array();
           $this->numOld = $result[0]['rows'];
        }
        
        return $this->numOld;
    }
    
    function numNew() {
        if ( $this->numNew === null) {
            $this->numNew = $this->numAll() - $this->numOld();
        }
        
        return $this->numNew;
    }
    
    function lastExport( $default = '-') {
        if ( $this->lastExport === null) {
           $qry = 'SELECT exportdate FROM '. TBL_EXCEL_EXPORT .' WHERE tbl_name="'. $this->name .'" ORDER BY id DESC';
           $this->sql->setQuery( $qry);
           $result = $this->sql->get_array();
           $exportdate = $result[0]['exportdate'];
           
           if ( $exportdate != 0) {
              $format = '<a href="%s">%s</a>';
              $lastexport = dirname( $_SERVER['PHP_SELF']) .'/include/addons/excel_export/files/'. $this->name .'/'. xlsFilename( $this->name, $exportdate);
              $this->lastExport = sprintf( $format, $lastexport, date( 'd.m.Y - H:i', $exportdate));;
           } else {
              $this->lastExport = $default;
           }
        }
        
        return $this->lastExport;
    }
    
    function _getResult( $qry) {
        $this->sql->setQuery( $qry);
        return $this->sql->result;
    }
    
    function getNew() {
//        echo 'SELECT * FROM '. $this->name .' LIMIT '. $this->numNew() .', 9999999999';
//        return null;
        if ( $this->dataNew === null) {
            $this->dataNew =& $this->_getResult( 'SELECT * FROM '. $this->name .' LIMIT '. $this->numNew() .', 9999999999');
        }
        
        return $this->dataNew;
    }
    
    function getAll() {
        if ( $this->dataAll === null) {
            $this->dataAll =& $this->_getResult( 'SELECT * FROM '. $this->name);
        }
        
        return $this->dataAll;
    }
}

?>