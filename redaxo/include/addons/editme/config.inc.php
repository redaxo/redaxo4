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

  include $REX['INCLUDE_PATH'].'/addons/editme/functions/functions.inc.php';

  $REX['ADDON']['tables']['editme'] = rex_em_getTables();

  $subpages = array();
  if(is_array($REX['ADDON']['tables']['editme']))
  {
    foreach($REX['ADDON']['tables']['editme'] as $table)
    {
      // Recht um das AddOn ueberhaupt einsehen zu koennen
      $table_perm = 'em['.$table["name"].']';
      $REX['EXTPERM'][] = $table_perm;

      // check active-state and permissions
      if($table['status'] == 1 && $table['hidden'] != 1 && 
         $REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($table_perm)))
      {
        // include dashbord-components
        if(rex_request('page', 'string') == 'be_dashboard')
        {
          require_once dirname(__FILE__) .'/classes/class.dashboard.inc.php';
  
          rex_register_extension (
            'DASHBOARD_COMPONENT',
            array(new rex_editme_component($table["name"]), 'registerAsExtension')
          );
        }
        
        // include page
        $be_page = new rex_be_main_page($table['label'], 'editme', array('page'=>'editme', 'subpage' => $table['name']));
        $be_page->setHref('index.php?page=editme&subpage='.$table['name']);
        $subpages[] = $be_page;
      }
    }
  }
  $REX['ADDON']['subpages']['editme'] = $subpages;
  

  function rex_editme_assets($params){
    $params['subject'] .= "\n  ".'<script src="../files/addons/editme/em.js" type="text/javascript"></script>';
    return $params['subject'];
  }
  rex_register_extension('PAGE_HEADER', 'rex_editme_assets');
}