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
 * rex_title( 'Headline', $subpages)
 * </code>
 * 
 * Beispiel für einen Seitentitel mit Rechteprüfung
 *
 * <code>  
 * $subpages = array(
 *  array( '', 'Index', 'index_perm'),
 *  array( 'lang', 'Sprachen', 'lang_perm'),
 *  array( 'groups', 'Gruppen', 'group_perm')
 * );
 * 
 * rex_title( 'Headline', $subpages)
 * </code>
 */
function rex_title($head, $subtitle = '', $styleclass = "grey", $width = '770px')
{
  $subtitle = rex_get_subtitle( $subtitle);
  
  print '  
	<div id="rex-title">
  		<h1>'.$head.'</h1>
  		'.$subtitle.'
	</div>
	
<!-- *** OUTPUT OF CONTENT - START *** -->
	<div id="rex-output">
	';
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
 * rex_small_title( 'Headline', $subpages)
 * </code>
 * 
 * Beispiel für einen Seitentitel mit Rechteprüfung
 *
 * <code>  
 * $subpages = array(
 *  array( '', 'Index', 'index_perm'),
 *  array( 'lang', 'Sprachen verwalten', 'lang_perm'),
 *  array( 'groups', 'Gruppen verwalten', 'group_perm')
 * );
 * 
 * rex_small_title( 'Headline', $subpages)
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
  
  $subtitle_str = $subline;
  $subtitle = $subline;
  $cur_subpage = empty($_REQUEST['subpage']) ? '' : $_REQUEST['subpage'];
  $cur_page    = empty($_REQUEST['page']) ? '' : $_REQUEST['page'];
  
  if (is_array($subline) && count( $subline) > 0)
  {
    $subtitle = array();
    $numPages = count($subline);

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
        $subtitle[] = sprintf($format, $label);
      }
      elseif ($link == '')
      {
        $format = '<a href="?page='. $cur_page .'"%s>%s</a>';
        $subtitle[] = sprintf($format, $attr, $label);
      }
      else
      {
        $format = '<a href="?page='. $cur_page .'&amp;subpage=%s"%s>%s</a>';
        $subtitle[] = sprintf($format, $link, $attr, $label);
      }
    }
    
    
    if(!empty($subtitle))
    {
      $items = '';
      $num_parts = count($subtitle);
      $i = 1;
      foreach($subtitle as $part)
      {
        if($i != $num_parts)
        {
          $part .= ' | ';
        }
        $items .= '<li>'. $part .'</li>
        ';
        $i++;
      }
      $subtitle_str = '
      <ul>
        '. $items .'        
      </ul>
      ';
    }
  }
  // \n aus Quellcode formatierungsgründen
  return $subtitle_str;
}
?>