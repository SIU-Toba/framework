<?php
/* 
V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  MSSQL support via ODBC. Requires ODBC. Works on Windows and Unix. 
  For Unix configuration, see http://phpbuilder.com/columns/alberto20000919.php3
*/

if (!defined('_ADODB_ODBC_LAYER')) {
	include(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}

 
class  ADODB_odbc_mssql extends ADODB_odbc {	
	var $databaseType = 'odbc_mssql';
	var $fmtDate = "'Y-m-d'";
	var $fmtTimeStamp = "'Y-m-d h:i:sA'";
	var $_bindInputArray = true;
	var $metaTablesSQL="select name from sysobjects where type='U' or type='V' and (name not in ('sysallocations','syscolumns','syscomments','sysdepends','sysfilegroups','sysfiles','sysfiles1','sysforeignkeys','sysfulltextcatalogs','sysindexes','sysindexkeys','sysmembers','sysobjects','syspermissions','sysprotects','sysreferences','systypes','sysusers','sysalternates','sysconstraints','syssegments','REFERENTIAL_CONSTRAINTS','CHECK_CONSTRAINTS','CONSTRAINT_TABLE_USAGE','CONSTRAINT_COLUMN_USAGE','VIEWS','VIEW_TABLE_USAGE','VIEW_COLUMN_USAGE','SCHEMATA','TABLES','TABLE_CONSTRAINTS','TABLE_PRIVILEGES','COLUMNS','COLUMN_DOMAIN_USAGE','COLUMN_PRIVILEGES','DOMAINS','DOMAIN_CONSTRAINTS','KEY_COLUMN_USAGE'))";
	var $metaColumnsSQL = "select c.name,t.name,c.length from syscolumns c join systypes t on t.xusertype=c.xusertype join sysobjects o on o.id=c.id where o.name='%s'";
	var $hasTop = 'top';		// support mssql/interbase SELECT TOP 10 * FROM TABLE
	var $sysDate = 'GetDate()';
	var $sysTimeStamp = 'GetDate()';
	var $leftOuter = '*=';
	var $rightOuter = '=*';
	var $ansiOuter = true; // for mssql7 or later
	var $identitySQL = 'select @@IDENTITY'; // 'select SCOPE_IDENTITY'; # for mssql 2000
	var $hasInsertID = true;
	
	function ADODB_odbc_mssql()
	{
		$this->ADODB_odbc();
	}

	function xServerInfo()
	{
		$row = $this->GetRow("execute sp_server_info 2");
		$arr['description'] = $row[2];
		$arr['version'] = ADOConnection::_findvers($arr['description']);
		return $arr;
	}
	
	
	function _insertid()
	{
	// SCOPE_IDENTITY()
	// Returns the last IDENTITY value inserted into an IDENTITY column in 
	// the same scope. A scope is a module -- a stored procedure, trigger, 
	// function, or batch. Thus, two statements are in the same scope if 
	// they are in the same stored procedure, function, or batch.
			return $this->GetOne($this->identitySQL);
	}
	
	function MetaTables()
	{
		return ADOConnection::MetaTables();
	}
	
	function MetaColumns($table)
	{
		return ADOConnection::MetaColumns($table);
	}
	
	// "Stein-Aksel Basma" <basma@accelero.no>
	// tested with MSSQL 2000
	function MetaPrimaryKeys($table)
	{
		$sql = "select k.column_name from information_schema.key_column_usage k,
		information_schema.table_constraints tc 
		where tc.constraint_name = k.constraint_name and tc.constraint_type =
		'PRIMARY KEY' and k.table_name = '$table'";
		
		$a = $this->GetCol($sql);
		if ($a && sizeof($a)>0) return $a;
		return false;	  
	}
	
	// Format date column in sql string given an input format that understands Y M D
	function SQLDate($fmt, $col=false)
	{	
		if (!$col) $col = $this->sysDate;
		$s = '';
		
		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			if ($s) $s .= '+';
			$ch = $fmt[$i];
			switch($ch) {
			case 'Y':
			case 'y':
				$s .= "datename(yyyy,$col)";
				break;
			case 'M':
			case 'm':
				$s .= "replace(str(month($col),2),' ','0')";
				break;
			
			case 'Q':
			case 'q':
				$s .= "datename(quarter,$col)";
				break;
				
			case 'D':
			case 'd':
				$s .= "replace(str(day($col),2),' ','0')";
				break;
			default:
				if ($ch == '\\') {
					$i++;
					$ch = substr($fmt,$i,1);
				}
				$s .= $this->qstr($ch);
				break;
			}
		}
		return $s;
	}
} 
 
class  ADORecordSet_odbc_mssql extends ADORecordSet_odbc {	
	
	var $databaseType = 'odbc_mssql';
	
	function ADORecordSet_odbc_mssql($id,$mode=false)
	{
		return $this->ADORecordSet_odbc($id,$mode);
	}	
}
?>