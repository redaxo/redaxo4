<?php

class rex_a62_tableManager
{
  var $tableName;
  
  function rex_a62_tableManager($tableName)
  {
    $this->tableName = $tableName;
  }
  
  function getTableName()
  {
    return $this->tableName;
  }
  
  function addColumn($name, $type, $length, $default = null, $nullable = true)
  {
    $qry = 'ALTER TABLE `'. $this->getTableName() .'` ADD '; 
    $qry .= '`'. $name .'` '. $type;
    
    if($length != 0)
       $qry .= '('. $length .')'; 
    
    if($default !== null)
      $qry .= ' DEFAULT \''. str_replace("'", "\'", $default) .'\'';

    if($nullable !== true)
      $qry .= ' NOT NULL';
      
    return $this->setQuery($qry);
  }
  
  function editColumn($oldname, $name, $type, $length, $default = null, $nullable = true)
  {
    $qry = 'ALTER TABLE `'. $this->getTableName() .'` CHANGE '; 
    $qry .= '`'. $oldname .'` `'. $name .'` '. $type; 
    
    if($length != 0)
       $qry .= '('. $length .')'; 

    if($default !== null)
      $qry .= ' DEFAULT \''. str_replace("'", "\'", $default) .'\'';

    if($nullable !== true)
      $qry .= ' NOT NULL';
      
    return $this->setQuery($qry);
  }
  
  function deleteColumn($name)
  {
    $qry = 'ALTER TABLE `'. $this->getTableName() .'` DROP '; 
    $qry .= '`'. $name .'`';
     
    return $this->setQuery($qry);
  }
  
  function setQuery($qry)
  {
    $sql = rex_sql::getInstance();
    return $sql->setQuery($qry);
  }
}
?>