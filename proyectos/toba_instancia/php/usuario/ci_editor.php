<?php

class ci_editor extends toba_ci
{
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

	function evt__proyecto__salida()
	{
		$this->datos('proyecto')->resetear_cursor();		
	}

	function evt__cuadro_proyectos__seleccion($seleccion)
	{
		$this->datos('proyecto')->set_cursor($seleccion);
	}
	
	function conf__cuadro_proyectos($componente)
	{
		$componente->set_datos($this->datos('proyecto')->get_filas());
	}

	function evt__form_proyectos__alta($datos)
	{
		$this->datos('proyecto')->nueva_fila($datos);
	}

	function evt__form_proyectos__modificacion($datos)
	{
		if (isset($datos['clave']) && $datos['clave'] == self::clave_falsa ) {
			unset($datos['clave']);	
		}
		$this->datos('proyecto')->set($datos);
		$this->datos('proyecto')->resetear_cursor();
	}

	function evt__form_proyectos__baja()
	{
		$this->datos('proyecto')->eliminar_fila( $this->datos('proyecto')->get_cursor() );
	}
	
	function evt__form_proyectos__cancelar()
	{
		$this->datos('proyecto')->resetear_cursor();
	}
	
	function conf__form_proyectos($componente)
	{
		if ($this->datos('proyecto')->hay_cursor()) {
			$datos = $this->datos('proyecto')->get();
			if (isset($datos)) {
				$datos['clave'] = self::clave_falsa;
			}
			$componente->set_datos($datos);
		}
	}
}
?>