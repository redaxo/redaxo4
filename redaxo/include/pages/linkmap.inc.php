<?php

function rex_linkmap_url($local = array(),$globals = array())
{
	$url = '';
	$first = true;
	foreach(array_merge($globals, $local) as $key => $value)
	{
		$separator = '&amp;';
		if($first)
		{
			$first = false;
			$separator = '?';
		}
	  $url .= $separator. $key .'='. $value;
	}

	return $url;
}

function rex_linkmap_backlink($id, $name)
{
  return 'javascript:insertLink(\'redaxo://'.$id.'\',\''.$name.'\');';
}

function rex_linkmap_format_li($OOobject, $current_category_id, $GlobalParams, $liAttr = '', $linkAttr = '')
{
	global $REX_USER;

	$liAttr .= $OOobject->getId() == $current_category_id ? ' id="rex-lmp-active"' : '';
	$linkAttr .= ' class="'. ($OOobject->isOnline() ? 'rex-online' : 'rex-offine'). '"';

	if(strpos($linkAttr, ' href=') === false)
		$linkAttr .= ' href="'. rex_linkmap_url(array('category_id' => $OOobject->getId()), $GlobalParams) .'"';

	$label = $OOobject->getName();
	if ($REX_USER->hasPerm('advancedMode[]'))
	  $label .= ' ['. $OOobject->getId() .']';

	return '<li'. $liAttr .'><a'. $linkAttr .'>'. $label .'</a>';
}

function rex_linkmap_tree($tree, $category_id, $children, $GlobalParams)
{
	$ul = '';
	if(is_array($children))
	{
		$li = '';
		$ulclasses = '';
		if (count($children)==1) $ulclasses .= 'rex-children-one ';
		foreach($children as $cat){
			$cat_children = $cat->getChildren();
			$cat_id = $cat->getId();
			$liclasses = '';
			$linkclasses = '';
			$sub_li = '';
			if (count($cat_children)>0) $liclasses .= 'rex-children ';

			if (next($children)== null ) $liclasses .= 'rex-children-last ';
			$linkclasses .= $cat->isOnline() ? 'rex-online ' : 'rex-offline ';
			if (is_array($tree) && in_array($cat_id,$tree))
			{
				$sub_li = rex_linkmap_tree($tree, $cat_id, $cat_children, $GlobalParams);
				$linkclasses .= 'rex-active ';
			}

      if($liclasses != '')
        $liclasses = ' class="'. rtrim($liclasses) .'"';

      if($linkclasses != '')
        $linkclasses = ' class="'. rtrim($linkclasses) .'"';

			$li .= '      <li'.$liclasses.'>';
			$li .= '<a'.$linkclasses.' href="'. rex_linkmap_url(array('category_id' => $cat_id), $GlobalParams).'">'.$cat->getName().'</a>';
			//$li .= ' '. $liclasses . $linkclasses;
			$li .= $sub_li;
			$li .= '</li>'. "\n";
		}

    if($ulclasses != '')
      $ulclasses = ' class="'. rtrim($ulclasses) .'"';

		if ($li!='') $ul = '<ul>'."\n".$li.'</ul>'. "\n";
	}
	return $ul;
}


// ------------------------ Ouput

//rex_title($REX['SERVERNAME'], 'Linkmap');
rex_title('Linkmap');

// ------- Default Values

$HTMLArea = rex_request('HTMLArea', 'string');
$opener_input_field = rex_request('opener_input_field', 'string');
$opener_input_field_name = rex_request('opener_input_field_name', 'string');
$category_id = rex_request('category_id', 'int');

$GlobalParams = array(
  'page' => $page,
  'HTMLArea' => $HTMLArea,
  'opener_input_field' => $opener_input_field,
  'opener_input_field_name' => $opener_input_field_name,
  'category_id' =>$category_id,
  'clang' => $clang
);

// ------- Build JS Functions

$func_body = '';
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

if ($opener_input_field != '' && $opener_input_field_name == '')
{
  $opener_input_field_name = $opener_input_field.'_NAME';
}

$func_body .= 'var linkid = link.replace("redaxo://","");
               var needle = new opener.getObj("'. $opener_input_field .'");
               needle.obj.value = linkid;
               var needle = new opener.getObj("'. $opener_input_field_name .'");
               needle.obj.value = name;';

// ------------------------ Print JS Functions

$search = rex_request('search', 'string');
?>
<script type="text/javascript">
  function insertLink(link,name){
    <?php echo $func_body. "\n" ?>
    self.close();
  }
</script>

<div class="rex-linkmap-searchbar">
  <form action="index.php<?php echo rex_linkmap_url(array(), $GlobalParams); ?>" method="post">
	  <fieldset>
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

<div class="rex-lmp-pth">
<ul>
<?php

$category = OOCategory::getCategoryById($category_id);
$link = rex_linkmap_url(array('category_id' => 0), $GlobalParams);
echo '<li>'.$I18N->msg('path').' </li>';
echo '<li>: <a href="'.$link.'">Homepage</a> </li>';

$tree = array();

if ($category = OOCategory::getCategoryById($category_id))
{
  $treee = $category->getParentTree();
  foreach($treee as $cat)
  {
    $tree[] = $cat->getId();
    $link = rex_linkmap_url(array('category_id' => $cat->getId()), $GlobalParams);
    echo '<li> : <a href="'. $link .'">'.$cat->getName().'</a></li>';
  }
}

?>
</ul>
</div>



<div id="rex-lmp">
  <div class="rex-lmp-cats">
    <h1>Kategorien</h1>
    <?php
    $roots = OOCategory::getRootCategories();
    echo rex_linkmap_tree($tree, $category_id, $roots, $GlobalParams);
    ?>
  </div>
  <div class="rex-lmp-arts">
    <h1>Artikel</h1>
  	<ul>
    <?php
    if ($category)
    {
      $articles = $category->getArticles();
      foreach($articles as $article)
    	  {
    		$liClass = $article->isStartpage() ? ' class="rex-lmp-startpage"' : '';
    		$url = rex_linkmap_backlink($article->getId(), $article->getName());

    		echo rex_linkmap_format_li($article, $category_id, $GlobalParams, $liClass, ' href="'. $url .'"');
    		echo '</li>';
    	  }
    	}
    ?>
  	</ul>
  </div>
  <div class="rex-clearer"> </div>
</div>