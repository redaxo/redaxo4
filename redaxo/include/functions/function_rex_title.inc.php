<?php
/**
 * Funktionen zur Ausgabe der Titel Leiste und Subnavigation
 * @package redaxo3
 * @version $Id$
 */ 
 
/**
 * Ausgabe des Seitentitels
 * 
 * Beispiel für einen Seitentitel
 *
 * <code>  
 * $subpages = array(
 *  array( '', 'Index'),
 *  array( 'lang', 'Sprachen'),
 *  array( 'groups', 'Gruppen')
 * );
 * 
 * title( 'Headline', $subpages)
 * </code>
 * 
 * Beispiel für einen Seitentitel mit Rechteprüfung
 *
 * <code>  
 * $subpages = array(
 *  array( '', 'Index', 'index_perm'),
 *  array( 'lang', 'lang_perm'),
 *  array( 'groups', 'group_perm')
 * );
 * 
 * title( 'Headline', $subpages)
 * </code>
 */
function rex_title($head, $subtitle = '', $styleclass = "grey", $width = '770px')
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
 * Ausgabe des Seitentitels für PopUps
 * 
 * Beispiel für einen Seitentitel
 *
 * <code>  
 * $subpages = array(
 *  array( '', 'Index'),
 *  array( 'lang', 'Sprachen'),
 *  array( 'groups', 'Gruppen')
 * );
 * 
 * small_title( 'Headline', $subpages)
 * </code>
 * 
 * Beispiel für einen Seitentitel mit Rechteprüfung
 *
 * <code>  
 * $subpages = array(
 *  array( '', 'Index', 'index_perm'),
 *  array( 'lang', 'lang_perm'),
 *  array( 'groups', 'group_perm')
 * );
 * 
 * small_title( 'Headline', $subpages)
 * </code>
 */
function rex_small_title($title, $subtitle) {
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
 * Helper function, die den Subtitle generiert
 */
function rex_get_subtitle($subline, $attr = '')
{
  global $REX_USER;
  
  if (empty($subline))
  {
    return  '';
  }
  
  $subtitle = $subline;
  $cur_subpage = empty($_REQUEST['subpage']) ? '' : $_REQUEST['subpage'];
  $cur_page    = empty($_REQUEST['page']) ? '' : $_REQUEST['page'];
  
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
      // Berechtigung prüfen
      if (!empty( $subpage[2]))
      {
        // Hat der User das Recht für die aktuelle Subpage?
        if (!$REX_USER->isValueOf('rights', $subpage[2]))
        {
          // Wenn der User kein Recht hat, und diese Seite öffnen will -> Fehler
          if ($cur_subpage == $link)
          {
            exit ('You have no permission to this area!');
          }
          // Den Punkt aus der Navi entfernen
          else
          {
            continue;
          }
        }
      }

      // Falls im Link parameter enthalten sind, diese Abschneiden
      if (($pos = strpos($link, '&')) !== false)
      {
        $link = substr($link, 0, $pos);
      }

      $active = (empty ($cur_subpage) && $link == '') || (!empty ($cur_subpage) && $cur_subpage == $link);

      // Auf der aktiven Seite den Link nicht anzeigen            
      if ($active)
      {
        $format = '%s';
        $subtitle .= sprintf($format, $label);
      }
      else
        if ($link == '')
        {
          $format = '<a href="?page='. $cur_page .'"%s>%s</a>';
          $subtitle .= sprintf($format, $attr, $label);
        }
        else
        {
          $format = '<a href="?page='. $cur_page .'&amp;subpage=%s"%s>%s</a>';
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