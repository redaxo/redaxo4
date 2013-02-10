<?php

$info = '';
$warning = '';
$func = rex_request("func","string");

$themes = array();
foreach (glob($REX["INCLUDE_PATH"]."/addons/be_style/plugins/agk_skin/files/codemirror/theme/*.css") as $filename) {
  $themes[] = substr(basename($filename),0,-4);
}

$tselect = new rex_select;
$tselect->setSize(1);
$tselect->setName('agk-skin-codemirror_theme');
$tselect->setStyle('class="rex-form-select"');
$tselect->setId('agk-skin-codemirror_theme');

foreach($themes as $theme) {
  $tselect->addOption($theme,$theme);
}

if ($func == 'update')
{

  $REX['ADDON']['be_style']['plugin_agk_skin']['codemirror_theme'] = htmlspecialchars(substr(rex_request("agk-skin-codemirror_theme","string"),0,20));

  $REX['ADDON']['be_style']['plugin_agk_skin']['labelcolor'] = htmlspecialchars(substr(rex_request("agk-skin-labelcolor","string"),0,20));

  $REX['ADDON']['be_style']['plugin_agk_skin']['codemirror'] = 0;
  if(rex_request("agk-skin-codemirror") == 1) {
    $REX['ADDON']['be_style']['plugin_agk_skin']['codemirror'] = 1;
  }

  $REX['ADDON']['be_style']['plugin_agk_skin']['showlink'] = 0;
  if(rex_request("agk-skin-showlink") == 1) {
    $REX['ADDON']['be_style']['plugin_agk_skin']['showlink'] = 1;
  }
 
  $content = '  
$REX[\'ADDON\'][\'be_style\'][\'plugin_agk_skin\'][\'labelcolor\'] = "'.$REX['ADDON']['be_style']['plugin_agk_skin']['labelcolor'].'";
$REX[\'ADDON\'][\'be_style\'][\'plugin_agk_skin\'][\'codemirror_theme\'] = "'.$REX['ADDON']['be_style']['plugin_agk_skin']['codemirror_theme'].'";
$REX[\'ADDON\'][\'be_style\'][\'plugin_agk_skin\'][\'codemirror\'] = '.$REX['ADDON']['be_style']['plugin_agk_skin']['codemirror'].';
$REX[\'ADDON\'][\'be_style\'][\'plugin_agk_skin\'][\'showlink\'] = '.$REX['ADDON']['be_style']['plugin_agk_skin']['showlink'].';
  ';

  $config_file = $REX['INCLUDE_PATH'] .'/addons/be_style/plugins/agk_skin/config.inc.php';
  if(rex_replace_dynamic_contents($config_file, $content) !== false) {
  	echo rex_info($I18N->msg("agk_skin_config_updated"));
  
  } else {
  	echo rex_warning($I18N->msg("agk_skin_config_update_failed",$config_file));
  
  }
  
}

$tselect->setSelected($REX['ADDON']['be_style']['plugin_agk_skin']['codemirror_theme']);

echo '
  <div class="rex-form" id="rex-form-system-setup">
    <form action="index.php" method="post">
      <input type="hidden" name="page" value="specials" />
      <input type="hidden" name="subpage" value="agk_skin" />
      <input type="hidden" name="func" value="update" />

      <div class="rex-area-col-2">
        <div class="rex-area-col-a">

          <h3 class="rex-hl2">'.$I18N->msg("agk_skin_features").'</h3>

          <div class="rex-area-content">
          
            <fieldset class="rex-form-col-1">

            <div class="rex-form-row">
              	<p class="rex-form-col-a rex-form-checkbox">
              		<label for="rex-agk-codemirror_check">'.$I18N->msg("agk_skin_codemirror_check").'</label>
              		<input class="rex-form-text" type="checkbox" id="rex-agk-codemirror_check" name="agk-skin-codemirror" value="1" ';
              if($REX['ADDON']['be_style']['plugin_agk_skin']['codemirror']) echo 'checked="checked"';
              echo ' />
              	</p>
            </div>

            <div class="rex-form-row">
              	<p class="rex-form-col-a rex-form-select">
              		<label for="rex-agk-codemirror_theme">'.$I18N->msg("agk_skin_codemirror_theme").'</label>
              		'.$tselect->get().'
              	</p>
            </div>

            <p>'.$I18N->msg("agk_skin_codemirror_info").'</p>

            </fieldset>

          </div>
        </div>

        <div class="rex-area-col-b">
          <h3 class="rex-hl2">'.$I18N->msg("agk_labeling").'</h3>

          <div class="rex-area-content">

            <fieldset class="rex-form-col-1">

              <div class="rex-form-wrapper">

                <div class="rex-form-row">
                  <p class="rex-form-col-a rex-form-text">
                    <label for="rex-form-agk-labelcolor">'.$I18N->msg("agk_skin_labelcolor").'</label>
                    <input class="rex-form-text" type="text" id="rex-form-agk-labelcolor" name="agk-skin-labelcolor" value="'. $REX['ADDON']['be_style']['plugin_agk_skin']['labelcolor'].'" />
                  </p>
                </div>

              <div class="rex-form-row">
              	<p class="rex-form-col-a rex-form-checkbox">
              		<label for="rex-form-agk-showlink">'.$I18N->msg("agk_skin_showlink").'</label>
              		<input class="rex-form-text" type="checkbox" id="rex-form-agk-showlink" name="agk-skin-showlink" value="1" ';
              if($REX['ADDON']['be_style']['plugin_agk_skin']['showlink']) echo 'checked="checked"';
              echo ' />
              	</p>
              </div>

                <div class="rex-form-row">
                  <p class="rex-form-col-a rex-form-submit">
                    <input type="submit" class="rex-form-submit" name="sendit" value="'.$I18N->msg("agk_skin_update").'" />
                  </p>
                </div>

            </fieldset>
          </div> <!-- Ende rex-area-content //-->

        </div> <!-- Ende rex-area-col-b //-->
      </div> <!-- Ende rex-area-col-2 //-->

    </form>
  </div>
  ';
