<?php

/**
 * PHPMailer Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_mailer extends PHPMailer
{
  public $AdminBcc = '';

  function rex_mailer()
  {
    global $REX;

// --- DYN
$this->From             = 'from@example.com';
$this->FromName         = 'Mailer';
$this->ConfirmReadingTo = '';
$this->AdminBcc         = '';
$this->Mailer           = 'mail';
$this->Host             = 'localhost';
$this->CharSet          = 'utf-8';
$this->WordWrap         = 120;
$this->Encoding         = '8bit';
$this->Priority         = 3;
$this->SMTPAuth         = false;
$this->Username         = '';
$this->Password         = '';
// --- /DYN

    $this->PluginDir = $REX['INCLUDE_PATH'] . '/addons/phpmailer/classes/';

    if($this->AdminBcc !== ''){
      parent::AddBCC($this->AdminBcc);
    }
  }

  function SetLanguage($lang_type = "de", $lang_path = null)
  {
    global $REX;

    if ($lang_path == null)
      $lang_path = $REX['INCLUDE_PATH'] . '/addons/phpmailer/classes/language/';

    return parent :: SetLanguage($lang_type, $lang_path);
  }
}
