<?php

class toba_referencia_fuente_datos extends toba_fuente_datos
{
	/**
	*	Una vez conectado a la base se crea una tabla temporal conteniendo el usuario actual 
	*/
	function post_conectar()
	{
		$usuario = toba::usuario()->get_id();
		$id_solicitud = toba::instancia()->get_id_solicitud();		
		if (! isset($usuario)) {
			$usuario = 'publico';
		}
		$sql = 'CREATE TEMP TABLE tt_usuario ( usuario VARCHAR(30), id_solicitud INTEGER);';
		$sql .= "INSERT INTO tt_usuario (usuario, id_solicitud) VALUES ('$usuario', $id_solicitud)";		 	
	   	$this->db->ejecutar($sql);
		
	}
	
}


?>