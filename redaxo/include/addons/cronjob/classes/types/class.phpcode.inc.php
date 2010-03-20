<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_cronjob_phpcode extends rex_cronjob
{ 
  /*public*/ function execute()
  {
    $code = preg_replace('/^\<\?(?:php)?/', '', $this->getParam('code'));
    $success = eval($code) !== false;
    return $success;
  }
  
  /*public*/ function getTypeName()
  {
    global $I18N;
    return $I18N->msg('cronjob_type_phpcode');
  }
  
  /*public*/ function getParamFields()
	{
		global $I18N;

		return array(
  		array(
        'label' => $I18N->msg('cronjob_type_phpcode'),
        'name'  => 'code',
        'type'  => 'textarea',
        'attributes' => array('rows' => 20)
      )
    );
	}
}