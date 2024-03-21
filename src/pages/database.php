<?php

function connect_to_database()
{
	global $mysqli;

	$dbhost = 'localhost';
	$dbuser = 'root'; //team002
	$dbpwd = ''; //uo4KXJgAT7fxEvEEVNKN
	$db = 'team002';

	$mysqli = new mysqli($dbhost, $dbuser, $dbpwd, $db);

	if (!$mysqli->connect_error) {
		// Setting the SQL mode
		$mysqli->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		$mysqli->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'STRICT_TRANS_TABLES',''))");
		$mysqli->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'NO_ZERO_DATE',''))");

		$mysqli->set_charset("utf8mb4");
		return true;
	} else {
		// You can handle connection errors here if needed
		return false;
	}
}



function savepost($table, $postvars, $id = 0, $pk = "id", $mappings = array())
/*	STOP WRITING CUSTOM SAVE FUNCS FOR EVERY FORM!
		will create (id=0) or update (id<>0) a record in $table using primary key $pk = $id (PK should be numeric)
		assumes $postvars is a key=>value array where key holds fieldnames
		$mappings is an array in the format "fieldname"=>"postvars name" in case $postvars doesn't contain actual field names
		returns pk if succuessful, otherwise error text
	*/
{
	//created/updated fields. doesn't matter if missing from the table as it's the table desc that drives the update
	global $userid, $mysqli;
	$postvars["updated"] = Date("d/m/Y H:i:s");		//will be converted to Y-m-d
	$postvars["updated_by"] = $userid;
	if (!$id) {
		$postvars['created'] = Date("d/m/Y H:i:s");
		$postvars['created_by'] = $userid;
	} else {
		if (isset($postvars['created'])) unset($postvars['created']);
		if (isset($postvars['created_by'])) unset($postvars['created_by']);
	}

	if (isset($postvars['@override_created'])) {
		$postvars['created'] = $postvars['@override_created'];
	}
	if (isset($postvars['@override_created_by'])) {
		$postvars['created_by'] = $postvars['@override_created_by'];
	}


	//has the table got an alias after it? if so split
	$tblalias = "";
	$fprefix = "";
	if (strpos($table, " ") !== false) {
		$t = explode(" ", $table);
		$table = $t[0];
		$tblalias = $t[1];
		$fprefix = $t[1] . ".";
	}




	//first we need to describe the table
	$tdesc = $mysqli->query("desc $table");

	if (!$tdesc) return "ERROR - Table $table not found";			//exit if sql error, i.e. $table doesn't exist
	$fieldcount = $mysqli->affected_rows;							//field count
	if (!$fieldcount) return "ERROR - Table $table has no fields";	//exit if no fields



	$set = "";
	$newrec = array();


	//always add created and updated unless already there
	if (!$id && !@$postvars["created"]) $postvars[$fprefix . "created"] = date('Y-m-d H:i:s');
	$postvars[$fprefix . "updated"] = date('Y-m-d H:i:s');

	for ($i = 0; $i < $fieldcount; $i++)		//loop for each field in table
	{
		$fdesc = $tdesc->fetch_assoc();		//field description row

		$fname = $fdesc["Field"];
		if ($fname == 'id') continue;

		$setname = isset($mappings[$fprefix . $fname]) ? $mappings[$fprefix . $fname] : $fname;
		$postname = $fprefix . $setname;

		if (array_key_exists($postname, $postvars))		//if field posted
		{
			$val = $postvars[$postname];

			//now we adjust the value depending on field type
			if (strpos($fdesc["Type"], "char") !== false || strpos($fdesc["Type"], "blob") !== false || strpos($fdesc["Type"], "text") !== false || strpos($fdesc["Type"], "enum") !== false)
			//text: escape
			{
				$newrec[$postname] = db_esc($val);
				$val = "\"" . $newrec[$postname] . "\"";
			}
			if (strpos($fdesc["Type"], "binary") !== false) {
				$newrec[$postname] = db_esc($val);
				$val = "0x" . bin2hex($val);
			} elseif (strpos($fdesc["Type"], "json") !== false)		//json
			{
				if (is_string($val)) {
					$newrec[$postname] = $val;
				} else {
					$newrec[$postname] = json_encode($val);
				}
				$val = "'{$newrec[$postname]}'";
			} elseif (strpos($fdesc["Type"], "tinyint") !== false)		//boolean, deal with posted checkbox
			{
				if (!in_array($val, array('0', '1'))) {
					$val = (($val == "on" || $val == 1) ? 1 : 0);		//be careful! unchecked checkboxes don't get posted!
				}
				$newrec[$postname] = $val;
			} elseif (strpos($fdesc["Type"], "int") !== false || strpos($fdesc["Type"], "float") !== false || strpos($fdesc["Type"], "decimal") !== false)	//check numeric
			{
				//get rid of any commas first
				$val = str_replace(",", "", $val);
				if (!is_numeric($val)) $val = 0;
				$newrec[$postname] = $val;
			} elseif (strpos($fdesc["Type"], "date") !== false || strpos($fdesc["Type"], "timestamp") !== false)		//date - change to sql from uk format
			{
				if (strlen($val) == 16) {
					$val .= ":00";
				}
				$chk = is_date($val);
				if ($chk & 1) {
					$val = convdate_sql($val);
				}
				if ($chk && $val != "'0000-00-00 00:00:00'") {
					$newrec[$postname] = $val;
					$val = "'{$val}'";
				} else $val = "null";
			} elseif ($fdesc["Type"] == "time")		//time - validate to hh:mm(:ss) (or h:mm(:ss)) in 24 hour format, or enter 00:00
			{
				if (!preg_match("/[0-2]?[0-9]:[0-5][0-9]:?[0-5]?[0-9]?/", $val)) $val = "'00:00'";
				else $val = "'$val'";
				$newrec[$postname] = substr($val, 1, strlen($val) - 2);
			}

			//add to set variable
			$set .= ($set == "" ? "" : ", ") . "`{$setname}`={$val}";
		}
	}

	//now build the sql and execute
	$sql = $id ? "update $table set " : "insert into $table set ";
	$sql .= $set;
	if ($id) {
		$sql .= " where $pk=$id";
		$old_rec = getrecord($table, $id);
	} else {
		$old_rec = false;
	}

	$result = $mysqli->query($sql);
	if ($result) {
		if (!$id) $retid = $mysqli->insert_id;
		else $retid = $id;



		// triggers($table, $newrec, $id, $retid);
		return $retid;
	} else {
		$out = "ERROR - There was a problem " . ($id ? "updating" : "inserting") . " the record. Are all required fields set?";
		$out .= "<br>$sql";			//uncomment for debugging;
		return $out;
	}
}		//end function savepost()


function getrecord($table = false, $id = false, $keyfield = "id", $override_da = false)
//get a record from a table by id. return the record as assoc array, false on no record or error text on failure
//apply data access permissions too
{
	global $mysqli;
	if ((!$table) || (!$id)) return false;
	$id = intval($id);

	//DA check
	//if(!$override_da)
	//	if(!check_data_access($table, $id)) return "Data access failed";

	$result = $mysqli->query("select * from `$table` where `$keyfield`=$id");
	if ($mysqli->error) return $mysqli->error;
	elseif ($mysqli->affected_rows) {
		$rec = $result->fetch_assoc();

		//check the business id matches
		if (isset($rec['business_id'])) {
			if ($rec['business_id'] && ($rec['business_id'] != $GLOBALS['userbizid'])) {
				return false;
			}
		}
		return $rec;
	} else return false;
}
function get_record_params($table = false, $params = [], $override_da = false, $list = false, $sort = false, $index_key = false)
//get a record from a table by assoc array of field params. return the record as assoc array, false on no record or error text on failure
//will only return one record
//apply data access permissions too
{
	if ((!$table) || (!count($params))) return "You must specify a table and parameters";

	global $mysqli;
	$where = array();
	foreach ($params as $key => $val)
		$where[] = "`$key`=\"" . db_esc($val) . "\"";


	$where = implode(" and ", $where);

	$sql = "select * from `$table` where $where " .
		($list ? ($sort ? "order by {$sort}" : "")
			: " limit 0,1");

	$result = $mysqli->query($sql);
	if ($mysqli->error) {
		return $mysqli->error;
	} elseif ($mysqli->affected_rows) {
		if (!$list)
			$rec = $result->fetch_assoc();
		else {
			$rec = array();
			while ($row = $result->fetch_assoc()) {
				if ($index_key) {
					$rec[$row[$index_key]] = $row;
				} else {
					$rec[] = $row;
				}
			}
		}
	} else return false;

	//DA check
	//if(!$override_da)
	//if(!check_data_access($table, $rec["id"])) return "Data access failed";

	return $rec;
}

/**
 * gets a list of records using parameters as get_record_params
 */
function get_record_list($table = false, $params = false, $override_da = false, $sort = false, $index_key = false)
{
	return get_record_params($table, $params, $override_da, true, $sort, $index_key);
}

function get_record_id($table = false, $params = false, $override_da = false)
//as get_record_params() but just returns id or error message
{
	$rec = get_record_params($table, $params, $override_da);
	if (is_array($rec)) return $rec["id"];
	else return $rec;
}

function get_record_field($table = false, $params = false, $field = false, $override_da = false)
//as get_record_params() but just returns $field or error message
{
	$rec = get_record_params($table, $params, $override_da);
	if (is_array($rec)) return $rec[$field];
	else return false;
}


function check_data_access($table = false, $id = false, $op = "read")
{
	if (!$table) return false;
	$rec = getrecord($table, $id, 'id', true);
	if (!$rec) {
		return false;
	}
	if (@$GLOBALS['userfullaccess']) 	//user full access
		return true;

	return true;		//for now until implemented properly
}


/**
 * run an sql query and return a single value
 */
function get_scalar_sql($sql)
{
	global $mysqli;
	$result = $mysqli->query($sql);
	if ($result) {
		$row = $result->fetch_row();
		return $row[0];
	} else {
		return false;
	}
}

/**
 * run an sql query and return an array of single values
 */
function get_scalar_list_sql($sql)
{
	global $mysqli;
	$result = $mysqli->query($sql);
	if ($result) {
		$out = array();
		for ($i = 0; $i < $mysqli->affected_rows; $i++) {
			$row = $result->fetch_row();
			$out[] = $row[0];
		}
		return $out;
	} else
		return false;
}
/**
 * run an sql query and return an associative array of row[0] => row[1]
 */
function get_assoc_list_sql($sql)
{
	global $mysqli;
	$result = $mysqli->query($sql);
	if ($mysqli->affected_rows) {
		$out = array();
		for ($i = 0; $i < $mysqli->affected_rows; $i++) {
			$row = $result->fetch_row();
			$out[$row[0]] = $row[1];
		}
		return $out;
	} else
		return false;
}

/**
 * run an sql query and return a record as assoc array
 */
function get_record_sql($sql, $assoc = true)
{
	global $mysqli;
	$result = $mysqli->query($sql);
	if ($result) {
		$row = $assoc ? $result->fetch_assoc() : $result->fetch_row();
		return $row;
	} else
		return false;
}
/**
 * run an sql query and return a 2D array
 * the first key is the values of the first field
 * each of these keys contains an array of the values of the second field
 * eg [123] => array('A100', 'A101', 'A102'), [128] => array('B100', 'B101') etc
 */
function get_2d_sql($sql)
{
	global $mysqli;
	$result = $mysqli->query($sql);
	if ($result) {
		$out = array();
		for ($i = 0; $i < $mysqli->affected_rows; $i++) {
			$row = $result->fetch_row();
			if (!isset($out[$row[0]])) $out[$row[0]] = array();
			$out[$row[0]][] = $row[1];
		}
		return $out;
	} else return array();
}

/**
 * return a list of arrays for an sql query
 * eg [0] => array(record 1....), [1] => array(record 2....) etc
 * third parameter allows the keys to come from a field in the result set
 */
function get_records_sql($sql, $assoc = true, $keyfield = false)
{
	global $mysqli;
	$result = $mysqli->query($sql);
	if ($mysqli->affected_rows) {
		$out = array();
		for ($i = 0; $i < $mysqli->affected_rows; $i++) {
			$rec = $assoc ? $result->fetch_assoc() : $result->fetch_row();
			$out[$keyfield ? $rec[$keyfield] : $i] = $rec;
		}

		return $out;
	} else
		return false;
}

/**
 * get a status ID from a special code and table name
 * just because getting a status id is quite a long and annoying line of code!
 */
function get_status_id($table, $code)
{
	return get_record_field("general_status", array("table_name" => $table, "special_code" => $code), "id");
}

/**
 * delete a record by id
 */
function delete_record($table, $id)
{
	global $mysqli;
	$sql = "delete from `" . db_esc($table) . "`
			where id=" . intval($id) . " limit 1";
	$mysqli->query($sql);
}

/**
 * increment (or decrement) a numeric field
 * returns the new value, or false if there is an error
 * remember to check for ===false to see if there has been an error as can legitimately return 0
 */
function increment_field($table, $field, $id, $interval = 1)
{
	global $mysqli;
	//exit if interval not numeric
	if (!is_numeric($interval))
		return false;

	$id = intval($id);
	$table = db_esc($table);
	$field = db_esc($field);

	$sql = "update `$table`
			set `$field` = `$field` + $interval
			where id=$id";
	$mysqli->query($sql);

	if ($mysqli->error)
		return false;

	$sql = "select `$field`
			from `$table`
			where id=$id";
	return get_scalar_sql($sql);
}

/**
 * create a link between two records
 */
function link_records($parent_table, $parent_id, $child_table, $child_id)
{
	$rec = array(
		'parent_table' => $parent_table,
		'parent_id' => $parent_id,
		'child_table' => $child_table,
		'child_id' => $child_id
	);

	//if there is an existing record, return its id
	$id = get_record_id('general_link', $rec);
	if ($id)
		return $id;

	//create the link
	$id = savepost('general_link', $rec, 0);

	return $id;
}

/**
 * save a record as a child record, using a general link
 */
function save_child($table, $data, $id, $parent_table, $parent_id)
{
	//check the parent exists
	if (!get_record_id($parent_table, $parent_id))
		return false;

	$ok = savepost($table, $data, $id);

	if (is_numeric($id))
		link_records($parent_table, $parent_id, $table, $id);

	return $ok;
}

/**
 * get all linked records, parent or child via the links table
 */
function get_linked_records($parent_table, $parent_id, $child_table)
{
	$sql = "select distinct x.*
			from 
			(
				select distinct c1.*
				from general_link l1
				inner join $child_table c1
					on l1.child_table = '$child_table' and l1.child_id=c.id
				where 
					l1.parent_table = '$parent_table' 
					and l1.parent_id='$parent_id'
					
				union select distinct c2.*
				from general_link l2
				inner join $child_table c2
					on l2.parent_table = '$child_table' and l2.parent_id=c2.id
				where 
					l2.child_table = '$parent_table' 
					and l2.child_id='$parent_id'
			) as x
			order by x.id
		";
	return get_records_sql($sql);
}
/**
 * escape a string
 */
function db_esc($string)
{
	global $mysqli;
	return $mysqli->real_escape_string($string);
}

/**
 * prepare an SQL statement or part of it
 * will replace :0 :1 :2 etc with db_esc'd values passed
 *
 * @param string $sql
 * @param string|array $replacement0 - if an array, these values are used and following params ignored
 * @param string $replacement1
 */
function db_prep($sql)
{
	$args = func_get_args();
	array_shift($args);
	if (!$args) {
		return $sql;
	}
	if (is_array($args[0])) {
		$args = $args[0];
	}
	foreach ($args as $k => $v) {
		$sql = str_replace(':' . $k, db_esc($v), $sql);
	}
	return $sql;
}

/**
 * this function will create an array of changed fields from the $_POST array
 * it compares the original record provided with the post and only sets changed fields
 * 2nd and subsequent args are the key names of the fields that are allowed to change
 * @param array $original
 * @param mixed first field name
 * @param mixed second field name etc
 * @returns array record to save
 */
function create_save_array()
{
	$args = func_get_args();
	$original = array_shift($args);
	$data = array();
	foreach ($args as $arg) {
		if (@$original[$arg] != @$_POST[$arg]) {
			$data[$arg] = @$_POST[$arg];
		}
	}
	return $data;
}

/**
 * return just the specified keys from $_POST
 */
function get_posted_data($keys, $json_arrays = false)
{
	$out = array();
	foreach ($keys as $k) {
		$out[$k] = @$_POST[$k];
		if (is_array($out[$k])) {
			$out[$k] = json_encode($out[$k]);
		}
	}
	return $out;
}

/**
 * abstract a basic query function
 */
function db_query($sql)
{
	global $mysqli;
	return $mysqli->query($sql);
}

function db_affected_rows()
{
	global $mysqli;
	return $mysqli->affected_rows;
}

function db_num_rows()
{
	global $mysqli;
	return $mysqli->result->num_rows;
}

function db_error()
{
	global $mysqli;
	return $mysqli->error;
}

function db_get_result_count($sql)
{
	global $mysqli;
	$result = $mysqli->query($sql);
	return $mysqli->affected_rows;
}
