<?php

function rex_read_rss_feed($feedUrl)
{
  $feed = new rex_rssReader($feedUrl);
  $encoding = $feed->get_encoding();
  
  // trans-table damit unabhaengig von feed/backend encoding sonderzeichen richtig dargestellt werden
  $allEntities = get_html_translation_table(HTML_ENTITIES, ENT_NOQUOTES);
  $specialEntities = get_html_translation_table(HTML_SPECIALCHARS, ENT_NOQUOTES);
  $noTags = array_diff($allEntities, $specialEntities);
  
  if($encoding == 'UTF-8')
  {
    //konvertiere trans-table nach utf8
    foreach($noTags as $charkey => $char)
    {
      // jedes zeichen nach utf8 kodieren
      $noTags[utf8_encode($charkey)]= utf8_encode($char);
      // uebrig gebliebenes iso zeichen entfernen
      unset($noTags[$charkey]);
    } 
  }

  $s = '';
  $s .= '<div class="rex-rss-feed">
           <h1 title="'. strtr($feed->get_description(), $noTags) .'">'. $feed->get_title() .'</h1>
           <ul>';
  
  foreach ($feed->get_items(0, 10) as $item) {
    $s .= '
        <li>
            <a href="'. $item->get_permalink() .'">
              <span>'. strtr($item->get_date('d.m.Y H:i'), $noTags) .'</span>
              '. strtr($item->get_title(), $noTags) .
            '</a>
        </li>';
  }
  
  $s .= '</ul>
  </div>';

  unset($feed);
  
  return $s;
}

function rex_dashboard_feeds($params)
{
  $feeds = array(
    'http://www.redaxo.de/261-0-news-rss-feed.html'
  );

  $content = $params['subject'];
  foreach($feeds as $feedUrl)
  {
    $feedContent = rex_read_rss_feed($feedUrl);
    $content .=  $feedContent;
  }
  
  return $content;
}
