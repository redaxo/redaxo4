<?php


/**
 * Addon Framework Classes 
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http//www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id class.rex_form.inc.php,v 1.3 2006/09/07 104351 kills Exp $
 */

$testMailer = new rex_mailer();

$fromname = rex_post('fromname', 'string', $testMailer->FromName);
$from = rex_post('from', 'string', $testMailer->From);
$confirmto = rex_post('confirmto', 'string', $testMailer->ConfirmReadingTo);
$mailer = rex_post('mailer', 'string', $testMailer->Mailer);
$host = rex_post('host', 'string', $testMailer->Host);
$charset = rex_post('charset', 'string', $testMailer->CharSet);
$wordwrap = rex_post('wordwrap', 'int', $testMailer->WordWrap);
$encoding = rex_post('encoding', 'string', $testMailer->Encoding);
$priority = rex_post('priority', 'int', $testMailer->Priority);

$message = '';

if (rex_post('btn_save', 'string') != '')
{
  $message  = 'Fehler beim speichern der Konfiguration!';
  $file = $REX['INCLUDE_PATH'] .'/addons/phpmailer/classes/class.rex_mailer.inc.php';
  if($hdl = fopen($file,'r'))
  {
    $file_content = fread($hdl, filesize($file));
    fclose($hdl);
    
    $template =
    "// --- DYN
    \$this->From             = '". $from ."';
    \$this->FromName         = '". $fromname ."';
    \$this->ConfirmReadingTo = '". $confirmto ."';
    \$this->Mailer           = '". $mailer ."';
    \$this->Host             = '". $host ."';
    \$this->CharSet          = '". $charset ."';
    \$this->WordWrap         = ". $wordwrap .";
    \$this->Encoding         = '". $encoding ."';
    \$this->Priority         = ". $priority .";
    // --- /DYN";
    
    $file_content = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", $template, $file_content);
    
    if($hdl = fopen($file, 'w+'))
    {
      if(fwrite($hdl, $file_content, strlen($file_content)))
      {
        $message = 'Konfiguration erfolgreich gespeichert!';
        fclose($hdl);
      }
    }
  }
}

$sel_mailer = new rex_select();
$sel_mailer->setId('mailer');
$sel_mailer->setName('mailer');
$sel_mailer->setSize(1);
$sel_mailer->setSelected($mailer);
foreach(array('mail', 'sendmail', 'smtp') as $type)
  $sel_mailer->addOption($type,$type);
  
$sel_encoding = new rex_select();
$sel_encoding->setId('encoding');
$sel_encoding->setName('encoding');
$sel_encoding->setSize(1);
$sel_encoding->setSelected($encoding);
foreach(array('7bit', '8bit', 'binary', 'base64', 'quoted-printable') as $enc)
  $sel_encoding->addOption($enc,$enc);

$sel_priority = new rex_select();
$sel_priority->setid('priority');
$sel_priority->setName('priority');
$sel_priority->setSize(1);
$sel_priority->setSelected($priority);
foreach(array(1 =>'Hoch',3 => 'Normal',5 => 'Niedrig') as $no => $name)
  $sel_priority->addOption($name,$no);

if($message != '')
  echo '<p class="rex-warning">'. $message .'</p>';
  
?>
<form action="" method="post">
  <fieldset>
    <legend class="rex-lgnd">Einstellungen</legend>
    <p>
      <label for="fromname">Absender (Name)</label>
      <input type="text" name="fromname" id="fromname" value="<?php echo $fromname ?>" />
    </p>
    <p>
      <label for="from">Absender (E-Mail)</label>
      <input type="text" name="from" id="from" value="<?php echo $from ?>" />
    </p>
    <p>
      <label for="confirmto">Lesebestätigung (E-Mail)</label>
      <input type="text" name="confirmto" id="confirmto" value="<?php echo $confirmto ?>" />
    </p>
    <p>
      <label for="mailer">Mailertype</label>
      <?php echo $sel_mailer->show(); ?>
    </p>
    <p>
      <label for="host">Host</label>
      <input type="text" name="host" id="host" value="<?php echo $host ?>" />
    </p>
    <p>
      <label for="charset">Zeichensatz</label>
      <input type="text" name="charset" id="charset" value="<?php echo $charset ?>" />
    </p>
    <p>
      <label for="wordwrap">WordWrap</label>
      <input type="text" name="wordwrap" id="wordwrap" value="<?php echo $wordwrap ?>" />
    </p>
    <p>
      <label for="encoding">Mailkodierung</label>
      <?php echo $sel_encoding->show(); ?>
    </p>
    <p>
      <label for="priority">Priorität</label>
      <?php echo $sel_priority->show(); ?>
    </p>
    <p>
      <input type="submit" name="btn_save" value="Speichern" />
      <input type="reset" name="btn_reset" value="Verwerfen" onclick="return confirm('Änderungen verwerfen?');"/>
    </p>
  </fieldset>
</form>