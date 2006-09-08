<?php
/**
 * 
 * @package redaxo3
 * @version $Id$
 */
 
function rex_a79_help_overview()
{
  global $REX_USER;
  
  $formats = rex_a79_help_overview_formats();

  echo '<div class="a79_help_overview">';  
  echo '<h3 class="a79">Anleitung/Hinweise</h3>';
  echo '<table style="width: 100%">';
  foreach($formats as $format)
  {
    $label = $format[0];
    $id = preg_replace('/[^a-zA-z0-9]/', '', htmlentities($label));
    
    echo '
          <thead>  
            <tr>  
              <th colspan="3"><a href="#" onclick="return toggleElement(\''. $id .'\');">'. $label .'</a></th>  
            </tr>  
          </thead>
         ';
    
    echo '<tbody id="'. $id .'" style="display: none">  
            <tr>  
              <th>Ausgabe</th>  
              <th>Eingabe</th>  
              <th>HTML-Code</th>  
            </tr>
           ';
            
    foreach($format[1] as $perm => $formats)
    {
      if($perm == '' || $REX_USER->hasPerm('admin[]') || $REX_USER->hasPerm('textile['. $perm .']'))
      {
        foreach($formats as $_format)
        {
          $desc = $_format[0];
          $code = $_format[1];
          
          if($code == '')
            $code = $desc;
            
          $code = trim(rex_a79_textile($code));
            
          echo '<tr>  
                  <td>'. $code .'</td>  
                  <td>'. nl2br(htmlspecialchars($desc)) .'</td>  
                  <td>'. htmlspecialchars($code) .'</td>  
                </tr>
                ';
        }
      }
    }
    
    echo '</tbody>';
  }
  echo '</table>';
  echo '</div>';
}

function rex_a79_help_overview_formats()
{
  return array(
    rex_a79_help_headlines(),
    rex_a79_help_formats(),
    rex_a79_help_links(),
    rex_a79_help_footnotes(),
    rex_a79_help_lists(),
    rex_a79_help_tables(),
  );
}

function rex_a79_help_overview_perms()
{
  $perms = array();
  $formats = rex_a79_help_overview_formats();
  
  foreach($formats as $format)
  {
    foreach($format[1] as $perm => $formats)
    {
      if($perm == '') continue;
      
      $perms[] = 'textile['. $perm .']';
    }
  }
  
  return $perms;  
}
function rex_a79_help_headlines()
{
  return array( 'Überschriften',
    array(
    'headlines1-3' =>
      array(
        array('h1. Überschrift1'),
        array('h2. Überschrift2'),
        array('h3. Überschrift3'),
      ),
    'headlines4-6' =>
      array(
        array('h4. Überschrift4'),
        array('h5. Überschrift5'),
        array('h6. Überschrift6'),
      ),
    )
  );
}

function rex_a79_help_formats()
{
  return array( 'Textformatierungen',
    array(
    'text_xhtml' =>
      array(
        array('_Kursiv_'),
        array('*Fett*'),
      ),
    'text_html' =>
      array(
        array('__Kursiv__'),
        array('**Fett**'),
      ),
    'cite' =>
      array(
        array('bq. Zitat'),
        array('??Quelle/Autor??'),
      ),
    'overwork' =>
      array(
        array('-Durchgestrichen-'),
        array('+Eingefügt+'),
      ),
    'overwork' =>
      array(
        array('^Hochgestellt^'),
        array('~Tiefgestellt~'),
      ),
    'code' =>
      array(
        array('@<?php echo "Hi"; ?>@'),
      ),
    )
  );
}

function rex_a79_help_links()
{
  return array( 'Links/Anker',
    array(
    'links_intern' =>
      array(
        array ("Link (intern):\n \"zum Impressum\":redaxo://5"),
        array ("Link (intern) mit Anker:\n \"zu unseren AGBs\":redaxo://7#AGB"),
      ),
    'links_extern' =>
      array(
        array ("Link (extern):\n \"zur REDAXO Dokumentation\":http://doku.redaxo.de"),
        array ("Link (extern) mit Anker:\n \"zu unserem Parnter\":http://www.unser-partner.de#News"),
      ),
    'anchor' =>
      array(
        array ("Anker definieren:\n\np(#Impressum). Hier steht das Impressum"),
      ),
    )
  );
}

function rex_a79_help_footnotes()
{
  return array( 'Fußnoten',
    array(
    'footnotes' =>
      array(
        array('AJAX[1] ist..'),
        array('fn1. Asynchronous JavaScript and XML'),
      ),
    )
  );
}

function rex_a79_help_lists()
{
  return array( 'Listen',
    array(
    'lists' =>
      array(
        array("Nummerierte-Liste:\n# redaxo.de\n# forum.redaxo.de"),
        array("Aufzählungs-Liste:\n* redaxo.de\n* forum.redaxo.de"),
      )
    )
  );
}

function rex_a79_help_tables()
{
  return array( 'Tabellen',
    array(
    'tables' =>
      array(
        array("|_. Id|_. Name|\n|1|Peter|"),
        array("|www.redaxo.de|35|\n|doku.redaxo.de|32|\n|wiki.redaxo.de|12|"),
      )
  ));
}

?>