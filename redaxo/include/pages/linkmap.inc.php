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

function rex_linkmap_tree($tree, $current_category_id, $GlobalParams)
{
	if($tree == null) return;
	
	$cat = array_shift($tree);
	
  $li = '';
  foreach($cat->getChildren() as $child)
  {
		$li .= rex_linkmap_format_li($child, $current_category_id, $GlobalParams, ' class="rex-map-startpage"');
		// Naechste Levels aufklappen
		if(isset($tree[0]) && OOCategory::isValid($tree[0]) && $tree[0]->getId() == $child->getId())
		{
			rex_linkmap_tree($tree, $current_category_id, $GlobalParams);
		}
		$li .= '</li>';
  }
  if ($li != '') echo '<ul>'.$li.'</ul>';
}

function rex_linkmap_format_li($OOobject, $current_category_id, $GlobalParams, $liAttr = '', $linkAttr = '')
{
	global $REX_USER;

	$liAttr .= $OOobject->getId() == $current_category_id ? ' id="rex-map-active"' : '';
	$linkAttr .= ' class="'. ($OOobject->isOnline() ? 'rex-online' : 'rex-offine'). '"';
	
	if(strpos($linkAttr, ' href=') === false)
		$linkAttr .= ' href="'. rex_linkmap_url(array('category_id' => $OOobject->getId()), $GlobalParams) .'"';
	
	$label = $OOobject->getName();
	if ($REX_USER->hasPerm('advancedMode[]'))
	  $label .= ' ['. $OOobject->getId() .']';
	
	return '<li'. $liAttr .'><a'. $linkAttr .'>'. $label .'</a>';
}

// ------------------------ Ouput

rex_small_title($REX['SERVERNAME'], 'Linkmap');

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

?>
<script language="JavaScript" type="text/javascript">
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

<div id="rex-linkmap">
  <div class="rex-map-categories">
    <h1>Kategorien</h1>
  	<ul>
    <?php
      $roots = OOCategory::getRootCategories();
      $category = OOCategory::getCategoryById($category_id);
      $tree = $category->getParentTree();
      
    	foreach($roots as $root)
    	{
    		echo rex_linkmap_format_li($root, $category_id, $GlobalParams, ' class="rex-map-startpage"');
    		
    		if($root->getId() == $tree[0]->getId())
    		{
    			rex_linkmap_tree($tree, $category_id, $GlobalParams);
    		}
    		
    		echo '</li>';
    	}
    ?>
  	</ul>
  </div>
  <div class="rex-map-articles">
    <h1>Artikel</h1>
  	<ul>
    <?php
      $articles = $category->getArticles();
      
      foreach($articles as $article)
    	{
    		$liClass = $article->isStartpage() ? ' class="rex-map-startpage"' : '';
    		$url = rex_linkmap_backlink($article->getId(), $article->getName());
    		
    		echo rex_linkmap_format_li($article, $category_id, $GlobalParams, $liClass, ' href="'. $url .'"');
    		echo '</li>';
    	}
    ?>
  	</ul>
  </div>
  <div class="rex-clearer"> </div>
</div>