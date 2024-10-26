<?php
//////////// FUNCTII MYSQL ////////////////////////////////////////////////
//http://www.asfromania.ro/consumatori/baza-de-date-cedam/cedam
/* USAGE:
require_once($_SERVER['DOCUMENT_ROOT']."mysql_replace.php");
OR require_once("/full/server/path/mysql_replace.php");

SETTINGS: NONE

KNOWN ISSUES:
mysql_list_processes() reurns incomplete data (precess id is returned but no database,table,time or other data )
mysql_field_type() can return some bad filed type descriptions, depending on the mysql server version
mysql_fetch_field() can return some types, depending on the mysql server version

TO IMPROVE:
sqli_h_type2txt() acording to mysql server version => it may fix the filed type issues

COMPLETE FUNCTION LIST:

#    mysql_affected_rows — Get number of affected rows in previous MySQL operation
#    mysql_client_encoding — Returns the name of the character set
#    mysql_close — Close MySQL connection
#    mysql_connect — Open a connection to a MySQL Server
#    mysql_create_db — Create a MySQL database
#    mysql_data_seek — Move internal result pointer
#    mysql_db_name — Retrieves database name from the call to mysql_list_dbs
#    mysql_db_query — Selects a database and executes a query on it
#    mysql_drop_db — Drop (delete) a MySQL database
#    mysql_errno — Returns the numerical value of the error message from previous MySQL operation
#    mysql_error — Returns the text of the error message from previous MySQL operation
#    mysql_escape_string — Escapes a string for use in a mysql_query
#    mysql_fetch_array — Fetch a result row as an associative array, a numeric array, or both
#    mysql_fetch_assoc — Fetch a result row as an associative array
#    mysql_fetch_field — Get column information from a result and return as an object
#    mysql_fetch_lengths — Get the length of each output in a result
#    mysql_fetch_object — Fetch a result row as an object
#    mysql_fetch_row — Get a result row as an enumerated array
#    mysql_field_flags — Get the flags associated with the specified field in a result
#    mysql_field_len — Returns the length of the specified field
#    mysql_field_name — Get the name of the specified field in a result
#    mysql_field_seek — Set result pointer to a specified field offset
#    mysql_field_table — Get name of the table the specified field is in
#    mysql_field_type — Get the type of the specified field in a result
#    mysql_free_result — Free result memory
#    mysql_get_client_info — Get MySQL client info
#    mysql_get_host_info — Get MySQL host info
#    mysql_get_proto_info — Get MySQL protocol info
#    mysql_get_server_info — Get MySQL server info
#    mysql_info — Get information about the most recent query
#    mysql_insert_id — Get the ID generated in the last query
#    mysql_list_dbs — List databases available on a MySQL server
#    mysql_list_fields — List MySQL table fields
#    mysql_listfields — Alias for mysql_list_fields; List MySQL table fields
#    mysql_list_processes — List MySQL processes
#    mysql_list_tables — List tables in a MySQL database
#    mysql_listtables — Alias for mysql_list_tables; List tables in a MySQL database
#    mysql_num_fields — Get number of fields in result
#    mysql_num_rows — Get number of rows in result
#    mysql_pconnect — Open a persistent connection to a MySQL server
#    mysql_ping — Ping a server connection or reconnect if there is no connection
#    mysql_query — Send a MySQL query
#    mysql_real_escape_string — Escapes special characters in a string for use in an SQL statement
#    mysql_result — Get result data
#    mysql_select_db — Select a MySQL database
#    mysql_set_charset — Sets the client character set
#    mysql_stat — Get current system status
#    mysql_tablename — Get table name of field
#    mysql_thread_id — Return the current thread ID
#    mysql_unbuffered_query — Send an SQL query to MySQL without fetching and buffering the result rows.
#    sqli_h_type2txt - parses the mysqli types depending on mysql server version
#    sqli_h_flags2txt - parses the mysqli flags
*/
if(!function_exists('mysql_connect') && function_exists('mysqli_connect')){

	define('MYSQL_ASSOC',MYSQLI_ASSOC);
	define('MYSQL_NUM',MYSQLI_NUM);
	define('MYSQL_BOTH',MYSQLI_BOTH);
	define('MYSQL_CLIENT_COMPRESS',MYSQLI_CLIENT_COMPRESS);
	define('MYSQL_CLIENT_SSL',MYSQLI_CLIENT_SSL);
	define('MYSQL_CLIENT_INTERACTIVE',MYSQLI_CLIENT_INTERACTIVE);
	define('MYSQL_CLIENT_IGNORE_SPACE',MYSQLI_CLIENT_IGNORE_SPACE);

	function mysql_connect($host,$user,$pass,$new_link=false,$clFlags=0){
		$res=mysqli_connect($host,$user,$pass);
		$GLOBALS['_MysqliSvRes']= $res;
		return $res;
	}
	function mysql_pconnect($host,$user,$pass,$new_link=false,$clFlags=0){return mysql_connect('p:'.$host,$user,$pass,$new_link=false,$clFlags);}
	function mysql_close($res){ return mysqli_close($res);}
//	function mysql_query($query,$res=NULL){	 if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_query($res,$query,MYSQLI_STORE_RESULT);}
	function mysql_query($query,$res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}else{$GLOBALS['_MysqliSvRes']=$res;}	return mysqli_query($res,$query,MYSQLI_STORE_RESULT);}
	function mysql_fetch_array($res,$result_type = MYSQLI_BOTH){ return mysqli_fetch_array ( $res, $result_type );}
	function mysql_fetch_assoc($res){ return mysqli_fetch_assoc ( $res);}
	function mysql_fetch_row($res){ return mysqli_fetch_row ( $res);}
	function mysql_insert_id($res=NULL){	if(!$res){$res=$GLOBALS['_MysqliSvRes'];}		return mysqli_insert_id($res);}
	function mysql_affected_rows($res=NULL){	if(!$res){$res=$GLOBALS['_MysqliSvRes'];}		return mysqli_affected_rows($res);}
	function mysql_error($res=NULL){ if(!$res && isset($GLOBALS['_MysqliSvRes'])){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_error($res);}
	function mysql_errno($res=NULL){ if(!$res && isset($GLOBALS['_MysqliSvRes'])){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_errno($res);}
	function mysql_set_charset($query,$res=NULL){		if(!$res){$res=$GLOBALS['_MysqliSvRes'];}		return mysqli_set_charset($res,$query);	}
	function mysql_select_db($database_name,$res=NULL){	if(!$res){$res=$GLOBALS['_MysqliSvRes'];}		return mysqli_select_db($res,$database_name);}
	function mysql_real_escape_string($str,$res=NULL){ if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_real_escape_string($res,$str);}
	function mysql_escape_string($str){return mysqli_real_escape_string($GLOBALS['_MysqliSvRes'],$str);}
	function mysql_num_rows($res){return mysqli_num_rows($res);}
	function mysql_client_encoding($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_character_set_name($res);}
	function mysql_create_db($db,$res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_query($res,"CREATE DATABASE `{$db}`");}
	function mysql_list_dbs($res=NULL){	if(!$res){$res=$GLOBALS['_MysqliSvRes'];} return mysqli_query($res,"SHOW DATABASES"); }
	function mysql_db_name($array,$i=0){return $array[$i];} //http://php.net/manual/en/function.mysql-db-name.php
	function mysql_db_query($db,$sql,$res=NULL){
		if(!$res){$res=$GLOBALS['_MysqliSvRes'];}
		mysqli_select_db ($res,$db) or mysqli_error($res);
		return mysqli_query($res,$sql);
	}
	function mysql_drop_db($db,$res=NULL){	return mysqli_query($res,"DROP `{$db}`"); }
	function mysql_fetch_lengths($res){ return mysqli_fetch_lengths($res);}
	function mysql_fetch_object($res,$classname='stdClass',$params=array()){ return mysqli_fetch_object($res,$classname,$params);}
	function mysql_field_flags($res,$field_offset=0){$o=mysqli_fetch_field_direct($res,$field_offset);	return sqli_h_flags2txt($o->flags);	}
	function mysql_field_len($res,$field_offset=0){$o=mysqli_fetch_field_direct($res,$field_offset); return $o->length;}
	function mysql_fetch_field($res,$offset=0){
		if(!mysqli_field_seek($res,$offset)){return false;}
		$obj= mysqli_fetch_field($res);
		$obj->not_null=0; //ok
		$obj->unique_key=0; //ok
		$obj->unsigned=0; //ok
		$obj->zerofill=0; //ok
		$obj->blob=0; //ok
		$obj->primary_key=0; //ok
		$obj->numeric=0;//ok
		$obj->multiple_key=0; //ok
		$obj->type_int=$obj->type;
		$obj->type=sqli_h_type2txt($obj->type);
		$obj->flags_str=sqli_h_flags2txt($obj->flags);
		$tmp=explode(' ',$obj->flags_str);
		foreach ($tmp as $val){
			if($val=='pri_key'){$val='primary_key';}
			elseif($val=='num'){$val='numeric';}
			if($val){$obj->$val=1;}
		}
		return $obj;
	}
	function mysql_field_name($res,$field=0){$obj=mysqli_fetch_field_direct($res,$field); return $obj->name;}
	function mysql_data_seek($res,$row_number){return  mysqli_data_seek($res,$row_number);}
	function mysql_field_seek($res,$field=0){ return mysqli_field_seek($res,$field);}
	function mysql_field_table($res,$field=0){ $obj=mysqli_fetch_field_direct($res,$field); return $obj->table;}
	function mysql_field_type($res,$field=0){ $obj=mysqli_fetch_field_direct($res,$field); return sqli_h_type2txt($obj->type);}
	function mysql_free_result($res){return mysqli_free_result($res);}
	function mysql_get_client_info(){return mysqli_get_client_info($GLOBALS['_MysqliSvRes']);}
	function mysql_get_host_info($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_get_host_info($res);}
	function mysql_get_proto_info($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_get_proto_info($res);}
	function mysql_get_server_info($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_get_server_info($res);}
	function mysql_info($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_info($res);}
	function mysql_listfields($db,$table,$res=NULL){return mysql_list_fields($db,$table,$res);} //alias
	function mysql_list_fields($db,$table,$res=NULL){
		if(!$res){$res=$GLOBALS['_MysqliSvRes'];}
		return mysqli_query($res,"SHOW COLUMNS FROM `{$db}`.`{$table}`");
	}
	function mysql_list_processes($res=NULL){
		if(!$res){$res=$GLOBALS['_MysqliSvRes'];}
		return array(
			'Id' => mysqli_thread_id($res),
			'User' => NULL,
			'Host' => NULL,
			'db' => mysqli_fetch_assoc(mysqli_query($res,"SELECT DATABASE() as current_database"))['current_database'],
			'Command' => 'Processlist',
			'Time' => -1,
			'State' => NULL,
			'Info' => NULL,
			);
	}
	function mysql_listtables($db,$res=NULL){return mysql_list_tables($db,$res);}
	function mysql_list_tables($db,$res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}return mysqli_query($res,"SHOW TABLES FROM `{$db}` ");}
	function mysql_num_fields($res){return mysqli_num_fields($res);}
	function mysql_ping($res){return mysqli_ping($res);}
	function mysql_result($result,$row,$field_name_or_offset=0){
		mysqli_data_seek($result,$row);
		//mysqli_field_seek($result,$field_name_or_offset);
		$data = mysqli_fetch_array($result);
		return $data[$field_name_or_offset];
	}
	function mysql_stat($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];} return mysqli_stat($res);}
	function mysql_tablename($res,$i=0){mysqli_data_seek($res,$i);	$t=mysqli_fetch_row($res);	return $t[0];}//input: mysql_list_tables($db)
	function mysql_thread_id($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];} return mysqli_thread_id($res);}
	function mysql_unbuffered_query($query,$res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];} return mysqli_query($res,$query,MYSQLI_USE_RESULT);}

	function sqli_h_type2txt($type_id){
		// TODO: IMPLEMENT LIST DEPENDING ON SERVER VERSION
		// $cl_info=mysqli_get_client_info($GLOBALS['_MysqliSvRes']);
		// this came from http://php.net/manual/en/mysqli-result.fetch-field-direct.php
		$mysql_data_type_hash = array(
			0=>'decimal',
			1=>'tinyint',
			2=>'smallint',
			3=>'int',
//			3=>'blob',
			4=>'float',
			5=>'double',
			6=>'null',
			7=>'timestamp',
			8=>'bigint',
			9=>'mediumint',
			10=>'date',
			11=>'time',
			12=>'datetime',
			13=>'year',
			14=>'newdate',
			16=>'bit',
			//252 is currently mapped to all text and blob types (MySQL 5.0.51a)
			246=>'decimal',
			247=>'enum',
			248=>'set',
			249=>'tiny_blob',
			250=>'medium_blob',
			251=>'long_blob',
			252=>'blob',
			253=>'varchar',
			// 253=>'var_string',
			254=>'char',
			//254=>'string',
			255=>'geomery',
		);
		if(isset($mysql_data_type_hash[$type_id])==3){return $mysql_data_type_hash[$type_id];}
		$types = array();
		$constants = get_defined_constants(true);
		foreach ($constants['mysqli'] as $c => $n) if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m)) $types[$n] = $m[1];
		$type= array_key_exists($type_id, $types)? strtolower($types[$type_id]) : NULL;
		return $type;
	}
	function sqli_h_flags2txt($flags_num){
		$flags = array();
		$constants = get_defined_constants(true);
		foreach ($constants['mysqli'] as $c => $n) if (preg_match('/MYSQLI_(.*)_FLAG$/', $c, $m)) if (!array_key_exists($n, $flags)) $flags[$n] = $m[1];
		$result = array();
		foreach ($flags as $n => $t) if ($flags_num & $n) $result[] = strtolower($t);
		return implode(' ', $result);
	}
}



/////////////////// 	legacy sql mode:
function prefixedTableFieldsWildcard($table,$alias_out='',$alias_query='',$db='',$exclusion=array()){
	$prefixed = array();
	if(!$alias_query){$alias_query=$table;}
	if(!$alias_out){$alias_out=$table;}
	if(!isset($GLOBALS['__sql_columns_structure_cache'][$db][$table])){
		$GLOBALS['__sql_columns_structure_cache'][$db][$table]=multiple_query("SHOW COLUMNS FROM ".($db?"`$db`.":"")."`$table`");
	}
	foreach ($GLOBALS['__sql_columns_structure_cache'][$db][$table] as $column){
		$field_name= $column["Field"];
		if(in_array($field_name,$exclusion)){continue;}
		$prefixed[] = "`{$alias_query}`.`{$field_name}` AS `".($db?$db.'.':'')."{$alias_out}.{$field_name}`";
	}
    return implode(", ", $prefixed);
}

function prefixedTableFieldsToInputName($composed_field_name){
	$out=0;
	foreach(explode('.',$composed_field_name) as $component){
		if(!$component){continue;}
		if(!$out){$out=$component;}
		else{$out.="[{$component}]";}		
	}
	return $out;
}

function tableFieldsListWildcard($table,$alias='',$db='',$exclusion=array()){
	$list = array();
	if(!$alias){$alias=$table;}
	if(!isset($GLOBALS['__sql_columns_structure_cache'][$db][$table])){
		$GLOBALS['__sql_columns_structure_cache'][$db][$table]=multiple_query("SHOW COLUMNS FROM ".($db?"`$db`.":"")."`$table`");
	}
	foreach ($GLOBALS['__sql_columns_structure_cache'][$db][$table] as $column){		$field_name= $column["Field"];
		if(in_array($field_name,$exclusion)){continue;}
		$list[] = "`{$alias}`.`{$field_name}`";
	}
    return implode(", ", $list);
}



function db_disconnect(){ global $th_mysql_cfg; @mysql_close($th_mysql_cfg['connection_res']);	}

function db_connect($server=-1) {
	global $th_mysql_cfg,$debug_thorr;
//    prea($th_mysql_cfg);	die; 
    $th_mysql_cfg['connection_res'] = @mysql_connect($th_mysql_cfg['host'], $th_mysql_cfg['username'],$th_mysql_cfg['pass']) or e500($th_mysql_cfg['db_connection_err_msg']);
	if($th_mysql_cfg['auto_disconnect_on_script_finish']){register_shutdown_function('db_disconnect');}
	
	// mysql_set_charset — Sets the client character set
	if($th_mysql_cfg['set_utf8_default_connection_charset']===true){ thfQ("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'"); }
	$db=$th_mysql_cfg['database_name'];
	
    if($db!='' and !@mysql_select_db($db)){//auto create db...
		thfQ("CREATE DATABASE `$db` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
		if(!@mysql_select_db($db)){e500($th_mysql_cfg['select_db_err_msg']);} 
		}
	
	//return $th_mysql_cfg; // mysql_close($dbcnx);
    } 

if($th_mysql_cfg['auto_connect']){ db_connect();	} //connecting to the database

function thfQ($query,$function_type='thfQ',$server=-1){ //$server=-1 the selected server, or the server index in config
	global $th_mysql_cfg,$debug_thorr;
//	$rez=mysqli_query($th_mysql_cfg['connection_res'],$query);
//	if(!$rez){ if($debug_thorr==1){die('count query eror: '.$str.' | '.mysqli_error($th_mysql_cfg['connection_res']));}	else{e500();} }
	//@$GLOBALS['thfQlog'][]=$query;
	if(!$function_type){$function_type='thfQ';}
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.start',[],0);
	$rez=mysql_query($query,$th_mysql_cfg['connection_res']);
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.end',func_get_args(),6);

	if(!$rez){
		if($debug_thorr==1){
			debug_print_backtrace();
			die($function_type.' query error: '.$query.' <br />'.thfQe($server));
			die('Line:'.__LINE__.'<br />File:'.__FILE__.'<br />Func:'.__FUNCTION__);	prea(get_defined_vars());		prea(get_declared_classes());		prea(get_declared_interfaces());		prea(get_defined_functions());
		}
		else{e500($function_type.' query error');}
	}
	return $rez;
	}
function thfQe($server=-1){ global $th_mysql_cfg;
	//daca nu am res de conectare, se poate rula si fara
	return mysql_error($th_mysql_cfg['connection_res']);
}
function thfQnf($rez){ global $th_mysql_cfg;
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.start',[],0);
	$r= mysql_num_fields($rez);
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__,func_get_args(),5);
    return $r;
}
function thfQfr($rez){  global $th_mysql_cfg;
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.start',[],0);
    $r= mysql_fetch_row($rez);
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.end',func_get_args(),5);
    return $r;
}
function thfQar($rez,$result_type = MYSQL_BOTH){  global $th_mysql_cfg;
	//$result_type= (MYSQL_ASSOC= 1; MYSQL_NUM=2; MYSQL_BOTH=3)
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.start',[],0);
    $r= mysql_fetch_array($rez,$result_type);
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.end',func_get_args(),5);
    return $r;
}
function thfQas($rez){ global $th_mysql_cfg;	return mysql_fetch_assoc($rez);}
function thfQli($server=-1){ global $th_mysql_cfg;
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.end',[],0);
	return mysql_insert_id($th_mysql_cfg['connection_res']);
}
function thfQafr($server=-1){ global $th_mysql_cfg;
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.start',[],0);
	$r= mysql_affected_rows($th_mysql_cfg['connection_res']);
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.end',func_get_args(),5);
    return $r;
}

function thfQsdb($db,$server=-1){ global $th_mysql_cfg;
	return mysql_select_db($db,$th_mysql_cfg['connection_res']);
}
function thfQsetSv($server,$db=''){ global $th_mysql_cfg;
	if($server!=-1){}
	if($db){thfQsdb($db,-1);}
}
function thfQgetSvRes(){global $th_mysql_cfg;
	return $th_mysql_cfg['connection_res'];
}


function sql_query($str,$die=1,$echo=1){
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.start',[],0);
	$r=thfQ($str,'sql_query',-1);
	AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.end',func_get_args(),5);
    return $r;
	}

function count_query($str,$server=-1){// $count=count_query("SELECT COUNT(*) FROM `tabel` WHERE `id`='$val' "); 
	$rez=thfQ($str,'count_query',$server);
	$data=thfQas($rez);
	if($data && is_array($data) && count($data)>0){foreach($data as $val){return @floor($val);}	}
	else return NULL;}
	
function one_query($str,$server=-1){// $data=one_query("SELECT `field` FROM `tabel` WHERE `id`='$val'  LIMIT 1 "); 
	$rez=thfQ($str,'one_query',$server);
	$data=thfQas($rez);
	if($data && is_array($data) && count($data)>0){foreach($data as $val){return $val;}	}
	else return NULL;
}

function many_query($str,$server=-1){// $data=many_query("SELECT * FROM `tabel` WHERE `id`='$val'  LIMIT 1 ");
	$rez=thfQ($str,'many_query',$server);
	return thfQas($rez);
}

function many_qa($tablename,$where,$return_query=false,$server=-1){
	$q="SELECT * FROM `$tablename` WHERE $where LIMIT 1 ";
	if($return_query){return $q;} return many_query($q,$server);
}

function insert_query($str,$server=-1){// insert_query("INSERT INTO `tabel` SET `col`='".q($val)."' , `col2`='".q($val2)."'");
	return (thfQ($str,'insert_query',$server)?thfQli($server):false);
	//thfQ($str,'insert_query',$server);	return thfQli($server);
}

function insert_qa($tablename,$a,$keys_to_exclude=array('id'),$setify_only_keys=array(),$return_query=false){

	$a=form_data_prepare($tablename,$a,$keys_to_exclude,$setify_only_keys);
	//prea($a); die;
	if(is_array($a) && count($a) && is_string($tablename) &&strlen($tablename)>0){
		$tablename='`'.trim($tablename,'`').'`';

		$q="INSERT INTO {$tablename} SET ";
/*		$q="INSERT INTO {$tablename} SET ";
		foreach($a as $i=>$v){
			if($v===NULL){$q.=" `$i`= NULL ,";}
			else{$q.=" `$i`='".q($v)."',";}
			}
		$q=trim($q,', ');*/
		$q="INSERT INTO {$tablename} ".setify_query($a);
		if($return_query){return $q;}
		return insert_query($q);
		}
	return false;
	}

function update_query($str,$server=-1){	// update_query("UPDATE `tabel` SET `col`='".q($val)."' , `col2`='".q($val2)."' WHERE `index`='4' LIMIT 1");
	thfQ($str,'update_query',$server);	return thfQafr($server);}

function update_qa($tablename,$a,$where,$limit='LIMIT 1',$return_query=false,$server=-1){
	if(strlen($limit)<1){$limit='LIMIT 1';}	
	if(is_array($a) && count($a)>0 && is_string($tablename) && strlen($tablename)>0){
		$q="UPDATE `$tablename` ".setify_query($a)." WHERE $where $limit";
		if($return_query){return $q;}
		else{return update_query($q,$server);}
		}
	else{return 0;}
	}
function update_qaf($tablename,$a,$where,$limit='LIMIT 1', $keys_to_exclude=array('id'), $setify_only_keys=array(), $return_query=false, $server=-1){
	$a=form_data_prepare($tablename,$a,$keys_to_exclude,$setify_only_keys);
	return update_qa($tablename,$a,$where,$limit,$return_query,$server);
	}

	
function delete_query($str,$server=-1){// delete_query("DELETE FROM `tabel` WHERE `index`='3' LIMIT 1");
	if(!thfQ($str,'delete_query',$server)){return false;} return true;}

function multiple_query($str,$id_fieldname=NULL,$safe=false,$server=-1){//$data=multiple_query("SELECT * FROM `tabel` WHERE `id`='$val'  LIMIT 100 ");
	$fields=array();
	$res=thfQ($str,'multiple_query',$server);
	while($data=thfQas($res)){
		if($id_fieldname){
			if($safe){$fields[s($data[$id_fieldname])]=$data;}
			else{$fields[$data[$id_fieldname]]=$data;}
			}
		else{$fields[]=$data;}
		}
	return $fields;
	}

//function setify_query($a){ if(is_array($a) && count($a)){	$q=" SET ";		foreach($a as $i=>$v){$q.=" `$i`='".q($v)."',";} return trim($q,', ');		} return NULL;	}
function setify_query($a,$set=' SET '){ if(is_array($a) && count($a)){	$q=" SET ";		$q=$set;
	foreach($a as $i=>$v){
		if($v===NULL && false){$q.=" `$i`= NULL ,";}
		else{$q.=" `$i`='".q($v)."',";}}
		
		return trim($q,', ');
		} 
		return NULL;	}

function describe_table($tablename,$db=NULL,$server=-1){
	global $th_mysql_cfg;
	//var_dump($db);  echo 'ddd';die;
	//!!!!!!!!!!!!!!!!!!! get current server id;
	$tablename='`'.trim($tablename,'`').'`';
	if($db){
		$db='`'.trim($db,'`').'`';
		$tablename=$db.'.'.$tablename;
	}

	if(!isset(	$th_mysql_cfg['describe_cache'][$db][$tablename])){
		$th_mysql_cfg['describe_cache'][$db][$tablename] = multiple_query("DESCRIBE $tablename ", 'Field', false);
	}
	return $th_mysql_cfg['describe_cache'][$db][$tablename];
}


//SHOW INDEX FROM `work_day_associates`  //'Column_name'
//INSERT INTO work_day_associates set team_id=25,associate_id=1557, work_day_id=64, hours=3.8   ON DUPLICATE KEY UPDATE hours=3.81


function insert_update($tablename,$a,$keys_to_exclude=array('id'),$setify_only_keys=array(),$return_query=false){
	$a=form_data_prepare($tablename,$a,$keys_to_exclude,$setify_only_keys);
	if(is_array($a) && count($a)>0 && is_string($tablename) && strlen($tablename)>0){
		
		foreach(table_indexes($tablename) as $index){// exclude keys from update command
			if($index['Non_unique']<1 && !in_array($index['Column_name'],$keys_to_exclude)){
				$keys_to_exclude[]=$index['Column_name'];
			}
			
		}
		$u=form_data_prepare($tablename,$a,$keys_to_exclude,$setify_only_keys);
		//prea($a); prea($u);
		if(!is_array($u) || count($u)<1){return false;}

		$q="INSERT INTO `$tablename` ".setify_query($a)."
		ON DUPLICATE KEY UPDATE ".setify_query($u,'');
		if($return_query){return $q;}
		
		if(!mysql_query($q)){global $debug_thorr; 
		if($debug_thorr==1){debug_print_backtrace();die('update query eror: '.$q.' | '.mysql_error());}else{e500();} }	else{return @mysql_affected_rows();}  
		//return insert_query($q);
	}
	else{	return false;	}
}

function table_indexes($tablename,$db=NULL,$server=-1){
	global $th_mysql_cfg;
	//!!!!!!!!!!!!!!!!!!! get current server id;
	$tablename='`'.trim($tablename,'`').'`';
	if($db){
		$db='`'.trim($db,'`').'`';
		$tablename=$db.'.'.$tablename;
	}
	if(!isset(	$th_mysql_cfg['table_indexes'][$db][$tablename])){
		$th_mysql_cfg['table_indexes'][$db][$tablename] = multiple_query("SHOW INDEX FROM $tablename ");
	}
	return $th_mysql_cfg['table_indexes'][$db][$tablename];
}




//RETURNS array; INPUT form_array = $_POST;  keys_to_exclude from SET OR keep only this keys
function form_data_prepare($table_name,$form_array,$keys_to_exclude=array('id'),$setify_only_keys=array(),$db=NULL,$server=-1){
	$out=array();	$d=describe_table($table_name);		if(count($d)<2 || !is_array($form_array)){return $out;}
	if($setify_only_keys && !is_array($setify_only_keys) && is_string($setify_only_keys) && strlen($setify_only_keys)){$setify_only_keys=explode(',',$setify_only_keys);}
	if($keys_to_exclude && !is_array($keys_to_exclude) && is_string($keys_to_exclude) && strlen($keys_to_exclude)){$keys_to_exclude=explode(',',$keys_to_exclude);}
	
	if(is_array($setify_only_keys) && count($setify_only_keys)){//only this keys
		foreach($setify_only_keys as $sk){	if(isset($d[$sk]) && isset($form_array[$sk])){$out[$sk]=$form_array[$sk];}	}
		}
	else{	foreach($form_array as $k=>$v){		if(isset($d[$k]) && !in_array($k,$keys_to_exclude)){$out[$k]=$v;}		}	}
	return $out;
	}


function thf_paginate_limit_sql($total_results=1,$curent_page=1,$results_per_page=50){
	$total_pages=ceil($total_results/$results_per_page);
	$curent_page=floor($curent_page);
	if($curent_page<1){$curent_page=1;}
	elseif($curent_page>$total_pages){$curent_page=$total_pages;}
	if($curent_page<2){return ' LIMIT '.$results_per_page;}
	return ' LIMIT '.(($curent_page-1)*$results_per_page).', '.$results_per_page;
	}
function get_sql_operator_from($fieldname,$operator,$fieldvalue){
	$out=" `$fieldname` ";
	if($operator=='='){		return $out."='".q($fieldvalue)."' ";}
	if($operator=='!='){	return $out."!='".q($fieldvalue)."' ";}
	if($operator=='>'){		return $out.">'".q($fieldvalue)."' ";}
	if($operator=='<'){		return $out."<'".q($fieldvalue)."' ";}
	if($operator=='like'){	return $out."LIKE '".q($fieldvalue)."' ";}
	if($operator=='like%.%'){	return $out."LIKE'%".q($fieldvalue)."%' ";}
	if($operator=='like%.'){	return $out."LIKE'%".q($fieldvalue)."' ";}
	if($operator=='like.%'){	return $out."LIKE'".q($fieldvalue)."%' ";}
	if($operator=='notlike%.%'){	return $out."NOT LIKE'%".q($fieldvalue)."%' ";}	
	}
function sql_parser($str){
	$sql=array();	//sql single command blocks
	$comment=false; 	// / *
	$diez=false;	// where 'a'=1; #some thing
	$minus2x=false; //-- comment
	$squot=false; //single quot
	$dquot=false; //double quot
	$tquot=false; //tilda ` quot

	$len=strlen($str); $str.='   ';//3x space at the end for safety
	$buffer=NULL;
	for($l=0;$l<$len;$l++){
		$i1=0; $i2=$i1+1; 
		
			if(!$comment && !$diez && !$minus2x && !$squot && !$dquot && $str[$l].$str[$l+1]=='/*'){$comment=true; $l+=$i2;}//enter comment procedure
		elseif($comment && $str[$l].$str[$l+1]=='*/'){$l+=$i2; $comment=false;}
		
		elseif(!$comment && !$diez && !$minus2x && !$squot && !$dquot && $str[$l].$str[$l+1]=='--'){$minus2x=true; $l+=$i2;}//enter comment procedure
		elseif($minus2x && $str[$l]=="\n"){$buffer.=' ';	$l+=$i1; $minus2x=false;	}
		
		elseif(!$comment && !$diez && !$minus2x && !$squot && !$dquot && $str[$l]=='#'){$diez=true; $l+=$i1;}//enter comment procedure
		elseif($diez && $str[$l]=="\n"){$buffer.=' ';	$l+=$i1; $diez=false;		}

		elseif(!$comment && !$diez && !$minus2x && !$squot && !$dquot && $str[$l]=="'"){$squot=true; $buffer.=$str[$l]; }//squot
		elseif($squot){	$buffer.=$str[$l];		if($str[$l]=="'" && $str[$l-1]!=chr(92)){$squot=false;}	}

		elseif(!$comment && !$diez && !$minus2x && !$squot && !$dquot && $str[$l]=='"'){$dquot=true; $buffer.=$str[$l]; }//dquot
		elseif($dquot){	$buffer.=$str[$l];		if($str[$l]=='"' && $str[$l-1]!=chr(92)){$dquot=false;}	}

		elseif(!$comment && !$diez && !$minus2x && !$squot && !$dquot && $str[$l]==';'){$sql[]=trim($buffer.';'); $buffer=NULL;}//buffer dump
		elseif(!$comment && !$diez && !$minus2x && !$squot && !$dquot){//buffer fill			
			//$buffer.=$str[$l]; //utf-8 fix
			$char = ord($str[$l]);
			if($char < 128){ $buffer.=$str[$l];}
			else{
				if($char < 224){$bytes = 2;}
				elseif($char < 240){$bytes = 3;}
				elseif($char < 248){$bytes = 4;}
				elseif($char == 252){$bytes = 5;}
				else{$bytes = 6;}
				$buffer.= substr($str, $l, $bytes);
				$l += $bytes-1;//-1 pt ca se incrementeaza oricum din for
				}
			}
		else{}//skipped chars
		
		}
	if($buffer && trim($buffer)){$sql[]=trim($buffer);$buffer=NULL;}
	return $sql;
	}

function sql_block_execute($sql_str,$debug=0){
	$true=0;$false=0;
	$arr=sql_parser($sql_str);
	foreach($arr as $q){
		if($debug){if(sql_query($q,0,1)){$true++;}else{$false++;}}
		else{if(sql_query($q)){$true++;}else{$false++;}}
		}
	return array('statements'=>count($arr),'true'=>$true,'false'=>$false);
	}
/*
$uniue_slug=array(//table_name => slug_colum
	'tbl_categories'=>'safe_name',
	'tbl_producers'=>'safe_name',
	'filtre_grupuri'=>'safe_name',
	'macrocategorii'=>'nume_safe',
	);
*/

function uniue_slug($slug=''){ global $uniue_slug;
	$tmp_match=array();	$tmp_match2=array();
	if(!isset($uniue_slug) || !is_array($uniue_slug)){$uniue_slug=array();}
	foreach($uniue_slug as $tbl=>$col){
		$multiple=multiple_query("SELECT `$col` FROM `$tbl`");
		foreach($multiple as $data){
			if(!isset($tmp_match[$data[$col]][$tbl])){$tmp_match[$data[$col]][$tbl]=1;}
			else{$tmp_match[$data[$col]][$tbl]++;}
			
			if(!isset($tmp_match2[$data[$col]][$tbl])){$tmp_match2[$data[$col]][$tbl]=1;}
			else{$tmp_match2[$data[$col]][$tbl]++;}
			}		
		}
	if(strlen($slug)){//unique slug
		foreach($tmp_match as $safe=>$tbl_array){
			if($slug==$safe){return FALSE;}
			}
		return TRUE;
		}
	else{// slug status:
		$out='';
		foreach($tmp_match as $safe=>$tbl_array){
			$te=''; $td='';	$tt='';			
			if($safe==''){
				foreach($tbl_array as $tk=>$tv){$te.=$tk.'('.$tv.'); ';}
				$out.='Empty slug in table(s): '.$te.PHP_EOL;
				}
			else{
				foreach($tbl_array as $tk=>$tv){
					if($tv>1){$td.=$tk.'('.$tv.'); ';}
					$tt.=$tk.', ';
					}
				if(count($tbl_array)>1){
					$out.='Duplicate entry `'.$safe.'` in tables: '.trim($tt,' ,').PHP_EOL;
					}
				if(strlen($td)){
					$out.='Duplicate entry `'.$safe.'` in table: '.$td.PHP_EOL;
					$td='';
					}
				}
			}
		if($out==''){return TRUE;}
		else{return $out;}
		}
	
	}



function get_server_value($colum_name){return one_query("SELECT `value` FROM `thf_server` WHERE `var`='".q($colum_name)."'  LIMIT 1 "); 	}

function submit_field($name='send',$val='Trimite',$disabled=0,$class='submit',$class_over='submit_over'){
	echo '<input type="submit" name="'.$name.'" id="'.$name.'" value="'.$val.'" class="'.$class.'" onmouseover="this.className=\''.$class_over.'\';" onmouseout="this.className=\''.$class.'\';" '.($disabled!=0 ? 'disabled="disabled"' : '').' />';}

function captcha_field($fieldname='security_code',$maxlength=5){ echo '<input name="'.$fieldname.'" type="text" class="camp_mic" id="'.$fieldname.'" style="text-transform:uppercase;" '."onblur=\"input_validate( 'security_code', 'camp_mic', 'camp_red_mic' );\" onclick=\"input_validate('security_code','camp_sel_mic','camp_red_mic');\" onkeyup=\"input_validate('security_code','camp_sel_mic','camp_red_mic');\" ".'value="" maxlength="'.$maxlength.'"  />'; }

//creez un camp in functie de ce este in baza de date
function form_field($tbl_name,$tbl_col,$filed_name_overwrite='',$templ=0,$valuee='',$required=0,$maxlen=0,$html_safe_fillter=0,$redaonly=0,$disabled=0){
	global $describe_tbl;	//structura tabelului
	//$templ=0 : TXT MARE;	$templ=1 : TXT MIC;	$templ=2 : PASS MARE;	$templ=3 : PASS MIC;	$templ=4 : TEXTAREA;
	// TO DO: $input_type=   checkbox	radio 	
	
	/*<span id="countdescriere">0</span> caractere  (<?php echo $rezervare_txt_max_len; ?> max)
    <script type="text/javascript">setInterval("textarea_len(<?php echo $rezervare_txt_max_len; ?>,'descriere','countdescriere')", 300 );</script>*/
	
	if($filed_name_overwrite==''){$filed_name_overwrite=$tbl_col;}// in caz ca numele la campul din form este diferit de numele la campul din tbl
	if($valuee==''){$valuee=trim($_POST[$filed_name_overwrite]);}//default value
	if($html_safe_fillter==1){$valuee=h($valuee);}
	if($tbl_name!='' && $tbl_col!=''){
		if(!is_array($describe_tbl[$tbl_name])){
			$res=thfQ("DESCRIBE `$tbl_name` "); //extragere maxlength
			while($data=thfQas($res)){	$describe_tbl[$tbl_name][$data['Field']]=$data;	}
			}
		$info_type=$describe_tbl[$tbl_name][$tbl_col]['Type'];
	
		if($info_type=='text'){$maxlength='';}
		elseif($info_type=='datetime'){$maxlength='maxlength="20" ';}
		elseif(substr_count($info_type,'(')==1 && substr_count($info_type,')')==1 ){
			$tmp=explode('(',$info_type);
			$tmp=explode(')',$tmp[1]);
			$maxlength='maxlength="'.$tmp[0].'" ';
			}
		else{die('form_field : unsupported table type...');}
		
		}
	elseif($maxlen>0){$maxlength='maxlength="'.$maxlen.'" ';}
	
	$template[0]['type']='<input type="text" ';
	$template[0]['name']='name="'.$filed_name_overwrite.'" id="'.$filed_name_overwrite.'" ';
	$template[0]['value']='value="'.$valuee.'" ';
	$template[0]['maxlength']=$maxlength;
	$template[0]['redaonly']=($redaonly==1 ? 'readonly="readonly" ' : '');
	$template[0]['disabled']=($disabled==1 ? 'disabled="disabled" ' : '');
	$template[0]['checked']=($checked==1 ? 'checked="checked" ' : '');//  pt checkbox sau pt radio???
	$template[0]['class_neselectat']='camp';
	$template[0]['class_selectat']='camp_sel';
	$template[0]['class_red']='camp_red';
	
	$template[1]=$template[0];
	$template[1]['class_neselectat']='camp_mic';
	$template[1]['class_selectat']='camp_sel_mic';
	$template[1]['class_red']='camp_red_mic';

	$template[2]=$template[0];
	$template[2]['type']='<input type="password" ';
	$template[2]['value']=''; if($templ==2){$valuee='';} //unset pt pass
	
	$template[3]=$template[2];
	$template[3]['class_neselectat']='camp_mic';
	$template[3]['class_selectat']='camp_sel_mic';
	$template[3]['class_red']='camp_red_mic';
	
	$template[4]=$template[0];
	$template[4]['type']='<textarea ';
	$template[4]['class_neselectat']='camp_termeni';
	$template[4]['class_selectat']='camp_termeni_sel';
	$template[4]['class_red']='camp_termeni_red';
	
	if($required==1 && $templ!=4){//validare java
		$rq ='onclick="input_validate('."'".$filed_name_overwrite."','".$template[$templ]['class_selectat']."','".$template[$templ]['class_red']."'".');" ';
		$rq.='onkeyup="input_validate('."'".$filed_name_overwrite."','".$template[$templ]['class_selectat']."','".$template[$templ]['class_red']."'".');" ';
		$rq.='onblur="input_validate('."'".$filed_name_overwrite."','".$template[$templ]['class_neselectat']."','".$template[$templ]['class_red']."'".');" ';
		//$rq.=' onchange="input_validate('."'".$filed_name_overwrite."','".$template[0]['class_selectat']."','".$template[0]['class_red']."'".');" ';
		}
	elseif($required==1 && $templ==4){
		die('textfield required cu ingrosare pe rosu neimplementat!!!!! in f_sql');
		//??? neimplementat... validate de lungime! + clasa culoare
		}
	else{//select simplu
		$rq ='onclick="this.className='."'".$template[$templ]['class_selectat']."'".';" ';
		$rq.='onblur="this.className='."'".$template[$templ]['class_neselectat']."'".';" ';
		}
	
	if($templ==4){//textarea
		echo $template[$templ]['type'];
		echo $template[$templ]['name'];
		//echo 'cols="45" rows="5" ';
		echo $template[$templ]['redaonly'];
		echo $template[$templ]['disabled'];
		if($required==1 && $_POST['send']!='' && $valuee==''){echo 'class="'.$template[$templ]['class_red'].'" ';}
		else{echo 'class="'.$template[$templ]['class_neselectat'].'" ';}
		echo $rq;
		echo '>'.$valuee.'</textarea>';
		}
	else{
		echo $template[$templ]['type'];
		echo $template[$templ]['name'];
		echo $template[$templ]['value'];
		echo $template[$templ]['maxlength']; //if != checkbox && radiob????
		echo $template[$templ]['redaonly'];
		echo $template[$templ]['disabled'];
		echo $template[$templ]['checked'];//  pt checkbox sau pt radio???
		if($required==1 && $_POST['send']!='' && $valuee==''){echo 'class="'.$template[$templ]['class_red'].'" ';}
		else{echo 'class="'.$template[$templ]['class_neselectat'].'" ';}
		echo $rq.' />';
		}
	
	
	}


/*
function pool($print_div=1,$voteaza_id=''){
	if(count_query("SELECT COUNT(*) FROM `pools` WHERE `finalizat`='nu' ")>0 ){//form voruti
		$pool=many_query("SELECT * FROM `pools` WHERE `finalizat`='nu' LIMIT 1 "); $out='';
		
		if($print_div==1){$out.='<div id="pool_container" onmouseout="rem_fx(this);" onmouseover="add_fx(this);">';}
		$out.='<div align="center" style="padding:5px 0 0 0;"><strong>'.h($pool['titlu']).'</strong></div>';
		
		if(is_numeric($voteaza_id) && $_SESSION['pool']!='votat'){//voteaza
			$val=one_query("SELECT `hits` FROM `pools_op` WHERE `id`='".q($voteaza_id)."'  LIMIT 1 "); $val++;
			update_query("UPDATE `pools_op` SET `hits`='$val' WHERE `id`='".q($voteaza_id)."' LIMIT 1 ");
			$_SESSION['pool']='votat';
			}
		if($_SESSION['pool']!='votat'){	$i=0;
			$res=mysql_query("SELECT * FROM `pools_op` WHERE `pool`= $pool[id] ORDER BY `id` ASC");
			while($data=mysql_fetch_array($res,1)){
				$out.='<div><label><input type="radio" name="vot" value="'.$data['id'].'" id="vot_'.$i.'" />'.h($data['optiune']).'</label></div>';
				$i++;}
			$out.='<div align="center" style="padding:5px 0 0 0;"><input type="button" name="save" id="save" value="Votează" class="submit" ';
			$out.='onmouseover="this.className=\'submit_over\';" onmouseout="this.className=\'submit\';" onclick="pool_send('.$i.');" /></div>';
			}
		else{
			$total_hits=0;	$res=mysql_query("SELECT * FROM `pools_op` WHERE `pool`= $pool[id] ORDER BY `id` ASC");
			while($data=mysql_fetch_array($res,1)){$total_hits+=$data['hits'];}
			$res=mysql_query("SELECT * FROM `pools_op` WHERE `pool`= $pool[id] ORDER BY `id` ASC");
			while($data=mysql_fetch_array($res,1)) 	$out.='<p>'.round( (100 * $data['hits']/$total_hits) , 1 ).'% '.h($data['optiune']).'</p>';
			$out.='<p align="center"><strong>Total '.$total_hits.' voturi.</strong></p>';
			}
		
		if($print_div==1){ $out.='</div>'; }elseif($print_div==-1){$_SESSION['pool']='nu';}
		echo $out;
		}//end of form voruti
	}//end of pool
*/




////  OPTIMIZE ALL TABLES  
function optimize_database($db='',$server=-1){
	// thfQsetSv($server,$db);
	$result = thfQ('SHOW TABLES') or die('Cannot get tables');
 	while($table = thfQfr($result)){
		thfQ('OPTIMIZE TABLE '.$table[0]) or die('Cannot optimize '.$table[0]);
		}
	}

function get_dbn($server=-1){// get current selected database name 
	/*
	
	de implementat exceptie pt sql lite
	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	
	*/
	return one_query('SELECT DATABASE() as current_database',$server);
	$rez=thfQ('SELECT DATABASE() as current_database','get_dbn',$server);
	$data=thfQas($rez);	return $data['current_database'];
	
//	$rez=mysql_query('SELECT DATABASE();');	if(!$rez){die('get database name eror! '.thfQe());}
//	$data=mysql_fetch_array($rez);	return $data[0];
}

function get_db_tables(){
    $tables=array();
	$result = thfQ('SHOW TABLES');
    while($row = thfQfr($result)){  $tables[$row[0]] = $row[0]; }
	ksort($tables);
	return $tables;
	}

function is_db_table($tbl_name){
	$tables = get_db_tables();
	foreach ($tables as $name){if($name==$tbl_name){return true;}}
	return false;
	}

function getTablePKeys($table_name,$db=''){
	$list=array(); if(!$db){$db=get_dbn();}
	$keys=many_query("SELECT GROUP_CONCAT(`COLUMN_NAME`) as 'list' FROM  `information_schema`.`columns` 
	WHERE  `table_schema`='".q($db)."'  AND `table_name`='".q($table_name)."' AND `COLUMN_KEY` LIKE '%PRI%'");
	if($keys && $keys['list']){$list=explode(',',$keys['list']);}
	return $list;
	}
function getTableUKeys($table_name,$db=''){
	$list=array(); if(!$db){$db=get_dbn();}
	$keys=many_query("SELECT GROUP_CONCAT(`COLUMN_NAME`) as 'list' FROM  `information_schema`.`columns` 
	WHERE  `table_schema`='".q($db)."'  AND `table_name`='".q($table_name)."' AND `COLUMN_KEY` LIKE '%UNI%'");
	if($keys && $keys['list']){$list=explode(',',$keys['list']);}
	return $list;
	}

function export_create_db_structure($dbn=''){
	if(!$dbn){$dbn=get_dbn();}
	$return="# ".date("Y-m-d H:i:s")." by thorr framework:
SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";
#DROP DATABASE IF EXISTS `$dbn`;
#CREATE DATABASE `$dbn` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER DATABASE `$dbn` CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `$dbn`;
";
	$tables = get_db_tables();

	foreach($tables as $table){
		//$return.= 'DROP TABLE '.$table.';';
		$row2 = thfQfr(thfQ('SHOW CREATE TABLE `'.$table.'`'));
		$row2[1] = str_replace("\n","\r\n",$row2[1]);
		$return.= "\r\n\r\n".$row2[1].";\r\n\r\n";
	}
	return $return;
}


function backup_all_databases_daily($sv_path='/home/itrepair/mysql_bk/',$exclude=array(),$max_sql_size=15,$precizion="Y-m-d",$copies_to_keep=25){

    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.start',[],0);

	if(!$sv_path || substr($sv_path,0,1)=='.'){return false;}
	$sv_path = trim(str_replace('\\', '/', $sv_path)); //replace windows paths
	$sv_path = rtrim($sv_path,'/').'/'; //add end slash
	if(!is_dir($sv_path) ){@mkdir($sv_path, 0775,true); @chmod($sv_path,0775);}

	$dbn=get_dbn();
	$dbs=multiple_query("SHOW DATABASES");
	if(count($dbs)<1){return false;}
	foreach($dbs as $db){
		$db=$db['Database'];
		echo $db . '<br>';
		if(in_array($db,array('information_schema','performance_schema')+$exclude)){continue;}
		thfQ("USE `$db`");
		$status=backup_database_v2($sv_path,$max_sql_size,$precizion);
	}
	if($dbn){thfQ("USE `$dbn`");}
	if($status){//clean old files here:
		$myDirectory = opendir($sv_path);	// open this directory
		$dirArray=array();
		while($entryName = readdir($myDirectory)) {
			if(filetype($sv_path.$entryName)=='dir' && $entryName!='.' && $entryName!='..'){
	//			echo $entryName."\r\n";
				$dirArray[filemtime($sv_path.$entryName)]=$entryName;
				}
			}
		closedir($myDirectory);	// close directory
		krsort($dirArray); //prea($dirArray);
		if(count($dirArray)>=$copies_to_keep && $copies_to_keep>1){
			$loop=1;
			foreach($dirArray as $timestamp=>$dirname){
				if($loop>$copies_to_keep){
					//echo '<br />'.$sv_path.$dirname;
					deleteAll($sv_path.$entryName);
				}
				$loop++;
			}

		}
	}
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.end',func_get_args(),5);

    return true;
}

function backup_database_v2($path='',$max_sql_size=20,$precizion="Y-m-d",$exclude_tables=array()){
    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.start',[],0);

    $dbn=get_dbn(); if(!$dbn){return false;}
	if(!$path){$path=THF_UPLOAD;}
	$path.=date($precizion).'/'.s($dbn).'/';
	if(!is_dir($path)){	mkdir($path,0755,true);	}
	//else{return true;}
	//set_time_limit(20000); //setat individual la fiecarea bucla mai jos!!!
	ini_set('memory_limit', '-1');
	
	$filename = s($dbn).'_create_structures_only.zip';
	if(is_file($path.$filename)){return true;}

	if(is_file($path.$filename)){unlink($path.$filename);}
	$sql_str=export_create_db_structure($dbn);
	$zip = new ZipArchive();
	if ($zip->open($path.$filename, ZIPARCHIVE::CREATE)!==TRUE) { return false; die("cannot open <$filename>\n");}
	$zip->addFromString(s($dbn).'_create_separat.sql', $sql_str );
	$zip->close();

	$zip_part=0; //must be zero; ai to 1
	$max_sql_bite_size=1024*1024*$max_sql_size; //50 mb
	$row_counter=0; //loop counter! must be zero!

	
	
	$return="# ".date("Y-m-d H:i:s")." by thorr framework:
SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";
#DROP DATABASE IF EXISTS `$dbn`;
#CREATE DATABASE `$dbn` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
#ALTER DATABASE `$dbn` CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `$dbn`;
";	
	$tables = get_db_tables();
	//cycle through
	foreach($tables as $table){
		if(in_array($table,$exclude_tables)){continue;}

		$result = thfQ('SELECT * FROM `'.$table.'`');
		$num_fields = thfQnf($result);
		
		//$return.= 'DROP TABLE '.$table.';';
		$row2 = thfQfr(thfQ('SHOW CREATE TABLE `'.$table.'`'));
		$row2[1] = str_replace("\n","\r\n",$row2[1]);
		$return.= "\r\n\r\n".$row2[1].";\r\n\r\n";
		
		for ($i = 0; $i < $num_fields; $i++){
			while($row = thfQfr($result)){
				$row_counter++;
				$return.= 'INSERT INTO `'.$table.'` VALUES( ';
				for($j=0; $j<$num_fields; $j++){
					//$row[$j] = addslashes($row[$j]);
					//$row[$j] = str_replace("\r\n","\\r\\n",$row[$j]);
					//if(isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					$return.= "'".q($row[$j])."'";
					if ($j<($num_fields-1)) { $return.= ','; }
					}
				$return.= ");\r\n";
				
				if($row_counter%$max_sql_size<1 && $result && strlen($return)>$max_sql_bite_size ){
					//check every 75 x times if the strlen of the $return buffers overflows
					$zip_part++;
					$zip = new ZipArchive();
					$filename = s($dbn).'_part'.str_pad($zip_part, 5, '0', STR_PAD_LEFT).'.zip';
					if(is_file($path.$filename)){unlink($path.$filename);}

					if ($zip->open($path.$filename, ZIPARCHIVE::CREATE)!==TRUE) { return false; die("cannot open <$filename>\n");}
					$zip->addFromString(s($dbn).'_part'.str_pad($zip_part, 5, '0', STR_PAD_LEFT).'.sql', $return );
					//$zip->addFile($thisdir . "/too.php","/testfromfile.php");
					//echo "numfiles: " . $zip->numFiles . "\n";
					//echo "status:" . $zip->status . "\n";
					$zip->close();
					$return='';
					set_time_limit(600);
					}
				
				}
			}
		}
	
	if($return){// save the last buffer 
		$zip_part++;
		$zip = new ZipArchive();
		$filename = s($dbn).'_part'.str_pad($zip_part, 5, '0', STR_PAD_LEFT).'.zip';
		if(is_file($path.$filename)){unlink($path.$filename);}

		if ($zip->open($path.$filename, ZIPARCHIVE::CREATE)!==TRUE) { return false; die("cannot open <$filename>\n");}
		$zip->addFromString(s($dbn).'_part'.str_pad($zip_part, 5, '0', STR_PAD_LEFT).'.sql', $return );
		//$zip->addFile($thisdir . "/too.php","/testfromfile.php");
		//echo "numfiles: " . $zip->numFiles . "\n";
		//echo "status:" . $zip->status . "\n";
		$zip->close();
		$return='';
	}

    AfrStandAloneLogger::getInstance()->mark(__FUNCTION__.'.end',func_get_args(),5);

	return true;

	}




function restore_db($backup_path_file){
	if(!is_file($backup_path_file) || !is_readable($backup_path_file)){return false;}
	$pathinfo=pathinfo($backup_path_file);
	$zip = new ZipArchive;
	$filename=array();
	if ($zip->open($backup_path_file) == TRUE) {
		$zip_elements=$zip->numFiles;
		for ($i = 0; $i < $zip_elements; $i++) {
			$filename[] = $zip->getNameIndex($i);
			}
		}
	$filename=$filename[ count($filename)-1 ];//last index :P
	$contents='#Inport Contents:'."\r\n";
	$fp = $zip->getStream($filename);
    if($fp){
		while (!feof($fp)) {$contents .= fread($fp, 2);  }
		fclose($fp);
		}
	else{return false;}
	$queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $contents);
	//prea($queries); die;
	foreach ($queries as $query){   if (strlen(trim($query)) > 0) thfQ($query); } 
	return true;
	}


function convert_db_tables_to_engine($db='',$from='InnoDB',$to='MyISAM',$server=-1){
	if(!$db){$db=get_dbn($server);}
	if(!$db){return array();} //no database selected
	$statements=multiple_query("SELECT  CONCAT('ALTER TABLE `', table_name, '` ENGINE=$to;') AS sql_statements
FROM    information_schema.tables AS tb
WHERE   table_schema = '$db'
AND     `ENGINE` = '$from'
AND     `TABLE_TYPE` = 'BASE TABLE'");
	
	
}

//////////////////////////////////////////////////////////////////////

	function log_operations_init($db=''){
		if($db){$db="`$db`.";}
		return thfQ("CREATE TABLE IF NOT EXISTS $db`logs_operations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tab` varchar(40) NOT NULL,
  `table_id` int(11) NOT NULL,
  `col` varchar(40) NOT NULL,
  `new_val` varchar(255) NOT NULL,
  `uid` smallint(6) NOT NULL DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `op` enum('i','u','d','ud') NOT NULL DEFAULT 'u' COMMENT 'insert/ update/ delete/ update_col_delete=1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;"); }

	function log_operations($tab,$tab_id,$col,$new_val,$op='u',$db=''){
		log_operations_init($db);
		if($db){$db="`$db`.";}
		$uid=(isset($GLOBALS['login'])?$GLOBALS['login']->get_uid():0);
		$tab=q($tab);	$tab_id=q($tab_id);		$col=q($col);		$new_val=q($new_val);
		return insert_query("INSERT INTO $db`logs_operations` (`tab`, `table_id`, `col`, `new_val`, `uid`, `ts`, `op`) VALUES 
		('$tab', '$tab_id', '$col', '$new_val', '$uid', CURRENT_TIMESTAMP, '$op'); ");
	}


	function retrive_log_operations($tab,$tab_id,$op='*',$db=''){
		if($db){$db="`$db`.";}
		global $__user_cache;
		if(!isset($__user_cache) || count($__user_cache)<1){
			$__user_cache=array();
			$res=thfQ("SELECT id,full_name FROM $db`thf_users` ");
			while($row = mysql_fetch_assoc($res)){
				$__user_cache[$row['id']]=$row;
			}
		}
		$opsql= ($op=='*'?'':" AND `op`='$op' ");
		
		$out=array();
		thfQ("SET SESSION group_concat_max_len = 4196;");
		$q="SELECT ls.*, GROUP_CONCAT(CONCAT(ls.uid,'~',ls.ts,'~',ls.new_val) SEPARATOR '|') as uids_tss FROM $db`logs_operations` as ls WHERE `tab` = '".q($tab)."' AND `table_id` = '".floor($tab_id)."' GROUP BY `col` ";
		$res=thfQ($q);
		while($row = mysql_fetch_assoc($res)){
			$col_log=array();				$col_log_str='';

			$tmp=explode('|',$row['uids_tss']);
			foreach($tmp as $rv){
				$rsvt=explode('~',$rv);
				$col_log[$rsvt[1]]=$__user_cache[$rsvt[0]]['full_name'].'~'.$rsvt[2];
			}
			krsort($col_log);
			foreach($col_log as $ts=>$name){
				$name=explode('~',$name);
				$col_log_str.=($col_log_str?"\r\n":'').$name[0].' ['.$ts.'] '.($name[1]?$name[1]:'-').'';
			}
			$out[$row['col']]=$col_log_str;
		}
		//$out['q']=$q;
		return $out;
	}



function table_mapper($tbl,$postare=array(),$post_key='map',$main_col='idt',$main_col_id=0,$link_col='ids',$orderCol='ord',$logOp=0){
	//prea($postare); die;
	if(!isset($postare[$post_key]) || count($postare[$post_key])<1){
		delete_query("DELETE FROM $tbl WHERE `$main_col`='".q($main_col_id)."' ");//remove all mapped
		if($logOp){log_operations($tbl,$main_col_id,$main_col,0,'d');}
		return true;
	}
	elseif(is_array($postare[$post_key]) && count($postare[$post_key])){
		$tbl_model=multiple_query("SELECT $link_col FROM $tbl WHERE $main_col='$main_col_id' ",$link_col);//fetch existent

		foreach($postare[$post_key] as $ord=>$new_map_id){
			$ins=array();
			if(!isset($tbl_model[$new_map_id])){
				$ins[$main_col]=$main_col_id;
				$ins[$link_col]=$new_map_id;
				if($orderCol){ $ins[$orderCol]=$ord; }
				$new_id=insert_qa($tbl,$ins);
				//log_operations($tab,$tab_id,$col,$new_val,$op='u',$db='')
				if($logOp){log_operations($tbl,$main_col_id,$main_col.'_'.$link_col,$new_map_id,'i');}
			}
			elseif($orderCol){
				update_qaf($tbl, array($orderCol=>$ord), "`$main_col`='$main_col_id' AND `$link_col`='$new_map_id'", 'LIMIT 1');//update order
				if($logOp){log_operations($tbl,$main_col_id,$orderCol,$ord,'u');}
			}
		}
		$imploded=@implode(',',$postare[$post_key]);
		if(strlen($imploded) && substr_count($imploded,'Array')<1){
			delete_query("DELETE FROM $tbl WHERE `$main_col`='".q($main_col_id)."' AND $link_col NOT IN (".$imploded.")");//remove old entrys
			if($logOp){log_operations($tbl,$main_col_id,$main_col,$imploded,'d');}
		}
		return $imploded;
	}
	else{return false;}
}
