<?php
/*
V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	  Set tabs to 4 for best viewing.
  
  Latest version of ADODB is available at http://php.weblogs.com/adodb
  ======================================================================
  
 This file provides PHP4 session management using the ADODB database
wrapper library.
 
 Example
 =======
 
 	GLOBAL $HTTP_SESSION_VARS;
	include('adodb.inc.php');
	include('adodb-session.php');
	session_start();
	session_register('AVAR');
	$HTTP_SESSION_VARS['AVAR'] += 1;
	print "<p>\$HTTP_SESSION_VARS['AVAR']={$HTTP_SESSION_VARS['AVAR']}</p>";
	
To force non-persistent connections, call adodb_session_open first before session_start():

 	GLOBAL $HTTP_SESSION_VARS;
	include('adodb.inc.php');
	include('adodb-session.php');
	adodb_session_open(false,false,false);
	session_start();
	session_register('AVAR');
	$HTTP_SESSION_VARS['AVAR'] += 1;
	print "<p>\$HTTP_SESSION_VARS['AVAR']={$HTTP_SESSION_VARS['AVAR']}</p>";

 
 Installation
 ============
 1. Create this table in your database (syntax might vary depending on your db):
 
  create table sessions (
	   SESSKEY char(32) not null,
	   EXPIRY int(11) unsigned not null,
	   EXPIREREF varchar(64),
	   DATA text not null,
	  primary key (sesskey)
  );


  2. Then define the following parameters in this file:
  	$ADODB_SESSION_DRIVER='database driver, eg. mysql or ibase';
	$ADODB_SESSION_CONNECT='server to connect to';
	$ADODB_SESSION_USER ='user';
	$ADODB_SESSION_PWD ='password';
	$ADODB_SESSION_DB ='database';
	$ADODB_SESSION_TBL = 'sessions'
	
  3. Recommended is PHP 4.0.6 or later. There are documented
	 session bugs in earlier versions of PHP.

  4. If you want to receive notifications when a session expires, then
  	 you can tag a session with an EXPIREREF, and before the session
	 record is deleted, we can call a function that will pass the EXPIREREF
	 as the first parameter, and the session key as the second parameter.
	 
	 To do this, define a notification function, say NotifyFn:
	 
	 	function NotifyFn($expireref, $sesskey)
	 	{
	 	}
	 
	 Then define a global variable, with the first parameter being the
	 global variable you would like to store in the EXPIREREF field, and
	 the second is the function name.
	 
	 In this example, we want to be notified when a user's session 
	 has expired, so we store the user id in $USERID, and make this
	 the value stored in the EXPIREREF field:
	 
	 	$ADODB_SESSION_EXPIRE_NOTIFY = array('USERID','NotifyFn');
*/

if (!defined('_ADODB_LAYER')) {
	include (dirname(__FILE__).'/adodb.inc.php');
}

if (!defined('ADODB_SESSION')) {

 define('ADODB_SESSION',1);
 
 /* if database time and system time is difference is greater than this, then give warning */
 define('ADODB_SESSION_SYNCH_SECS',60); 

/****************************************************************************************\
	Global definitions
\****************************************************************************************/
GLOBAL 	$ADODB_SESSION_CONNECT, 
	$ADODB_SESSION_DRIVER,
	$ADODB_SESSION_USER,
	$ADODB_SESSION_PWD,
	$ADODB_SESSION_DB,
	$ADODB_SESS_CONN,
	$ADODB_SESS_LIFE,
	$ADODB_SESS_DEBUG,
	$ADODB_SESSION_EXPIRE_NOTIFY,
	$ADODB_SESSION_CRC;
	
	
	$ADODB_SESS_LIFE = ini_get('session.gc_maxlifetime');
	if ($ADODB_SESS_LIFE <= 1) {
	 // bug in PHP 4.0.3 pl 1  -- how about other versions?
	 //print "<h3>Session Error: PHP.INI setting <i>session.gc_maxlifetime</i>not set: $ADODB_SESS_LIFE</h3>";
	 	$ADODB_SESS_LIFE=1440;
	}
	$ADODB_SESSION_CRC = false;
	//$ADODB_SESS_DEBUG = true;
	
	//////////////////////////////////
	/* SET THE FOLLOWING PARAMETERS */
	//////////////////////////////////
	
	if (empty($ADODB_SESSION_DRIVER)) {
		$ADODB_SESSION_DRIVER='mysql';
		$ADODB_SESSION_CONNECT='localhost';
		$ADODB_SESSION_USER ='root';
		$ADODB_SESSION_PWD ='';
		$ADODB_SESSION_DB ='xphplens_2';
	}
	
	if (empty($ADODB_SESSION_EXPIRE_NOTIFY)) {
		$ADODB_SESSION_EXPIRE_NOTIFY = false;
	}
	//  Made table name configurable - by David Johnson djohnson@inpro.net
	if (empty($ADODB_SESSION_TBL)){
		$ADODB_SESSION_TBL = 'sessions';
	}
	
	/*
	$ADODB_SESS['driver'] = $ADODB_SESSION_DRIVER;
	$ADODB_SESS['connect'] = $ADODB_SESSION_CONNECT;
	$ADODB_SESS['user'] = $ADODB_SESSION_USER;
	$ADODB_SESS['pwd'] = $ADODB_SESSION_PWD;
	$ADODB_SESS['db'] = $ADODB_SESSION_DB;
	$ADODB_SESS['life'] = $ADODB_SESS_LIFE;
	$ADODB_SESS['debug'] = $ADODB_SESS_DEBUG;
	
	$ADODB_SESS['debug'] = $ADODB_SESS_DEBUG;
	$ADODB_SESS['table'] = $ADODB_SESS_TBL;
	*/
	
/****************************************************************************************\
	Create the connection to the database. 
	
	If $ADODB_SESS_CONN already exists, reuse that connection
\****************************************************************************************/
function adodb_sess_open($save_path, $session_name,$persist=true) 
{
GLOBAL $ADODB_SESS_CONN;
	if (isset($ADODB_SESS_CONN)) return true;
	
GLOBAL 	$ADODB_SESSION_CONNECT, 
	$ADODB_SESSION_DRIVER,
	$ADODB_SESSION_USER,
	$ADODB_SESSION_PWD,
	$ADODB_SESSION_DB,
	$ADODB_SESS_DEBUG;
	
	// cannot use & below - do not know why...
	$ADODB_SESS_CONN = ADONewConnection($ADODB_SESSION_DRIVER);
	if (!empty($ADODB_SESS_DEBUG)) {
		$ADODB_SESS_CONN->debug = true;
		ADOConnection::outp( " conn=$ADODB_SESSION_CONNECT user=$ADODB_SESSION_USER pwd=$ADODB_SESSION_PWD db=$ADODB_SESSION_DB ");
	}
	if ($persist) $ok = $ADODB_SESS_CONN->PConnect($ADODB_SESSION_CONNECT,
			$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB);
	else $ok = $ADODB_SESS_CONN->Connect($ADODB_SESSION_CONNECT,
			$ADODB_SESSION_USER,$ADODB_SESSION_PWD,$ADODB_SESSION_DB);
	
	if (!$ok) ADOConnection::outp( "<p>Session: connection failed</p>",false);
}

/****************************************************************************************\
	Close the connection
\****************************************************************************************/
function adodb_sess_close() 
{
global $ADODB_SESS_CONN;

	if ($ADODB_SESS_CONN) $ADODB_SESS_CONN->Close();
	return true;
}

/****************************************************************************************\
	Slurp in the session variables and return the serialized string
\****************************************************************************************/
function adodb_sess_read($key) 
{
global $ADODB_SESS_CONN,$ADODB_SESSION_TBL,$ADODB_SESSION_CRC;

	$rs = $ADODB_SESS_CONN->Execute("SELECT data FROM $ADODB_SESSION_TBL WHERE sesskey = '$key' AND expiry >= " . time());
	if ($rs) {
		if ($rs->EOF) {
			$v = '';
		} else 
			$v = rawurldecode(reset($rs->fields));
			
		$rs->Close();
		
		// new optimization adodb 2.1
		$ADODB_SESSION_CRC = strlen($v).crc32($v);
		
		return $v;
	}
	
	return ''; // thx to Jorma Tuomainen, webmaster#wizactive.com
}

/****************************************************************************************\
	Write the serialized data to a database.
	
	If the data has not been modified since adodb_sess_read(), we do not write.
\****************************************************************************************/
function adodb_sess_write($key, $val) 
{
	global
		$ADODB_SESS_CONN, 
		$ADODB_SESS_LIFE, 
		$ADODB_SESSION_TBL,
		$ADODB_SESS_DEBUG, 
		$ADODB_SESSION_CRC,
		$ADODB_SESSION_EXPIRE_NOTIFY;

	$expiry = time() + $ADODB_SESS_LIFE;
	
	// crc32 optimization since adodb 2.1
	// now we only update expiry date, thx to sebastian thom in adodb 2.32
	if ($ADODB_SESSION_CRC !== false && $ADODB_SESSION_CRC == strlen($val).crc32($val)) {
		if ($ADODB_SESS_DEBUG) echo "<p>Session: Only updating date - crc32 not changed</p>";
		$qry = "UPDATE $ADODB_SESSION_TBL SET expiry=$expiry WHERE sesskey='$key' AND expiry >= " . time();
		$rs = $ADODB_SESS_CONN->Execute($qry);	
		return true;
	}
	$val = rawurlencode($val);
	
	$arr = array('sesskey' => $key, 'expiry' => $expiry, 'data' => $val);
	if ($ADODB_SESSION_EXPIRE_NOTIFY) {
		$var = reset($ADODB_SESSION_EXPIRE_NOTIFY);
		global $$var;
		$arr['expireref'] = $$var;
	}
	$rs = $ADODB_SESS_CONN->Replace($ADODB_SESSION_TBL,$arr,
    	'sesskey',$autoQuote = true);
	
	if (!$rs) {
		ADOConnection::outp( '<p>Session Replace: '.$ADODB_SESS_CONN->ErrorMsg().'</p>',false);
	}  else {
		// bug in access driver (could be odbc?) means that info is not commited
		// properly unless select statement executed in Win2000
		if ($ADODB_SESS_CONN->databaseType == 'access') 
			$rs = $ADODB_SESS_CONN->Execute("select sesskey from $ADODB_SESSION_TBL WHERE sesskey='$key'");
	}
	return !empty($rs);
}

function adodb_sess_destroy($key) 
{
	global $ADODB_SESS_CONN, $ADODB_SESSION_TBL,$ADODB_SESSION_EXPIRE_NOTIFY;
	
	if ($ADODB_SESSION_EXPIRE_NOTIFY) {
		reset($ADODB_SESSION_EXPIRE_NOTIFY);
		$fn = next($ADODB_SESSION_EXPIRE_NOTIFY);
		$savem = $ADODB_SESS_CONN->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $ADODB_SESS_CONN->Execute("SELECT expireref,sesskey FROM $ADODB_SESSION_TBL WHERE sesskey='$key'");
		$ADODB_SESS_CONN->SetFetchMode($savem);
		if ($rs) {
			$ADODB_SESS_CONN->BeginTrans();
			while (!$rs->EOF) {
				$ref = $rs->fields[0];
				$key = $rs->fields[1];
				$fn($ref,$key);
				$del = $ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE sesskey='$key'");
				$rs->MoveNext();
			}
			$ADODB_SESS_CONN->CommitTrans();
		}
	} else {
		$qry = "DELETE FROM $ADODB_SESSION_TBL WHERE sesskey = '$key'";
		$rs = $ADODB_SESS_CONN->Execute($qry);
	}
	return $rs ? true : false;
}

function adodb_sess_gc($maxlifetime) 
{
	global $ADODB_SESS_DEBUG, $ADODB_SESS_CONN, $ADODB_SESSION_TBL,$ADODB_SESSION_EXPIRE_NOTIFY;
	
	if ($ADODB_SESSION_EXPIRE_NOTIFY) {
		reset($ADODB_SESSION_EXPIRE_NOTIFY);
		$fn = next($ADODB_SESSION_EXPIRE_NOTIFY);
		$savem = $ADODB_SESS_CONN->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $ADODB_SESS_CONN->Execute("SELECT expireref,sesskey FROM $ADODB_SESSION_TBL WHERE expiry < " . time());
		$ADODB_SESS_CONN->SetFetchMode($savem);
		if ($rs) {
			$ADODB_SESS_CONN->BeginTrans();
			while (!$rs->EOF) {
				$ref = $rs->fields[0];
				$key = $rs->fields[1];
				$fn($ref,$key);
				$del = $ADODB_SESS_CONN->Execute("DELETE FROM $ADODB_SESSION_TBL WHERE sesskey='$key'");
				$rs->MoveNext();
			}
			$ADODB_SESS_CONN->CommitTrans();
		}
	} else {
		$qry = "DELETE FROM $ADODB_SESSION_TBL WHERE expiry < " . time();
		$ADODB_SESS_CONN->Execute($qry);
	
		if ($ADODB_SESS_DEBUG) ADOConnection::outp("<p><b>Garbage Collection</b>: $qry</p>");
	}
	// suggested by Cameron, "GaM3R" <gamr@outworld.cx>
	if (defined('ADODB_SESSION_OPTIMIZE')) {
	global $ADODB_SESSION_DRIVER;
	
		switch( $ADODB_SESSION_DRIVER ) {
			case 'mysql':
			case 'mysqlt':
				$opt_qry = 'OPTIMIZE TABLE '.$ADODB_SESSION_TBL;
				break;
			case 'postgresql':
			case 'postgresql7':
				$opt_qry = 'VACUUM '.$ADODB_SESSION_TBL;	
				break;
		}
		if (!empty($opt_qry)) {
			$ADODB_SESS_CONN->Execute($opt_qry);
		}
	}
	
	$rs = $ADODB_SESS_CONN->SelectLimit('select '.$ADODB_SESS_CONN->sysTimeStamp.' from '. $ADODB_SESSION_TBL,1);
	if ($rs && !$rs->EOF) {
	
		$dbt = reset($rs->fields);
		$rs->Close();
		$dbt = $ADODB_SESS_CONN->UnixTimeStamp($dbt);
		$t = time();
		if (abs($dbt - $t) >= ADODB_SESSION_SYNCH_SECS) {
		global $HTTP_SERVER_VARS;
			$msg = "adodb-session.php: Server time for webserver {$HTTP_SERVER_VARS['HTTP_HOST']} not in synch: database=$dbt, webserver=".$t;
			error_log($msg);
			if ($ADODB_SESS_DEBUG) ADOConnection::outp("<p>$msg</p>");
		}
	}
	
	return true;
}

session_module_name('user'); 
session_set_save_handler(
	"adodb_sess_open",
	"adodb_sess_close",
	"adodb_sess_read",
	"adodb_sess_write",
	"adodb_sess_destroy",
	"adodb_sess_gc");
}

/*  TEST SCRIPT -- UNCOMMENT */

if (0) {
GLOBAL $HTTP_SESSION_VARS;

	session_start();
	session_register('AVAR');
	$HTTP_SESSION_VARS['AVAR'] += 1;
	ADOConnection::outp( "<p>\$HTTP_SESSION_VARS['AVAR']={$HTTP_SESSION_VARS['AVAR']}</p>",false);
}

?>