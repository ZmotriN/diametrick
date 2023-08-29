<?php	//version my202

if(!$this->isPost()) {
	$this->e404();
}


//set allowTestMenu to false to disable System/Server test page
$GLOBALS['allowTestMenu'] = true;

$GLOBALS['use_mysqli'] = function_exists("mysqli_connect");

header("Content-Type: text/plain; charset=utf-8");
error_reporting(0);
set_time_limit(0);

function phpversion_int()
{
	list($maVer, $miVer, $edVer) = preg_split("(/|\.|-)", phpversion());
	return $maVer*10000 + $miVer*100 + $edVer;
}

if (phpversion_int() < 50300)
{
	set_magic_quotes_runtime(0);
}

function GetLongBinary($num)
{
	return pack("N",$num);
}

function GetShortBinary($num)
{
	return pack("n",$num);
}

function GetDummy($count)
{
	$str = "";
	for($i=0;$i<$count;$i++)
		$str .= "\x00";
	return $str;
}

function GetBlock($val)
{
	$len = strlen($val);
	if( $len < 254 )
		return chr($len).$val;
	else
		return "\xFE".GetLongBinary($len).$val;
}

function EchoHeader($errno)
{
	$str = GetLongBinary(1111);
	$str .= GetShortBinary(202);
	$str .= GetLongBinary($errno);
	$str .= GetDummy(6);
	echo $str;
}

function EchoConnInfo($conn)
{
	if ($GLOBALS['use_mysqli']) {
		$str = GetBlock(mysqli_get_host_info($conn));
		$str .= GetBlock(mysqli_get_proto_info($conn));
		$str .= GetBlock(mysqli_get_server_info($conn));
		echo $str;
	} else {
		$str = GetBlock(mysql_get_host_info($conn));
		$str .= GetBlock(mysql_get_proto_info($conn));
		$str .= GetBlock(mysql_get_server_info($conn));
		echo $str;
	}
}

function EchoResultSetHeader($errno, $affectrows, $insertid, $numfields, $numrows)
{
	$str = GetLongBinary($errno);
	$str .= GetLongBinary($affectrows);
	$str .= GetLongBinary($insertid);
	$str .= GetLongBinary($numfields);
	$str .= GetLongBinary($numrows);
	$str .= GetDummy(12);
	echo $str;
}

function EchoFieldsHeader($res, $numfields)
{
	$str = "";
	for( $i = 0; $i < $numfields; $i++ ) {
		if ($GLOBALS['use_mysqli']) {
			$finfo = mysqli_fetch_field_direct($res, $i);
			$str .= GetBlock($finfo->name);
			$str .= GetBlock($finfo->table);
			
			$type = $finfo->type;
			$length = $finfo->length;
		
			$str .= GetLongBinary($type);
	
			$intflag = $finfo->flags;
			$str .= GetLongBinary($intflag);
	
			$str .= GetLongBinary($length);
		} else {
			$str .= GetBlock(mysql_field_name($res, $i));
			$str .= GetBlock(mysql_field_table($res, $i));
	
			$type = mysql_field_type($res, $i);
			$length = mysql_field_len($res, $i);
			switch ($type) {
				case "int":
					if( $length > 11 ) $type = 8;
					else $type = 3;
					break;
				case "real":
					if( $length == 12 ) $type = 4;
					elseif( $length == 22 ) $type = 5;
					else $type = 0;
					break;
				case "null":
					$type = 6;
					break;
				case "timestamp":
					$type = 7;
					break;
				case "date":
					$type = 10;
					break;
				case "time":
					$type = 11;
					break;
				case "datetime":
					$type = 12;
					break;
				case "year":
					$type = 13;
					break;
				case "blob":
					if( $length > 16777215 ) $type = 251;
					elseif( $length > 65535 ) $type = 250;
					elseif( $length > 255 ) $type = 252;
					else $type = 249;
					break;
				default:
					$type = 253;
			}
			$str .= GetLongBinary($type);
	
			$flags = explode( " ", mysql_field_flags ( $res, $i ) );
			$intflag = 0;
			if(in_array( "not_null", $flags )) $intflag += 1;
			if(in_array( "primary_key", $flags )) $intflag += 2;
			if(in_array( "unique_key", $flags )) $intflag += 4;
			if(in_array( "multiple_key", $flags )) $intflag += 8;
			if(in_array( "blob", $flags )) $intflag += 16;
			if(in_array( "unsigned", $flags )) $intflag += 32;
			if(in_array( "zerofill", $flags )) $intflag += 64;
			if(in_array( "binary", $flags)) $intflag += 128;
			if(in_array( "enum", $flags )) $intflag += 256;
			if(in_array( "auto_increment", $flags )) $intflag += 512;
			if(in_array( "timestamp", $flags )) $intflag += 1024;
			if(in_array( "set", $flags )) $intflag += 2048;
			$str .= GetLongBinary($intflag);
	
			$str .= GetLongBinary($length);
		}
	}
	echo $str;
}

function EchoData($res, $numfields, $numrows)
{
	for( $i = 0; $i < $numrows; $i++ ) {
		$str = "";
		$row = null;
		if ($GLOBALS['use_mysqli'])
			$row = mysqli_fetch_row( $res );
		else
			$row = mysql_fetch_row( $res );
		for( $j = 0; $j < $numfields; $j++ ){
			if( is_null($row[$j]) )
				$str .= "\xFF";
			else
				$str .= GetBlock($row[$j]);
		}
		echo $str;
	}
}


function doSystemTest()
{
	function output($description, $succ, $resStr) {
		echo "<tr><td class=\"TestDesc\">$description</td><td ";
		echo ($succ)? "class=\"TestSucc\">$resStr[0]</td></tr>" : "class=\"TestFail\">$resStr[1]</td></tr>";
	}
	output("PHP version >= 4.0.5", phpversion_int() >= 40005, array("Yes", "No"));
	output("mysql_connect() available", function_exists("mysql_connect"), array("Yes", "No"));
	output("mysqli_connect() available", function_exists("mysqli_connect"), array("Yes", "No"));
	if (phpversion_int() >= 40302 && substr($_SERVER["SERVER_SOFTWARE"], 0, 6) == "Apache" && function_exists("apache_get_modules")){
		if (in_array("mod_security2", apache_get_modules()))
			output("Mod Security 2 installed", false, array("No", "Yes"));
	}
}

/////////////////////////////////////////////////////////////////////////////
////

	if (phpversion_int() < 40005) {
		EchoHeader(201);
		echo GetBlock("unsupported php version");
		exit();
	}

	if (phpversion_int() < 40010) {
		global $HTTP_POST_VARS;
		$_POST = &$HTTP_POST_VARS;	
	}

	if (!isset($_POST["actn"]) || !isset($_POST["host"]) || !isset($_POST["port"]) || !isset($_POST["login"])) {
		$testMenu = $allowTestMenu;
		if (!$testMenu){
			EchoHeader(202);
			echo GetBlock("invalid parameters");
			exit();
		}
	}

	if (!@$testMenu){
		if (@$_POST["encodeBase64"] == '1') {
			for($i=0;$i<count($_POST["q"]);$i++)
				$_POST["q"][$i] = base64_decode($_POST["q"][$i]);
		}
		
		if (!function_exists("mysql_connect") && !function_exists("mysqli_connect")) {
			EchoHeader(203);
			echo GetBlock("MySQL not supported on the server");
			exit();
		}
		
		$errno_c = 0;
		$hs = $_POST["host"];
		if ($GLOBALS['use_mysqli']) {
			if( $_POST["port"] ) 
				$conn = mysqli_connect($hs, $_POST["login"], $_POST["password"], '', $_POST["port"]);
			else
				$conn = mysqli_connect($hs, $_POST["login"], $_POST["password"]);
			$errno_c = mysqli_connect_errno($conn);
			if($errno_c > 0) {
				EchoHeader($errno_c);
				echo GetBlock(mysqli_connect_error($conn));
				exit;
			}
			// mysqli_set_charset($conn, "utf8mb4");
			// SET NAMES 'utf8'
			

			if(($errno_c <= 0) && ( $_POST["db"] != "" )) {
				$res = mysqli_select_db($conn, $_POST["db"] );
				$errno_c = mysqli_errno($conn);
			}
			
			mysqli_query($conn, "SET NAMES 'utf8'");

			EchoHeader($errno_c);
			if($errno_c > 0) {
				echo GetBlock(mysqli_error($conn));
			} elseif($_POST["actn"] == "C") {
				EchoConnInfo($conn);
			} elseif($_POST["actn"] == "Q") {
				for($i=0;$i<count($_POST["q"]);$i++) {
					$query = $_POST["q"][$i];
					if($query == "") continue;
					if (phpversion_int() < 50400){  
						if(get_magic_quotes_gpc())
							$query = stripslashes($query);
					}
					$res = mysqli_query($conn, $query);
					$errno = mysqli_errno($conn);
					$affectedrows = mysqli_affected_rows($conn);
					$insertid = mysqli_insert_id($conn);				
					if (false !== $res) {
						$numfields = mysqli_field_count($conn);
						$numrows = mysqli_num_rows($res);
					}
					else {
						$numfields = 0;
						$numrows = 0;
					}
					EchoResultSetHeader($errno, $affectedrows, $insertid, $numfields, $numrows);
					if($errno > 0)
						echo GetBlock(mysqli_error($conn));
					else {
						if($numfields > 0) {
							EchoFieldsHeader($res, $numfields);
							EchoData($res, $numfields, $numrows);
						} else {
							if(phpversion_int() >= 40300)
								echo GetBlock(mysqli_info($conn));
							else
								echo GetBlock("");
						}
					}
					if($i<(count($_POST["q"])-1))
						echo "\x01";
					else
						echo "\x00";
					if (false !== $res)
						mysqli_free_result($res);
				}
			}
		} else {
			if( $_POST["port"] ) $hs .= ":".$_POST["port"];
			$conn = mysql_connect($hs, $_POST["login"], $_POST["password"]);
			$errno_c = mysql_errno();
			//if (phpversion_int() >= 50203){  // for unicode database name
			//	mysql_set_charset('UTF8');
			//}
			if(($errno_c <= 0) && ( $_POST["db"] != "" )) {
				$res = mysql_select_db( $_POST["db"], $conn);
				$errno_c = mysql_errno();
			}
			
			EchoHeader($errno_c);
			if($errno_c > 0) {
				echo GetBlock(mysql_error());
			} elseif($_POST["actn"] == "C") {
				EchoConnInfo($conn);
			} elseif($_POST["actn"] == "Q") {
				for($i=0;$i<count($_POST["q"]);$i++) {
					$query = $_POST["q"][$i];
					if($query == "") continue;
					if (phpversion_int() < 50400){  
						if(get_magic_quotes_gpc())
							$query = stripslashes($query);
					}
					$res = mysql_query($query, $conn);
					$errno = mysql_errno();
					$affectedrows = mysql_affected_rows($conn);
					$insertid = mysql_insert_id($conn);
					$numfields = mysql_num_fields($res);
					$numrows = mysql_num_rows($res);
					EchoResultSetHeader($errno, $affectedrows, $insertid, $numfields, $numrows);
					if($errno > 0)
						echo GetBlock(mysql_error());
					else {
						if($numfields > 0) {
							EchoFieldsHeader($res, $numfields);
							EchoData($res, $numfields, $numrows);
						} else {
							if(phpversion_int() >= 40300)
								echo GetBlock(mysql_info($conn));
							else
								echo GetBlock("");
						}
					}
					if($i<(count($_POST["q"])-1))
						echo "\x01";
					else
						echo "\x00";
					mysql_free_result($res);
				}
			}
		}
		exit();
	}

	// header("Content-Type: text/html"); 