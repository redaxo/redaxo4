<?php

/**
 * Editme
 *
 * @author jan@kristinus.de
 *
 * @package redaxo4
 * @version svn:$Id$
 */

if($REX["REDAXO"] && !$REX['SETUP'])
{

  // Sprachdateien anhaengen
  $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/editme/lang/');

  // $REX['ADDON']['rxid']["editme"] = '';
  // $REX['ADDON']['page']["editme"] = "editme";
  $REX['ADDON']['name']["editme"] = $I18N->msg("editme");
  $REX['ADDON']['perm']["editme"] = 'em[]';

  // Credits
  $REX['ADDON']['version']["editme"] = '0.9';
  $REX['ADDON']['author']["editme"] = 'Jan Kristinus';
  $REX['ADDON']['supportpage']["editme"] = 'forum.redaxo.de';

  // *************
  // $REX['PERM'][] = 'em[1]';
  // $REX['PERM'][] = 'em[2]';

  // Fuer Benutzervewaltung
  $REX['PERM'][] = 'em[]';

  // Linke Navigation

  include $REX['INCLUDE_PATH'].'/addons/editme/functions/functions.inc.php';

  $REX['ADDON']['editme']['subpages'] = array();

  if ($REX['USER'] && ($REX['USER']->isAdmin()))
  $REX['ADDON']['editme']['subpages'][] = array( '' , $I18N->msg("em_overview"));

  if($tables = rex_em_getTables())
  {
    foreach($tables as $table)
    {
      // Recht um das AddOn ueberhaupt einsehen zu koennen
      $table_perm = 'em['.$table["label"].']';
      $REX['EXTPERM'][] = $table_perm;

      if($table["status"] == 1)
      {
        if ($REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($table_perm)) )
        {
          $REX['ADDON']['editme']['subpages'][] = array( $table["label"] , $table["name"]);
          
          // include dashbord-components
          if(rex_request('page', 'string') == 'be_dashboard')
          {
            require_once dirname(__FILE__) .'/classes/class.dashboard.inc.php';
            
            rex_register_extension (
              'DASHBOARD_COMPONENT',
              array(new rex_editme_component($table["label"]), 'registerAsExtension')
            );
          }  
        }
      }
    }
    
    function rex_editme_navigation($params)
    {
      if($tables = rex_em_getTables())
      {
        foreach($tables as $table)
        {
          $item = array();
          $item['title'] = $table['name'];
          $item['href'] = 'index.php?page=editme&subpage='.$table['label'];
          $params['subject']->addElement('editme', $item);
        }
      }
      
      return $params['subject'];
    }

    rex_register_extension('NAVI_PREPARED', 'rex_editme_navigation');
  }

  function rex_editme_assets($params){
    // $params["subject"] .= "\n".'  <link rel="stylesheet" type="text/css" href="../files/addons/editme/em.css" media="screen, projection, print" />';
    $params['subject'] .= "\n  ".'<script src="../files/addons/editme/em.js" type="text/javascript"></script>';
    return $params['subject'];
  }
   
  rex_register_extension('PAGE_HEADER', 'rex_editme_assets');
}