<?php

/**
 * Image-Resize Addon
 * 
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author marcus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]yakmara[dot]de Jan Kristinus
 * 
 * @package redaxo4
 * @version $Id$
 */

// rex_request();

$func = rex_request('func', 'string');
$max_cachefiles = rex_request('max_cachefiles', 'int');
$max_filters = rex_request('max_filters', 'int');
$max_resizekb = rex_request('max_resizekb', 'int');
$max_resizepixel = rex_request('max_resizepixel', 'int');


if ($func == "update")
{
	
	$REX['ADDON']['image_resize']['max_cachefiles'] = $max_cachefiles;
	$REX['ADDON']['image_resize']['max_filters'] = $max_filters;
	$REX['ADDON']['image_resize']['max_resizekb'] = $max_resizekb;
	$REX['ADDON']['image_resize']['max_resizepixel'] = $max_resizepixel;
	$REX['ADDON']['image_resize']['jpg_quality'] = $jpg_quality;
	
	$content = '$REX[\'ADDON\'][\'image_resize\'][\'max_cachefiles\'] = '.$max_cachefiles.';
$REX[\'ADDON\'][\'image_resize\'][\'max_filters\'] = '.$max_filters.';
$REX[\'ADDON\'][\'image_resize\'][\'max_resizekb\'] = '.$max_resizekb.';
$REX[\'ADDON\'][\'image_resize\'][\'max_resizepixel\'] = '.$max_resizepixel.';
$REX[\'ADDON\'][\'image_resize\'][\'jpg_quality\'] = '.$jpg_quality.';
';

	$file = $REX['INCLUDE_PATH']."/addons/image_resize/config.inc.php";
  rex_replace_dynamic_contents($file, $content);

  echo '<p class="rex-warning"><span>' . 'Konfiguration wurde aktualisiert' . '</span></p>';

}

?>

<style>

#rex-page-image-resize div.rex-addon-content form p{
padding-bottom:10px;
}

#rex-page-image-resize div.rex-addon-content form p label {
width:300px;
}

</style>

<?php

echo '

<div class="rex-addon-output">
  <h2>Konfiguration</h2>
  <div class="rex-addon-content">

  <form action="index.php" method="post">
    <input type="hidden" name="page" value="image_resize" />
    <input type="hidden" name="subpage" value="config" />
    <input type="hidden" name="func" value="update" />

        <fieldset>
          <p>
            <label for="max_cachefiles">Maximale Anzahl von Cachefiles pro Datei</label>
            <input type="text" id="max_cachefiles" name="max_cachefiles" value="'. htmlspecialchars($REX['ADDON']['image_resize']['max_cachefiles']).'" />
          </p>
          <p>
            <label for="max_filters">Maximale Anzahl von Filtern, die auf eine Datei gleichzeitig angewendet werden k&ouml;nnen</label>
            <input type="text" id="max_filters" name="max_filters" value="'. htmlspecialchars($REX['ADDON']['image_resize']['max_filters']).'" />
          </p>
          <p>
            <label for="max_resizekb">Maximale Gr&ouml;sse einer Datei, die &uuml;ber Image-Resize umgewandelt werden darf</label>
            <input type="text" id="max_resizekb" name="max_resizekb" value="'. htmlspecialchars($REX['ADDON']['image_resize']['max_resizekb']).'" />
          </p>
          <p>
            <label for="max_resizepixel">Maximale Gr&ouml;sse einer Datei in Pixel, die &uuml;ber Image-Resize umgewandelt werden darf</label>
            <input type="text" id="max_resizepixel" name="max_resizepixel" value="'. htmlspecialchars($REX['ADDON']['image_resize']['max_resizepixel']).'" />
          </p>
          <p>
            <label for="jpg_quality">Optional bei JPG Bildern: JPG Qualit&auml;t [0-100]</label>
            <input type="text" id="jpg_quality" name="jpg_quality" value="'. htmlspecialchars($REX['ADDON']['image_resize']['jpg_quality']).'" />
          </p>
          <p>
            <input type="submit" class="rex-sbmt" name="sendit" value="'.$I18N->msg("update").'" />
          </p>
        </fieldset>
  </form>
  </div>
</div>
  ';

?>