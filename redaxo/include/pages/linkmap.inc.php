<?php


// ------------------------ Class Definitions

class rex_map
{
  var $title;
  var $current_node_id;
  var $ignore_offlines;

  var $link_params;

  function rex_map($title, $current_node_id, $ignore_offlines = false)
  {
    $this->title = $title;
    $this->current_node_id = $current_node_id;
    $this->ignore_offlines = $ignore_offlines;
  }

  function addLinkParam($name, $value)
  {
    $this->link_params[$name] = $value;
  }

  function getLinkParams()
  {
    return $this->link_params;
  }

  function getCurrentNodeId()
  {
    return $this->current_node_id;
  }

  function getChildNodes($node)
  {
    return array ();
  }

  function getNodeLink($node)
  {
    return '';
  }

  function getNodeValue($node)
  {
    return $node->getName();
  }

  function formatHref($node, $attr = false)
  {
    $_attr = '';
    $attr = is_array($attr) ? $attr : array ();

    if ($node->getId() == $this->getCurrentNodeId())
    {
      $attr['id'] = 'map-current';
    }

    if (count($attr) > 0)
    {
      foreach ($attr as $name => $value)
      {
        $_attr = ' '.$name.'="'.$value.'"';
      }
    }

    $s = '';
    $s .= '<a href="'.$this->getNodeLink($node).'"'.$_attr.'>';
    $s .= $this->getNodeValue($node);
    $s .= '</a>';

    return $s;
  }

  function formatNode($node)
  {
    global $REX_USER;

    if ($node == null)
    {
      return '';
    }

    $s = '';
    $linkClass = $node->isOnline() ? 'online' : 'offline';

    $liClass = '';
    if ($node->isStartPage() == 1)
    {
      $liClass = ' class="map-startpage"';
    }

    $liId = '';
    if ($node->getId() == $this->getCurrentNodeId())
    {
      $liId = ' id="map-active"';
    }
    $s .= '<li'.$liClass.$liId.'>';

    $s .= $this->formatHref($node, array (
      'class' => $linkClass
    ));

    // Im Advanced Mode, ID anzeigen
    if ($REX_USER->isValueOf('rights', 'advancedMode[]'))
    {
      $s .= ' ['.$node->getId().']';
    }

    $childs = $this->getChildNodes($node);
    $s .= $this->formatNodes($childs);

    $s .= '</li>';

    return $s;
  }

  function formatNodes($nodes)
  {
    $s = '';

    if (is_array($nodes) && count($nodes) > 0)
    {
      $s .= '<ul>';
      foreach ($nodes as $node)
      {
        $s .= $this->formatNode($node);
      }
      $s .= '</ul>'."\n";
    }

    return $s;
  }

  function get($nodes)
  {
    $s = '';

    if (!empty ($this->title))
    {
      $s .= '<h1>'.$this->title.'</h1>';
    }

    $s .= $this->formatNodes($nodes);

    return $s;
  }

  function show()
  {
    echo $this->get();
  }
}

class rex_category_map extends rex_map
{
  function rex_category_map($title, $category_id, $ignore_offlines = false)
  {
    $this->rex_map($title, $category_id, $ignore_offlines);
  }

  function getChildNodes($node)
  {
    return $node->getChildren($this->ignore_offlines);
  }

  function getNodeLink($node)
  {
    $url = '';
    $params = $this->getLinkParams();
    if (is_array($params) && count($params) > 0)
    {
      foreach ($params as $name => $value)
      {
        $url .= '&'.$name.'='.$value;
      }
    }

    return htmlspecialchars('index.php?page=linkmap&category_id='.$node->getId().$url);
  }

  function get()
  {
    $s = '';

    $root_categories = OOCategory :: getRootCategories($this->ignore_offlines);
    $s .= parent :: get($root_categories);

    return $s;
  }
}

class rex_article_map extends rex_map
{
  function rex_article_map($title, $category_id, $ignore_offlines = false)
  {
    $this->rex_map($title, $category_id, $ignore_offlines);
  }

  function getNodeLink($node)
  {
    return rex_linkmap_link($node->getId(), $node->getName());
  }

  function getArticles()
  {
    $articles = array ();
    $category_id = $this->getCurrentNodeId();

    if (empty ($category_id))
    {
      $articles = OOArticle :: getRootArticles($this->ignore_offlines);
    }
    else
    {
      $articles = OOArticle :: getArticlesOfCategory($category_id, $this->ignore_offlines);
    }

    return $articles;
  }

  function get()
  {
    return parent :: get($this->getArticles());
  }
}

/**
 * Klasse zur Darstellung eines Suchergebnisses
 */
class rex_article_search_map extends rex_article_map
{
  var $qry;

  function rex_article_search_map($title, $qry, $ignore_offlines = false)
  {
    $this->qry = $qry;
    // Kategorie ID unwichtig => Dummy Value 
    $this->rex_article_map($title, 0, $ignore_offlines = false);
  }

  function getArticles()
  {
    $sql = new sql();
    $sql->setQuery($this->qry);

    $articles = array ();
    for ($i = 0; $i < $sql->getRows(); $i++)
    {
      $articles[] = OOArticle :: getArticleById($sql->getValue('id'));
      $sql->next();
    }

    usort($articles, array('rex_article_search_map','rex_sortArticleByName'));

    return $articles;
  }
  
  // Funktion zur Sortierung 
  function rex_sortArticleByName($articleA, $articleB)
  {
    $nameA = $articleA->getName();
    $nameB = $articleB->getName();

    if ($nameA == $nameB)
    {
      return 0;
    }

    $arr = array (
      $nameA,
      $nameB
    );

    sort($arr, SORT_STRING);

    return $arr[0] == $nameA ? -1 : 1;
  }
}

// ------------------------ Functions

function rex_linkmap_link($id, $name)
{
  return 'javascript:insertLink(\'redaxo://'.$id.'\',\''.$name.'\')';
}
// ------------------------ Ouput

rex_small_title($REX['SERVERNAME'], 'Linkmap');

// ------- Default Values

$func_body = '';
if (!isset ($HTMLArea))
  $HTMLArea = '';
if (!isset ($opener_input_field))
  $opener_input_field = '';
if (!isset ($opener_input_field_name))
  $opener_input_field_name = '';
if (!isset ($category_id))
  $category_id = 0;

$search = empty ($search) ? '' : $search;
$search = empty ($search_close) ? $search : '';

// ------- Build JS Functions

if ($HTMLArea != '')
{
  if ($HTMLArea == 'TINY')
  {
    $func_body = 'window.opener.tinyMCE.insertLink(link);';
  }
  else
  {
    $func_body = 'window.opener.'.$HTMLArea.'.surroundHTML("<a href="+link+">","</a>");';
  }
}

if ($opener_input_field_name != '')
{
  $link_id_field = $opener_input_field_name;
  $link_name_field = $opener_input_field_name.'_NAME';
}
elseif ($opener_input_field != '')
{
  $link_id_field = 'LINK['. $opener_input_field .']';
  $link_name_field = 'LINK_NAME['. $opener_input_field .']';
}

$func_body .= 'var linkid = link.replace("redaxo://","");
               var needle = new opener.getObj("'. $link_id_field .'");
               needle.obj.value = linkid;
               var needle = new opener.getObj("'. $link_name_field .'");
               needle.obj.value = name;';
              
// ------------------------ Print JS Functions
?>
<script language="JavaScript" type="text/javascript">
  function insertLink(link,name){
    <?php echo $func_body. "\n" ?>
    self.close();
  }
</script>
<?php


// ------------------------ Print CSS
?>

<div class="searchbar">
  <?php


if (count($REX['CLANG']) > 1)
{
  echo '<ul>
          <li>Sprachen:
            <ul>';
  foreach ($REX['CLANG'] as $clang_id => $clang_name)
  {
    $active = $clang_id == $REX['CUR_CLANG'] ? ' class="aktiv"' : '';
    $url = 'index.php?page='.$page.'&amp;clang='.$clang_id.'&amp;search='.$search.'&amp;HTMLArea='.$HTMLArea.'&amp;opener_input_field='.$opener_input_field.'&amp;opener_input_field_name='.$opener_input_field_name;
    echo '<li><a href="'.$url.'"'.$active.'>'.$clang_name.'</a></li>';
  }
  echo '    </ul>
          </li>
        </ul>';
}
?>
  <form action="index.php" method="post">
  <fieldset>
    <input type="hidden" name="page" value="<?php echo $page ?>" />
    <input type="hidden" name="clang" value="<?php echo $clang ?>" />
    <input type="hidden" name="HTMLArea" value="<?php echo $HTMLArea ?>" />
    <input type="hidden" name="opener_input_field" value="<?php echo $opener_input_field ?>" />
    <input type="hidden" name="opener_input_field_name" value="<?php echo $opener_input_field_name?>" />
    <input type="text" name="search" value="<?php echo $search ?>" />
    <input type="submit" name="search_button" value="Suchen" />
<?php

if ($search != '')
{
  echo '<input type="submit" name="search_close" value="Suche Aufheben" />';
}
?>
	</fieldset>
  </form>
</div>
<?php


$map_categories = '';
$map_articles = '';

// ------------------------ Category Tree

$cat_map = new rex_category_map('Kategorien', $category_id);
$cat_map->addLinkParam('HTMLArea', $HTMLArea);
$cat_map->addLinkParam('opener_input_field', $opener_input_field);
$cat_map->addLinkParam('opener_input_field_name', $opener_input_field_name);
$map_categories = $cat_map->get();

if ($search != '')
{
  // ------------------------ Article Search Tree

  $qry = 'SELECT id FROM rex_article WHERE (name LIKE "%'.$search.'%" or keywords LIKE "%'.$search.'%" or description LIKE "%'.$search.'%" ) AND clang='.$REX['CUR_CLANG'].' LIMIT 40';
  $art_map = new rex_article_search_map('Gefundene Artikel:', $qry);
  $map_articles = $art_map->get();
}
else
{
  // ------------------------ Article Tree

  $art_map = new rex_article_map('Artikel', $category_id);
  $map_articles = $art_map->get();
}
?>
<div id="rex-linkmap">
  <div class="rex-map-categories">
    <?php echo $map_categories ?>
  </div>
  <div class="rex-map-articles">
    <?php echo $map_articles ?>
  </div>
  <div class="rex-clearer"> </div>
</div>