<?

title("User","");


if ($function == "update")
{
	$updateuser = new sql;
	$updateuser->setTable("rex_user");
	$updateuser->where("user_id='$user_id'");
	$updateuser->setValue("name",$username);
	$updateuser->setValue("psw",$userpsw);
	$updateuser->setValue("rights",$userrights);
	$updateuser->update();
	$user_id = 0;
	$function = "";	
	$message = $I18N->msg("user_data_updated");
}elseif($function == "delete")
{
	$deleteuser = new sql;
	$deleteuser->query("delete from rex_user where user_id='$user_id'");
	$message = $I18N->msg("user_deleted");
}elseif($function == "add" && $save == 1)
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
		$function = "";	
		$message = $I18N->msg("user_data_updated");
	}else
	{
		$message = $I18N->msg("user_login_exists");
	}
}



echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
	<tr>
		<th width=30><a href=index.php?page=user&function=add><img src=pics/user_plus.gif width=16 height=16 border=0></a></th>
		<th align=left>".$I18N->msg("login")."</th>
		<th align=left>".$I18N->msg("password")."</th>
		<th align=left>".$I18N->msg("name")."</th>
		<th align=left>".$I18N->msg("permissions")."</th>
		<th align=left>-</th>
	</tr>
	";

if ($message != "")
{
	echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=5 class=warning>$message</td></tr>";
}

$sql = new sql;
$sql->setQuery("select * from rex_user order by user.name");

if ($function == "add")
{
	echo "	<tr>
		<form action=index.php method=post>
		<input type=hidden name=page value=user>
		<input type=hidden name=save value=1>
		<td class=dgrey align=center><img src=pics/user.gif width=16 height=16></td>
		<td class=dgrey><input style='width:100%' type=text size=20 name=userlogin value=".htmlentities($userlogin)."></td>
		<td class=dgrey><input style='width:100%' type=text size=20 name=userpsw value=".htmlentities($userpsw)."></td>
		<td class=dgrey><input style='width:100%' type=text size=20 name=username value=".htmlentities($username)."></td>
		<td class=dgrey><textarea style='width:100%' cols=30 rows=5 name=userrights>".$userrights."</textarea></td>
		<td class=dgrey><input type=submit name=function value=".$I18N->msg("add")."></td>
		</form>
		</tr>";	
}



for($i=0;$i<$sql->getRows();$i++)
{

	if ($user_id == $sql->getValue("rex_user.user_id"))
	{
		echo "	<tr>
			<form action=index.php method=post>
			<input type=hidden name=page value=user>
			<input type=hidden name=user_id value=$user_id>
			<td class=dgrey align=center><img src=pics/user.gif width=16 height=16></td>
			<td class=dgrey>".htmlentities($sql->getValue("rex_user.login"))."</td>
			<td class=dgrey><input style='width:100%' type=text size=20 name=userpsw value=".htmlentities($sql->getValue("rex_user.psw"))."></td>
			<td class=dgrey><input style='width:100%' type=text size=20 name=username value=".htmlentities($sql->getValue("rex_user.name"))."></td>
			<td class=dgrey><textarea style='width:100%' cols=30 rows=5 name=userrights>".$sql->getValue("rex_user.rights")."</textarea></td>
			<td class=dgrey><input type=submit name=function value=".$I18N->msg("update")."><br><input type=submit name=function value=".$I18N->msg("delete")."></td>
			</form>
			</tr>";		
	}else
	{
		echo "	<tr>
			<td class=grey align=center><img src=pics/user.gif width=16 height=16></td>
			<td class=grey><a href=index.php?page=user&user_id=".$sql->getValue("rex_user.user_id").">".$sql->getValue("rex_user.login")."</a></td>
			<td class=grey>".$sql->getValue("rex_user.psw")."</td>
			<td class=grey>".htmlentities($sql->getValue("rex_user.name"))."</td>
			<td class=grey>".nl2br($sql->getValue("rex_user.rights"))."&nbsp;</td>
			<td class=grey>-</td>
			</tr>";
	}
	$sql->counter++;
}

echo "</table>";




?>