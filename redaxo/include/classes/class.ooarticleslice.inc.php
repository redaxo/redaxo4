<?php


/**
 *
 * The OOArticleSlice class is an object wrapper over the database table rex_articel_slice.
 * Together with OOArticle and OOCategory it provides an object oriented
 * Framework for accessing vital parts of your website.
 * This framework can be used in Modules, Templates and PHP-Slices!
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class OOArticleSlice
{
  var $_id;
  var $_article_id;
  var $_clang;
  var $_ctype;
  var $_modultyp_id;

  var $_re_article_slice_id;
  var $_next_article_slice_id;

  var $_createdate;
  var $_updatedate;
  var $_createuser;
  var $_updateuser;
  var $_revision;

  var $_values;
  var $_files;
  var $_filelists;
  var $_links;
  var $_linklists;
  var $_php;
  var $_html;

  /*
   * Constructor
   */
  function OOArticleSlice(
  	$id, $article_id, $clang, $ctype, $modultyp_id,
    $re_article_slice_id, $next_article_slice_id,
    $createdate,$updatedate,$createuser,$updateuser,$revision,
    $values, $files, $filelists, $links, $linklists, $php, $html)
  {
    $this->_id = $id;
    $this->_article_id = $article_id;
    $this->_clang = $clang;
    $this->_ctype = $ctype;
    $this->_modultyp_id = $modultyp_id;

    $this->_re_article_slice_id = $re_article_slice_id;
    $this->_next_article_slice_id = $next_article_slice_id;

    $this->_createdate = $createdate;
    $this->_updatedate = $updatedate;
    $this->_createuser = $createuser;
    $this->_updateuser = $updateuser;
    $this->_revision = $revision;

    $this->_values = $values;
    $this->_files = $files;
    $this->_filelists = $filelists;
    $this->_links = $links;
    $this->_linklists = $linklists;
    $this->_php = $php;
    $this->_html = $html;
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

    return OOArticleSlice::_getSliceWhere('id='. $an_id .' AND clang='. $clang);
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

    return OOArticleSlice::_getSliceWhere('a.article_id='. $an_article_id .' AND
                                          a.clang='. $clang .' AND
                                          (
                                           (a.re_article_slice_id=0 AND a.ctype=1 AND a.id = b.id)
                                            OR
                                           (b.ctype=2 AND a.ctype=1 AND b.id = a.re_article_slice_id)
                                          )',
                                          $REX['TABLE_PREFIX'].'article_slice a, '. $REX['TABLE_PREFIX'].'article_slice b',
                                          'a.*'
                                          );
  }

  /*
   * CLASS Function:
   * Returns the first slice of the given ctype of an article
   */
  function getFirstSliceForCtype($ctype, $an_article_id, $clang = false)
  {
    global $REX;

    if ($clang === false)
      $clang = $REX['CUR_CLANG'];

    return OOArticleSlice::_getSliceWhere('a.article_id='. $an_article_id .' AND
                                          a.clang='. $clang .' AND
                                          a.ctype='. $ctype .' AND
                                          (
                                           (a.re_article_slice_id=0  AND a.id = b.id)
                                            OR
                                           (b.ctype != a.ctype AND b.id = a.re_article_slice_id)
                                          )',
                                          $REX['TABLE_PREFIX'].'article_slice a, '. $REX['TABLE_PREFIX'].'article_slice b',
                                          'a.*'
                                          );
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

    return OOArticleSlice::_getSliceWhere('article_id='. $an_article_id .' AND clang='. $clang .' AND modultyp_id='. $a_moduletype_id, array());
  }

  /*
   * Object Function:
   * Return the next slice for this article
   * Returns an OOArticleSlice object.
   */
  function getNextSlice()
  {
    return OOArticleSlice::_getSliceWhere('re_article_slice_id = '. $this->_id .' AND clang = '. $this->_clang);
  }

  /*
   * Object Function:
   */
  function getPreviousSlice()
  {
    return OOArticleSlice::_getSliceWhere('id = '. $this->_re_article_slice_id .' AND clang = '. $this->_clang);
  }

  /**
   * Gibt den Slice formatiert zurück
   * @since 4.1 - 29.05.2008
   */
  function getSlice()
  {
    $art = new rex_article();
    $art->setArticleId($this->getArticleId());
    $art->setClang($this->getClang());
    $art->getSlice = $this->getId();
    return $art->replaceLinks( $art->getArticle() );
  }

  function _getSliceWhere($whereCluase, $table = null, $fields = null, $default = null)
  {
    global $REX;

    if(!$table)
      $table = $REX['TABLE_PREFIX'].'article_slice';

    if(!$fields)
      $fields = '*';

    $sql = new rex_sql;
//     $sql->debugsql = true;
    $query = '
      SELECT '. $fields .'
      FROM '. $table .'
      WHERE '. $whereCluase;

    $sql->setQuery($query);
    $rows = $sql->getRows();
    if ($rows == 1)
    {
      return new OOArticleSlice(
        $sql->getValue('id'), $sql->getValue('article_id'), $sql->getValue('clang'), $sql->getValue('ctype'), $sql->getValue('modultyp_id'),
        $sql->getValue('re_article_slice_id'), $sql->getValue('next_article_slice_id'),
        $sql->getValue('createdate'), $sql->getValue('updatedate'), $sql->getValue('createuser'), $sql->getValue('updateuser'), $sql->getValue('revision'),
        array($sql->getValue('value1'), $sql->getValue('value2'), $sql->getValue('value3'), $sql->getValue('value4'), $sql->getValue('value5'), $sql->getValue('value6'), $sql->getValue('value7'), $sql->getValue('value8'), $sql->getValue('value9'), $sql->getValue('value10'), $sql->getValue('value11'), $sql->getValue('value12'), $sql->getValue('value13'), $sql->getValue('value14'), $sql->getValue('value15'), $sql->getValue('value16'), $sql->getValue('value17'), $sql->getValue('value18'), $sql->getValue('value19'), $sql->getValue('value20')),
        array($sql->getValue('file1'), $sql->getValue('file2'), $sql->getValue('file3'), $sql->getValue('file4'), $sql->getValue('file5'), $sql->getValue('file6'), $sql->getValue('file7'), $sql->getValue('file8'), $sql->getValue('file9'), $sql->getValue('file10')),
        array($sql->getValue('filelist1'), $sql->getValue('filelist2'), $sql->getValue('filelist3'), $sql->getValue('filelist4'), $sql->getValue('filelist5'), $sql->getValue('filelist6'), $sql->getValue('filelist7'), $sql->getValue('filelist8'), $sql->getValue('filelist9'), $sql->getValue('filelist10')),
        array($sql->getValue('link1'), $sql->getValue('link2'), $sql->getValue('link3'), $sql->getValue('link4'), $sql->getValue('link5'), $sql->getValue('link6'), $sql->getValue('link7'), $sql->getValue('link8'), $sql->getValue('link9'), $sql->getValue('link10')),
        array($sql->getValue('linklist1'), $sql->getValue('linklist2'), $sql->getValue('linklist3'), $sql->getValue('linklist4'), $sql->getValue('linklist5'), $sql->getValue('linklist6'), $sql->getValue('linklist7'), $sql->getValue('linklist8'), $sql->getValue('linklist9'), $sql->getValue('linklist10')),
        $sql->getValue('php'), $sql->getValue('html'));
    } else if($rows > 1)
    {
      $slices = array ();
      for ($i = 0; $i < $rows; $i++)
      {
        $slices[] = new OOArticleSlice(
        $sql->getValue('id'), $sql->getValue('article_id'), $sql->getValue('clang'), $sql->getValue('ctype'), $sql->getValue('modultyp_id'),
        $sql->getValue('re_article_slice_id'), $sql->getValue('next_article_slice_id'),
        $sql->getValue('createdate'), $sql->getValue('updatedate'), $sql->getValue('createuser'), $sql->getValue('updateuser'), $sql->getValue('revision'),
        array($sql->getValue('value1'), $sql->getValue('value2'), $sql->getValue('value3'), $sql->getValue('value4'), $sql->getValue('value5'), $sql->getValue('value6'), $sql->getValue('value7'), $sql->getValue('value8'), $sql->getValue('value9'), $sql->getValue('value10'), $sql->getValue('value11'), $sql->getValue('value12'), $sql->getValue('value13'), $sql->getValue('value14'), $sql->getValue('value15'), $sql->getValue('value16'), $sql->getValue('value17'), $sql->getValue('value18'), $sql->getValue('value19'), $sql->getValue('value20')),
        array($sql->getValue('file1'), $sql->getValue('file2'), $sql->getValue('file3'), $sql->getValue('file4'), $sql->getValue('file5'), $sql->getValue('file6'), $sql->getValue('file7'), $sql->getValue('file8'), $sql->getValue('file9'), $sql->getValue('file10')),
        array($sql->getValue('filelist1'), $sql->getValue('filelist2'), $sql->getValue('filelist3'), $sql->getValue('filelist4'), $sql->getValue('filelist5'), $sql->getValue('filelist6'), $sql->getValue('filelist7'), $sql->getValue('filelist8'), $sql->getValue('filelist9'), $sql->getValue('filelist10')),
        array($sql->getValue('link1'), $sql->getValue('link2'), $sql->getValue('link3'), $sql->getValue('link4'), $sql->getValue('link5'), $sql->getValue('link6'), $sql->getValue('link7'), $sql->getValue('link8'), $sql->getValue('link9'), $sql->getValue('link10')),
        array($sql->getValue('linklist1'), $sql->getValue('linklist2'), $sql->getValue('linklist3'), $sql->getValue('linklist4'), $sql->getValue('linklist5'), $sql->getValue('linklist6'), $sql->getValue('linklist7'), $sql->getValue('linklist8'), $sql->getValue('linklist9'), $sql->getValue('linklist10')),
        $sql->getValue('php'), $sql->getValue('html'));

        $sql->next();
      }
      return $slices;
    }

    return $default;
  }

  function getArticle()
  {
    return OOArticle :: getArticleById($this->getArticleId());
  }

  function getArticleId()
  {
    return $this->_article_id;
  }

  function getClang()
  {
    return $this->_clang;
  }

  function getCtype()
  {
    return $this->_ctype;
  }

  function getModuleId()
  {
    return $this->_modultyp_id;
  }

  function getId()
  {
    return $this->_id;
  }

  function getValue($index)
  {
    if(is_int($index))
      return $this->_values[$index-1];

    $attrName = '_'. $index;
    if(isset($this->$attrName))
      return $this->$attrName;

    return null;
  }

  function getLink($index)
  {
    return $this->_links[$index-1];
  }

  function getLinkUrl($index)
  {
    return rex_getUrl($this->getLink($index));
  }

  function getLinkList($index)
  {
    return $this->_linklists[$index-1];
  }

  function getMedia($index)
  {
    return $this->_files[$index-1];
  }

  function getMediaUrl($index)
  {
    global $REX;
    return $REX['MEDIAFOLDER'].'/'.$this->getMedia($index);
  }

  function getMediaList($index)
  {
    return $this->_filelists[$index-1];
  }

  function getHtml()
  {
    return $this->_html;
  }

  function getPhp()
  {
    return $this->_php;
  }

  /**
   * Alter Alias aus BC Gruenden
   * @deprecated 4.1 - 05.03.2008
   */
  function getFile($index)
  {
    return $this->_files[$index-1];
  }

  /**
   * Alter Alias aus BC Gruenden
   * @deprecated 4.1 - 05.03.2008
   */
  function getFileUrl($index)
  {
    global $REX;
    return $REX['MEDIAFOLDER'].'/'.$this->getFile($index);
  }

  /**
   * Alter Alias aus BC Gruenden
   * @deprecated 4.1 - 05.03.2008
   */
  function getFileList($index)
  {
    return $this->_filelists[$index-1];
  }

  /**
   * Alter Alias aus BC Gruenden
   * @deprecated 4.1 - 05.03.2008
   */
  function getModulId()
  {
    return $this->_modultyp_id;
  }

  /**
   * Alter Alias aus BC Gruenden
   * @deprecated 4.1 - 05.03.2008
   */
  function getModulTyp()
  {
    return $this->getModulId();
  }

  /**
   * Alter Alias aus BC Gruenden
   * @deprecated 4.1 - 07.03.2008
   */
  function getPrevSlice()
  {
    return OOArticleSlice::_getSliceWhere('id = '. $this->_re_article_slice_id .' AND clang = '. $this->_clang);
  }
}