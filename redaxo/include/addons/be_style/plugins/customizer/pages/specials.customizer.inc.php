<?php

$info = '';
$warning = '';
$func = rex_request("func","string");

$themes = array();
foreach (glob($REX["INCLUDE_PATH"]."/addons/be_style/plugins/customizer/files/codemirror/theme/*.css") as $filename) {
  $themes[] = substr(basename($filename),0,-4);
}

$tselect = new rex_select;
$tselect->setSize(1);
$tselect->setName('customizer-codemirror_theme');
$tselect->setStyle('class="rex-form-select"');
$tselect->setId('customizer-codemirror_theme');

foreach($themes as $theme) {
  $tselect->addOption($theme,$theme);
}

if ($func == 'update')
{

  $REX['ADDON']['be_style']['plugin_customizer']['codemirror_theme'] = htmlspecialchars(substr(rex_request("customizer-codemirror_theme","string"),0,20));

  $REX['ADDON']['be_style']['plugin_customizer']['projectname'] = htmlspecialchars(rex_request('customizer-projectname', 'string'));

  $labelcolor = rex_request("customizer-labelcolor","string");
  if ($labelcolor == '') {
    $REX['ADDON']['be_style']['plugin_customizer']['labelcolor'] = '';
  }
  elseif (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $labelcolor)) {
    $REX['ADDON']['be_style']['plugin_customizer']['labelcolor'] = htmlspecialchars($labelcolor);
  }
  else {
    $warning = $I18N->msg('customizer_labelcolor_error');
  }

  $REX['ADDON']['be_style']['plugin_customizer']['codemirror'] = 0;
  if(rex_request("customizer-codemirror") == 1) {
    $REX['ADDON']['be_style']['plugin_customizer']['codemirror'] = 1;
  }

  $REX['ADDON']['be_style']['plugin_customizer']['showlink'] = 0;
  if(rex_request("customizer-showlink") == 1) {
    $REX['ADDON']['be_style']['plugin_customizer']['showlink'] = 1;
  }

  $REX['ADDON']['be_style']['plugin_customizer']['textarea'] = 0;
  if(rex_request("customizer-textarea") == 1) {
    $REX['ADDON']['be_style']['plugin_customizer']['textarea'] = 1;
  }

  $REX['ADDON']['be_style']['plugin_customizer']['liquid'] = 0;
  if(rex_request("customizer-liquid") == 1) {
    $REX['ADDON']['be_style']['plugin_customizer']['liquid'] = 1;
  }
 
  $content = '
$REX[\'ADDON\'][\'be_style\'][\'plugin_customizer\'][\'labelcolor\'] = "'.$REX['ADDON']['be_style']['plugin_customizer']['labelcolor'].'";
$REX[\'ADDON\'][\'be_style\'][\'plugin_customizer\'][\'codemirror_theme\'] = "'.$REX['ADDON']['be_style']['plugin_customizer']['codemirror_theme'].'";
$REX[\'ADDON\'][\'be_style\'][\'plugin_customizer\'][\'codemirror\'] = '.$REX['ADDON']['be_style']['plugin_customizer']['codemirror'].';
$REX[\'ADDON\'][\'be_style\'][\'plugin_customizer\'][\'showlink\'] = '.$REX['ADDON']['be_style']['plugin_customizer']['showlink'].';
$REX[\'ADDON\'][\'be_style\'][\'plugin_customizer\'][\'textarea\'] = '.$REX['ADDON']['be_style']['plugin_customizer']['textarea'].';
$REX[\'ADDON\'][\'be_style\'][\'plugin_customizer\'][\'liquid\'] = '.$REX['ADDON']['be_style']['plugin_customizer']['liquid'].';
  ';

  $config_file = $REX['INCLUDE_PATH'] .'/addons/be_style/plugins/customizer/config.inc.php';
  if($warning == '' && rex_replace_dynamic_contents($config_file, $content) !== false) {
  	echo rex_info($I18N->msg("customizer_config_updated"));
  
  } else {
  	echo rex_warning($I18N->msg("customizer_config_update_failed",$config_file));
  
  }
  
}

$tselect->setSelected($REX['ADDON']['be_style']['plugin_customizer']['codemirror_theme']);

if ($warning != '') {
  echo rex_warning($warning);
}

echo '
  <div class="rex-form" id="rex-form-system-setup">
    <form action="index.php" method="post">
      <input type="hidden" name="page" value="specials" />
      <input type="hidden" name="subpage" value="customizer" />
      <input type="hidden" name="func" value="update" />

      <div class="rex-area-col-2">
        <div class="rex-area-col-a">

          <h3 class="rex-hl2">'.$I18N->msg("customizer_features").'</h3>

          <div class="rex-area-content">
          
            <fieldset class="rex-form-col-1">

            <div class="rex-form-row">
              	<p class="rex-form-col-a rex-form-checkbox">
              		<label for="rex-agk-codemirror_check">'.$I18N->msg("customizer_codemirror_check").'</label>
              		<input class="rex-form-text" type="checkbox" id="rex-agk-codemirror_check" name="customizer-codemirror" value="1" ';
              if($REX['ADDON']['be_style']['plugin_customizer']['codemirror']) echo 'checked="checked"';
              echo ' />
              	</p>
            </div>

            <div class="rex-form-row">
              	<p class="rex-form-col-a rex-form-select">
              		<label for="rex-agk-codemirror_theme">'.$I18N->msg("customizer_codemirror_theme").'</label>
              		'.$tselect->get().'
              	</p>
            </div>

            <p>'.$I18N->msg("customizer_codemirror_info").'</p>

            </fieldset>

          </div>
        </div>

        <div class="rex-area-col-b">
          <h3 class="rex-hl2">'.$I18N->msg("customizer_labeling").'</h3>

          <div class="rex-area-content">

            <fieldset class="rex-form-col-1">

              <div class="rex-form-wrapper">

                <div class="rex-form-row">
                  <p class="rex-form-col-a rex-form-text">
                    <label for="rex-form-agk-labelcolor">'.$I18N->msg("customizer_labelcolor").'</label>
                    <input class="rex-form-text" type="text" id="rex-form-agk-labelcolor" name="customizer-labelcolor" value="'. $REX['ADDON']['be_style']['plugin_customizer']['labelcolor'].'" />
                  </p>
                </div>

                <div class="rex-form-row">
                  <p class="rex-form-col-a rex-form-checkbox">
                    <input class="rex-form-checkbox" type="checkbox" id="rex-form-agk-showlink" name="customizer-showlink" value="1" ';
                    if($REX['ADDON']['be_style']['plugin_customizer']['showlink']) echo 'checked="checked"';
                    echo ' />
                    <label for="rex-form-agk-showlink">'.$I18N->msg("customizer_showlink").'</label>
                  </p>
                </div>

                <div class="rex-form-row">
                  <p class="rex-form-col-a rex-form-checkbox">
                    <input class="rex-form-checkbox" type="checkbox" id="rex-form-agk-textarea" name="customizer-textarea" value="1" ';
                    if($REX['ADDON']['be_style']['plugin_customizer']['textarea']) echo 'checked="checked"';
                    echo ' />
                    <label for="rex-form-agk-textarea">'.$I18N->msg("customizer_textarea").'</label>
                  </p>
                </div>

                <div class="rex-form-row">
                  <p class="rex-form-col-a rex-form-checkbox">
                    <input class="rex-form-checkbox" type="checkbox" id="rex-form-agk-liquid" name="customizer-liquid" value="1" ';
                    if($REX['ADDON']['be_style']['plugin_customizer']['liquid']) echo 'checked="checked"';
                    echo ' />
                    <label for="rex-form-agk-liquid">'.$I18N->msg("customizer_liquid").'</label>
                  </p>
                </div>

                <div class="rex-form-row">
                  <p class="rex-form-col-a rex-form-submit">
                    <input type="submit" class="rex-form-submit" name="sendit" value="'.$I18N->msg("customizer_update").'" />
                  </p>
                </div>

               </div>

            </fieldset>
          </div> <!-- Ende rex-area-content //-->

        </div> <!-- Ende rex-area-col-b //-->
      </div> <!-- Ende rex-area-col-2 //-->

    </form>
  </div>
  ';
