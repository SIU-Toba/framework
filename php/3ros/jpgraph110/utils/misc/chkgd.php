<?php
//=======================================================================
// File:	CHKGD.PHP
// Description:	Check which version of GD is installed
// Created: 	2002-10-13
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id: chkgd.php,v 1.1.1.1 2004/06/15 19:27:30 cvs Exp $
//
// License:	This code is released under QPL 1.0 
// Copyright (C) 2001,2002 Johan Persson 
//========================================================================


function CheckGDVersion($aInfo=true) {
    ob_start();
    phpinfo(8); // Just get the modules loaded
    $a = ob_get_contents();
    ob_end_clean();
    if( preg_match('/.*GD Version.*(1[0-9|\.]+).*/',$a,$m) ) {
	$r=1;$v=$m[1];
    }
    elseif( preg_match('/.*GD Version.*(2[0-9|\.]+).*/',$a,$m) ) {
	$r=2;$v=$m[1];
    }
    else {
	$r=0;$v=$m[1];
    }

    if( $aInfo ) {
	if( $r==1 )
	    echo "You have GD 1 installed. Version: $v (or higher)\n";
	elseif( $r==2 ) 
	    echo "You have GD 2 installed. Version: $v (or higher)\n";
	else
	    echo "You don't seem to have any GD support in your PHP installation";
    }

    return array($r,$v);
}

echo "<b>Checking GD version ...</b>\n<p>";

CheckGDVersion();

?>