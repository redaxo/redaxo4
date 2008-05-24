<?php


/**
 * Abtrackte Basisklasse für REX_VARS
 * @package redaxo4
 * @version $Id: class.rex_var.inc.php,v 1.2 2008/01/11 14:00:19 kills Exp $
 */

class rex_var
{
  // --------------------------------- Actions

  /**
   * Actionmethode:
   * Zum füllen des sql aus dem $REX_ACTION Array
   */
  function setACValues(& $sql, $REX_ACTION, $escape = false)
  {
    // nichts tun
  }

  /**
   * Actionmethode:
   * Zum füllen des $REX_ACTION Arrays aus den Input Formularwerten
   * @return REX_ACTION Array
   */
  function getACRequestValues($REX_ACTION)
  {
    return $REX_ACTION;
  }

  /**
   * Actionmethode:
   * Zum füllen des $REX_ACTION Arrays aus der Datenbank (rex_sql)
   * @return REX_ACTION Array
   */
  function getACDatabaseValues($REX_ACTION, & $sql)
  {
    return $REX_ACTION;
  }

  /**
   * Actionmethode:
   * Ersetzen der Werte in dem Aktionsscript
   * @return output String
   */
  function getACOutput($REX_ACTION, $content)
  {
    $sql = new rex_sql();
    $this->setACValues($sql, $REX_ACTION);
    return $this->getBEOutput($sql, $content);
  }

  // --------------------------------- Ouput

  /**
   * Ausgabe eines Modules fürs Frontend
   * sql Objekt mit der passenden Slice
   *
   * FE = Frontend
   */
  function getFEOutput(& $sql, $content)
  {
    return $this->getBEOutput($sql, $content);
  }

  /**
   * Ausgabe eines Modules im Backend bei der Ausgabe
   * sql Objekt mit der passenden Slice
   *
   * BE = Backend
   */
  function getBEOutput(& $sql, $content)
  {
    return $content;
  }

  /**
   * Ausgabe eines Modules im Backend bei der Eingabe
   * sql Objekt mit der passenden Slice
   *
   * BE = Backend
   */
  function getBEInput(& $sql, $content)
  {
    return $this->getBEOutput($sql, $content);
  }

  /**
   * Ausgabe eines Templates
   */
  function getTemplate($content)
  {
  	return $content;
  }

  /**
   * Wandelt PHP Code in Einfache Textausgaben um
   */
  function stripPHP($content)
  {
    $content = str_replace('<?', '&lt;?', $content);
    $content = str_replace('?>', '?&gt;', $content);
    return $content;
  }

  /**
   * GetValue Wrapper, da hier immer auf die gleiche Tabelle gearbeitet wird und
   * mit MySQL 3.x mit Tabellenprefix angegeben werden muss, da der SQL gleichnamige
   * Spalten unterschiedlicher Tabellen enthält.
   */
  function getValue(& $sql, $value)
  {
    global $REX;
    return $sql->getValue($REX['TABLE_PREFIX'] . 'article_slice.' . $value);
  }
  /**
   * setValue Wrapper, da hier immer auf die gleiche Tabelle gearbeitet wird und
   * mit MySQL 3.x mit Tabellenprefix angegeben werden muss, da der SQL gleichnamige
   * Spalten unterschiedlicher Tabellen enthält.
   */
  function setValue(& $sql, $fieldname, $value, $escape = false)
  {
    global $REX;

    if($escape)
      return $sql->setValue($REX['TABLE_PREFIX'] . 'article_slice.' . $fieldname, addslashes($value));

    return $sql->setValue($REX['TABLE_PREFIX'] . 'article_slice.' . $fieldname, $value);
  }

  /**
   * Callback um nicht explizit gehandelte OutputParameter zu behandeln
   */
  function handleDefaultParam($varname, $args, $name, $value)
  {
    switch($name)
    {
      case 'ifempty':
      case 'instead':
      $args[$name] = (string) $value;
    }
    return $args;
  }

  /**
   * Parameter aus args auf den Wert einer Variablen anwenden
   */
  function handleGlobalParams($varname, $args, $value)
  {
    // TODO Was hier tun?
    // if(isset($args['instead']))

    if(isset($args['ifempty']) && $value == '')
      return $args['ifempty'];

    return $value;
  }

  /**
   * Findet die Parameter der Variable $varname innerhalb des Strings $content.
   *
   * @access protected
   */
  function getVarParams($content, $varname)
  {
    $result = array ();

    $match = $this->matchVar($content, $varname);
    foreach ($match as $param_str)
    {
      $params = $this->splitString($param_str);

      $id = '';
      $args = array();
      foreach ($params as $name => $value)
      {
        switch ($name)
        {
          case '0' :
          case 'id' :
            $id = (int) $value;
            break;
          default :
            $args = $this->handleDefaultParam($varname, $args, $name, $value);
        }
      }

      if ($id != '')
      {
        $result[] = array (
          $param_str,
          $id,
          $args
        );
      }
    }

    return $result;
  }

  /**
   * Durchsucht den String $content nach Variablen mit dem Namen $varname.
   * Gibt die Parameter der Treffer (Text der Variable zwischen den []) als Array zurück.
   */
  function matchVar($content, $varname)
  {
    $result = array ();

    if (preg_match_all('/' . preg_quote($varname, '/') . '\[([^\]]*)\]/ms', $content, $matches))
    {
      foreach ($matches[1] as $match)
      {
        $result[] = $match;
      }
    }

    return $result;
  }

  /**
   * Trennt einen String an Leerzeichen auf.
   * Abschnitte die in "" oder '' stehen, werden als ganzes behandelt und
   * darin befindliche Leerzeichen nicht getrennt.
   * @access protected
   */
  function splitString($string)
  {
    return rex_split_string($string);
  }

  function isAddEvent()
  {
    return (rex_request('btn_save', 'string') != '') && rex_request('function', 'string') == 'add';
  }

  function isEditEvent()
  {
    return (rex_request('btn_update', 'string') != '' || rex_request('btn_save', 'string') != '') && rex_request('function', 'string') == 'edit';
  }

  function isDeleteEvent()
  {
    return rex_request('function', 'string') == 'delete';
  }
}
?>