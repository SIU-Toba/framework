<?php 

    //$this->observar("info","Esta es una observacion");
 	//echo comunicador::empaquetar($this->datos_recibidos, false);
 	
 	$sql = "INSERT INTO apex_elemento_infra_input (elemento_infra, datos) 
 			VALUES ('item','".addslashes(serialize($this->datos_recibidos))."')";

	global $db;
	if ($db["instancia"][apex_db_con]->Execute($sql) === false){
		$mensaje = "TODO MAL: " . $db["instancia"][apex_db_con]->ErrorMsg();
	}else{
		$mensaje = "OK";
	}

	//echo $mensaje;
	echo comunicador::empaquetar(array($mensaje), false);
 	
     
?> 
