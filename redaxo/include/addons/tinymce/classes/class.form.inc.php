<?php

/**
 * TinyMCE Addon
 *
 * @author andreaseberhard[at]gmail[dot]com Andreas Eberhard
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

/*abstract*/ class rex_form_tinymce extends rex_form
{

  /*protected*/ function validate()
  {
    global $I18N;
    $msg = '';

    $el =& $this->getElement($this->fieldset, 'name');
    if (trim($el->getValue()) == '') {
      $msg .= $I18N->msg('tinymce_profiles_noname')."<br />";
    }
    if ( preg_match('/[^0-9A-Za-z]/', $el->getValue()) ) {
      $msg .= $I18N->msg('tinymce_profiles_nameinvalid')."<br />";
    }

    $el =& $this->getElement($this->fieldset, 'description');
    if (trim($el->getValue()) == '') {
      $msg .= $I18N->msg('tinymce_profiles_nodesc')."<br />";
    }

    $el =& $this->getElement($this->fieldset, 'configuration');
    if (trim($el->getValue()) == '') {
      $msg .= $I18N->msg('tinymce_profiles_noconfig')."<br />";
    }

    if ($msg<>'')
      return $msg;
    else
      return true;
  }

} // End class rex_form_tinymce
