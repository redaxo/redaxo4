<?

// class sql 1.0
//
// erstellt 01.12.2001
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

class sql
{

	var $feld; 			// array der feldnamen die gesetzt werden
	var $wert;			// array der feldwerte die gesetzt werden sollen
	var $zaehler;		// wieviele felder existieren
	var $table;			// Tabelle setzen
	var $error;			// error wert
	var $wherevar;		// where ...
	var $select;		// select:
	var $counter;		// select: welcher datensatz ist dran
	var $rows;			// select: anzahl der treffer
	var $result;		// select: alle angaben gespeichert
	var $last_insert_id;
	var $debugsql;
	var $identifier;
	var $DBID;
	var $insertID;

	function sql($DBID=1)
	{
		global $DB,$REX;

		$this->identifier = @mysql_pconnect($DB[$DBID][HOST],$DB[$DBID][LOGIN],$DB[$DBID][PSW]);
		$this->debugsql = false;
		$this->DBID = $DBID;
		$this->selectDB();
		$this->zaehler = 0;
		$this->counter = 0;
	}

	function selectDB()
	{
		global $DB,$REX;

		if (!@mysql_select_db($DB[$this->DBID][NAME]))
		{
			echo "<font style='color:red; font-family:verdana,arial; font-size:11px;'>Class SQL 1.1 | Database down. | Please contact <a href=mailto:".$REX[error_emailaddress].">".$REX[error_emailaddress]."</a>\n | Thank you!\n</font>";
			exit;
		}
	}

	function setQuery($select)
	{
		$this->zaehler = 0;
		$this->counter = 0;
		$this->select = $select;
		$this->selectDB();
		$this->result = @mysql_query("$select");
		$this->rows   = @mysql_num_rows($this->result);
		$this->insertID = @mysql_insert_id($this->result);
		$this->error = @mysql_error($this->result);

		if ( $this->debugsql ) echo htmlentities($select)."<br>".$this->rows." found<br>";
	}

	function setTable($table)
	{
		$this->table = $table;
	}

	function setValue($feldname,$wertigkeit)
	{
		$this->feld[$this->zaehler] = $feldname;
		$this->wert[$this->zaehler] = $wertigkeit;
		$this->zaehler++;
	}

	function isValueOf($feld,$prop)
	{

		$value = @mysql_result($this->result,$this->counter,"$feld");
		if ($prop == "")
		{
			return TRUE;
		}else if (strstr($value,$prop))
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	function where($where)
	{
		$this->wherevar = "where $where";
	}

	function getValue($value)
	{
		// wenn db verwechslungen, dann hier aktiv setzen
		// $this->selectDB();
		$back = @mysql_result($this->result,$this->counter,"$value");
		return $back;
	}

	function getRows()
	{
		return $this->rows;
        }

	function nextValue()
	{
		$this->counter++;
	}

	function resetCounter()
	{
		$this->counter = 0;
	}

	function liste()
	{
		$back = "";

		for ($i=0;$i<$this->getRows();$i++)
		{
			for ($j=0;$j<$this->zaehler;$j++)
			{
				$back .= $this->getValue($this->feld[$j])." \n";
			}

			$back .= "<br>";
			$this->counter++;
		}

		return $back;
	}

	function update()
	{
		$sql = "";
		for ($i=0;$i<$this->zaehler;$i++)
		{
			if ($sql!=""){ $sql .= ","; }
			$sql .= $this->feld[$i]."='".addslashes( $this->wert[$i])."'";
		}

		$this->selectDB();
		$this->error = mysql_query("update $this->table set $sql $this->wherevar");
		$this->message = "event updated<br>";
		if ( $this->debugsql ) echo "update $this->table set $sql $this->wherevar";
	}

	function insert ()
	{
		$sql1 = "";
		$sql2 = "";
		for ($i=0;$i<$this->zaehler;$i++)
		{
			if ($sql1!=""){ $sql1 .= ","; }
			if ($sql2!=""){ $sql2 .= ","; }
			$sql1 .= $this->feld[$i];
			$sql2 .= "'".addslashes( $this->wert[$i])."'";
		}

		$this->selectDB();
		$this->error = mysql_query("insert into $this->table ($sql1) VALUES ($sql2)");
		$this->last_insert_id = mysql_insert_id($this->identifier);
		$this->error = @mysql_error();
		$this->message = "new event inserted<br>";
		if ( $this->debugsql ) echo htmlentities("insert into $this->table ($sql1) VALUES ($sql2)");
	}

	function delete()
	{
		$this->selectDB();
		$this->error = mysql_query("delete from $this->table $this->wherevar");
	}

	function flush()
	{
		$this->zaehler = 0;
		$this->table = "";
		$this->error = "";
		$this->wherevar = "";
		$this->select = "";
		$this->counter = 0;
		$this->rows = 0;
		$this->result = "";
	}

	function query($sql)
	{
		$this->selectDB();
		$this->error = mysql_query("$sql");
		if ( $this->debugsql ) echo $sql."<br>";
	}

	function next()
	{
		$this->counter++;
	}

	function getLastID()
	{
		return $this->last_insert_id;
	}

	// GET ARRAY RESULT
	function get_array($sql=""){

		if($sql!=""){
			$this->setQuery($sql);
		}

		while ($row = @mysql_fetch_assoc($this->result)) {
	            $data[] = $row;
	        }

	        return $data;
	        unset($data);

	}

	function setNewId($field)
	{
		$result = mysql_query("select $field from $this->table order by $field desc LIMIT 1");
		$id = mysql_result($result,0,"$field");
		$id++;
		$this->setValue($field,$id);
		return $id;
	}


	// ------------------------- ORDER FUNKTIONEN


	// Function zum DB Order change
	//$db->order_down($order_id,"table_name","field_name_order_id");
	//$sql="SELECT * FROM test ORDER by order_id DESC"; zum anzeigen auf der site oder in der admin
	function order_up($value="",$table_name,$field_name,$where_name="",$where_value=""){
		if($where_name!=""){
			$add  = "WHERE $where_name='$where_value'";
			$add2 = "AND $where_name='$where_value'";
		}
		if($value!=""){
			$res = $this->get_array("SELECT $field_name FROM $table_name $add ORDER BY $field_name DESC");
			foreach($res as $key => $var){
				if($var[$field_name] == $value){
					$save_id = $key + 1;
				}
			}
			if(count($res) != $save_id){

				$next_id = $res[$save_id][$field_name];

				$sql= "UPDATE $table_name SET $field_name=0 WHERE $field_name=$value $add2";
				$this->setQuery($sql);
				$sql= "UPDATE $table_name SET $field_name = $field_name+1 WHERE $field_name='$next_id' $add2";
				$this->setQuery($sql);
				$sql= "UPDATE $table_name SET $field_name='$next_id' WHERE $field_name='0' $add2";
				$this->setQuery($sql);
			}
		}

	}

	// Function zum DB Order change
	//$db->order_up($order_id,"table_name","field_name_order_id");
	//$sql="SELECT * FROM test ORDER by order_id DESC"; zum anzeigen auf der site oder in der admin
	function order_down($value="",$table_name,$field_name,$where_name="",$where_value=""){

		if($where_name!=""){
			$add  = "WHERE $where_name='$where_value'";
			$add2 = "AND $where_name='$where_value'";
		}

		if($value!=""){
			$res = $this->get_array("SELECT $field_name FROM $table_name $add ORDER BY $field_name DESC");
			foreach($res as $key => $var){
				if($var[$field_name] == $value){
					$save_id = $key - 1;
				}
			}

			if($save_id!=-1){

				$next_id = $res[$save_id][$field_name];

				$sql= "UPDATE $table_name SET $field_name=0 WHERE $field_name=$value $add2";
				$this->setQuery($sql);

				$sql= "UPDATE $table_name SET $field_name = $field_name-1 WHERE $field_name='$next_id' $add2";
				$this->setQuery($sql);

				$sql= "UPDATE $table_name SET $field_name='$next_id' WHERE $field_name='0' $add2";
				$this->setQuery($sql);
			}

		}

	}

	function order_top($value="",$table_name,$field_name,$where_name="",$where_value=""){

		if($where_name!=""){
			$add  = "WHERE $where_name='$where_value'";
			$add2 = "AND $where_name='$where_value'";
		}

		if($value!=""){

			$res = $this->get_array("SELECT $field_name FROM $table_name $add ORDER BY $field_name ASC LIMIT 0,1");
			$save_id = $res[0][$field_name];

			if($save_id!=$value){

				$sql= "UPDATE $table_name SET $field_name=0 WHERE $field_name=$value $add2";
				$this->setQuery($sql);

				$sql= "UPDATE $table_name SET $field_name=$field_name + 1 WHERE $field_name!=0 AND $field_name!=$save_id $add2";
				$this->setQuery($sql);

				$sql= "UPDATE $table_name SET $field_name = $field_name + 1 WHERE $field_name='$save_id' $add2";
				$this->setQuery($sql);

				$sql= "UPDATE $table_name SET $field_name='$save_id' WHERE $field_name='0' $add2";
				$this->setQuery($sql);
			}

		}
	}

	function order_bottom($value="",$table_name,$field_name,$where_name="",$where_value=""){

		if($where_name!=""){
			$add  = "WHERE $where_name='$where_value'";
			$add2 = "AND $where_name='$where_value'";
		}

		if($value!=""){

			$res = $this->get_array("SELECT $field_name FROM $table_name $add ORDER BY $field_name DESC LIMIT 0,1");
			$save_id = $res[0][$field_name];

			if($save_id!=$value){

				$sql= "UPDATE $table_name SET $field_name=$save_id + 1 WHERE $field_name=$value $add2";
				$this->setQuery($sql);
			}

		}

	}

	// Function zum anlegen der ORDER_ID
	//$order_id = $db->new_order("test","order_id");
	//$db->execute("INSERT INTO test (order_id) VALUES ('$order_id') ");
	function new_order($table_name,$field_name,$where_name="",$where_value=""){

		if($where_name != ""){
			$add = "WHERE $where_name = '$where_value'";
		}

		$sql = "UPDATE $table_name SET $field_name=$field_name + 1 $add";
		$this->setQuery($sql);
		return 1;

	}

	//ORDER ENTRY TO POSITION
	function order_position($position,$value,$value_field_name,$table_name,$field_name,$where_name="",$where_value=""){

		if($where_name!=""){
			$add  = "WHERE $where_name='$where_value'";
			$add2 = "AND $where_name='$where_value'";
		}

		if($position!=""){

			$sql = "SELECT $field_name,$value_field_name FROM $table_name $add ORDER BY $field_name ASC";
			$res = $this->get_array($sql);
			$last = count($res);

			if($position > $last){
				$position = $last;
			}

			if($position == $last){
				$max_prior = $res[$last-1][$field_name];
				$sql = "UPDATE $table_name SET $field_name = $max_prior + 1 WHERE $value_field_name = $value";
				$this->setQuery($sql);
			} else {

				$c=0;
				foreach($res as $var){
					if($var[$value_field_name]==$value){
						$old_pos = $c + 1;
					}
					$c++;
				}
				if($old_pos < $position){
	            	$pos_prior = $res[$position][$field_name];
	            } else {
					$pos_prior = $res[$position-1][$field_name];
				}

	            if($pos_prior > 0){
	                $sql = "UPDATE $table_name SET $field_name = $field_name + 1 WHERE $field_name >= $pos_prior $add2";
	                $this->setQuery($sql);
	                $sql = "UPDATE $table_name SET $field_name = $pos_prior WHERE $value_field_name = $value";
	                $this->setQuery($sql);
	            }
	        }

		}
	}

}

?>