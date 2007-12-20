<?php

class toba_referencia_fuente_datos extends toba_fuente_datos
{
	/**
	*	Una vez conectado a la base se crea una tabla temporal conteniendo el usuario actual 
	*/
	function post_conectar()
	{
		$usuario = toba::usuario()->get_id();
		if (isset($usuario)) {
			$sql = 'CREATE TEMP TABLE tt_usuario ( usuario VARCHAR(30) );';
			$sql .= "INSERT INTO tt_usuario (usuario) VALUES ('$usuario')";		 	
	   		$this->db->ejecutar($sql);
		}
	}
	
}


?>