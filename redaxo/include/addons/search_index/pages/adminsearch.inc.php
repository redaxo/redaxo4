<?php

/**
 * 
 * @package redaxo3
 * @version $Id$
 */
 
?>

<table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>
  <tr>
    <th>Redaxo Admin Suche</th>
  </tr>
  <tr>
    <td>
      <form name="rex_suche" action="index.php" method="GET">
        <input type="hidden" name="page" value="search_index">
        Suchen
        <input type="field" name="rexsearch" value="<?php echo $rexsearch ?>" style="width:190px">
      </form>
    </td>
  </tr>
<?php

$search = new rex_search_index();
$search->searchIds = true;
$result = $search->rex_search($rexsearch);
if (is_array($result))
{
  foreach ($result as $var)
  {
    $treestring = '<b>';
    $article = OOArticle :: getArticleById($var['id'], $var['clang']);
    $tree = $article->getParentTree();
    if (is_array($tree))
    {
      foreach ($tree as $cat)
      {
        $treestring .= $cat->_catname.">";
      }
    }
    $treestring = substr($treestring, 0, -1).'</b>';
    print "<tr><td>";
    print "Artikel: <b><a href=index.php?page=content&article_id=".$var['id']."&mode=edit&clang=".$var['clang'].">".$var['name']."</a></b> Sprache: <b>".$REX['CLANG'][$var['clang']]."</b> Pfad: $treestring "."<br>".$var['highlightedtext'];
    print "</td></tr>";
  }
  print "<tr><td>";
  print count($result)." Artikel gefunden";
  print "</td></tr>";
}

?>
</table>