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

    // this is the new style constructor used by newer php versions.
    // important: if you change the signatur of this method, change also the signature of rex_mailer()
    function __construct()
    {
        $this->rex_mailer();
    }

     // this is the deprecated old style constructor kept for compat reasons. 
    // important: if you change the signatur of this method, change also the signature of __construct()
    function rex_mailer()
    {
        global $REX;

        $this->From             = 'from@example.com';
        $this->FromName         = 'Mailer';
        $this->ConfirmReadingTo = '';
        $this->AdminBcc         = '';
        $this->Mailer           = 'mail';
        $this->Host             = 'localhost';
        $this->Port             = 25;
        $this->CharSet          = 'utf-8';
        $this->WordWrap         = 120;
        $this->Encoding         = '8bit';
        $this->Priority         = 3;
        $this->SMTPSecure       = '';
        $this->SMTPAuth         = false;
        $this->Username         = '';
        $this->Password         = '';

        $settings = rex_path::addonData('phpmailer', 'settings.inc.php');
        if (file_exists($settings)) {
            include $settings;
        }

        $this->PluginDir = $REX['INCLUDE_PATH'] . '/addons/phpmailer/classes/';

        if ($this->AdminBcc !== '') {
            parent::AddBCC($this->AdminBcc);
        }
    }

    function SetLanguage($lang_type = 'de', $lang_path = null)
    {
        global $REX;

        if ($lang_path == null) {
            $lang_path = $REX['INCLUDE_PATH'] . '/addons/phpmailer/classes/language/';
        }

        return parent :: SetLanguage($lang_type, $lang_path);
    }
}
