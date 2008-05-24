<?php

/**
 * REX_ARTICLE[1]
 *
 * REX_ARTICLE_VAR['description']
 * REX_ARTICLE_VAR['id']
 * REX_ARTICLE_VAR['category_id']
 * ...
 *
 * @package redaxo4
 * @version $Id: class.rex_var_article.inc.php,v 1.3 2008/01/11 14:57:08 kills Exp $
 */

class rex_var_article extends rex_var
{
  // --------------------------------- Output

  function getTemplate($content)
  {
    $content = $this->matchArticleVar($content);
    return $this->matchArticle($content);
  }

  function getBEOutput(& $sql, $content)
  {
    return $this->getTemplate($content);
  }

  /**
   * @see rex_var::handleDefaultParam
   */
  function handleDefaultParam($varname, $args, $name, $value)
  {
    switch($name)
    {
      case '1' :
      case 'clang' :
        $args['clang'] = (int) $value;
        break;
    }
    return parent::handleDefaultParam($varname, $args, $name, $value);
  }

  /**
   * Wert für die Ausgabe
   */
  function matchArticleVar($content)
  {
    $var = 'REX_ARTICLE_VAR';
    $matches = $this->getVarParams($content, $var);

    foreach ($matches as $match)
    {
      list ($param_str) = $match;

      $content = str_replace($var . '[' . $param_str . ']', '<?php echo $this->getValue(\''. addslashes($param_str) .'\') ?>', $content);
    }

    return $content;
  }

  /**
   * Wert für die Ausgabe
   */
  function matchArticle($content)
  {
    global $REX;

    $var = 'REX_ARTICLE';
    $matches = $this->getVarParams($content, $var);

    foreach ($matches as $match)
    {
      list ($param_str, $article_id, $args) = $match;

      $clang = $REX['CUR_CLANG'];
      if(isset ($args['clang']))
      {
        $clang = $args['clang'];
        unset ($args['clang']);
      }

      // bezeichner wählen, der keine variablen
      // aus modulen/templates überschreibt
      $varname = '$__rex_art'. $article_id .'_'. $clang;
      $tpl = '<?php
      '. $varname .' = new rex_article();
      '. $varname .'->setArticleId('. $article_id .');
      '. $varname .'->setClang('. $clang .');
      echo '. $varname .'->getArticle();
      ?>';

      $content = str_replace($var . '[' . $param_str . ']', $tpl, $content);
    }

    return $content;
  }
}
?>