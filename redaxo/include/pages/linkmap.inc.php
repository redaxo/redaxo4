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

function rex_linkmap_format_label($OOobject)
{
  global $REX_USER, $I18N;

  $label = $OOobject->getName();

  if(trim($label) == '')
    $label = '&nbsp;';

  if ($REX_USER->hasPerm('advancedMode[]'))
    $label .= ' ['. $OOobject->getId() .']';

  if(OOArticle::isValid($OOobject) && !$OOobject->hasTemplate())
    $label .= ' ['.$I18N->msg('lmap_has_no_template').']';

  return $label;
}

function rex_linkmap_format_li($OOobject, $current_category_id, $GlobalParams, $liAttr = '', $linkAttr = '')
{
	$liAttr .= $OOobject->getId() == $current_category_id ? ' id="rex-lmp-active"' : '';
	$linkAttr .= ' class="'. ($OOobject->isOnline() ? 'rex-online' : 'rex-offine'). '"';

	if(strpos($linkAttr, ' href=') === false)
		$linkAttr .= ' href="'. rex_linkmap_url(array('category_id' => $OOobject->getId()), $GlobalParams) .'"';

	$label = rex_linkmap_format_label($OOobject);

	return '<li'. $liAttr .'><a'. $linkAttr .'>'. $label . '</a>';
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
			if (count($cat_children)>0) {
				$liclasses .= 'rex-children ';
				$linkclasses .= 'rex-lmp-is-not-empty ';
			}

			if (next($children)== null ) $liclasses .= 'rex-children-last ';
			$linkclasses .= $cat->isOnline() ? 'rex-online ' : 'rex-offline ';
			if (is_array($tree) && in_array($cat_id,$tree))
			{
				$sub_li = rex_linkmap_tree($tree, $cat_id, $cat_children, $GlobalParams);
				$liclasses .= 'rex-active ';
				$linkclasses .= 'rex-active ';
			}

      if($liclasses != '')
        $liclasses = ' class="'. rtrim($liclasses) .'"';

      if($linkclasses != '')
        $linkclasses = ' class="'. rtrim($linkclasses) .'"';

      $label = rex_linkmap_format_label($cat);

			$li .= '      <li'.$liclasses.'>';
			$li .= '<a'.$linkclasses.' href="'. rex_linkmap_url(array('category_id' => $cat_id), $GlobalParams).'">'.$label.'</a>';
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
if($opener_input_field=="TINY"){
	$func_body .= 'window.opener.insertLink(link,name)';
} else {
$func_body .= 'var linkid = link.replace("redaxo://","");
               window.opener.document.getElementById("'. $opener_input_field .'").value = linkid;
               window.opener.document.getElementById("'. $opener_input_field_name .'").value = name;';
}


// ------------------------ Print JS Functions

?>
<script type="text/javascript">
  function insertLink(link,name){
    <?php echo $func_body. "\n" ?>
    self.close();
  }
</script>

<div class="rex-lmp-pth">
<ul>
<?php

$isRoot = $category_id === 0;
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
    <h1><?php echo $I18N->msg('lmap_categories'); ?></h1>
    <?php
    $roots = OOCategory::getRootCategories();
    echo rex_linkmap_tree($tree, $category_id, $roots, $GlobalParams);
    ?>
  </div>
  <div class="rex-lmp-arts">
    <h1><?php echo $I18N->msg('lmap_articles'); ?></h1>
  	<ul>
    <?php
    $articles = null;
    if($isRoot)
      $articles = OOArticle::getRootArticles();
    else if($category)
      $articles = $category->getArticles();

    if ($articles)
    {
      foreach($articles as $article)
  	  {
    		$liClass = $article->isStartpage() ? ' class="rex-lmp-startpage"' : '';
    		$url = rex_linkmap_backlink($article->getId(), $article->getName());

    		echo rex_linkmap_format_li($article, $category_id, $GlobalParams, $liClass, ' href="'. $url .'"');
    		echo '</li>'. "\n";
  	  }
  	}
    ?>
  	</ul>
  </div>
  <div class="rex-clearer"> </div>
</div>