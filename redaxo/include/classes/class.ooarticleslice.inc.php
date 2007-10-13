<?php


/** 
 *  
 * The OOArticleSlice class is an object wrapper over the database table rex_articel_slice.
 * Together with OOArticle and OOCategory it provides an object oriented
 * Framework for accessing vital parts of your website.
 * This framework can be used in Modules, Templates and PHP-Slices!
 *
 * @package redaxo4
 * @version $Id$
 */

class OOArticleSlice
{
  var $_id = "";
  var $_re_article_slice_id = "";
  var $_value = array();
  var $_file = array();
  var $_link = array();
  var $_php = "";
  var $_html = "";
  var $_article_id = "";
  var $_modultyp_id = "";
  var $_clang = "";

  /*
   * Constructor
   */
  function OOArticleSlice(	
  	$id, $re_article_slice_id, 
  	$value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8, $value9, $value10, $value11, $value12, $value13, $value14, $value15, $value16, $value17, $value18, $value19, $value20, 
  	$file1, $file2, $file3, $file4, $file5, $file6, $file7, $file8, $file9, $file10, 
  	$link1, $link2, $link3, $link4, $link5, $link6, $link7, $link8, $link9, $link10, 
  	$php, $html, $article_id, $modultyp_id, $clang)
  {
    $this->_id = $id;
    $this->_re_article_slice_id = $re_article_slice_id;
    $this->_value[1] = $value1;
    $this->_value[2] = $value2;
    $this->_value[3] = $value3;
    $this->_value[4] = $value4;
    $this->_value[5] = $value5;
    $this->_value[6] = $value6;
    $this->_value[7] = $value7;
    $this->_value[8] = $value8;
    $this->_value[9] = $value9;
    $this->_value[10] = $value10;
    $this->_value[11] = $value11;
    $this->_value[12] = $value12;
    $this->_value[13] = $value13;
    $this->_value[14] = $value14;
    $this->_value[15] = $value15;
    $this->_value[16] = $value16;
    $this->_value[17] = $value17;
    $this->_value[18] = $value18;
    $this->_value[19] = $value19;
    $this->_value[20] = $value20;
    $this->_file[1] = $file1;
    $this->_file[2] = $file2;
    $this->_file[3] = $file3;
    $this->_file[4] = $file4;
    $this->_file[5] = $file5;
    $this->_file[6] = $file6;
    $this->_file[7] = $file7;
    $this->_file[8] = $file8;
    $this->_file[9] = $file9;
    $this->_file[10] = $file10;
    $this->_link[1] = $link1;
    $this->_link[2] = $link2;
    $this->_link[3] = $link3;
    $this->_link[4] = $link4;
    $this->_link[5] = $link5;
    $this->_link[6] = $link6;
    $this->_link[7] = $link7;
    $this->_link[8] = $link8;
    $this->_link[9] = $link9;
    $this->_link[10] = $link10;
    $this->_php = $php;
    $this->_html = $html;
    $this->_article_id = $article_id;
    $this->_modultyp_id = $modultyp_id;
    $this->_clang = $clang;
  }

  /*
   * CLASS Function:
   * Return an ArticleSlice by its id
   * Returns an OOArticleSlice object
   */
  function getArticleSliceById($an_id, $clang = false)
  {
    global $REX;
    
    if ($clang === false)
      $clang = $REX['CUR_CLANG'];
      
    $table = $REX['TABLE_PREFIX']."article_slice";
    $sql = new rex_sql;
    $query =<<<EOD
SELECT
  id,re_article_slice_id,
  value1,value2,value3,value4,value5,value6,value7,value8,value9,value10,value11,value12,value13,value14,value15,value16,value17,value18,value19,value20,
  file1,file2,file3,file4,file5,file6,file7,file8,file9,file10,
  link1,link2,link3,link4,link5,link6,link7,link8,link9,link10,
  php,html,
  article_id,modultyp_id,clang
FROM $table
WHERE id = $an_id AND clang = $clang
EOD;
    $sql->setQuery($query);
    if ($sql->getRows() == 1)
    {
      return new OOArticleSlice(
      	$sql->getValue("id"), 
      	$sql->getValue("re_article_slice_id"), 
      	$sql->getValue("value1"), $sql->getValue("value2"), $sql->getValue("value3"), $sql->getValue("value4"), $sql->getValue("value5"), $sql->getValue("value6"), $sql->getValue("value7"), $sql->getValue("value8"), $sql->getValue("value9"), $sql->getValue("value10"), $sql->getValue("value11"), $sql->getValue("value12"), $sql->getValue("value13"), $sql->getValue("value14"), $sql->getValue("value15"), $sql->getValue("value16"), $sql->getValue("value17"), $sql->getValue("value18"), $sql->getValue("value19"), $sql->getValue("value20"), 
      	$sql->getValue("file1"), $sql->getValue("file2"), $sql->getValue("file3"), $sql->getValue("file4"), $sql->getValue("file5"), $sql->getValue("file6"), $sql->getValue("file7"), $sql->getValue("file8"), $sql->getValue("file9"), $sql->getValue("file10"), $sql->getValue("link1"), $sql->getValue("link2"), $sql->getValue("link3"), $sql->getValue("link4"), $sql->getValue("link5"), $sql->getValue("link6"), $sql->getValue("link7"), $sql->getValue("link8"), $sql->getValue("link9"), $sql->getValue("link10"), 
      	$sql->getValue("php"), $sql->getValue("html"), 
      	$sql->getValue("article_id"), $sql->getValue("modultyp_id"), $sql->getValue("clang"));
    }
    return null;
  }

  /*
   * CLASS Function:
   * Return the first slice for an article.
   * This can then be used to iterate over all the
   * slices in the order as they appear using the
   * getNextSlice() function.
   * Returns an OOArticleSlice object
   */
  function getFirstSliceForArticle($an_article_id, $clang = false)
  {
    global $REX;
    
    if ($clang === false)
      $clang = $REX['CUR_CLANG'];
      
    $table = $REX['TABLE_PREFIX'].'article_slice';
    $sql = new rex_sql;
    $query =<<<EOD
SELECT
  id,re_article_slice_id,
  value1,value2,value3,value4,value5,value6,value7,value8,value9,value10,value11,value12,value13,value14,value15,value16,value17,value18,value19,value20,
  file1,file2,file3,file4,file5,file6,file7,file8,file9,file10,
  link1,link2,link3,link4,link5,link6,link7,link8,link9,link10,
  php,html,
  article_id,modultyp_id,clang
FROM $table
WHERE article_id = $an_article_id AND re_article_slice_id = 0 AND clang = $clang
EOD;
    $sql->setQuery($query);
    if ($sql->getRows() == 1)
    {
      return new OOArticleSlice(
      	$sql->getValue("id"), $sql->getValue("re_article_slice_id"), 
      	$sql->getValue("value1"), $sql->getValue("value2"), $sql->getValue("value3"), $sql->getValue("value4"), $sql->getValue("value5"), $sql->getValue("value6"), $sql->getValue("value7"), $sql->getValue("value8"), $sql->getValue("value9"), $sql->getValue("value10"), $sql->getValue("value11"), $sql->getValue("value12"), $sql->getValue("value13"), $sql->getValue("value14"), $sql->getValue("value15"), $sql->getValue("value16"), $sql->getValue("value17"), $sql->getValue("value18"), $sql->getValue("value19"), $sql->getValue("value20"), 
      	$sql->getValue("file1"), $sql->getValue("file2"), $sql->getValue("file3"), $sql->getValue("file4"), $sql->getValue("file5"), $sql->getValue("file6"), $sql->getValue("file7"), $sql->getValue("file8"), $sql->getValue("file9"), $sql->getValue("file10"), 
      	$sql->getValue("link1"), $sql->getValue("link2"), $sql->getValue("link3"), $sql->getValue("link4"), $sql->getValue("link5"), $sql->getValue("link6"), $sql->getValue("link7"), $sql->getValue("link8"), $sql->getValue("link9"), $sql->getValue("link10"), 
      	$sql->getValue("php"), $sql->getValue("html"), 
      	$sql->getValue("article_id"), $sql->getValue("modultyp_id"), $sql->getValue("clang"));
    }
    return null;
  }

  /*
   * CLASS Function:
   * Return all slices for an article that have a certain
   * module type.
   * Returns an array of OOArticleSlice objects
   */
  function getSlicesForArticleOfType($an_article_id, $a_moduletype_id, $clang = false)
  {
    global $REX;
    
    if ($clang === false)
      $clang = $REX['CUR_CLANG'];
      
    $table = $REX['TABLE_PREFIX'].'article_slice';
    $sql = new rex_sql;
    $query =<<<EOD
SELECT
  id,re_article_slice_id,
  value1,value2,value3,value4,value5,value6,value7,value8,value9,value10,value11,value12,value13,value14,value15,value16,value17,value18,value19,value20,
  file1,file2,file3,file4,file5,file6,file7,file8,file9,file10,
  link1,link2,link3,link4,link5,link6,link7,link8,link9,link10,
  php,html,
  article_id,modultyp_id,clang
FROM $table
WHERE article_id = $an_article_id AND clang = $clang AND modultyp_id = $a_moduletype_id
EOD;
    $sql->setQuery($query);
    $slices = array ();
    for ($i = 0; $i < $sql->getRows(); $i++)
    {
      $slices[] = new OOArticleSlice(
      	$sql->getValue("id"), $sql->getValue("re_article_slice_id"), 
      	$sql->getValue("value1"), $sql->getValue("value2"), $sql->getValue("value3"), $sql->getValue("value4"), $sql->getValue("value5"), $sql->getValue("value6"), $sql->getValue("value7"), $sql->getValue("value8"), $sql->getValue("value9"), $sql->getValue("value10"), $sql->getValue("value11"), $sql->getValue("value12"), $sql->getValue("value13"), $sql->getValue("value14"), $sql->getValue("value15"), $sql->getValue("value16"), $sql->getValue("value17"), $sql->getValue("value18"), $sql->getValue("value19"), $sql->getValue("value20"), 
      	$sql->getValue("file1"), $sql->getValue("file2"), $sql->getValue("file3"), $sql->getValue("file4"), $sql->getValue("file5"), $sql->getValue("file6"), $sql->getValue("file7"), $sql->getValue("file8"), $sql->getValue("file9"), $sql->getValue("file10"), 
      	$sql->getValue("link1"), $sql->getValue("link2"), $sql->getValue("link3"), $sql->getValue("link4"), $sql->getValue("link5"), $sql->getValue("link6"), $sql->getValue("link7"), $sql->getValue("link8"), $sql->getValue("link9"), $sql->getValue("link10"), 
      	$sql->getValue("php"), $sql->getValue("html"), 
      	$sql->getValue("article_id"), $sql->getValue("modultyp_id"), $sql->getValue("clang"));
      $sql->next();
    }
    return $slices;
  }

  /*
   * Object Function:
   * Return the next slice for this article
   * Returns an OOArticleSlice object.
   */
  function getNextSlice()
  {
    global $REX;
    $table = '';
    $table = $REX['TABLE_PREFIX']."article_slice";
    $sql = new rex_sql;
    $query =<<<EOD
SELECT
  id,re_article_slice_id,
  value1,value2,value3,value4,value5,value6,value7,value8,value9,value10,value11,value12,value13,value14,value15,value16,value17,value18,value19,value20,
  file1,file2,file3,file4,file5,file6,file7,file8,file9,file10,
  link1,link2,link3,link4,link5,link6,link7,link8,link9,link10,
  php,html,article_id,modultyp_id,clang
FROM $table
WHERE re_article_slice_id = {$this->_id} AND clang = {$this->_clang}
EOD;
    $sql->setQuery($query);
    if ($sql->getRows() == 1)
    {
      return new OOArticleSlice(
      	$sql->getValue("id"), $sql->getValue("re_article_slice_id"), 
      	$sql->getValue("value1"), $sql->getValue("value2"), $sql->getValue("value3"), $sql->getValue("value4"), $sql->getValue("value5"), $sql->getValue("value6"), $sql->getValue("value7"), $sql->getValue("value8"), $sql->getValue("value9"), $sql->getValue("value10"), $sql->getValue("value11"), $sql->getValue("value12"), $sql->getValue("value13"), $sql->getValue("value14"), $sql->getValue("value15"), $sql->getValue("value16"), $sql->getValue("value17"), $sql->getValue("value18"), $sql->getValue("value19"), $sql->getValue("value20"), 
      	$sql->getValue("file1"), $sql->getValue("file2"), $sql->getValue("file3"), $sql->getValue("file4"), $sql->getValue("file5"), $sql->getValue("file6"), $sql->getValue("file7"), $sql->getValue("file8"), $sql->getValue("file9"), $sql->getValue("file10"), 
      	$sql->getValue("link1"), $sql->getValue("link2"), $sql->getValue("link3"), $sql->getValue("link4"), $sql->getValue("link5"), $sql->getValue("link6"), $sql->getValue("link7"), $sql->getValue("link8"), $sql->getValue("link9"), $sql->getValue("link10"), 
      	$sql->getValue("php"), $sql->getValue("html"), 
      	$sql->getValue("article_id"), $sql->getValue("modultyp_id"), $sql->getValue("clang"));
    }
    return null;
  }

  /*
   * Object Function:
   */
  function getPreviousSlice()
  {
    return OOArticleSlice :: getArticleSliceById($this->_re_article_slice_id);
  }

  function getArticle()
  {
    return OOArticle :: getArticleById($this->getArticleId());
  }

  function getArticleId()
  {
    return $this->_article_id;
  }

  function getModulTyp()
  {
    return $this->_modultyp_id;
  }

  function getId()
  {
    return $this->_id;
  }

  function getValue($index)
  {
    return $this->_value[$index];
  }

  function getUrl()
  {
    return rex_getUrl($this->getArticleId());
  }

  function getLink($index)
  {
    return $this->_link[$index];
  }

  function getLinkUrl($index)
  {
    return rex_getUrl($this->getLink($index));
  }

  function getFile($index)
  {
    return $this->_file[$index];
  }

  function getFileUrl($index)
  {
    global $REX;
    return $REX['MEDIAFOLDER']."/".$this->getFile($index);
  }

  function getHtml()
  {
    return $this->_html;
  }

  function getPhp()
  {
    return $this->_php;
  }
}
?>