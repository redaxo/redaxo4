<?

title($I18N->msg("title_user"),"");

if ($FUNC_UPDATE != "")
{
	$updateuser = new sql;
	$updateuser->setTable("rex_user");
	$updateuser->where("user_id='$user_id'");
	$updateuser->setValue("name",$username);
	$updateuser->setValue("psw",$userpsw);
	$updateuser->setValue("rights",$userrights);
	$updateuser->update();
	$user_id = 0;
	unset($FUNC_UPDATE);
	$message = $I18N->msg("user_data_updated");

}elseif($FUNC_DELETE != "")
{
	if ($REX_UID!=$user_id)
	{
		$deleteuser = new sql;
		$deleteuser->query("delete from rex_user where user_id='$user_id'");
		$message = $I18N->msg("user_deleted");
	}

}elseif($FUNC_ADD != "" && $save == 1)
{
	$adduser = new sql;
	$adduser->setQuery("select * from rex_user where login='$userlogin'");

	if ($adduser->getRows()==0 or $userlogin == "")
	{
		$adduser = new sql;
		$adduser->setTable("rex_user");
		$adduser->setValue("name",$username);
		$adduser->setValue("psw",$userpsw);
		$adduser->setValue("login",$userlogin);
		$adduser->setValue("rights",$userrights);
		$adduser->insert();
		$user_id = 0;
		unset($FUNC_ADD);
		$message = $I18N->msg("user_added");
	}else
	{
		$message = $I18N->msg("user_login_exists");
	}
}

$SHOW = true;

if ($FUNC_ADD)
{
	$SHOW = false;

	echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
		<form action=index.php method=post>
		<input type=hidden name=page value=user>
		<input type=hidden name=save value=1>
		<input type=hidden name=FUNC_ADD value=1>
		<tr><th align=left colspan=2><b>".$I18N->msg("create_user")."</b></th></tr>
		<tr>
		<td class=grey width=100>".$I18N->msg("name")."</td>
		<td class=grey><input style='width:100%' type=text size=20 name=username value=".htmlentities($username)."></td>
		</tr>
		<tr>
		<td class=grey>".$I18N->msg("login_name")."</td>
		<td class=grey><input style='width:100%' type=text size=20 name=userlogin value=".htmlentities($userlogin)."></td>
		</tr>
		<tr>
		<td class=grey>".$I18N->msg("password")."</td>
		<td class=grey><input style='width:100%' type=text size=20 name=userpsw value=".htmlentities($userpsw)."></td>
		</tr>
		<tr>
		<td class=grey valign=top>".$I18N->msg("permissions")."</td>
		<td class=grey><textarea style='width:100%; height:200;' cols=30 rows=5 name=userrights>".$userrights."</textarea></td>
		</tr>
		<tr>
		<td class=grey>&nbsp;</td>
		<td class=grey><input type=submit name=function value='".$I18N->msg("add_user")."'></td>
		</tr>
		</form>
		</table>";


}elseif($user_id != "")
{

	$sql = new sql;
	$sql->setQuery("select * from rex_user where user_id='$user_id'");

	if ($sql->getRows()==1)
	{

		echo "
		<table border=0 cellpadding=5 cellspacing=1 width=770>
		<form action=index.php method=post>
		<input type=hidden name=page value=user>
		<input type=hidden name=user_id value=$user_id>
		<tr><th align=left colspan=2><b>".$I18N->msg("edit_user")."</b></th></tr>
		<tr>
		<td class=grey width=100>".$I18N->msg("name")."</td>
		<td class=grey><input style='width:100%' type=text size=20 name=username value=\"".htmlentities($sql->getValue("rex_user.name"))."\"></td>
		</tr>
		<tr>
		<td class=grey>".$I18N->msg("login_name")."</td>
		<td class=grey><b>".htmlentities($sql->getValue("rex_user.login"))."</b></td>
		</tr>
		<tr>
		<td class=grey>".$I18N->msg("password")."</td>
		<td class=grey><input style='width:100%' type=text size=20 name=userpsw value=\"".htmlentities($sql->getValue("rex_user.psw"))."\"></td>
		</tr>
		<tr>
		<td class=grey valign=top>".$I18N->msg("permissions")."</td>
		<td class=grey><textarea style='width:100%;height:200;' cols=30 rows=5 name=userrights>".$sql->getValue("rex_user.rights")."</textarea></td>
		</tr>
		<tr>
		<td class=grey>&nbsp;</td>
		<td class=grey>";


		echo "<table cellpadding=0 cellspacing=0 border=0><tr><td><input type=submit name=FUNC_UPDATE value='".$I18N->msg("update")."'></td>";
		if ($REX_UID!=$user_id)
		{
			echo "<td width=100>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><input type=submit name=FUNC_DELETE value='".$I18N->msg("delete")."'></td>";
		}
		echo "</tr></table>";

		echo "</td></tr>
		</form>
		</table>";

		$SHOW = false;
	}

}



if ($SHOW)
{

	echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
		<tr>
			<th width=30><a href=index.php?page=user&FUNC_ADD=1><img src=pics/user_plus.gif width=16 height=16 border=0></a></th>
			<th align=left width=300>Name</th>
			<th align=left>Login</th>
		</tr>
		";

	if ($message != "")
	{
		echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=5 class=warning>$message</td></tr>";
	}

	$sql = new sql;
	$sql->setQuery("select * from rex_user order by rex_user.name");





	for($i=0;$i<$sql->getRows();$i++)
	{


		echo "	<tr>
			<td class=grey align=center><img src=pics/user.gif width=16 height=16></td>
			<td class=grey><a href=index.php?page=user&user_id=".$sql->getValue("rex_user.user_id").">".htmlentities($sql->getValue("rex_user.name"))."</a></td>
			<td class=grey>".$sql->getValue("rex_user.login")."</td>
			</tr>";

		$sql->counter++;
	}

	echo "</table>";

}


?>
