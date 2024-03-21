<?php

/*function to replace new lines with br tags, regardless of OS (better than nl2br)*/
function replace_nl($inputstring, $replacestring = "<br>")
{
	$inputstring = str_replace("\r\n", $replacestring, $inputstring);	//windows
	$inputstring = str_replace("\n", $replacestring, $inputstring);		//linux/unix
	$inputstring = str_replace("\r", $replacestring, $inputstring);		//mac

	return $inputstring;
}

/*create calendar popup - note requires appropriate javascript in head*/
function calpopup($name, $form, $showtime = false)
{
	$s = "<a HREF=\"#\" onClick=\"cal.select(document.forms['$form'].$name,'cal_$name','dd-MM-yyyy" . ($showtime ? " HH:mm:ss" : "") . "'); return false;\"";
	$s .= "NAME='cal_$name' ID='cal_$name'><img src=cal.png border=0></A>";
	return $s;
}

function convdate_uk($date, $withtime = true)
{
	$iso = iso($date, 0);
	$out = $iso[1] . ($withtime ? " " . $iso[3] : "");
	return $out;
}

function convdate_sql($date, $withtime = true)
{
	$iso = iso($date, 4);
	$out = $iso[2] . ($withtime ? " " . $iso[3] : "");
	return $out;
}


function date_get_monday($dateYMD)
{
	$dow = date('N', strtotime($dateYMD));
	$date = date('Y-m-d', strtotime($dateYMD) - (($dow - 1) * 86400));

	return $date;
}


//convert dates to ISO format / US.
function iso($date, $format)
{

	//next choose a format to output to user
	switch ($format) {
			//returns uk formated date from a sql statement
		case "0":

			//firstly lets split the date into segments
			$y = substr($date, 0, 4);
			$m = substr($date, 5, 2);
			$d = substr($date, 8, 2);
			$h = substr($date, 11, 2);
			$mm = substr($date, 14, 2);
			$s = substr($date, 17, 2);

			if (is_null($s)) {
				$s = "00";
			}

			if ($y == "") $y = "0000";
			if ($m == "") $m = "00";
			if ($d == "") $d = "00";
			if ($h == "") $h = "00";
			if ($mm == "") $mm = "00";
			if ($s == "") $s = "00";


			$iso = array($d, $m, $y, $h, $mm, $s);
			$isodate = $d . "/" . $m . "/" . $y;
			$sqldate = $y . "-" . $m . "-" . $d;
			$time = $h . ":" . $mm . ":" . $s;
			break;

			//returns sql formated date from a us date
		case "1":
			$y = substr($date, 6, 4);
			$m = substr($date, 0, 2);
			$d = substr($date, 3, 2);

			if ($y == "") $y = "0000";
			if ($m == "") $m = "00";
			if ($d == "") $d = "00";

			$iso = array($y, $m, $d);
			$isodate = $y . "-" . $m . "-" . $d;
			$sqldate = $y . "-" . $m . "-" . $d;
			$time = "";
			break;

			//returns uk date from sql formated
		case "2":
			$y = substr($date, 6, 4);
			$m = substr($date, 3, 2);
			$d = substr($date, 0, 2);
			// 17/10/2006 VIV Added time
			$h = substr($date, 11, 2);
			$mm = substr($date, 14, 2);
			$s = substr($date, 17, 2);

			if (is_null($s))	 $s = "00";

			if ($y == "") $y = "0000";
			if ($m == "") $m = "00";
			if ($d == "") $d = "00";
			if ($h == "") $h = "00";
			if ($mm == "") $mm = "00";
			if ($s == "") $s = "00";

			$iso = array($y, $m, $d);
			$isodate = $d . "-" . $m . "-" . $y;
			$sqldate = $y . "-" . $m . "-" . $d;
			$time = $h . ":" . $mm . ":" . $s;
			break;

			//returns us formated date
		case "3":
			$y = substr($date, 6, 4);
			$m = substr($date, 3, 2);
			$d = substr($date, 0, 2);

			if ($y == "") $y = "0000";
			if ($m == "") $m = "00";
			if ($d == "") $d = "00";

			$iso = array($m, $d, $y);
			$isodate = $m . "-" . $d . "-" . $y;
			$sqldate = $y . "-" . $m . "-" . $d;
			$time = "";
			break;

			//returns sql formatted date/time from uk date/time
		case "4":

			//firstly lets split the date into segments
			$y = substr($date, 6, 4);
			$m = substr($date, 3, 2);
			$d = substr($date, 0, 2);
			$h = substr($date, 11, 2);
			$mm = substr($date, 14, 2);
			$s = substr($date, 17, 2);

			if (is_null($s)) {
				$s = "00";
			}

			if ($y == "") $y = "0000";
			if ($m == "") $m = "00";
			if ($d == "") $d = "00";
			if ($h == "") $h = "00";
			if ($mm == "") $mm = "00";
			if ($s == "") $s = "00";

			$iso = array($d, $m, $y, $h, $mm, $s);
			$isodate = $d . "-" . $m . "-" . $y;
			$sqldate = $y . "-" . $m . "-" . $d;
			$time = $h . ":" . $mm . ":" . $s;
	}

	return array($iso, $isodate, $sqldate, $time);
}

function is_date($input)
/*return values:
	 	1 for dd/mm/yyyy (or mm/dd/yyyy)
	 	2 for yyyy-mm-dd
	 	+4 if time is included (hh:mm:ss)
	 	so "24/01/2007 12:00:00" would return 5, "2007-01-24 12:00:00" would be 6
	 	otherwise zero
	 	note year must be in range 1000-2999
	*/
{
	$retval = 0;
	$input = trim($input);
	if (strlen($input) == 16) $input .= ":00";		//append seconds if string appropriate length
	if (strlen($input) != 10 && strlen($input) != 19) return 0;		//ret 0 if wrong char count for a date

	$hastime = (strlen($input) == 19);

	$datepart = $hastime ? substr($input, 0, 10) : $input;

	if (preg_match("/[0-3][0-9]\/[0-3][0-9]\/[1-2][0-9][0-9][0-9]/", $datepart) || preg_match("/[0-3][0-9]\-[0-3][0-9]\-[1-2][0-9][0-9][0-9]/", $datepart)) $retval += 1;
	if (preg_match("/[1-2][0-9][0-9][0-9]\-[0-1][0-9]\-[0-3][0-9]/", $datepart)) $retval += 2;

	if ($hastime) {
		$timepart = substr($input, 11);
		if (preg_match("/[0-2][0-9]:[0-5][0-9]:[0-5][0-9]/", $timepart)) $retval += 4;

		if ($retval < 5) return 0;		//return if date missing from time, or 19 char string with no time
	}

	return $retval;
}


/**
 * takes a date in unknown (either YMD or DMY) format and converts to Y-m-d
 * @param string $date - the date string to convert
 * @param bool $inc_time - whether to return the time or not
 * @returns string YMD string OR false if not a date
 */
function get_converted_date($date, $inc_time = false)
{
	$check = is_date($date);
	if (!$check) {
		return false;
	}
	if ($inc_time && !($check & 4)) {
		$date .= " 00:00:00";
	}
	if ($check & 1) {
		$out = convdate_sql($date, $inc_time);
	} elseif ($check & 2) {
		$out = $date;
	} else {
		return false;
	}

	if ($inc_time) {
		return $out;
	} else {
		return substr($out, 0, 10);
	}
}



function openfile($file_path, $file_name, $file_type, $string_input = false, $no_attach = false)
{
	header('Content-Description: File Transfer');
	header('Cache-Control: private');
	header('Content-Type: ' . $file_type);
	header('Content-Length: ' . ($string_input ? strlen($string_input) : filesize($file_path)));
	if (!$no_attach) {
		header('Content-Disposition: attachment; filename="' . $file_name . '"');
	}
	if ($string_input) echo $string_input;
	else readfile($file_path);
}

// 02/10/06 VIV Func Get Input From Type
// input  $type    -> type
//	  $colname -> column name
//	  $id	  -> row id
//	  $value	  -> column value
// output $output  -> correct <input type=...>
// function get_input_from_type($type, $colname, $id, $value, $ajax)
// {

// 	$input_id = $id . "_" . $colname;
// 	//echo $input_id . "<br />";
// 	//echo $type . "<br />";

// 	switch ($type) {
// 		case "date":
// 		case "datetime":
// 		case "timestamp":
// 		case "time":

// 			$d = iso($value, 0);
// 			//echo strlen($value);
// 			if (strlen($value) == 10)
// 				$output = "<input type='text' " . $ajax . " style=\"width:100%;font:10px;\" id=\"$input_id\" name=\"$input_id\" value=\"" . $d[1] . "\">\n";
// 			else
// 				$output = "<input type='text' " . $ajax . " style=\"width:100%;font:10px;\" id=\"$input_id\" name=\"$input_id\" value=\"" . $d[1] . " " . $d[3] . "\">\n";
// 			break;

// 		case "tinyint":
// 		case "tinyblob":
// 		case "tinytext":

// 			// checkbox
// 			//$output = "<input style=\"width:30px;\" type='checkbox' id=\"$input_id\" name=\"$input_id\"\" ($value == 1 ? " checked" : "" )  \"">\n";
// 			break;

// 		case "int":
// 		case "smallint":
// 		case "mediumint":
// 		case "bigint":
// 		case "float":
// 		case "double":
// 		case "decimal":
// 		case "year":
// 			$w = (($colname == "site_name") ? 150 : (($colname == "Start_Date") ? 50 : ($colname == "Post_Code") ? 60 : 80));
// 			// number
// 			$output = "<input type='text'  " . $ajax . "  style=\"width:{$w}px;font:10px;\" id=\"$input_id\" name=\"$input_id\" value=\"" . $value . "\">\n";
// 			break;

// 		default:
// 			// the rest inc. strings
// 			$w = (($colname == "site_name") ? 150 : (($colname == "Start_Date") ? 50 : ($colname == "Post_Code") ? 60 : 80));
// 			$output = "<input type='text'  " . $ajax . "  style=\"width:{$w}px;font:10px;\" id=\"$input_id\" name=\"$input_id\" value=\"" . $value . "\">\n";
// 			break;
// 	}

// 	return $output;
// }




/**
 * CSV outputter
 * assumes the first row holds all column headings
 * subsequent rows can omit but not add headings
 */
function output_csv($keyed_array, $file_name = "download.csv")
{
	if (!$keyed_array || !is_array($keyed_array)) {
		return false;
	}
	$headings = array_keys($keyed_array[0]);
	$fp = fopen("php://temp", "w");
	fputcsv($fp, $headings);
	foreach ($keyed_array as $row) {
		$out_row = array();
		foreach ($headings as $h) {
			$out_row[$h] = array_key_exists($h, $row) ? $row[$h] : '';
		}
		fputcsv($fp, $out_row);
	}
	//rewind the stream and dump
	$stats = fstat($fp);
	rewind($fp);
	header('Content-Description: File Transfer');
	header('Cache-Control: private');
	header('Content-Type: text/csv');
	header('Content-Length: ' . $stats['size']);
	header('Content-Disposition: attachment; filename="' . $file_name . '"');
	fpassthru($fp);
	exit;
}

/**
 * set a session message
 */
function set_message($message, $type = "normal", $title = false)
{
	if ($type == 'debug' && (is_array($message) || is_object($message))) {
		$message = print_r($message, 1);
	}
	if ($title) {
		$message = '<b>' . $title . "</b>\n" . $message;
	}
	if (!isset($_SESSION['messages'])) {
		$_SESSION['messages'] = array();
	}
	$_SESSION['messages'][] = array($type, $message);
}
/**
 * debug message
 * can pass multiple arguments to this, will set a message of debug class which is preformatted
 */
function dm()
{
	$backtrace = debug_backtrace();
	$line = $backtrace[0];
	$b = "Debug from {$line['file']} line {$line['line']}";
	foreach (func_get_args() as $msg) {
		set_message($msg, 'debug', $b);
	}
}

/**
 * redirect the browser using arguments passed
 * $params is translated into additional URL args
 */
function browser_redirect($action, $id, $params = array())
{
	$link = "?action=$action&id=$id";
	foreach ($params as $key => $value)
		$link .= "&{$key}=" . urlencode($value);
	header("Location: $link");
	exit;
}



/**
 * filter a record set array
 * @param array $input - the array of associative arrays to input
 * @param array $filters - the filter (field => value)
 * @param array $not_filters - reverse filters, so if field != value return the row
 * @returns array filtered record set
 */
function filter_records($input, $filters = array(), $not_filters = array())
{
	$output = array();
	foreach ($input as $rec) {
		$keep = true;
		foreach ($filters as $key => $val) {
			if ($rec[$key] != $val) {
				$keep = false;
				break;
			}
		}
		foreach ($not_filters as $key => $val) {
			if ($rec[$key] == $val) {
				$keep = false;
				break;
			}
		}
		if ($keep) {
			$output[] = $rec;
		}
	}
	return $output;
}


function get_generic_email_domains()
{
	return [
		'gmail.com',
		'yahoo.com',
		'hotmail.com',
		'aol.com',
		'hotmail.co.uk',
		'hotmail.fr',
		'msn.com',
		'yahoo.fr',
		'wanadoo.fr',
		'orange.fr',
		'comcast.net',
		'yahoo.co.uk',
		'yahoo.com.br',
		'yahoo.co.in',
		'live.com',
		'rediffmail.com',
		'free.fr',
		'gmx.de',
		'web.de',
		'yandex.ru',
		'ymail.com',
		'libero.it',
		'outlook.com',
		'uol.com.br',
		'bol.com.br',
		'mail.ru',
		'cox.net',
		'hotmail.it',
		'sbcglobal.net',
		'sfr.fr',
		'live.fr',
		'verizon.net',
		'live.co.uk',
		'googlemail.com',
		'yahoo.es',
		'ig.com.br',
		'live.nl',
		'bigpond.com',
		'terra.com.br',
		'yahoo.it',
		'neuf.fr',
		'yahoo.de',
		'alice.it',
		'rocketmail.com',
		'att.net',
		'laposte.net',
		'facebook.com',
		'bellsouth.net',
		'yahoo.in',
		'hotmail.es',
		'charter.net',
		'yahoo.ca',
		'yahoo.com.au',
		'rambler.ru',
		'hotmail.de',
		'tiscali.it',
		'shaw.ca',
		'yahoo.co.jp',
		'sky.com',
		'earthlink.net',
		'optonline.net',
		'freenet.de',
		't-online.de',
		'aliceadsl.fr',
		'virgilio.it',
		'home.nl',
		'qq.com',
		'telenet.be',
		'me.com',
		'yahoo.com.ar',
		'tiscali.co.uk',
		'yahoo.com.mx',
		'voila.fr',
		'gmx.net',
		'mail.com',
		'planet.nl',
		'tin.it',
		'live.it',
		'ntlworld.com',
		'arcor.de',
		'yahoo.co.id',
		'frontiernet.net',
		'hetnet.nl',
		'live.com.au',
		'yahoo.com.sg',
		'zonnet.nl',
		'club-internet.fr',
		'juno.com',
		'optusnet.com.au',
		'blueyonder.co.uk',
		'bluewin.ch',
		'skynet.be',
		'sympatico.ca',
		'windstream.net',
		'mac.com',
		'centurytel.net',
		'chello.nl',
		'live.ca',
		'aim.com',
		'bigpond.net.au',
		'example.com',
	];
}

function format_phone($number)
{
	$number = trim($number);
	//delete everything except digits and +
	$number = preg_replace('/[^\d|\+]/', '', $number);
	//+44, format as UK
	if (substr($number, 0, 3) == '+44') {
		$number = '0' . substr($number, 3);
	}

	if (substr($number, 0, 1) == '+') {
		return $number;
	} else {
		//02x area codes
		if (substr($number, 0, 2) == '02') {
			return substr($number, 0, 3) . ' global.php' . substr($number, 3, 4) . ' ' . substr($number, 7, 4);
		}
		//011x and 01x1 numbers
		elseif (substr($number, 0, 3) == '011' || (substr($number, 0, 2) == '01' && substr($number, 3, 1) == '1')) {
			return substr($number, 0, 4) . ' global.php' . substr($number, 4, 3) . ' ' . substr($number, 7, 4);
		}
		//mobile and geographic 6 digit numbers
		elseif (substr($number, 0, 2) == '07' || (substr($number, 0, 2) == '01')) {
			return substr($number, 0, 5) . ' global.php' . substr($number, 5, 3) . ' ' . substr($number, 8, 3);
		} else {
			return substr($number, 0, 4) . ' global.php' . substr($number, 4, 3) . ' ' . substr($number, 7, 4);
		}
	}
}

function object_to_array($obj)
{
	//only process if it's an object or array being passed to the function
	if (is_object($obj) || is_array($obj)) {
		$ret = (array) $obj;
		foreach ($ret as &$item) {
			//recursively process EACH element regardless of type
			$item = object_to_array($item);
		}
		return $ret;
	}
	//otherwise (i.e. for scalar values) return without modification
	else {
		return $obj;
	}
}
