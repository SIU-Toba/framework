<?php
//==============================================================================
// Name:        JPGENDBDRIVER.PHP
// Description:	Driver for scanning project files to update the DB
// Created: 	2002-06-06 22:50 
// Author:		johanp@aditus.nu
// Version: 	$Id: jpgendbdriver.php,v 1.1.1.1 2004/06/15 19:27:30 cvs Exp $
//
// License:	QPL 1.0
// Copyright (C) 2002 Johan Persson
//
//==============================================================================

include 'de_utils.php';
include 'jpgendb.php';

class ScanProjFiles {
	var $iProjname;
	var $iDBUtils;
	var $iDB;
	
	function ScanProjFiles($aDBUtils) {
		$this->iDBUtils = $aDBUtils;
		$this->iDB = $aDBUtils->iDBServer;
	}
	
	function Run($aProjname,$aForceUpdate=false) {
		$this->iProjname = $aProjname;
		
		HTMLGenerator::CloseWinButton();
		
		echo "<b>Scanning files for project '$aProjname'</b><br>";
				
		// Find full filename of all project files in the project
		$proj = $this->iDBUtils->GetProject($aProjname);
		$projidx = $proj['fld_key'];
		
		echo "<i>($proj[fld_projdir])</i><p>\n";
		
		$q = "SELECT * FROM tbl_projfiles WHERE fld_projidx=$projidx";
		$res = $this->iDB->Query($q);
		$n = $res->NumRows();
		$ptimer = new JpgTimer();
		while( $n-- > 0 ) {
			$r = $res->Fetch();
			$fname = $proj['fld_projdir'].'/'.$r['fld_name'];
			$modtime=@filemtime($fname);
			if( $modtime == false ) {
				die("Can't access file: $fname");
			}
			
			$dbtime = strtotime($r['fld_dbupdtime']);
			if( $aForceUpdate || $modtime > $dbtime ) {
			    
				echo "Parsing '".basename($fname)."'...\n";flush();
				$dbdriver = new DBDriver($aProjname,$fname,$this->iDB);
				$ptimer->Push();
				$dbdriver->Run();
				$t = round($ptimer->Pop()/1000,2);
				$q = "UPDATE tbl_projfiles SET fld_dbupdtime=now() WHERE fld_key=".$r['fld_key'];
				$this->iDB->Query($q);
				echo "[in $t s]<br>\n";
			}
			else {
				echo "DB is up to date for '".basename($fname)."'<br>\n";
			}
		}
		echo "<p><h3>Done.</h3>";
		HTMLGenerator::CloseWinButton();
	}
}

class DbGenDriver extends DocEditDriver {
	function Run($aForceUpdate=false) {
		if( !empty($this->iProjidx) && $this->iProjidx > 0 ) {
			$scan = new ScanProjFiles($this->iDBUtils);
			$projname = $this->iDBUtils->GetProjNameForKey($this->iProjidx);//$regen_projidx);
			$scan->Run($projname,$aForceUpdate);
		}
		else echo "No project index";
	}
}

$force    = @$HTTP_GET_VARS['force'] ;

if( isset($force) && $force=='true' )
	$force=1;
else
	$force=0;

$driver = new DbGenDriver();
$driver->Run($force);
$driver->Close();
?>