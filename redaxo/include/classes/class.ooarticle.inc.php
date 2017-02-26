<?php

/**
 * Object Oriented Framework: Bildet einen Artikel der Struktur ab
 * @package redaxo4
 * @version svn:$Id$
 */

class OOArticle extends OORedaxo
{
    // this is the new style constructor used by newer php versions.
    // important: if you change the signatur of this method, change also the signature of OOArticle()
    /*protected*/ function __construct($params = false, $clang = false)
    {
        $this->OOArticle($params, $clang);
    }

    // this is the deprecated old style constructor kept for compat reasons. 
    // important: if you change the signatur of this method, change also the signature of __construct()
    /*protected*/ function OOArticle($params = false, $clang = false)
    {
        parent :: OORedaxo($params, $clang);
    }

    /**
     * CLASS Function:
     * Return an OORedaxo object based on an id
     */
    static /*public*/ function getArticleById($article_id, $clang = false, $OOCategory = false)
    {
        global $REX;

        $article_id = (int) $article_id;

        if (!is_int($article_id)) {
            return null;
        }

        if ($clang === false) {
            $clang = $REX['CUR_CLANG'];
        }

        $article_path = $REX['GENERATED_PATH'] . '/articles/' . $article_id . '.' . $clang . '.article';
        if (!file_exists($article_path)) {
            require_once $REX['INCLUDE_PATH'] . '/functions/function_rex_generate.inc.php';
            rex_generateArticleMeta($article_id, $clang);
        }

        if (file_exists($article_path)) {
            require_once $article_path;

            if ($OOCategory) {
                return new OOCategory(OORedaxo :: convertGeneratedArray($REX['ART'][$article_id], $clang));
            } else {
                return new self(OORedaxo :: convertGeneratedArray($REX['ART'][$article_id], $clang));
            }
        }

        return null;
    }

    /**
     * CLASS Function:
     * Return the site wide start article
     */
    static /*public*/ function getSiteStartArticle($clang = false)
    {
        global $REX;

        if ($clang === false) {
            $clang = $REX['CUR_CLANG'];
        }

        return self :: getArticleById($REX['START_ARTICLE_ID'], $clang);
    }

    /**
     * CLASS Function:
     * Return start article for a certain category
     */
    static /*public*/ function getCategoryStartArticle($a_category_id, $clang = false)
    {
        global $REX;

        if ($clang === false) {
            $clang = $REX['CUR_CLANG'];
        }

        return self :: getArticleById($a_category_id, $clang);
    }

    /**
     * CLASS Function:
     * Return a list of articles for a certain category
     */
    static /*public*/ function getArticlesOfCategory($a_category_id, $ignore_offlines = false, $clang = false)
    {
        global $REX;

        if ($clang === false) {
            $clang = $REX['CUR_CLANG'];
        }

        $articlelist = $REX['GENERATED_PATH'] . '/articles/' . $a_category_id . '.' . $clang . '.alist';
        if (!file_exists($articlelist)) {
            include_once $REX['INCLUDE_PATH'] . '/functions/function_rex_generate.inc.php';
            rex_generateLists($a_category_id, $clang);
        }

        $artlist = array ();
        if (file_exists($articlelist)) {
            include_once $articlelist;

            if (isset($REX['RE_ID'][$a_category_id])) {
                foreach ($REX['RE_ID'][$a_category_id] as $var) {
                    $article = self :: getArticleById($var, $clang);
                    if ($ignore_offlines) {
                        if ($article->isOnline()) {
                            $artlist[] = $article;
                        }
                    } else {
                        $artlist[] = $article;
                    }
                }
            }
        }

        return $artlist;
    }

    /**
     * CLASS Function:
     * Return a list of top-level articles
     */
    static /*public*/ function getRootArticles($ignore_offlines = false, $clang = false)
    {
        return self :: getArticlesOfCategory(0, $ignore_offlines, $clang);
    }

    /**
     * Accessor Method:
     * returns the category id
     */
    /*public*/ function getCategoryId()
    {
        return $this->isStartPage() ? $this->getId() : $this->getParentId();
    }

    /*
     * Object Function:
     * Returns the parent category
     */
    /*public*/ function getCategory()
    {
        return OOCategory :: getCategoryById($this->getCategoryId(), $this->getClang());
    }

    /**
     * Accessor Method:
     * returns the path of the category/article
     */
    /*public*/ function getPath()
    {
            if ($this->isStartArticle()) {
                return $this->_path . $this->_id . '|';
            }

            return $this->_path;
    }

    /**
     * Accessor Method:
     * returns the path ids of the category/article as an array
     */
    /*public*/ function getPathAsArray()
    {
        $path = explode('|', $this->getPath());
        return array_values(array_map('intval', array_filter($path)));
    }

    /*
     * Static Method: Returns True when the given article is a valid OOArticle
     */
    static /*public*/ function isValid($article)
    {
        return is_object($article) && is_a($article, 'ooarticle');
    }

    /*public*/ function getValue($value)
    {
        // alias für re_id -> category_id
        if (in_array($value, array('re_id', '_re_id', 'category_id', '_category_id'))) {
            // für die CatId hier den Getter verwenden,
            // da dort je nach ArtikelTyp unterscheidungen getroffen werden müssen
            return $this->getCategoryId();
        }
        return parent::getValue($value);
    }

    static /*public*/ function hasValue($value)
    {
        return parent::hasValueWithPrefixes($value, array('art_'));
    }

}
