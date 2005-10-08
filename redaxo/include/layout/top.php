<?php 
if (!isset($page_name)) $page_name = ''; 

$page_title = $REX['SERVERNAME'];
if ( $page_name != '') {
   $page_title .= ' - '. $page_name;
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="<?php echo $I18N->msg("htmllang"); ?>">
<head>
  <title><?php echo $page_title ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $I18N->msg("htmlcharset"); ?>" />
  <meta http-equiv="Content-Language" content="<?php echo $I18N->msg("htmllang"); ?>" />
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <script src="js/standard.js" type="text/javascript"></script>
  <script type="text/javascript">
  <!--
  var redaxo = true;
  //-->
  </script>
</head>
<body onunload="closeAll();">
<table class="rexHeader" cellpadding="5" cellspacing="0">
  <tr>
    <th colspan="2"><?php echo $REX['SERVERNAME']; ?></th>
  </tr>
  <tr>
    <td><?php
    if ($LOGIN)
    {
      echo "<a href=index.php?page=structure class=white>".$I18N->msg("structure")."</a> ";
      echo " | <a href=# onclick=openMediaPool(); class=white>".$I18N->msg("pool_name")."</a>";
      if ($REX_USER->isValueOf("rights","template[]") || $REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","dev[]")) echo " | <a href=index.php?page=template class=white>".$I18N->msg("template")."</a>";
      if ($REX_USER->isValueOf("rights","module[]") || $REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","dev[]")) echo " | <a href=index.php?page=module class=white>".$I18N->msg("module")."</a>"; 
      if ($REX_USER->isValueOf("rights","user[]") || $REX_USER->isValueOf("rights","admin[]")) echo " | <a href=index.php?page=user class=white>".$I18N->msg("user")."</a>"; 
      if ($REX_USER->isValueOf("rights","addon[]") || $REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","dev[]")) echo " | <a href=index.php?page=addon class=white>".$I18N->msg("addon")."</a>"; 
      if ($REX_USER->isValueOf("rights","specials[]") || $REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","dev[]")) echo " | <a href=index.php?page=specials class=white>".$I18N->msg("specials")."</a>"; 
      
      if (is_Array($REX['ADDON']['status'])) reset($REX['ADDON']['status']);
      for ($i=0; $i < count($REX['ADDON']['status']); $i++)
      {
        $apage = key($REX['ADDON']['status']);
        if (isset($REX['ADDON']['perm'][$apage])) { $perm = $REX['ADDON']['perm'][$apage]; } else { $perm = ''; }
        if (isset($REX['ADDON']['name'][$apage])) { $name = $REX['ADDON']['name'][$apage]; } else { $name = ''; }
        if (isset($REX['ADDON']['popup'][$apage])) { $popup = $REX['ADDON']['popup'][$apage]; } else { $popup = ''; }
        if (current($REX['ADDON']['status']) == 1 && $REX['ADDON']['name'][$apage] != '' && ($REX_USER->isValueOf("rights",$perm) || $perm == "" || $REX_USER->isValueOf("rights","admin[]")) )
        {
          if ($popup == 1) echo " | <a href=javascript:newPoolWindow('index.php?page=$apage'); class=white>$name</a>";
          else if ($popup == "" or $popup == 0) echo " | <a href=index.php?page=$apage class=white>$name</a>";
          else echo " | <a href=\"javascript:$popup\" class=white>$name</a>";
        }
        next($REX['ADDON']['status']);
      }
    }
    ?></td>
    <?php if ($LOGIN): ?><td class="logstatus" valign="top">
      <span class="label"><?php echo $I18N->msg('name'); ?> : </span>
    <span class="username"><?php echo $REX_USER->getValue('name'); ?></span>
    <span class="logout" style="font-weight: normal;">[<a href="index.php?FORM[logout]=1" class="white" style="font-weight: bold;"><?php echo $I18N->msg('logout'); ?></a>]</span>
    </td><?php else: ?> 
      <td valign="top" style="text-align: right"><?php echo $I18N->msg('logged_out') ?></td>
  <?php endif; ?></tr>
</table>
