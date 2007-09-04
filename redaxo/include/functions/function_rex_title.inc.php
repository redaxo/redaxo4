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
function rex_title($head, $subtitle = '')
{
	global $article_id, $category_id, $page;

  if($subtitle == '')
  {
    $subtitle = '<p>&nbsp;</p>';
  }
  else
  {
	  $subtitle = '<div class="rex-title-row">'.rex_get_subtitle($subtitle).'</div>';
  }

  // ----- EXTENSION POINT
  $head = rex_register_extension_point('PAGE_TITLE', $head, array('category_id' => $category_id, 'article_id' => $article_id, 'page' => $page));

  print '
	<div id="rex-title">
  		<div class="rex-title-row"><h1>'.$head.'</h1></div>
  		'.$subtitle.'
	</div>';

  rex_register_extension_point('PAGE_TITLE_SHOWN', $subtitle, array('category_id' => $category_id, 'article_id' => $article_id, 'page' => $page));

  print '
<!-- *** OUTPUT OF CONTENT - START *** -->
	<div id="rex-output">
	';
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
        if (!$REX_USER->hasPerm('admin[]') && !$REX_USER->hasPerm($subpage[2]))
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
        // $format = '%s';
        // $subtitle[] = sprintf($format, $label);
        $format = '<a href="?page='. $cur_page .'&amp;subpage=%s"%s'. rex_tabindex() .' class="rex-subpage-active">%s</a>';
        $subtitle[] = sprintf($format, $link, $attr, $label);
      }
      elseif ($link == '')
      {
        $format = '<a href="?page='. $cur_page .'"%s'. rex_tabindex() .'>%s</a>';
        $subtitle[] = sprintf($format, $attr, $label);
      }
      else
      {
        $format = '<a href="?page='. $cur_page .'&amp;subpage=%s"%s'. rex_tabindex() .'>%s</a>';
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