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

  $REX['ADDON']['name']["editme"] = $I18N->msg("editme");
  $REX['ADDON']['perm']["editme"] = 'em[]';

  // Credits
  $REX['ADDON']['version']["editme"] = '0.9';
  $REX['ADDON']['author']["editme"] = 'Jan Kristinus';
  $REX['ADDON']['supportpage']["editme"] = 'forum.redaxo.de';

  // Fuer Benutzervewaltung
  $REX['PERM'][] = 'em[]';

  // Linke Navigation

  include $REX['INCLUDE_PATH'].'/addons/editme/functions/functions.inc.php';

  $REX['ADDON']['editme']['subpages'] = array();
 	$REX['ADDON']['navigation']['editme'] = array('active_when'=>array('page'=>'editme','subpage'=>''));

 	/*
 	 if ($REX['USER'] && ($REX['USER']->isAdmin()))
 	 $REX['ADDON']['editme']['subpages'][] = array( '' , $I18N->msg("em_overview"));
 	 */

  $REX['ADDON']['editme']['tables'] = rex_em_getTables();

  if(count($REX['ADDON']['editme']['tables']))
  {
    foreach($REX['ADDON']['editme']['tables'] as $table)
    {
      // Recht um das AddOn ueberhaupt einsehen zu koennen
      $table_perm = 'em['.$table["name"].']';
      $REX['EXTPERM'][] = $table_perm;

      // include dashbord-components
      if($REX["USER"] && rex_request('page', 'string') == 'be_dashboard' && $table["hidden"] != 1)
      {
        require_once dirname(__FILE__) .'/classes/class.dashboard.inc.php';

        rex_register_extension (
              'DASHBOARD_COMPONENT',
        array(new rex_editme_component($table["name"]), 'registerAsExtension')
        );
      }
      
    }
     

    function rex_editme_navigation($params)
    {
      global $REX;
      foreach($REX['ADDON']['editme']['tables'] as $table)
      {
        $table_perm = 'em['.$table["name"].']';
        if($table["status"] == 1 && $table["hidden"] != 1 && $REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($table_perm)) )
        {
       	  $item = array();
       	  $item['title'] = $table['label'];
       	  $item['href'] = 'index.php?page=editme&subpage='.$table['name'];
       	  $item['active_when'] = array('page'=>'editme', 'subpage' => $table['name']);
       	  $params['subject']->addElement('editme', $item);
        }
      }
      return $params['subject'];
    }
    rex_register_extension('NAVI_PREPARED', 'rex_editme_navigation');

  }

  function rex_editme_assets($params){
    $params['subject'] .= "\n  ".'<script src="../files/addons/editme/em.js" type="text/javascript"></script>';
    return $params['subject'];
  }
  rex_register_extension('PAGE_HEADER', 'rex_editme_assets');
   
}