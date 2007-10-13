<?php


/**
 * Image-Resize Addon
 * 
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author <a href="http://www.vscope.at">www.vscope.at</a>
 * 
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * 
 * @package redaxo4
 * @version $Id$
 */

include $REX['INCLUDE_PATH'] . '/layout/top.php';

if (isset ($subpage) and $subpage == 'clear_cache')
{
  $c = thumbnail::deleteCache();
  $msg = 'Cache cleared - ' . $c . ' cachefiles removed';
}

// Build Subnavigation 
$subpages = array (
  array (
    'clear_cache',
    'Resize Cache l&ouml;schen'
  ),
  
);

rex_title('Image Resize', $subpages);

if (isset ($msg) and $msg != '')
  echo '<p class="rex-warning"><span>' . $msg . '</span></p>';
  
?>
<div class="rex-addon-output">
  <h2>Instructions</h2>
  <div class="rex-addon-content">
    <?php include dirname(__FILE__). '/../help.inc.php'; ?>
  </div>
</div>
<?php

include $REX['INCLUDE_PATH'] . '/layout/bottom.php';

?>