<?php

/**
 * Klasse regelt den Zugriff auf Artikelinhalte.
 * DB Anfragen werden vermieden, caching läuft über generated Dateien.
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_article extends rex_article_base
{
    // bc schalter
    var $viasql;

    /*public*/ function rex_article($article_id = null, $clang = null)
    {
        $this->viasql = false;
        parent::rex_article_base($article_id, $clang);
    }

    // bc
    /*public*/ function getContentAsQuery($viasql = true)
    {
        if ($viasql !== true) {
            $viasql = false;
        }
        $this->viasql = $viasql;
    }

    /*public*/ function setArticleId($article_id)
    {
        // bc
        if ($this->viasql) {
            return parent::setArticleId($article_id);
        }

        global $REX;

        $article_id = (int) $article_id;
        $this->article_id = $article_id;

        $OOArticle = OOArticle::getArticleById($article_id, $this->clang);
        if (OOArticle::isValid($OOArticle)) {
            $this->category_id = $OOArticle->getCategoryId();
            $this->template_id = $OOArticle->getTemplateId();
            return true;
        }

        $this->article_id = 0;
        $this->template_id = 0;
        $this->category_id = 0;
        return false;
    }

    /*protected*/ function correctValue($value)
    {
        // bc
        if ($this->viasql) {
            return parent::correctValue($value);
        }

        if ($value == 'category_id') {
            if ($this->getValue('startpage') != 1) {
                $value = 're_id';
            } else {
                $value = 'article_id';
            }
        }

        return $value;
    }

    /*protected*/ function _getValue($value)
    {
        // bc
        if ($this->viasql) {
            return parent::_getValue($value);
        }

        global $REX;
        $value = $this->correctValue($value);

        return $REX['ART'][$this->article_id][$value][$this->clang];
    }

    /*public*/ function hasValue($value)
    {
        // bc
        if ($this->viasql) {
            return parent::hasValue($value);
        }

        global $REX;
        $value = $this->correctValue($value);

        return isset($REX['ART'][$this->article_id][$value][$this->clang]);
    }

    /*public*/ function getArticle($curctype = -1)
    {
        global $REX;

        $this->ctype = $curctype;

        // bc
        if ($this->viasql) {
            $CONTENT = parent::getArticle($curctype);

        } elseif (!$this->getSlice && $this->article_id != 0) {
            // ----- start: article caching
            ob_start();
            ob_implicit_flush(0);

            $article_content_file = $REX['GENERATED_PATH'] . '/articles/' . $this->article_id . '.' . $this->clang . '.content';
            if (!file_exists($article_content_file)) {
                include_once $REX['INCLUDE_PATH'] . '/functions/function_rex_generate.inc.php';
                $generated = rex_generateArticleContent($this->article_id, $this->clang);
                if ($generated !== true) {
                    // fehlermeldung ausgeben
                    echo $generated;
                }
            }

            if (file_exists($article_content_file)) {
                eval (rex_get_file_contents($article_content_file));
            }

            // ----- end: article caching
            $CONTENT = ob_get_contents();
            ob_end_clean();

        } else {
            // Inhalt ueber sql generierens
            $CONTENT = parent::getArticle($curctype);

        }

        $CONTENT = rex_register_extension_point('ART_CONTENT', $CONTENT,
            array (
                'ctype' => $curctype,
                'article' => &$this,
            )
        );

        return $CONTENT;
    }
}
