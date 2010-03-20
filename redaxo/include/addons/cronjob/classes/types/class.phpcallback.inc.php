<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_cronjob_phpcallback extends rex_cronjob
{ 
  /*public*/ function execute()
  {
    if (preg_match('/^\s*(?:(.*?)\:\:)?(.*?)(?:\((.*?)\))?\;?\s*$/', $this->getParam('callback'), $matches))
    {
      $callback = $matches[2];
      if ($matches[1] != '')
      {
        $callback = array($matches[1], $callback);
      }
      if(!is_callable($callback))
        return false;
      $params = array();
      if($matches[3] != '') 
      {
        $params = explode(',', $matches[3]);
        foreach($params as $i => $param)
        {
          $param = preg_replace('/^(\\\'|\")?(.*?)\\1$/', '$2', trim($param));
          $params[$i] = $param;
        }
      }
      return call_user_func_array($callback, $params) !== false;
    }
    return false;
  }
  
  /*public*/ function getTypeName()
  {
    global $I18N;
    return $I18N->msg('cronjob_type_phpcallback');
  }
  
  /*public*/ function getParamFields()
	{
		global $I18N;

		return array(
  		array(
        'label' => $I18N->msg('cronjob_type_phpcallback'),
        'name'  => 'callback',
        'type'  => 'text',
        'notice' => $I18N->msg('cronjob_examples') .': foo(), foo(1, \'string\'), foo::bar()'
      )
    );
	}
}