<?php

class usuario_toba
{
	var $id;
	var $datos;
	
	function __construct($id)
	{
		$this->id = $id;
		$sql = "SELECT nombre 
				FROM apex_usuario
				WHERE usuario='{$this->id}'";
		$datos = toba::get_db('instancia')->consultar($sql);
		$this->datos = $datos[0];
		if (empty($this->datos)) {
			throw new excepcion_toba("");
		}
	}
	
	function nombre()
	{
		return $this->datos['nombre'];
	}
	
	function id()
	{
		return $this->id;	
	}

}


?>