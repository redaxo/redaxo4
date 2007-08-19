<?php

/**
 * Object Oriented Framework: Basisklasse für die Strukturkomponenten
 * @package redaxo3
 * @version $Id$
 */

class OORedaxo
{
  /*
   * this vars get read out
   */
  var $_id = "";
  var $_re_id = "";
  var $_clang = "";
  var $_name = "";
  var $_catname = "";
  var $_template_id = "";
  var $_path = "";
  var $_prior = "";
  var $_startpage = "";
  var $_status = "";
  var $_attributes = "";
  var $_updatedate = "";
  var $_createdate = "";
  var $_updateuser = "";
  var $_createuser = "";

  /*
   * Constructor
   */
  function OORedaxo($params = false, $clang = false)
  {
    if ($params !== false)
    {
      foreach (OORedaxo :: getClassVars('array') as $var)
      {
        if(is_array($var))
        {
          $class_var = '_'. $var[1];
        	  if (isset($params[$var[0]])) $value = $params[$var[0]];
        	  else $value = '';
        }else
        {
	        $class_var = '_'.$var;
	        $value = $params[$var];
        }

        $this->$class_var = $value;
      }
    }

    if ($clang !== false)
    {
      $this->setClang($clang);
    }
  }

  function setClang($clang)
  {
    $this->_clang = $clang;
  }

  /*
   * Class Function:
   * Returns Object Value
   */
  function getValue($value)
  {
    if (substr($value, 0, 1) != '_')
    {
      $value = "_".$value;
    }
    // damit alte rex_article felder wie teaser, online_from etc
    // noch funktionieren
    // gleicher BC code nochmals in article::getValue
    if($this->hasValue($value))
    {
      return $this->$value;
    }
    elseif ($this->hasValue('art_'. $value))
    {
      return $this->getValue('art_'. $value);
    }
    elseif ($this->hasValue('cat_'. $value))
    {
      return $this->getValue('cat_'. $value);
    }
  }

  function hasValue($value)
  {
    if (substr($value, 0, 1) != '_')
    {
      $value = "_".$value;
    }
    return isset($this->$value);
  }

  /**
   * CLASS Function:
   * Returns an Array containing article field names
   */
  function getClassVars($mode = '')
  {
    static $vars = array ();

    if (empty($vars[$mode]))
    {
      global $REX;

      $vars[$mode] = array();

      $file = $REX['INCLUDE_PATH']. '/generated/articles/'.  $REX['START_ARTICLE_ID'] .'.0.article';
      if($REX['GG'] && file_exists($file))
      {
        // Im GetGenerated Modus, die Spaltennamen aus den generated Dateien holen
        include_once($file);

        // da getClassVars() eine statische Methode ist, können wir hier nicht mit $this->getId() arbeiten!
        $genVars = OORedaxo::convertGeneratedArray($REX['ART'][$REX['START_ARTICLE_ID']],0);
        unset($genVars['article_id']);
        unset($genVars['last_update_stamp']);
        foreach($genVars as $name => $value)
        {
          $vars[$mode][] = $name;
        }
      }
      else
      {
        // Im Backend die Spalten aus der DB auslesen / via EP holen
        $class_vars = get_class_vars('OORedaxo');

        foreach ($class_vars as $name => $value)
        {
          if (substr($name, 0, 1) == '_')
          {
            $vars[$mode][] = substr($name, 1);
          }
        }

        // ----- Extension Point
        $new_vars = rex_register_extension_point('OOF_META_PARAMS', array());

        foreach($new_vars as $name)
        {
          if(is_array($name))
          {
  //          if($mode === 'sql_alias')
  //            $vars[$mode][] = $name[0] .' AS '.$name[1];
  //          elseif($mode == 'alias')
  //            $vars[$mode][] = $name[1];
  //          else
            if($mode == 'array')
              $vars[$mode][] = $name;
            else
              $vars[$mode][] = $name[0];
          }
          else
          {
            $vars[$mode][] = $name;
          }
        }
      }
    }

    return $vars[$mode];
  }

  /*
  * CLASS Function:
  * Converts Genernated Array to OOBase Format Array
  */
  function convertGeneratedArray($generatedArray, $clang)
  {
    $OORedaxoArray['id'] = $generatedArray['article_id'][$clang];
    $OORedaxoArray['clang'] = $clang;
    foreach ($generatedArray as $key => $var)
    {
      $OORedaxoArray[$key] = $var[$clang];
    }
    unset ($OORedaxoArray['_article_id']);
    return $OORedaxoArray;
  }

  /*
   * Accessor Method:
   * returns the clang of the category
   */
  function getClang()
  {
    return $this->_clang;
  }

  /*
   * Object Helper Function:
   * Returns a url for linking to this article
   */
  function getUrl($params = '')
  {
    return rex_getUrl($this->getId(), $this->getClang(), $params);
  }

  /*
   * Accessor Method:
   * returns the id of the article
   */
  function getId()
  {
    return $this->_id;
  }

  /*
   * Accessor Method:
   * returns the parent_id of the article
   */
  function getParentId()
  {
    return $this->_re_id;
  }

  /*
   * Accessor Method:
   * returns the parent object of the article
   */
  function getParent()
  {
    return OOArticle::getArticleById($this->_re_id);
  }

  /*
   * Accessor Method:
   * returns the name of the article
   */
  function getName()
  {
    return $this->_name;
  }

  /*
   * Accessor Method:
   * returns the name of the article
   */
  function getFile()
  {
    return $this->_file;
  }

  /*
   * Accessor Method:
   * returns the name of the article
   */
  function getFileMedia()
  {
    return OOMedia :: getMediaByFileName($this->_file);
  }

  /*
   * Accessor Method:
   * returns the article description.
   */
  function getDescription()
  {
    return $this->_description;
  }

  /*
   * Accessor Method:
   * returns the article priority
   */
  function getPriority()
  {
    return $this->_prior;
  }

  /*
   * Accessor Method:
   * returns the last update user
   */
  function getUpdateUser()
  {
    return $this->_updateuser;
  }

  /*
   * Accessor Method:
   * returns the last update date
   */
  function getUpdateDate($format = null)
  {
    return OOMedia :: _getDate($this->_updatedate, $format);
  }

  /*
   * Accessor Method:
   * returns the creator
   */
  function getCreateUser()
  {
    return $this->_createuser;
  }

  /*
   * Accessor Method:
   * returns the creation date
   */
  function getCreateDate($format = null)
  {
    return OOMedia :: _getDate($this->_createdate, $format);
  }

  /*
   * Accessor Method:
   * returns true if article is online.
   */
  function isOnline()
  {
    return $this->_status == 1 ? true : false;
  }

  /*
   * Accessor Method:
   * Returns a link to this article
   *
   * @param [$params] Parameter für den Link
   * @param [$attributes] array Attribute die dem Link hinzugefügt werden sollen. Default: null
   * @param [$sorround_tag] string HTML-Tag-Name mit dem der Link umgeben werden soll, z.b. 'li', 'div'. Default: null
   * @param [sorround_attributes] array Attribute die Umgebenden-Element hinzugefügt werden sollen. Default: null
   */
  function toLink($params = '', $attributes = null, $sorround_tag = null, $sorround_attributes = null)
  {
    $name = htmlspecialchars($this->getName());
    $link = '<a href="'.$this->getUrl($params).'"'.$this->_toAttributeString($attributes).' title="'.$name.'">'.$name.'</a>';

    if ($sorround_tag !== null && is_string($sorround_tag))
    {
      $link = '<'.$sorround_tag.$this->_toAttributeString($sorround_attributes).'>'.$link.'</'.$sorround_tag.'>';
    }

    return $link;
  }

  function _toAttributeString($attributes)
  {
    $attr = '';

    if ($attributes !== null && is_array($attributes))
    {
      foreach ($attributes as $name => $value)
      {
        $attr .= ' '.$name.'="'.$value.'"';
      }
    }

    return $attr;
  }

  /*
   * Object Function:
   * Return a array of all parentCategories for an Breadcrumb for instance
   * Returns an array of OORedaxo objects sorted by $prior.
   */
  function getParentTree()
  {
    $return = array ();

    if ($this->_path)
    {
      $explode = explode('|', $this->_path.$this->_id.'|');
      if (is_array($explode))
      {
        foreach ($explode as $var)
        {
          if ($var != '')
          {
            $return[] = OOCategory :: getCategoryById($var, $this->_clang);
          }
        }
      }
    }

    return $return;
  }

  /**
   *  Accessor Method:
   * returns true if this Article is the Startpage for the category.
   * @deprecated
   */
  function isStartPage()
  {
    return $this->isStartArticle();
  }

  /**
   *  Accessor Method:
   * returns true if this Article is the Startpage for the category.
   */
  function isStartArticle()
  {
    return $this->_startpage;
  }

  /**
   *  Accessor Method:
   * returns true if this Article is the Startpage for the entire site.
   */
  function isSiteStartArticle()
  {
    global $REX;
    return $this->_id == $REX['START_ARTICLE_ID'];
  }

  /*
   * Object Helper Function:
   * Returns a String representation of this object
   * for debugging purposes.
   */
  function toString()
  {
    return $this->_id.", ".$this->_name.", ". ($this->isOnline() ? "online" : "offline");
  }
}
?>