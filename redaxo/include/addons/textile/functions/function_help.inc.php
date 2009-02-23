<?php
/**
 * Textile Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 * @package redaxo4
 * @version $Id: function_help.inc.php,v 1.5 2008/03/11 16:04:40 kills Exp $
 */

function rex_a79_help_overview()
{
  global $REX, $I18N_A79;

  // check perms
  if(!$REX['USER']->hasPerm('textile[help]'))
  {
  	return;
  }

  $formats = rex_a79_help_overview_formats();

  echo '<div class="a79_help_overview">
          <h3 class="a79">'. $I18N_A79->msg('instructions') .'</h3>
          <table style="width: 100%">
            <colgroup>
              <col width="50%" />
              <col width="50%" />
            </colgroup>
        ';
  foreach($formats as $format)
  {
    $label = $format[0];
    $id = preg_replace('/[^a-zA-z0-9]/', '', htmlentities($label));

    echo '
            <thead>
              <tr>
                <th colspan="3"><a href="#" onclick="toggleElement(\''. $id .'\'); return false;">'. htmlspecialchars($label) .'</a></th>
              </tr>
            </thead>

            <tbody id="'. $id .'" style="display: none">
              <tr>
                <th>'. $I18N_A79->msg('input') .'</th>
                <th>'. $I18N_A79->msg('preview') .'</th>
              </tr>
           ';

    foreach($format[1] as $perm => $formats)
    {
      foreach($formats as $_format)
      {
        $desc = $_format[0];

        $code = '';
        if(isset($_format[1]))
        	$code = $_format[1];

        if($code == '')
          $code = $desc;

        $code = trim(rex_a79_textile($code));

        echo '<tr>
                <td>'. nl2br(htmlspecialchars($desc)) .'</td>
                <td>'. $code .'</td>
              </tr>
              ';
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

function rex_a79_help_headlines()
{
  global $I18N_A79;

  return array($I18N_A79->msg('headlines'),
    array(
    'headlines1-3' =>
      array(
        array('h1. '. $I18N_A79->msg('headline') .' 1'),
        array('h2. '. $I18N_A79->msg('headline') .' 2'),
        array('h3. '. $I18N_A79->msg('headline') .' 3'),
      ),
    'headlines4-6' =>
      array(
        array('h4. '. $I18N_A79->msg('headline') .' 4'),
        array('h5. '. $I18N_A79->msg('headline') .' 5'),
        array('h6. '. $I18N_A79->msg('headline') .' 6'),
      ),
    )
  );
}

function rex_a79_help_formats()
{
  global $I18N_A79;

  return array($I18N_A79->msg('text_formatting'),
    array(
    'text_xhtml' =>
      array(
        array('_'. $I18N_A79->msg('text_italic') .'_'),
        array('*'. $I18N_A79->msg('text_bold') .'*'),
      ),
    'text_html' =>
      array(
        array('__'. $I18N_A79->msg('text_italic') .'__'),
        array('**'. $I18N_A79->msg('text_bold') .'**'),
      ),
    'cite' =>
      array(
        array('bq. '. $I18N_A79->msg('text_cite')),
        array('??'. $I18N_A79->msg('text_source_author') .'??'),
      ),
    'overwork' =>
      array(
        array('-'. $I18N_A79->msg('text_strike') .'-'),
        array('+'. $I18N_A79->msg('text_insert') .'+'),
        array('^'. $I18N_A79->msg('text_sup') .'^'),
        array('~'. $I18N_A79->msg('text_sub') .'~'),
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
  global $I18N_A79;

  return array($I18N_A79->msg('links'),
    array(
    'links_intern' =>
      array(
        array ($I18N_A79->msg('link_internal') .':redaxo://5'),
        array ($I18N_A79->msg('link_internal_anchor') .':redaxo://7#AGB'),
      ),
    'links_extern' =>
      array(
        array ($I18N_A79->msg('link_external') .':http://doku.redaxo.de'),
        array ($I18N_A79->msg('link_external_anchor') .':http://www..redaxo.de#news'),
      ),
    'anchor' =>
      array(
        array ($I18N_A79->msg('link_anchor') .":\n\np(#Impressum). ". $I18N_A79->msg('link_anchor_text')),
      ),
    )
  );
}

function rex_a79_help_footnotes()
{
  global $I18N_A79;

  return array($I18N_A79->msg('footnotes'),
    array(
    'footnotes' =>
      array(
        array($I18N_A79->msg('footnote_text'). '[1] ..'),
        array('fn1. '. $I18N_A79->msg('footnote_note')),
      ),
    )
  );
}

function rex_a79_help_lists()
{
  global $I18N_A79;

  return array($I18N_A79->msg('lists'),
    array(
    'lists' =>
      array(
        array($I18N_A79->msg('numeric_list') .":\n# redaxo.de\n# forum.redaxo.de"),
        array($I18N_A79->msg('enum_list') .":\n* redaxo.de\n* forum.redaxo.de"),
      )
    )
  );
}

function rex_a79_help_tables()
{
  global $I18N_A79;

  return array($I18N_A79->msg('tables'),
    array(
    'tables' =>
      array(
        array("|_. Id|_. Name|\n|1|Peter|"),
        array("|www.redaxo.de|35|\n|doku.redaxo.de|32|\n|wiki.redaxo.de|12|"),
      )
  ));
}