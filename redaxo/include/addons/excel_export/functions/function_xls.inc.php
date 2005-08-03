<?php

function doExport( $table, $mode)
{
    global $REX, $REX_USER, $XLS_TABLES, $XLS_MODES;
    
    $Basedir = dirname( __FILE__);
    require_once $Basedir. '/../libs/Spreadsheet_Excel_Writer-0.8/Writer.php';
    
    if ( array_key_exists( $table, $XLS_TABLES) && array_key_exists( $mode, $XLS_MODES)) 
    {
        $createstamp = time();
        $oTable = new ExportTable( $table);
        
        if( $mode == 'all') {
            $result = $oTable->getAll();
        } 
        else if( $mode == 'new') 
        {
            $result = $oTable->getNew();
        }
        
        if ( mysql_error() == '') 
        {
            rs2xls( xlsLocation( $table, $createstamp), xlsFilename( $tables[ $table], $createstamp), $result);
            
            $values = array(
               0,
               $table,
               mysql_num_rows( $result),
               $REX_USER->getValue('login'),
               $createstamp
            );
            
            $set = '';
            $first = true;
            foreach ( $values as $value) 
            {
                if ( $first) 
                {
                    $first = false;
                }
                else
                {
                    $set .= ', ';
                }
                if ( is_string( $value)) 
                {
                   $set .= '"'. mysql_escape_string( $value).'"';
                }
                else 
                {
                   $set .= $value;
                }
            }
            
            $sql = new CompatSql();
            $qry = 'INSERT INTO '. TBL_EXCEL_EXPORT .' VALUES ('. $set .')';
            $sql->setQuery( $qry);
        } else {
            echo 'Fehler beim ausführen der Abfrage!';
        }
    }
}

/**
 * Exportiert eine MySQL Tabelle ins Excel format und sendet die Datei zum Browser
 * @param string $location Speicherort auf dem Server
 * @param string $filename Dateiname mit dem die Datei zum Download angeboten wird
 * @param mysql_result $mysql_result Resultobject von mysql_query
 */
function rs2xls( $location, $filename, $mysql_result) 
{
    $filename = str_replace( ' ', '_', $filename);
    $filename = str_replace( 'ä', 'ae', $filename);
    $filename = str_replace( 'ö', 'oe', $filename);
    $filename = str_replace( 'ü', 'ue', $filename);
    $filename = str_replace( 'Ä', 'Ae', $filename);
    $filename = str_replace( 'Ö', 'Oe', $filename);
    $filename = str_replace( 'Ü', 'Ue', $filename);
    
    $xls =& new Spreadsheet_Excel_Writer( $location);
    
    // Send HTTP headers to tell the browser what's coming
    // handleError( $xls->send( $filename)); 

    // Arbeitsblatt hinzufügen
    handleError( $sheet =& $xls->addWorksheet( 'Tabelle1'));

    $printHeaders = true;
    $line = 0;
    while(( $row = mysql_fetch_row( $mysql_result) )!== false) {
        if ( $printHeaders) {
            for ( $i = 0; $i < count( $row); $i++) {
                $column = ucwords( mysql_field_name( $mysql_result, $i));
                handleError( $sheet->write( $line, $i, $column));
            }
            $line++;
            $printHeaders = false;
        }
        
        for ( $i = 0; $i < count( $row); $i++) {
            handleError( $sheet->write( $line, $i, $row[ $i]));
        }
        $line++;
    }
    
    handleError( $xls->close());
    
    return $filename;
}

function xlsDate( $timestamp = null) {
    if ( $timestamp == null) {
        $timestamp = time();
    }
    return date( 'Ymd_Hi', $timestamp);
}

function xlsFilename( $table, $timestamp = null) {
    return xlsDate( $timestamp) .'.xls';
}

function xlsLocation( $table, $timestamp = null) {
    global $REX;
    
    $location = dirname(__FILE__). '/../files/'. $table .'/';
    
    if ( !is_dir( $location)) {
        mkdir( $location);
    }
    
    return $location . xlsFilename( $table, $timestamp);
    
}

function handleError( $obj) {
    if (PEAR::isError($obj)) {
        exit($obj->getMessage());
    } 
}
?>