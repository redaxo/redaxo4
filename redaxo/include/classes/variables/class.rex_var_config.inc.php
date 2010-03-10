<?php

/**
 * REX_CONFIG[field=xzy]
 * 
 * Attribute:
 *   - field    => Feld, das ausgegeben werden soll
 *   
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_var_config extends rex_var
{
  // --------------------------------- Output

  /*public*/ function getTemplate($content)
  {
    return $this->matchConfig($content);
  }

  /*public*/ function getBEOutput(& $sql, $content)
  {
    return $this->matchConfig($content);
  }
  
  /**
   * Werte für die Ausgabe
   */
  /*private*/ function matchConfig($content)
  {
  	global $REX;

    $var = 'REX_CONFIG';
    $matches = $this->getVarParams($content, $var);
    
    foreach ($matches as $match)
    {
    	list ($param_str, $args)   = $match;
      list ($field, $args)       = $this->extractArg('field', $args, '');
      
      $field = strtoupper($field);
      $tpl = '';

      $varname = '$REX[\''. addslashes($field) .'\']';
      $tpl = '<?php
      if(isset('. $varname .')) echo htmlspecialchars('. $this->handleGlobalVarParamsSerialized($var, $args, $varname) .');
      ?>';
      
      if($tpl != '')
        $content = str_replace($var . '[' . $param_str . ']', $tpl, $content);
    }

    return $content;
  }
}