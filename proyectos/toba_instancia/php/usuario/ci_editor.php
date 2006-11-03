<?php

class ci_editor extends toba_ci
{
	protected $s__filtro;
	const clave_falsa = "xS34Io9gF2JD";					//La clave no se envia al cliente

	function datos($tabla)
	{
		return	$this->controlador->dep('datos')->tabla($tabla);
	}

	function conf()
	{
		$usuario = $this->datos('basica')->get();
		$desc = 'Usuario: <strong>' . $usuario['nombre'] . '</strong>';
		$this->pantalla()->set_descripcion($desc);	
	}

	//---- Info BASICA -------------------------------------------------------

	function evt__basica__modificacion($datos)
	{
		if ($datos['clave'] == self::clave_falsa ) {
			unset($datos['clave']);	
		}
		$this->datos('basica')->set($datos);
	}

	function conf__basica()
	{
		$datos = $this->datos('basica')->get();
		if (isset($datos)) {
			$datos['clave'] = self::clave_falsa;
		}
		return $datos;
	}

	//---- Asociacion a PROYECTOS -------------------------------------------------

	function evt__proyecto__modificacion($datos)
	{
		if ($datos['clave'] == self::clave_falsa ) {
			unset($datos['clave']);	
		}
		$this->datos('basica')->set($datos);
	}

	function conf__proyecto()
	{
		$datos = $this->datos('basica')->get();
		if (isset($datos)) {
			$datos['clave'] = self::clave_falsa;
		}
		return $datos;
	}

}
?>