<?php
/**
 * @ignore
 */
abstract class toba_codigo_metodo extends toba_codigo_elemento
{
	protected $descripcion;
	protected $parametros;
	protected $comentarios;
	protected $contenido = array();
	protected $mostrar_comentarios = true;
	protected $phpdoc;
	protected $tipo_funcion = '';
	
	function __construct($nombre, $parametros=array(), $comentarios=array(), $descripcion=null)
	{
		$this->nombre = $nombre;
		$this->descripcion = isset($descripcion) ? $descripcion : $this->nombre;
		if(!is_array($parametros)){
			throw new toba_error_asistentes("Error en el metodo: $nombre. Los PARAMETROS deben ser un array");	
		}
		$this->parametros = $parametros;
		if(!is_array($comentarios)){
			throw new toba_error_asistentes("Error en el metodo: $nombre. Los COMENTARIOS deben ser un array");	
		}
		$this->comentarios = $comentarios;
	}
	
	function set_mostrar_comentarios($activado=true)
	{
		$this->mostrar_comentarios = $activado;
	}
	
	function get_comentarios()
	{
		return implode("\n", $this->comentarios);
	}
	
	function get_descripcion()
	{
		return $this->descripcion;	
	}
	
	function set_contenido($contenido)
	{
		if ( !is_array($contenido) ) {
			$this->contenido = explode( "\n" ,$contenido);
		} else {
			$this->contenido = $contenido;
		}
	}

	function set_doc($doc)
	{
		$this->phpdoc = $doc;
	}
	
	function get_doc()
	{
		return $this->phpdoc;
	}

	function set_tipo_funcion($tipo)
	{
		$this->tipo_funcion = $tipo;
	}

	function get_tipo_funcion()
	{
		return $this->tipo_funcion;
	}

	//--- Generacion ------------------------------------
	
	abstract function get_declaracion();
	
	function get_codigo()
	{
		$funcion = '';
		if ( $this->mostrar_comentarios && !empty($this->comentarios)) {
			// Comentarios
			$funcion .= $this->identado()."/**"."\n";
			foreach($this->comentarios as $fila) {
				$funcion .= $this->identado() . " * $fila" . "\n";
			}
			$funcion .= $this->identado()." */"."\n";
		}
		// Cabecera
		$funcion .= $this->identado() . $this->get_declaracion() . "\n";
		$funcion .= $this->identado() . "{" . "\n";
		// Contenido
		$this->identar(1);
		foreach($this->contenido as $fila) {
			$funcion .= $this->identado() . "$fila" . "\n";
		}
		$this->identar(-1);
		$funcion .= $this->identado() ."}" . "\n";
		return $funcion;
	}
}
?>