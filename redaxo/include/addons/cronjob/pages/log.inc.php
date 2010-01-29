<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$folder = REX_LOG_FOLDER;
$years = rex_a630_log_years($folder);

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

$max_year = 0;
$year_exists = false;
foreach($years as $i => $year)
{
  $files[$year] = rex_a630_log_files($folder, $year);
  if (empty($files[$year]))
  {
    unset($years[$i]);
    unset($files[$year]);
  }
  else
  {
    if ($year > $max_year)
      $max_year = $year;
      
    if ($year == $log['year'])
      $year_exists = true;
      
    $year_sel->addOption($year, $year);
  }
}

if (empty($years))
  echo $I18N->msg('cronjob_no_log_files');
else
{
  if(!$year_exists)
    $log['year'] = $max_year;
  $year_sel->setSelected($log['year']);
  
  $max_month = 0;
  $month_exists = false;
  $month_files = array();
  foreach($files[$log['year']] as $file)
  {
    $month = substr($file, -6, 2);
    $month_files[$month] = $file;
    $month_sel->addOption($month, $month);
    $max_month = $month;
    if ($month == $log['month'])
      $month_exists = true;
  }
  if(!$month_exists)
    $log['month'] = $max_month;
  $month_sel->setSelected($log['month']);
  
  echo '
    <form action="index.php" method="get">
      <fieldset>
        <input type="hidden" name="page" value="cronjob" />
        <input type="hidden" name="subpage" value="log" />
  ';
  
  $year_sel->show();
  echo ' - ';
  $month_sel->show();
  
  $file = $month_files[$log['month']];
  $content = rex_get_file_contents($file);
  $content = preg_replace('/^(.*?ERROR.*?)$/m','<strong style="color:red">$1</strong>',$content);
  
  echo '
      </fieldset>
    </form>
    <br />
    <pre>'.$content.'</pre>
  ';
}