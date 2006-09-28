<?php
require_once('toba_molde_elemento.php');

class toba_molde_separador extends toba_molde_elemento
{
	protected $descripcion;
	protected $tipo;
	
	function __construct($nombre, $descripcion=null, $tipo='chico')
	{
		$this->nombre = $nombre;
		$this->descripcion = isset($descripcion) ? $descripcion : $this->nombre;
		if( ($tipo != 'chico') && ($tipo != 'grande') ) {
			throw new toba_error('Error en la construccion del molde_separador: los tipos validos son \'chico\' y \'grande\'. Tipo solicitado: ' .$tipo . ' - Separador "' . $nombre . '"' );
		}
		$this->tipo = $tipo;
	}
	
	function get_tipo()
	{
		return $this->tipo;	
	}

	function get_descripcion()
	{
		return $this->descripcion;
	}
	
	function get_codigo()
	{
		$metodo = 'separador_' . $this->tipo;
		return $this->$metodo();
	}

	function separador_chico()
	{	
		$salida = $this->identado() . "//---- {$this->nombre} -------------------------------------------------------";	
		$salida .= salto_linea();
		return $salida;
	}	
	
	function separador_grande()
	{
		$salida = $this->identado() . "//-------------------------------------------------------------------";
		$salida .= salto_linea();
		$salida .= $this->identado() . "//--- {$this->nombre}";
		$salida .= salto_linea();
		$salida .= $this->identado() . "//-------------------------------------------------------------------";
		$salida .= salto_linea();
		return $salida;
	}	
}
?>