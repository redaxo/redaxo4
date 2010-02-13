<?php

/**
 * Editme Dashboard-Komponenten
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_editme_component extends rex_dashboard_component
{
  function rex_editme_component($tableName)
  {
    global $I18N;
    
    parent::rex_dashboard_component('editme_'. $tableName);
//    $this->setTitle($I18N->msg('cronjob_dashboard_component_title'));
    $this->setTitle($tableName);
    $this->setTitleUrl('index.php?page=editme&amp;subpage='. $tableName);
    $this->setBlock('editme');
  }
  
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $this->setContent('uio');
  }
}