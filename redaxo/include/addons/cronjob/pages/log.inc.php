<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$year_sel = new rex_select();
$year_sel->setSize(1);
$year_sel->setName('log[year]');
$year_sel->setAttribute('class','rex-form-select');
$year_sel->setAttribute('onchange','this.form.submit();');
$year_sel->setStyle('width: 100px');

$month_sel = new rex_select();
$month_sel->setSize(1);
$month_sel->setName('log[month]');
$month_sel->setAttribute('class','rex-form-select');
$month_sel->setAttribute('onchange','this.form.submit();');
$month_sel->setStyle('width: 100px');

$log = rex_request('log', 'array', array());
if(!isset($log['year']) || !$log['year'])
  $log['year'] = date('Y');
if(!isset($log['month']) || !$log['month'])
  $log['month'] = date('m');
  
$array = rex_cronjob_log::getYearMonthArray();

if (empty($array))
  echo $I18N->msg('cronjob_no_log_files');
else
{
  $countYears = count($array);
  $i = 0;
  $yearSelected = false;
  foreach($array as $year => $months)
  {
    $i++;
    $year_sel->addOption($year, $year);
    if ($year == $log['year'] || (!$yearSelected && $i == $countYears))
    {
      $year_sel->setSelected($year);
      $log['year'] = $year;
      $yearSelected = true;
      $countMonths = count($months);
      $j = 0;
      $monthSelected = false;
      foreach($months as $month)
      {
        $j++;
        $month_sel->addOption(strftime('%B',mktime(0,0,0,$month)), $month);
        if ($month == $log['month'] || (!$monthSelected && $j == $countMonths))
        {
          $month_sel->setSelected($month);
          $log['month'] = $month;
          $monthSelected = true;
        }
      }
    }
  }
  
  echo '
    <div class="rex-toolbar rex-toolbar-has-form">
      <div class="rex-toolbar-content">
        <div class="rex-form">
          <form action="index.php" method="get">
            <fieldset>
              <input type="hidden" name="page" value="cronjob" />
              <input type="hidden" name="subpage" value="log" />
              <label for="" style="font-weight: bold">'.$I18N->msg('cronjob_log_year').':</label>
              '.$year_sel->get().' - 
              <label for="" style="font-weight: bold">'.$I18N->msg('cronjob_log_month').':</label>
              '.$month_sel->get().'
              <noscript>
                <input type="submit" value="'.$I18N->msg('cronjob_log_ok').'" />
              </noscript>
            </fieldset>
          </form>
        </div>
        <div class="rex-clearer"></div>
      </div>
    </div>';
  
  $content = rex_cronjob_log::getLogOfMonth($log['month'], $log['year']);
  $content = preg_replace('/^(.*?ERROR.*?)$/m','<strong style="color:red">$1</strong>',$content);
  
  echo '
    <table cellpadding=5 class="rex-table">
      <tr><td style="font-family: Courier, Monospace; white-space: pre;">'.$content.'</td></tr>
    </table>
  ';
}