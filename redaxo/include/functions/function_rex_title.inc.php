<?php
/**
 * Funktionen zur Ausgabe der Titel Leiste und Subnavigation
 * @package redaxo3
 * @version $Id$
 */ 
 
/**
 * @example
 * $subpages = array(
 *  array( '', 'Index'),
 *  array( 'lang', 'Sprachen'),
 *  array( 'groups', 'Gruppen')
 * );
 * 
 * title( 'Headline', $subpages)
 */
function title($head, $subtitle = '', $styleclass = "grey", $width = '770px')
{
  $subtitle = rex_get_subtitle( $subtitle);
  if ( $subtitle != '')
  {
    $subtitle = '<b style="line-height:18px">'. $subtitle .'</b>';
  }
?>
  <br />
  
  <table style="width: <?php echo $width ?>" cellpadding="0" cellspacing="0">
    
        <tr style="height: 30px">
            <td class="<?php echo $styleclass ?>">&nbsp;&nbsp;<b class="head"><?php echo $head ?></b></td>
            <td rowspan="3" style="width: 153px"><img src="pics/logo.gif" style="width: 153px; height: 61px;"/></td>
        </tr>
        
        <tr style="height: 1px">
            <td></td>
        </tr>
        
        <tr style="height: 30px">
            <td class="<?php echo $styleclass ?>" >
                 <?php echo $subtitle ?>
            </td>
        </tr>
    
  </table>
    
  <br />
<?php
}

/**
 * @example
 * $subpages = array(
 *  array( '', 'Index'),
 *  array( 'lang', 'Sprachen'),
 *  array( 'groups', 'Gruppen')
 * );
 * 
 * small_title( 'Headline', $subpages)
 */
function small_title($title, $subtitle) {
  $subtitle = rex_get_subtitle( $subtitle, ' class="white"');
  $subtitle = $subtitle != '' ? '<b>'. $subtitle .'</b>' : ''; 
?>
  <table border="0" cellpadding="5" cellspacing="1" width="100%">
    <tr>
      <td colspan="3" class="grey" align="right"><?php echo $title ?></td>
    </tr>
    <tr>
      <td class="greenwhite">
        <?php echo $subtitle ?>
      </td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>
  </table>
<?php  
}

/**
 * Helper function
 */
function rex_get_subtitle($subline, $attr = '')
{
  if (empty($subline))
  {
    return  '';
  }
  
  $subtitle = $subline;
  if (is_array($subline))
  {
    $subtitle = '&nbsp;&nbsp;&nbsp;';
    $numPages = count($subline);
    $i = 0;

    foreach ($subline as $subpage)
    {
      if (!is_array($subpage))
      {
        continue;
      }

      $link = $subpage[0];
      $label = $subpage[1];

      // Falls im Link parameter enthalten sind, diese Abschneiden
      if (($pos = strpos($link, '&')) !== false)
      {
        $link = substr($link, 0, $pos);
      }

      $active = (empty ($_REQUEST['subpage']) && $link == '') || (!empty ($_REQUEST['subpage']) && $_REQUEST['subpage'] == $link);

      // Auf der aktiven Seite den Link nicht anzeigen            
      if ($active)
      {
        $format = '%s';
        $subtitle .= sprintf($format, $label);
      }
      else
        if ($link == '')
        {
          $format = '<a href="?page='.$_REQUEST['page'].'"%s>%s</a>';
          $subtitle .= sprintf($format, $attr, $label);
        }
        else
        {
          $format = '<a href="?page='.$_REQUEST['page'].'&amp;subpage=%s"%s>%s</a>';
          $subtitle .= sprintf($format, $link, $attr, $label);
        }

      if ($i != ($numPages -1))
      {
        $subtitle .= ' | ';
      }

      $i ++;
    }
  }
  // \n aus Quellcode formatierungsgründen
  return $subtitle."\n" ;
}
?>