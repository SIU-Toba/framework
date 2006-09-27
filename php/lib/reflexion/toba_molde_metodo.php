<?php
require_once('toba_molde_elemento.php');

abstract class toba_molde_metodo extends toba_molde_elemento
{
	protected $nombre;
	protected $parametros;
	protected $comentarios;
	protected $contenido;
	
	function __construct($nombre, $parametros=array(), $comentarios=array(), $descripcion=null)
	{
		$this->descripcion = isset($descripcion) ? $descripcion : $this->nombre;
		$this->nombre = $nombre;
		if(!is_array($parametros)){
			throw new toba_error("Error en el metodo: $nombre. Los parametros debern ser un array");	
		}
		$this->parametros = $parametros;
		if(!is_array($comentarios)){
			throw new toba_error("Error en el metodo: $nombre. Los comentarios debern ser un array");	
		}
		$this->comentarios = $comentarios;
	}
	
	function get_nombre()
	{
		return $this->nombre;	
	}
		
	function set_contenido($contenido)
	{
		if ( !is_array($contenido) ) {
			$this->contenido = explode( salto_linea() ,$contenido);
		}
		$this->contenido = $contenido;
	}

	//--- Generacion ------------------------------------
	
	abstract function get_declaracion();
	
	function get_codigo()
	{
		$funcion = '';
		// Comentarios
		foreach($this->comentarios as $fila) {
			$funcion .= $this->identado() . "//$fila" . salto_linea();
		}
		// Cabecera
		$funcion .= $this->identado() . $this->get_declaracion() . salto_linea();
		$funcion .= $this->identado() . "{" . salto_linea();
		// Contenido
		$this->identar(1);
		foreach($this->contenido as $fila) {
			$funcion .= $this->identado() . "$fila" . salto_linea();
		}
		$this->identar(-1);
		$funcion .= $this->identado() ."}" . salto_linea();
		return $funcion;
	}
}
?>