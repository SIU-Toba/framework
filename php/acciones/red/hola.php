<?php

	//Solicitud de pruba, enviar listado de usuarios

	global $ADODB_FETCH_MODE, $db, $cronometro;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT usuario, nombre FROM apex_usuario;";
	$rs = $db["instancia"][apex_db_con]->Execute($sql);
	if((!$rs)||($rs->EOF)){
		$mensaje["status"] = "Error";
	}else{
		$mensaje['usuarios'] = $rs->getArray();
	}	
	echo comunicador::empaquetar($mensaje, false);
	
?>