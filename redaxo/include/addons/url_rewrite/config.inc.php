<?php

/**
 * URL-Rewrite Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version svn:$Id$
 */
 
if ($REX['MOD_REWRITE'] !== false)
{
  $UrlRewriteBasedir = dirname(__FILE__);
  require_once $UrlRewriteBasedir.'/classes/class.urlrewriter.inc.php';
  
    
  // --------- configuration
  
  // Modify this line to include the right rewriter
  require_once $UrlRewriteBasedir.'/classes/class.rewrite_fullnames.inc.php';
  
  // --------- end of configuration


  $rewriter = new myUrlRewriter();
  $rewriter->prepare();
  
  rex_register_extension('URL_REWRITE', array ($rewriter, 'rewrite'));
}
