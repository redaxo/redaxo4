<?php

function rex_a656_dashboard_component($feedUrl)
{
  global $I18N;
  
  $feed = new rex_rssReader($feedUrl);
  $encoding = $feed->get_encoding();
  
  $componentTitle = rex_a656_convert($feed->get_title(), $encoding);
  $componentTitle = $I18N->msg('rss_feed') .': ' . $componentTitle;
  $componentBody = rex_a656_rss_teaser($feedUrl);
  
  return rex_a655_component_wrapper($componentTitle, $componentBody);
}


function rex_a656_dashboard_feeds($params)
{
  $feeds = array(
    'http://www.redaxo.de/261-0-news-rss-feed.html'
  );

  $content = $params['subject'];
  foreach($feeds as $feedUrl)
  {
    $feedContent = rex_a656_dashboard_component($feedUrl);
    $content .=  $feedContent;
  }
  
  return $content;
}
